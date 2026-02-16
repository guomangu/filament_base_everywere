<?php

namespace App\Livewire\User;

use Livewire\Component;

class Profile extends Component
{
    public \App\Models\User $user;

    public function mount(\App\Models\User $user)
    {
        $this->user = $user->load(['joinedCircles', 'achievements.skill', 'achievements.circle']);
    }

    public function render()
    {
        return view('livewire.user.profile', [
            'totalVouchs' => \App\Models\CircleMember::where('user_id', $this->user->id)->whereNotNull('vouched_by_id')->count()
        ]);
    }
}
