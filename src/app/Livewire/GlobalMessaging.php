<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Message;
use App\Models\Project;
use App\Models\Circle;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;

class GlobalMessaging extends Component
{
    use WithFileUploads;
    public $isOpen = false;
    public $activeTab = 'private'; // 'private', 'forums'
    public $selectedParticipantId = null;
    public $messageText = '';

    // For change detection
    public $lastUnreadCount = 0;
    public $lastForumActivity = null;

    // Attachments & Context
    public $attachment = null;
    public $upload = null;
    public $contextItems = []; // Received from page components

    #[On('toggleMessaging')]
    public function toggle()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->dispatch('messagingOpened');
        }
    }

    #[On('openConversationWith')]
    public function openConversationWith($userId)
    {
        $this->isOpen = true;
        $this->activeTab = 'private';
        $this->selectedParticipantId = (int) $userId;
        $this->dispatch('messagingOpened');
    }

    #[On('updateMessagingContext')]
    public function updateContext($items)
    {
        $this->contextItems = $items;
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

    public function selectTab($tab)
    {
        $this->activeTab = $tab;
        $this->selectedParticipantId = null;
    }

    public function selectConversation($id)
    {
        $this->selectedParticipantId = $id ? (int) $id : null;
        
        // Mark as read
        if ($this->selectedParticipantId) {
            Message::where('sender_id', $this->selectedParticipantId)
                   ->where('receiver_id', Auth::id())
                   ->whereNull('read_at')
                   ->update(['read_at' => now()]);
            
            $this->syncUnreadCount();
        }
    }

    public function sendMessage()
    {
        $this->validate([
            'messageText' => 'required|min:1|max:1000',
            'upload' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf|max:10240', // 10MB limit
        ]);

        $metadata = $this->attachment ? ['attachment' => $this->attachment] : null;

        if ($this->upload) {
            $path = $this->upload->store('attachments', 'public');
            $metadata = $metadata ?? [];
            $metadata['file'] = [
                'path' => $path,
                'name' => $this->upload->getClientOriginalName(),
                'type' => $this->upload->getMimeType(),
                'size' => $this->upload->getSize(),
            ];
        }

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedParticipantId,
            'content' => $this->messageText,
            'type' => 'chat',
            'metadata' => $metadata,
        ]);

        $this->messageText = '';
        $this->attachment = null;
        $this->upload = null;
        $this->dispatch('messagingOpened'); // For scrolling
    }

    public function refresh()
    {
        if (!Auth::check()) return;

        $user = Auth::user();

        // 1. Detect New Unread Messages
        $currentUnreadCount = Message::where('receiver_id', $user->id)
                                    ->whereNull('read_at')
                                    ->count();

        // 2. Detect New Forum Activity
        $latestProjectMsg = Message::whereHas('project', function($q) use ($user) {
            $q->whereHas('members', function($sub) use ($user) {
                $sub->where('memberable_id', $user->id)->where('status', 'active');
            })->orWhere('owner_id', $user->id);
        })->latest()->first();

        $latestCircleMsg = Message::whereHas('circle', function($q) use ($user) {
            $q->whereHas('members', function($sub) use ($user) {
                $sub->where('user_id', $user->id)->where('status', 'active');
            })->orWhere('owner_id', $user->id);
        })->latest()->first();

        $currentForumActivity = max(
            $latestProjectMsg?->created_at?->timestamp ?? 0,
            $latestCircleMsg?->created_at?->timestamp ?? 0
        );

        // 3. Dispatch Notifications if something is new
        if ($currentUnreadCount > $this->lastUnreadCount || ($this->lastForumActivity !== null && $currentForumActivity > $this->lastForumActivity)) {
            $this->dispatch('global-messaging-updated');
        }

        // 4. Always sync the navigation badge if unread count changed
        if ($currentUnreadCount !== $this->lastUnreadCount) {
            $this->syncUnreadCount();
        }

        $this->lastUnreadCount = $currentUnreadCount;
        $this->lastForumActivity = $currentForumActivity;
    }

    protected function syncUnreadCount()
    {
        if (!Auth::check()) return;
        
        $count = Message::where('receiver_id', Auth::id())
                       ->whereNull('read_at')
                       ->count();
        
        $this->dispatch('unread-count-updated', count: $count);
    }

    public function render()
    {
        if (!Auth::check()) {
            return '<div></div>';
        }

        $user = Auth::user();

        // Initialize last activity on first render
        if ($this->lastForumActivity === null) {
            $this->refresh();
        }

        // Private conversations
        $rawConversations = Message::where(function($query) use ($user) {
                $query->where('sender_id', $user->id)->whereNotNull('receiver_id');
            })
            ->orWhere(function($query) use ($user) {
                $query->where('receiver_id', $user->id);
            })
            ->with(['sender', 'receiver'])
            ->latest()
            ->take(50) // Safety fetch for grouping, will limit final list to 20
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
                    'latest_timestamp' => $latest->created_at->timestamp,
                ];
            })
            ->sortByDesc('latest_timestamp')
            ->take(20);

        // Forums
        $projects = Project::whereHas('members', function($q) use ($user) {
                $q->where('memberable_type', User::class)->where('memberable_id', $user->id)->where('status', 'active');
            })
            ->orWhere('owner_id', $user->id)
            ->with(['messages' => fn($q) => $q->latest()->limit(1)])
            ->get()
            ->map(function($p) {
                $p->forum_type = 'project';
                $p->forum_name = $p->title;
                $p->latest_activity_dt = $p->messages->first()?->created_at ?? $p->created_at;
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
                $c->forum_name = $c->name;
                $c->latest_activity_dt = $c->messages->first()?->created_at ?? $c->created_at;
                return $c;
            });

        $forums = $projects->concat($circles)->sortByDesc('latest_activity_dt')->take(20);

        // Active conversation messages
        $activeMessages = $this->selectedParticipantId 
            ? Message::where(function($q) use ($user) {
                    $q->where('sender_id', $user->id)->where('receiver_id', $this->selectedParticipantId);
                })->orWhere(function($q) use ($user) {
                    $q->where('sender_id', $this->selectedParticipantId)->where('receiver_id', $user->id);
                })->with('sender')->latest()->take(20)->get()->reverse()
            : collect();

        return view('livewire.global-messaging', [
            'conversations' => $conversations,
            'forums' => $forums,
            'activeMessages' => $activeMessages,
            'selectedParticipant' => $this->selectedParticipantId ? User::find($this->selectedParticipantId) : null,
        ]);
    }
}
