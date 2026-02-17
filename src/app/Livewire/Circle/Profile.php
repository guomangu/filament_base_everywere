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
            'members.user',
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
        $memberIds = $this->circle->members()->pluck('user_id')->push($this->circle->owner_id);

        return view('livewire.circle.profile', [
            'circleSkills' => \App\Models\Achievement::whereIn('user_id', $memberIds)
                ->with(['skill', 'user'])
                ->latest()
                ->get()
                ->groupBy('skill_id')
        ]);
    }
}
