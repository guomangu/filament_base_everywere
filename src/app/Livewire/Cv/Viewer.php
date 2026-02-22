<?php

namespace App\Livewire\Cv;

use Livewire\Component;
use App\Models\User;
use App\Models\Circle;

class Viewer extends Component
{
    public ?User $user = null;
    public ?\App\Models\Skill $skill = null;
    public ?\App\Models\Project $project = null;
    public string $type = 'user'; // 'user', 'mission', or 'project'

    public function mount($user = null, $skill = null, $project = null)
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
        } elseif ($skill) {
            $this->type = 'mission';
            $this->skill = $skill instanceof \App\Models\Skill ? $skill : \App\Models\Skill::findOrFail($skill);
            $this->skill->load([
                'projects' => fn($q) => $q->with(['owner', 'activeMembers.memberable', 'reviews']),
            ]);
        } elseif ($project) {
            $this->type = 'project';
            $this->project = $project instanceof \App\Models\Project ? $project : \App\Models\Project::findOrFail($project);
            $this->project->load([
                'owner',
                'activeMembers.memberable',
                'offers.informations',
                'informations',
                'skills'
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
