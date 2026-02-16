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

    public function mount(\App\Models\Circle $circle)
    {
        $this->circle = $circle->load([
            'owner', 
            'members.user.achievements.skill', 
            'messages.sender',
            'achievements.skill', // Also load achievements directly linked to circle
            'achievements.user'
        ]);
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
        return view('livewire.circle.profile');
    }
}
