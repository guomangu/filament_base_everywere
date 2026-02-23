<?php

namespace App\Livewire\Circle;

use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;
    use \App\Traits\HandlesOfferActions;
    public \App\Models\Circle $circle;
    public string $message = '';
    public $showInformationManager = false;
    public $showMemberManager = false;
    public $guestName = '';
    public $guestContact = '';

    protected function rules()
    {
        return [
            'message' => 'required|min:2|max:1000',
            'guestName' => auth()->guest() ? 'required|min:2|max:50' : 'nullable',
            'guestContact' => auth()->guest() ? 'required|min:2|max:100' : 'nullable',
        ];
    }

    public $lastMessageCount = 0;
    public $lastPendingCount = 0;
    public $attachment = null; // ['type' => 'user', 'id' => 1, 'name' => '...']
    public $upload = null;

    public function mount(\App\Models\Circle $circle)
    {
        $this->circle = $circle;
        $this->refresh();

        $this->lastMessageCount = $this->circle->messages->count();
        $this->lastPendingCount = $this->circle->members()->where('status', 'pending')->count();
        
        $this->dispatchContext();
    }

    public function dispatchContext()
    {
        $items = collect([
            ['type' => 'circle', 'id' => $this->circle->id, 'name' => $this->circle->name],
            ['type' => 'user', 'id' => $this->circle->owner_id, 'name' => $this->circle->owner->name],
        ]);
        
        foreach($this->circle->activeMembers as $m) {
            $items->push(['type' => 'user', 'id' => $m->user_id, 'name' => $m->user->name]);
        }
        
        foreach($this->circle->achievements as $a) {
            $items->push(['type' => 'skill', 'id' => $a->skill_id, 'name' => $a->skill->name]);
        }

        $this->dispatch('updateMessagingContext', items: $items->unique(fn($o) => $o['type'].$o['id'])->values()->toArray())->to(\App\Livewire\GlobalMessaging::class);
    }

    public function joinCircle()
    {
        if (!auth()->check()) return redirect()->route('login');
        
        \App\Models\CircleMember::updateOrCreate(
            ['circle_id' => $this->circle->id, 'user_id' => auth()->id()],
            ['status' => 'active', 'role' => 'member', 'joined_at' => now()]
        );

        $this->circle->load('members');
    }

    public function leaveCircle()
    {
        if (!auth()->check()) return;

        \App\Models\CircleMember::where('circle_id', $this->circle->id)
            ->where('user_id', auth()->id())
            ->delete();

        $this->circle->load('members');
    }

    public function toggleApprove($memberId, $status)
    {
        if (auth()->id() !== $this->circle->owner_id) return;

        $member = \App\Models\CircleMember::findOrFail($memberId);
        $member->update(['status' => $status]);

        $this->refresh();
    }

    public function refresh()
    {
        $this->circle->load([
            'owner.achievements.skill',
            'owner.proches.achievements.skill',
            'activeMembers.user',
            'messages' => fn($q) => $q->with('sender')->latest()->take(20),
            'achievements.skill'
        ]);

        $currentMessageCount = $this->circle->messages->count();
        $currentPendingCount = $this->circle->members()->where('status', 'pending')->count();

        if ($currentMessageCount !== $this->lastMessageCount || $currentPendingCount !== $this->lastPendingCount) {
            $this->dispatch('circle-updated');
        }
        
        $this->lastMessageCount = $currentMessageCount;
        $this->lastPendingCount = $currentPendingCount;
    }

    public function sendMessage()
    {
        $rules = [
            'message' => 'required|min:2|max:1000',
            'upload' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf|max:10240', // 10MB limit
        ];
        if (auth()->guest()) {
            $rules['guestName'] = 'required|min:2|max:50';
            $rules['guestContact'] = 'required|min:2|max:100';
        }
        $this->validate($rules);
        
        $metadata = $this->attachment ? ['attachment' => $this->attachment] : [];
        
        if (auth()->guest()) {
            $metadata['guest'] = [
                'name' => $this->guestName,
                'contact' => $this->guestContact,
            ];
        }

        if ($this->upload) {
            $path = $this->upload->store('attachments', 'public');
            $metadata['file'] = [
                'path' => $path,
                'name' => $this->upload->getClientOriginalName(),
                'type' => $this->upload->getMimeType(),
                'size' => $this->upload->getSize(),
            ];
        }

        \App\Models\Message::create([
            'circle_id' => $this->circle->id,
            'sender_id' => auth()->id(),
            'content' => $this->message,
            'type' => 'chat',
            'metadata' => !empty($metadata) ? $metadata : null,
        ]);

        $this->message = '';
        $this->attachment = null;
        $this->upload = null;
        $this->guestName = '';
        $this->guestContact = '';
        $this->circle->load('messages.sender');
        $this->dispatch('messageSent');
    }

    public function selectAttachment($type, $id, $name)
    {
        $this->attachment = [
            'type' => $type,
            'id' => $id,
            'name' => $name
        ];
    }

    public function removeAttachment()
    {
        $this->attachment = null;
    }

    public function render()
    {
        try {
            // 1. Get Member IDs (including owner) for project aggregation
            $memberIds = $this->circle->activeMembers()->pluck('user_id')->push($this->circle->owner_id)->unique();

            // 2. Member Projects (owned or member of)
            $memberProjects = \App\Models\Project::where(function($q) use ($memberIds) {
                $q->whereIn('owner_id', $memberIds)
                  ->orWhereHas('members', function($subQ) use ($memberIds) {
                      $subQ->where('memberable_type', 'App\\Models\\User')
                           ->whereIn('memberable_id', $memberIds)
                           ->where('status', 'active');
                  });
            })
            ->where('is_open', true)
            ->with(['owner', 'activeMembers'])
            ->latest()
            ->take(20)
            ->get();

            // 3. Aggregate all offers & demands from these projects
            $memberOffers = \App\Models\ProjectOffer::whereIn('project_id', $memberProjects->pluck('id'))
                ->with(['project', 'informations'])
                ->latest()
                ->take(20)
                ->get()
                ->sortByDesc(fn($o) => $o->type === 'offer');
        } catch (\Exception $e) {
            // If anything fails, return empty collections
            $memberProjects = collect([]);
            $memberOffers = collect([]);
        }

        $trustScore = $this->circle->getAverageTrustScore();
        $location = $this->circle->city ? ' à ' . $this->circle->city : '';
        
        return view('livewire.circle.profile', [
            'memberProjects' => $memberProjects ?? collect([]),
            'memberOffers' => $memberOffers ?? collect([]),
        ])->layoutData([
            'title' => 'Cercle ' . $this->circle->name . $location . ' | Expertise & Confiance (' . $trustScore . '%)',
            'description' => \Illuminate\Support\Str::limit('Rejoignez le cercle ' . $this->circle->name . $location . '. Une communauté locale de confiance avec un score de fiabilité de ' . $trustScore . '%. ' . strip_tags($this->circle->description), 160, '...'),
            'og_image' => $this->circle->owner->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($this->circle->owner->name),
            'breadcrumbCircle' => $this->circle,
        ]);
    }
}
