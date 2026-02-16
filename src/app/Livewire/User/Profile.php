<?php

namespace App\Livewire\User;

use Livewire\Component;

class Profile extends Component
{
    public \App\Models\User $user;
    public bool $showCreateModal = false;
    public int $step = 1;

    // Step 1: Skill
    public string $skillName = '';
    public ?int $selectedSkillId = null;

    // Step 2: Proof
    public string $proofTitle = '';
    public string $proofDescription = '';

    protected $rules = [
        'skillName' => 'required_if:step,1|min:2',
        'proofTitle' => 'required_if:step,2|min:3',
        'proofDescription' => 'required_if:step,2|min:10',
    ];

    public function mount(\App\Models\User $user)
    {
        $this->user = $user->load(['joinedCircles', 'achievements.skill', 'achievements.circle']);
    }

    public function openCreateModal()
    {
        $this->reset(['step', 'skillName', 'selectedSkillId', 'proofTitle', 'proofDescription']);
        $this->showCreateModal = true;
    }

    public function addProofForSkill($skillName)
    {
        $this->reset(['step', 'skillName', 'selectedSkillId', 'proofTitle', 'proofDescription']);
        $this->skillName = $skillName;
        
        $skill = \App\Models\Skill::where('name', 'like', $this->skillName)->first();
        if ($skill) {
            $this->selectedSkillId = $skill->id;
        }

        $this->step = 2;
        $this->showCreateModal = true;
    }

    public function goToStep2()
    {
        $this->validateOnly('skillName');

        // Try to find if skill exists
        $skill = \App\Models\Skill::where('name', 'like', $this->skillName)->first();
        if ($skill) {
            $this->selectedSkillId = $skill->id;
        }

        $this->step = 2;
    }

    public function submitProof()
    {
        $this->validate();

        // Ensure skill exists
        if (!$this->selectedSkillId) {
            $skill = \App\Models\Skill::create([
                'name' => $this->skillName,
                'slug' => \Illuminate\Support\Str::slug($this->skillName),
            ]);
            $this->selectedSkillId = $skill->id;
        }

        // Create Achievement
        \App\Models\Achievement::create([
            'user_id' => auth()->id(),
            'skill_id' => $this->selectedSkillId,
            'circle_id' => null, // No longer selected manually
            'title' => $this->proofTitle,
            'description' => $this->proofDescription,
            'is_verified' => false, // Default to false
        ]);

        $this->showCreateModal = false;
        $this->user->load('achievements.skill', 'achievements.circle');
        
        $this->dispatch('notify', [
            'message' => 'Preuve ajoutée avec succès !',
            'type' => 'success'
        ]);
    }

    public function render()
    {
        return view('livewire.user.profile', [
            'totalVouchs' => \App\Models\CircleMember::where('user_id', $this->user->id)->whereNotNull('vouched_by_id')->count(),
            'groupedAchievements' => $this->user->achievements->groupBy(fn($ach) => $ach->skill->name)
        ]);
    }
}
