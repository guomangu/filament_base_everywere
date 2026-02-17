<?php

namespace App\Livewire\Circle;

use Livewire\Component;

class Profile extends Component
{
    public \App\Models\Circle $circle;
    public string $message = '';

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
            'owner',
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
        // 1. Immediate Active Members (including owner)
        $memberIds = $this->circle->activeMembers()->pluck('user_id')->push($this->circle->owner_id)->unique();

        // 2. Immediate Skills
        $immediateAchievements = \App\Models\Achievement::whereIn('user_id', $memberIds)
            ->where('title', '!=', '__SKELETON__')
            ->with(['skill', 'user'])
            ->get();

        // 3. Extended Network (Proximity 2)
        // Find other circles these members belong to (only active memberships)
        $secondaryCircleIds = \App\Models\CircleMember::whereIn('user_id', $memberIds)
            ->where('status', 'active')
            ->where('circle_id', '!=', $this->circle->id)
            ->pluck('circle_id')
            ->unique();

        // Find experts in those secondary circles who are NOT in the current circle
        $extendedExperts = \App\Models\User::whereHas('circleMembers', function($q) use ($secondaryCircleIds) {
                $q->whereIn('circle_id', $secondaryCircleIds)
                  ->where('status', 'active');
            })
            ->whereNotIn('id', $memberIds)
            ->with(['achievements.skill'])
            ->get();

        // 4. Map and Deduplicate
        // Immediate first (priority)
        $skills = $immediateAchievements->map(fn($a) => [
            'skill' => $a->skill->name,
            'expert' => $a->user->name,
            'trust' => $a->user->trust_score,
            'expert_id' => $a->user->id,
            'is_extended' => false
        ]);

        // Extended second
        $extendedSkills = $extendedExperts->flatMap(function($expert) {
            return $expert->achievements
                ->reject(fn($a) => $a->title === '__SKELETON__')
                ->map(fn($a) => [
                    'skill' => $a->skill->name,
                    'expert' => $expert->name,
                    'trust' => $expert->trust_score,
                    'expert_id' => $expert->id,
                    'is_extended' => true
                ]);
        });

        $allSkills = $skills->concat($extendedSkills)
            ->unique('skill') // Keep immediate if skill name matches
            ->sortByDesc('trust')
            ->take(60);

        return view('livewire.circle.profile', [
            'allSkills' => $allSkills
        ]);
    }
}
