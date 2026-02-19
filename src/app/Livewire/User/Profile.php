<?php

namespace App\Livewire\User;

use App\Models\Project;
use App\Models\ProjectMember;
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
    public $realizedAt = '';
    public int $valCount = 0;
    public int $rejCount = 0;

    // Validation Details
    public bool $showValidationModal = false;
    public ?\App\Models\Achievement $selectedAchievement = null;
    public string $validationComment = '';
    public ?string $votingType = null;
    public string $replyText = '';

    // Project Creation UX
    public bool $isCreatingProject = false;
    public string $projectType = '';
    public string $projectTitle = '';

    protected $rules = [
        'skillName' => 'required_if:step,1|min:2',
        'proofTitle' => 'required_if:step,2|min:3',
        'proofDescription' => 'required_if:step,2|min:10',
        'procheName' => 'required_if:showProcheModal,true|min:2',
        'realizedAt' => 'required_if:showCreateModal,true|date',
    ];

    public function mount(\App\Models\User $user)
    {
        $this->user = $user->load([
            'activeJoinedCircles.owner.achievements.skill',
            'activeJoinedCircles.activeMembers.user.achievements.skill', 
            'achievements.skill', 
            'achievements.circle', 
            'achievements.validations.user.achievements.skill',
            'achievements.validations.user.proches.achievements.skill',
            'proches.achievements.skill',
            'proches.achievements.validations.user.achievements.skill',
            'proches.achievements.validations.user.proches.achievements.skill',
            'validationsReceived'
        ]);

        $this->valCount = $this->user->validationsReceived->where('type', 'validate')->count();
        $this->rejCount = $this->user->validationsReceived->where('type', 'reject')->count();

        if (auth()->check() && auth()->user()->coordinates && auth()->user()->location) {
            $this->lat = auth()->user()->coordinates['lat'];
            $this->lng = auth()->user()->coordinates['lng'];
            $this->locationName = auth()->user()->location;
        }
    }

    public function setLocation($lat, $lng, $name)
    {
        if (auth()->check()) {
            auth()->user()->update([
                'coordinates' => ['lat' => $lat, 'lng' => $lng],
                'location' => $name
            ]);
        }
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
            'realized_at' => $this->realizedAt,
            'is_verified' => false, 
        ]);

        $this->showCreateModal = false;
        $this->user->recalculateTrustScore(); // Might change if we had base points for adding proofs
        $this->user->load('achievements.skill', 'achievements.circle', 'proches.achievements.skill');
        
        $this->dispatch('notify', [
            'message' => 'Preuve ajoutée avec succès !',
            'type' => 'success'
        ]);
    }

    public function initiateValidation(int $achievementId, string $type)
    {
        if (!auth()->check() || auth()->id() === $this->user->id) return;

        // Perform vote immediately
        \App\Models\AchievementValidation::updateOrCreate(
            ['user_id' => auth()->id(), 'achievement_id' => $achievementId],
            ['type' => $type]
        );

        $this->selectedAchievement = \App\Models\Achievement::with([
            'validations.user.achievements.skill',
            'validations.user.proches.achievements.skill'
        ])->find($achievementId);
        $this->votingType = $type; // Still useful to show "You voted [Type]" in modal
        $this->showValidationModal = true;
        
        $this->user->recalculateTrustScore();
        $this->user->load('achievements.validations.user', 'proches.achievements.validations.user');
        
        // Load my existing comment if any
        $myV = $this->selectedAchievement->validations->where('user_id', auth()->id())->first();
        $this->validationComment = $myV ? ($myV->comment ?? '') : '';
    }

    public function confirmValidation()
    {
        if (!$this->selectedAchievement) return;
        if (!auth()->check() || auth()->id() === $this->user->id) return;

        $v = \App\Models\AchievementValidation::where('user_id', auth()->id())
            ->where('achievement_id', $this->selectedAchievement->id)
            ->first();

        // Lock if reply exists
        if ($v && $v->reply) {
            $this->dispatch('notify', ['message' => 'Impossible de modifier un vote ayant reçu une réponse.', 'type' => 'error']);
            return;
        }

        if ($v) {
            $v->update(['comment' => $this->validationComment]);
        }

        $this->user->load('achievements.validations.user', 'proches.achievements.validations.user');
        
        $this->showValidationModal = false;
        $this->votingType = null;
        $this->validationComment = '';

        $this->dispatch('notify', ['message' => 'Commentaire enregistré !', 'type' => 'success']);
    }

    public function openValidationModal(int $achievementId)
    {
        $this->selectedAchievement = \App\Models\Achievement::with('validations.user')->find($achievementId);
        $this->votingType = null; // Read-only mode
        $this->showValidationModal = true;
        $this->replyText = ''; // Clear reply buffer
    }

    public function submitReply(int $validationId)
    {
        $v = \App\Models\AchievementValidation::with('achievement')->find($validationId);
        if (!$v || $v->achievement->user_id !== auth()->id()) return;
        if ($v->reply) return; // Already replied

        $v->update(['reply' => $this->replyText]);
        
        $this->replyText = '';
        $this->selectedAchievement = \App\Models\Achievement::with([
            'validations.user.achievements.skill',
            'validations.user.proches.achievements.skill'
        ])->find($v->achievement_id);
        $this->user->load([
            'achievements.validations.user.achievements.skill', 
            'achievements.validations.user.proches.achievements.skill',
            'proches.achievements.validations.user.achievements.skill',
            'proches.achievements.validations.user.proches.achievements.skill'
        ]);
        
        $this->dispatch('notify', ['message' => 'Réponse envoyée ! L\'échange est maintenant verrouillé.', 'type' => 'success']);
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

    public function startCreatingProject(string $type)
    {
        if (auth()->id() !== $this->user->id) return;
        $this->projectType = $type;
        $this->projectTitle = '';
        $this->isCreatingProject = true;
    }

    public function cancelProjectCreation()
    {
        $this->isCreatingProject = false;
        $this->projectType = '';
        $this->projectTitle = '';
    }

    public function confirmProjectCreation()
    {
        if (auth()->id() !== $this->user->id) return;
        
        $this->validate([
            'projectTitle' => 'required|min:3|max:255',
        ]);

        $project = Project::create([
            'title' => $this->projectTitle,
            'description' => $this->projectType === 'offer' ? 'Nouvelle offre de service' : 'Nouveau besoin exprimé',
            'owner_id' => auth()->id(),
            'is_open' => true,
        ]);

        // Create the initial offer/demand
        $project->allOffers()->create([
            'title' => $this->projectTitle,
            'description' => 'Détails à personnaliser...',
            'type' => $this->projectType,
        ]);

        return redirect()->route('projects.show', $project);
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

        // 4. User Projects (owned or active member)
        try {
            $memberProjectIds = ProjectMember::where('memberable_type', \App\Models\User::class)
                ->where('memberable_id', $this->user->id)
                ->where('status', 'active')
                ->pluck('project_id');

            $userProjects = Project::where('owner_id', $this->user->id)
                ->orWhereIn('id', $memberProjectIds)
                ->with(['owner', 'activeMembers', 'offers', 'demands', 'reviews'])
                ->latest()
                ->get();
        } catch (\Exception $e) {
            $userProjects = collect([]);
        }

        return view('livewire.user.profile', [
            'groupedAchievements' => $allAchievements->groupBy(fn($ach) => $ach->skill->name),
            'networkExperts' => $networkExperts,
            'canEdit' => $this->canEdit(),
            'userProjects' => $userProjects,
            'activeProject' => $this->user->activeProject(),
            'allAchievements' => $allAchievements,
        ])->layoutData([
            'title' => $this->user->name . ' - Expertises et Réseau | TrustCircle',
            'description' => \Illuminate\Support\Str::limit('Découvrez le profil de ' . $this->user->name . ' sur TrustCircle, ses compétences validées (' . $allAchievements->count() . ' preuves) et son réseau de confiance.', 160, '...'),
            'og_image' => $this->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($this->user->name),
        ]);
    }

    public function toggleProjectStatus(int $projectId)
    {
        $project = Project::findOrFail($projectId);
        if (!$project->canManage(auth()->user())) return;
        $project->toggleStatus();
    }
}
