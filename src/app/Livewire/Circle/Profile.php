<?php

namespace App\Livewire\Circle;

use Livewire\Component;

class Profile extends Component
{
    public \App\Models\Circle $circle;
    public string $message = '';
    public $showInformationManager = false;
    public $showMemberManager = false;

    protected $rules = [
        'message' => 'required|min:2|max:1000',
    ];

    public $lastMessageCount = 0;
    public $lastPendingCount = 0;

    public function mount(\App\Models\Circle $circle)
    {
        $this->circle = $circle;
        $this->refresh();

        $this->lastMessageCount = $this->circle->messages->count();
        $this->lastPendingCount = $this->circle->members()->where('status', 'pending')->count();
    }

    public function joinCircle()
    {
        if (!auth()->check()) return redirect()->route('login');
        
        \App\Models\CircleMember::updateOrCreate(
            ['circle_id' => $this->circle->id, 'user_id' => auth()->id()],
            ['status' => 'pending', 'role' => 'member', 'joined_at' => now()]
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
            $this->lastMessageCount = $currentMessageCount;
            $this->lastPendingCount = $currentPendingCount;
        }
    }

    public function sendMessage()
    {
        $this->validate();

        \App\Models\Message::create([
            'circle_id' => $this->circle->id,
            'sender_id' => auth()->id(),
            'content' => $this->message,
            'type' => 'chat',
        ]);

        $this->message = '';
        $this->circle->load('messages.sender');
    }

    public function render()
    {
        try {
            // 1. Get Immediate Active Members (including owner)
            $memberIds = $this->circle->activeMembers()->pluck('user_id')->push($this->circle->owner_id)->unique();

            // 2. Immediate Experts & Skills (including Proches)
            $memberExperts = \App\Models\User::whereIn('id', $memberIds)
                ->with([
                    'achievements' => fn($q) => $q->where('title', '!=', '__SKELETON__')->with('skill'),
                    'proches.achievements' => fn($q) => $q->where('title', '!=', '__SKELETON__')->with('skill')
                ])
                ->get()
                ->filter(fn($u) => $u->achievements->count() > 0 || $u->proches->flatMap->achievements->count() > 0);

            // 3. Distance 2 Circle Identifiers
            $secondaryCircleIds = \App\Models\CircleMember::whereIn('user_id', $memberIds)
                ->where('status', 'active')
                ->where('circle_id', '!=', $this->circle->id)
                ->pluck('circle_id')
                ->unique();

            // 4. Member owned Circles (Organizations - Proximity 1)
            $memberOwnedCircles = \App\Models\Circle::whereIn('owner_id', $memberIds)
                ->where('id', '!=', $this->circle->id)
                ->with(['achievements' => fn($q) => $q->where('title', '!=', '__SKELETON__')->with('skill'), 'owner'])
                ->get();

            // 5. Secondary Circles (Organizations - Proximity 2)
            $secondaryCircles = \App\Models\Circle::whereIn('id', $secondaryCircleIds)
                ->whereNotIn('owner_id', $memberIds) // Avoid duplicates with direct
                ->with(['achievements' => fn($q) => $q->where('title', '!=', '__SKELETON__')->with('skill'), 'owner'])
                ->get();

            // 6. Merge for Vivier (Direct Users + All Organizations)
            $allExperts = collect([])
                ->merge($memberExperts)       // Direct Users
                ->merge($memberOwnedCircles) // Member Owned Circles (P1)
                ->merge($secondaryCircles)    // Secondary Circles (P2)
                ->unique(fn($item) => get_class($item) . $item->id)
                ->sortByDesc(fn($item) => $item instanceof \App\Models\User ? $item->trust_score : $item->getAverageTrustScore());

            // 6. Member Projects (owned or member of)
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
            ->get();

            // 7. Aggregate all offers & demands from these projects
            $memberOffers = \App\Models\ProjectOffer::whereIn('project_id', $memberProjects->pluck('id'))
                ->with(['project', 'informations'])
                ->latest()
                ->get()
                ->sortByDesc(fn($o) => $o->type === 'offer');
        } catch (\Exception $e) {
            // If anything fails, return empty collections
            $allExperts = collect([]);
            $networkExperts = collect([]);
            $memberProjects = collect([]);
            $memberOffers = collect([]);
        }

        return view('livewire.circle.profile', [
            'memberExperts' => $allExperts ?? collect([]),
            'networkExperts' => collect([]), // No longer needed as separate
            'memberProjects' => $memberProjects ?? collect([]),
            'memberOffers' => $memberOffers ?? collect([]),
        ]);
    }
}
