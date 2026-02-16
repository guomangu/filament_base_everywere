<?php

namespace App\Livewire\Circle;

use Livewire\Component;

class Profile extends Component
{
    public \App\Models\Circle $circle;

    public function mount(\App\Models\Circle $circle)
    {
        $this->circle = $circle->load(['owner', 'members.user.achievements.skill', 'messages.sender']);
    }

    public function render()
    {
        return view('livewire.circle.profile');
    }
}
