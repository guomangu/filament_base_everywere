<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Message;
use App\Models\Project;
use App\Models\Circle;
use Illuminate\Support\Facades\Auth;

class GlobalMessaging extends Component
{
    public $isOpen = false;
    public $activeTab = 'private'; // 'private', 'projects', 'circles'
    
    // For direct messaging
    public $selectedParticipantId = null;
    public $messageText = '';

    protected $listeners = ['toggleMessaging' => 'toggle'];

    public function toggle()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->dispatch('messagingOpened');
        }
    }

    public function selectTab($tab)
    {
        $this->activeTab = $tab;
        $this->selectedParticipantId = null;
    }

    public function selectConversation($participantId)
    {
        $this->selectedParticipantId = $participantId;
    }

    public function sendMessage()
    {
        $this->validate(['messageText' => 'required|min:1|max:1000']);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedParticipantId,
            'content' => $this->messageText,
            'type' => 'chat',
        ]);

        $this->messageText = '';
    }

    public function render()
    {
        if (!Auth::check()) {
            return view('livewire.global-messaging', [
                'conversations' => collect(),
                'projects' => collect(),
                'circles' => collect(),
            ]);
        }

        $user = Auth::user();

        // Private conversations (grouped by participant)
        $conversations = Message::where(function($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->whereNotNull('receiver_id');
            })
            ->orWhere(function($query) use ($user) {
                $query->where('receiver_id', $user->id);
            })
            ->with(['sender', 'receiver'])
            ->latest()
            ->get()
            ->groupBy(function($msg) use ($user) {
                return $msg->sender_id === $user->id ? $msg->receiver_id : $msg->sender_id;
            });

        // Joined projects (latest messages)
        $projects = Project::whereHas('members', function($q) use ($user) {
                $q->where('memberable_type', \App\Models\User::class)
                  ->where('memberable_id', $user->id)
                  ->where('status', 'active');
            })
            ->orWhere('owner_id', $user->id)
            ->with(['messages' => fn($q) => $q->latest()->limit(1)])
            ->get();

        // Joined circles (latest messages)
        $circles = Circle::whereHas('members', function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('status', 'active');
            })
            ->orWhere('owner_id', $user->id)
            ->with(['messages' => fn($q) => $q->latest()->limit(1)])
            ->get();

        return view('livewire.global-messaging', [
            'conversations' => $conversations,
            'projects' => $projects,
            'circles' => $circles,
            'activeMessages' => $this->selectedParticipantId 
                ? Message::where(function($q) use ($user) {
                        $q->where('sender_id', $user->id)->where('receiver_id', $this->selectedParticipantId);
                    })->orWhere(function($q) use ($user) {
                        $q->where('sender_id', $this->selectedParticipantId)->where('receiver_id', $user->id);
                    })->oldest()->get()
                : collect(),
            'selectedParticipant' => $this->selectedParticipantId ? \App\Models\User::find($this->selectedParticipantId) : null,
        ]);
    }
}
