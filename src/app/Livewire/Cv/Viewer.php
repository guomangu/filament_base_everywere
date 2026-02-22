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
    public $allUserProjects = null;
    public string $type = 'user'; // 'user', 'mission', or 'project'

    public function mount($user = null, $skill = null, $project = null)
    {
        if ($user) {
            $this->type = 'user';
            $this->user = $user instanceof User ? $user : User::findOrFail($user);
            $this->user->load([
                'achievements' => fn($q) => $q->where('title', '!=', '__SKELETON__')->with(['skill', 'validations.user', 'informations']),
                'informations',
                'activeJoinedCircles',
                'validationsReceived'
            ]);

            // Load mission-based projects
            $memberProjectIds = \App\Models\ProjectMember::where('memberable_type', \App\Models\User::class)
                ->where('memberable_id', $this->user->id)
                ->where('status', 'active')
                ->pluck('project_id');

            $this->allUserProjects = \App\Models\Project::where(function($q) use ($memberProjectIds) {
                    $q->where('owner_id', $this->user->id)
                      ->orWhereIn('id', $memberProjectIds);
                })
                ->whereNotNull('skill_id')
                ->with(['owner', 'activeMembers.memberable', 'skill', 'informations'])
                ->get();
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
        }
        
        if (!$this->allUserProjects) {
            $this->allUserProjects = collect();
        }
    }

    public function render()
    {
        return view('livewire.cv.viewer');
    }
}
