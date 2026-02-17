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
        $this->user = $user->load(['activeJoinedCircles.members.user.achievements.skill', 'achievements.skill', 'achievements.circle', 'vouchesReceived.voucher']);
    }

    public function vouch()
    {
        if (!auth()->check()) return redirect()->route('login');
        if (auth()->id() === $this->user->id) return;

        $existing = \App\Models\Vouch::where('voucher_id', auth()->id())
            ->where('vouchee_id', $this->user->id)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            \App\Models\Vouch::create([
                'voucher_id' => auth()->id(),
                'vouchee_id' => $this->user->id,
            ]);
        }

        $this->user->recalculateTrustScore();
        $this->user->load('vouchesReceived.voucher');
        
        $this->dispatch('notify', [
            'message' => $existing ? 'Garantie retirée' : 'Vous vous portez désormais garant !',
            'type' => 'success'
        ]);
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
        
        $skill = \App\Models\Skill::where('name', $this->skillName)->first();
        if (!$skill) {
             $skill = \App\Models\Skill::create([
                'name' => $this->skillName,
                'slug' => \Illuminate\Support\Str::slug($this->skillName),
            ]);
        }
        
        $this->selectedSkillId = $skill->id;
        $this->step = 2; // Direct to Proof step
        $this->showCreateModal = true;
    }

    public function submitSkillOnly()
    {
        $this->validateOnly('skillName');

        $skill = \App\Models\Skill::firstOrCreate([
            'name' => $this->skillName,
        ], [
            'slug' => \Illuminate\Support\Str::slug($this->skillName),
        ]);

        \App\Models\Achievement::create([
            'user_id' => auth()->id(),
            'skill_id' => $skill->id,
            'title' => '__SKELETON__',
            'description' => 'Détails et preuves à venir...',
            'is_verified' => false,
        ]);

        $this->showCreateModal = false;
        $this->user->load('achievements.skill', 'achievements.circle');
        
        $this->dispatch('notify', [
            'message' => 'Compétence ajoutée à votre profil !',
            'type' => 'success'
        ]);
    }

    public function submitProof()
    {
        $this->validate();

        // Create Achievement
        \App\Models\Achievement::create([
            'user_id' => auth()->id(),
            'skill_id' => $this->selectedSkillId,
            'circle_id' => null, 
            'title' => $this->proofTitle,
            'description' => $this->proofDescription,
            'is_verified' => false, 
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
        // 1. Members of my ACTIVE circles (excluding myself)
        $circleMemberIds = \App\Models\CircleMember::whereIn('circle_id', $this->user->activeJoinedCircles->pluck('id'))
            ->where('status', 'active')
            ->where('user_id', '!=', $this->user->id)
            ->pluck('user_id')
            ->unique();

        // 2. Experts from these circles (limit and sort by trust)
        $networkExperts = \App\Models\User::whereIn('id', $circleMemberIds)
            ->with(['achievements' => fn($q) => $q->where('title', '!=', '__SKELETON__')->with('skill')])
            ->get()
            ->filter(fn($u) => $u->achievements->count() > 0)
            ->sortByDesc('trust_score')
            ->take(30);

        return view('livewire.user.profile', [
            'totalVouchs' => $this->user->vouchesReceived->count(),
            'groupedAchievements' => $this->user->achievements->groupBy(fn($ach) => $ach->skill->name),
            'networkExperts' => $networkExperts
        ]);
    }
}
