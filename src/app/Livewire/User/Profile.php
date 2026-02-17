<?php

namespace App\Livewire\User;

use Livewire\Component;

class Profile extends Component
{
    public \App\Models\User $user;
    // Proches and Skills management
    public bool $showProcheModal = false;
    public string $procheName = '';
    public bool $showCreateModal = false;
    public int $step = 1;
    public string $skillName = '';
    public string $proofTitle = '';
    public string $proofDescription = '';
    public ?int $selectedSkillId = null;
    public ?int $selectedProcheId = null;

    protected $rules = [
        'skillName' => 'required_if:step,1|min:2',
        'proofTitle' => 'required_if:step,2|min:3',
        'proofDescription' => 'required_if:step,2|min:10',
        'procheName' => 'required_if:showProcheModal,true|min:2',
    ];

    public function mount(\App\Models\User $user)
    {
        $this->user = $user->load(['activeJoinedCircles.members.user.achievements.skill', 'achievements.skill', 'achievements.circle', 'vouchesReceived.voucher', 'proches.achievements.skill']);
    }

    public function canEdit(): bool
    {
        if (!auth()->check()) return false;
        
        // Owner can edit
        if (auth()->id() === $this->user->id) return true;
        
        // Parent can edit Proche
        if ($this->user->is_managed && $this->user->parent_id === auth()->id()) return true;
        
        return false;
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

    public function openCreateModal($procheId = null)
    {
        if (!$this->canEdit()) return;
        $this->reset(['step', 'skillName', 'selectedSkillId', 'selectedProcheId', 'proofTitle', 'proofDescription']);
        $this->selectedProcheId = $procheId;
        $this->showCreateModal = true;
    }

    public function addProofForSkill($skillName, $procheId = null)
    {
        if (!$this->canEdit()) return;
        $this->reset(['step', 'skillName', 'selectedSkillId', 'selectedProcheId', 'proofTitle', 'proofDescription']);
        $this->skillName = $skillName;
        $this->selectedProcheId = $procheId;
        
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
        if (!$this->canEdit()) return;
        $this->validateOnly('skillName');

        $skill = \App\Models\Skill::firstOrCreate([
            'name' => $this->skillName,
        ], [
            'slug' => \Illuminate\Support\Str::slug($this->skillName),
        ]);

        \App\Models\Achievement::create([
            'user_id' => $this->selectedProcheId ? null : $this->user->id,
            'proche_id' => $this->selectedProcheId,
            'skill_id' => $skill->id,
            'title' => '__SKELETON__',
            'description' => 'Détails et preuves à venir...',
            'is_verified' => false,
        ]);

        $this->showCreateModal = false;
        $this->user->load('achievements.skill', 'achievements.circle');
        
        $this->dispatch('notify', [
            'message' => 'Compétence ajoutée au profil !',
            'type' => 'success'
        ]);
    }

    public function submitProof()
    {
        if (!$this->canEdit()) return;
        $this->validate();

        // Create Achievement
        \App\Models\Achievement::create([
            'user_id' => $this->selectedProcheId ? null : $this->user->id,
            'proche_id' => $this->selectedProcheId,
            'skill_id' => $this->selectedSkillId,
            'circle_id' => null, 
            'title' => $this->proofTitle,
            'description' => $this->proofDescription,
            'is_verified' => false, 
        ]);

        $this->showCreateModal = false;
        $this->user->load('achievements.skill', 'achievements.circle', 'proches.achievements.skill');
        
        $this->dispatch('notify', [
            'message' => 'Preuve ajoutée avec succès !',
            'type' => 'success'
        ]);
    }

    public function openProcheModal()
    {
        if (auth()->id() !== $this->user->id) return;
        $this->reset('procheName');
        $this->showProcheModal = true;
    }

    public function createProche()
    {
        if (auth()->id() !== $this->user->id) return;
        $this->validateOnly('procheName');

        \App\Models\Proche::create([
            'name' => $this->procheName,
            'parent_id' => auth()->id(),
        ]);

        $this->showProcheModal = false;
        $this->user->load('proches');

        $this->dispatch('notify', [
            'message' => 'Proche créé avec succès !',
            'type' => 'success'
        ]);
    }

    public function generateTransfer(int $procheId)
    {
        $proche = \App\Models\Proche::find($procheId);
        if (!$proche || $proche->parent_id !== auth()->id()) return;

        $proche->update([
            'transfer_token' => \Illuminate\Support\Str::random(40),
            'transfer_code' => strtoupper(\Illuminate\Support\Str::random(6)),
        ]);

        $this->user->load('proches');

        $this->dispatch('notify', [
            'message' => 'Code de transfert généré !',
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

        // 3. Proches Achievements for networking
        $prochesAchievements = \App\Models\Achievement::whereIn('proche_id', $this->user->proches->pluck('id'))
            ->with(['skill', 'proche'])
            ->get();

        // Combine local and proches achievements
        $allAchievements = $this->user->achievements->merge($prochesAchievements);

        return view('livewire.user.profile', [
            'totalVouchs' => $this->user->vouchesReceived->count(),
            'groupedAchievements' => $allAchievements->groupBy(fn($ach) => $ach->skill->name),
            'networkExperts' => $networkExperts,
            'canEdit' => $this->canEdit()
        ]);
    }
}
