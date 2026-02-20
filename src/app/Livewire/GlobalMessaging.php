<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Message;
use App\Models\Project;
use App\Models\Circle;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class GlobalMessaging extends Component
{
    public $isOpen = false;
    public $activeTab = 'private'; // 'private', 'forums'
    public $selectedParticipantId = null;
    public $messageText = '';

    #[On('toggleMessaging')]
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

    public function selectConversation($id)
    {
        $this->selectedParticipantId = (int) $id;
        
        // Mark as read
        Message::where('sender_id', $this->selectedParticipantId)
               ->where('receiver_id', Auth::id())
               ->whereNull('read_at')
               ->update(['read_at' => now()]);
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
            return '<div></div>';
        }

        $user = Auth::user();

        // Private conversations: simpler structure for the view
        $rawConversations = Message::where(function($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->whereNotNull('receiver_id');
            })
            ->orWhere(function($query) use ($user) {
                $query->where('receiver_id', $user->id);
            })
            ->with(['sender', 'receiver'])
            ->latest()
            ->get();

        $conversations = $rawConversations->groupBy(function($msg) use ($user) {
                return $msg->sender_id === $user->id ? $msg->receiver_id : $msg->sender_id;
            })
            ->map(function($msgs, $pId) use ($user) {
                $latest = $msgs->first();
                $participant = $latest->sender_id === $user->id ? $latest->receiver : $latest->sender;
                
                return [
                    'id' => (int)$pId,
                    'name' => $participant->name ?? 'Inconnu',
                    'avatar' => $participant->avatar ?? 'https://ui-avatars.com/api/?name=?',
                    'latest_msg' => $latest->content,
                    'latest_time' => $latest->created_at->diffForHumans(),
                    'is_mine' => $latest->sender_id === $user->id,
                    'unread_count' => $msgs->where('receiver_id', $user->id)->whereNull('read_at')->count(),
                ];
            })
            ->sortByDesc(function($c) {
                // Ensure sorting by actual timestamp of the latest message
                return Message::where(function($q) use ($c) {
                    $q->where('sender_id', auth()->id())->where('receiver_id', $c['id']);
                })->orWhere(function($q) use ($c) {
                    $q->where('sender_id', $c['id'])->where('receiver_id', auth()->id());
                })->latest()->first()?->created_at;
            });

        // Forums
        $projects = Project::whereHas('members', function($q) use ($user) {
                $q->where('memberable_type', User::class)->where('memberable_id', $user->id)->where('status', 'active');
            })
            ->orWhere('owner_id', $user->id)
            ->with(['messages' => fn($q) => $q->latest()->limit(1)])
            ->get()
            ->map(function($p) {
                $p->forum_type = 'project';
                $p->latest_activity = $p->messages->first()?->created_at ?? $p->created_at;
                return $p;
            });

        $circles = Circle::whereHas('members', function($q) use ($user) {
                $q->where('user_id', $user->id)->where('status', 'active');
            })
            ->orWhere('owner_id', $user->id)
            ->with(['messages' => fn($q) => $q->latest()->limit(1)])
            ->get()
            ->map(function($c) {
                $c->forum_type = 'circle';
                $c->latest_activity = $c->messages->first()?->created_at ?? $c->created_at;
                return $c;
            });

        $forums = $projects->concat($circles)->sortByDesc('latest_activity');

        // Active conversation messages
        $activeMessages = $this->selectedParticipantId 
            ? Message::where(function($q) use ($user) {
                    $q->where('sender_id', $user->id)->where('receiver_id', $this->selectedParticipantId);
                })->orWhere(function($q) use ($user) {
                    $q->where('sender_id', $this->selectedParticipantId)->where('receiver_id', $user->id);
                })->oldest()->get()
            : collect();

        return view('livewire.global-messaging', [
            'conversations' => $conversations,
            'forums' => $forums,
            'activeMessages' => $activeMessages,
            'selectedParticipant' => $this->selectedParticipantId ? User::find($this->selectedParticipantId) : null,
        ]);
    }
}
