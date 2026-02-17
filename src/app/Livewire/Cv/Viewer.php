<?php

namespace App\Livewire\Cv;

use Livewire\Component;
use App\Models\User;
use App\Models\Circle;

class Viewer extends Component
{
    public ?User $user = null;
    public ?Circle $circle = null;
    public string $type = 'user'; // 'user' or 'circle'

    public function mount($user = null, $circle = null)
    {
        if ($user) {
            $this->type = 'user';
            $this->user = $user instanceof User ? $user : User::findOrFail($user);
            $this->user->load([
                'achievements' => fn($q) => $q->where('title', '!=', '__SKELETON__')->with(['skill', 'validations.user']),
                'informations',
                'activeJoinedCircles',
                'validationsReceived'
            ]);
        } elseif ($circle) {
            $this->type = 'circle';
            $this->circle = $circle instanceof Circle ? $circle : Circle::findOrFail($circle);
            $this->circle->load([
                'owner',
                'activeMembers.user'
            ]);
        } else {
            abort(404);
        }
    }

    public function render()
    {
        return view('livewire.cv.viewer');
    }
}
