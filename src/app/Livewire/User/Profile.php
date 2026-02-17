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
    public $realizedAt = '';

    // Validation Details
    public bool $showValidationModal = false;
    public ?\App\Models\Achievement $selectedAchievement = null;
    public string $validationComment = '';
    public ?string $votingType = null;
    public string $replyText = '';
    public string $search = '';

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
            'activeJoinedCircles.members.user.achievements.skill', 
            'achievements.skill', 
            'achievements.circle', 
            'achievements.validations.user',
            'vouchesReceived.voucher', 
            'proches.achievements.skill',
            'proches.achievements.validations.user'
        ]);
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

        $this->selectedAchievement = \App\Models\Achievement::with('validations.user')->find($achievementId);
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
        $this->selectedAchievement = \App\Models\Achievement::with('validations.user')->find($v->achievement_id);
        $this->user->load('achievements.validations.user', 'proches.achievements.validations.user');

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

        // Network Results (1st & 2nd degree) if searching
        $searchResults = collect();
        if (!empty($this->search) && auth()->check() && auth()->id() === $this->user->id) {
            $me = auth()->user();
            $searchTerm = '%' . strtolower($this->search) . '%';

            // 1. Get 1st Degree: Members of circles I am in
            $myCircleIds = $me->activeJoinedCircles->pluck('id');
            $firstDegreeUserIds = \App\Models\CircleMember::whereIn('circle_id', $myCircleIds)
                ->where('status', 'active')
                ->pluck('user_id')
                ->unique();

            // 2. Get 2nd Degree: Circles these members are in, and THEIR members
            $secondDegreeCircleIds = \App\Models\CircleMember::whereIn('user_id', $firstDegreeUserIds)
                ->where('status', 'active')
                ->pluck('circle_id')
                ->unique();

            $secondDegreeUserIds = \App\Models\CircleMember::whereIn('circle_id', $secondDegreeCircleIds)
                ->where('status', 'active')
                ->pluck('user_id')
                ->unique()
                ->diff($firstDegreeUserIds)
                ->diff([$me->id]);

            $networkUserIds = $firstDegreeUserIds->merge($secondDegreeUserIds)->unique();

            $searchResults = \App\Models\User::whereIn('id', $networkUserIds)
                ->with([
                    'achievements.skill', 
                    'proches.achievements.skill', 
                    'activeJoinedCircles',
                    'vouchesReceived.voucher'
                ])
                ->where(function($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                      ->orWhereRaw('LOWER(bio) LIKE ?', [$searchTerm])
                      ->orWhereHas('achievements', function($aq) use ($searchTerm) {
                          $aq->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                            ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm])
                            ->orWhereHas('skill', function($sq) use ($searchTerm) {
                                $sq->whereRaw('LOWER(name) LIKE ?', [$searchTerm]);
                            });
                      })
                      ->orWhereHas('proches', function($pq) use ($searchTerm) {
                          $pq->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                            ->orWhereHas('achievements', function($aq) use ($searchTerm) {
                                $aq->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                                  ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm])
                                  ->orWhereHas('skill', function($sq) use ($searchTerm) {
                                      $sq->whereRaw('LOWER(name) LIKE ?', [$searchTerm]);
                                  });
                            });
                      });
                })
                ->get()
                ->map(function($u) use ($firstDegreeUserIds, $searchTerm) {
                    $u->degree = $firstDegreeUserIds->contains($u->id) ? 1 : 2;
                    
                    // Identify match reason
                    $s = str_replace('%', '', $searchTerm);
                    if (str_contains(strtolower($u->name), $s)) $u->matchReason = "Nom";
                    else if (str_contains(strtolower($u->bio), $s)) $u->matchReason = "Bio";
                    else {
                        $matchedSkill = $u->achievements->first(fn($a) => str_contains(strtolower($a->skill?->name), $s));
                        if ($matchedSkill) $u->matchReason = "Compétence: " . $matchedSkill->skill->name;
                        else {
                            $matchedProche = $u->proches->first(fn($p) => str_contains(strtolower($p->name), $s));
                            if ($matchedProche) $u->matchReason = "Proche: " . $matchedProche->name;
                            else {
                                $matchedPach = $u->proches->flatMap->achievements->first(fn($a) => str_contains(strtolower($a->skill?->name), $s));
                                if ($matchedPach) $u->matchReason = "Compétence Proche: " . $matchedPach->skill->name;
                                else $u->matchReason = "Expertise";
                            }
                        }
                    }
                    return $u;
                })
                ->sortBy('degree');
        }

        return view('livewire.user.profile', [
            'totalVouchs' => $this->user->vouchesReceived->count(),
            'groupedAchievements' => $allAchievements->groupBy(fn($ach) => $ach->skill->name),
            'networkExperts' => $networkExperts,
            'searchResults' => $searchResults,
            'canEdit' => $this->canEdit()
        ]);
    }
}
