<?php

namespace App\Livewire\Project;

use App\Models\Project;
use App\Models\ProjectReview;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Show extends Component
{
    public Project $project;
    public $activeTab = 'overview'; // overview, offers, demands, reviews, messages
    
    // Review form
    public $reviewType = 'validate';
    public $reviewComment = '';
    public $replyTo = null;

    // Message form
    public $message = '';
    public $lastMessageCount = 0;

    // Membership management
    public $userSearch = '';
    public $lastPendingCount = 0;
    public $showMemberManager = false;

    // Progressive management (Petit à Petit)
    public $showOfferForm = false;
    public $showDemandForm = false;
    public $offerTitle = '';
    public $offerDescription = '';
    public $demandTitle = '';
    public $demandDescription = '';
    
    // Editing states
    public $editingOfferId = null;
    public $editingDemandId = null;
    
    // Smart Skill Selector
    public $selectedSkills = []; // Format: ['Skill name 1', 'Skill name 2']
    public $skillSearch = '';

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->refresh();
        $this->lastMessageCount = $this->project->messages->count();
    }

    public function refresh()
    {
        try {
            $this->project->load([
                'owner',
                'activeMembers.memberable',
                'members.memberable',
                'offers.skills',
                'demands.skills',
                'reviews.user',
                'reviews.replies.user',
                'messages' => fn($q) => $q->with('sender')->latest()->take(30),
                'informations'
            ]);

            $currentMessageCount = $this->project->messages->count();
            $currentPendingCount = $this->project->members()->where('status', 'pending')->count();

            if ($currentMessageCount !== $this->lastMessageCount || $currentPendingCount !== $this->lastPendingCount) {
                $this->dispatch('project-updated');
                $this->lastMessageCount = $currentMessageCount;
                $this->lastPendingCount = $currentPendingCount;
            }
        } catch (\Exception $e) {
            // Silently fail for polling
        }
    }

    public function addSkill($name = null)
    {
        $name = $name ?: $this->skillSearch;
        if (empty($name)) return;
        
        $name = mb_convert_case(trim($name), MB_CASE_TITLE, "UTF-8");
        if (!in_array($name, $this->selectedSkills)) {
            $this->selectedSkills[] = $name;
        }
        $this->skillSearch = '';
    }

    public function removeSkill($name)
    {
        $this->selectedSkills = array_filter($this->selectedSkills, fn($s) => $s !== $name);
    }

    protected function attachSkillsToOffer($offer)
    {
        foreach ($this->selectedSkills as $name) {
            $skill = \App\Models\Skill::firstOrCreate(
                ['name' => $name],
                ['slug' => \Illuminate\Support\Str::slug($name)]
            );
            $offer->skills()->syncWithoutDetaching([$skill->id]);
        }
    }

    public function editOffer($id)
    {
        $offer = \App\Models\ProjectOffer::findOrFail($id);
        if (!$this->project->canManage(Auth::user())) return;

        $this->editingOfferId = $id;
        $this->offerTitle = $offer->title;
        $this->offerDescription = $offer->description;
        $this->selectedSkills = $offer->skills->pluck('name')->toArray();
        $this->showOfferForm = true;
        // Scroll adjustment usually handled by JS or simple UI placement
    }

    public function deleteOffer($id)
    {
        $offer = \App\Models\ProjectOffer::findOrFail($id);
        if (!$this->project->canManage(Auth::user())) return;

        $offer->delete();
        $this->project->refresh();
        session()->flash('success', 'Offre supprimée.');
    }

    public function addOffer()
    {
        if (!$this->project->canManage(Auth::user())) return;

        $this->validate([
            'offerTitle' => 'required|min:3',
            'offerDescription' => 'nullable',
        ]);

        $data = [
            'title' => $this->offerTitle,
            'description' => $this->offerDescription,
            'type' => 'offer',
        ];

        $isUpdate = (bool)$this->editingOfferId;
        if ($this->editingOfferId) {
            $projectOffer = \App\Models\ProjectOffer::findOrFail($this->editingOfferId);
            $projectOffer->update($data);
            $projectOffer->skills()->detach(); // Refresh skills
        } else {
            $projectOffer = $this->project->allOffers()->create($data);
        }
        
        $this->attachSkillsToOffer($projectOffer);

        $this->offerTitle = '';
        $this->offerDescription = '';
        $this->selectedSkills = [];
        $this->showOfferForm = false;
        $this->editingOfferId = null;
        $this->project->refresh();
        session()->flash('success', $isUpdate ? 'Offre mise à jour !' : 'Offre ajoutée !');
    }

    public function editDemand($id)
    {
        $demand = \App\Models\ProjectOffer::findOrFail($id);
        if (!$this->project->canManage(Auth::user())) return;

        $this->editingDemandId = $id;
        $this->demandTitle = $demand->title;
        $this->demandDescription = $demand->description;
        $this->selectedSkills = $demand->skills->pluck('name')->toArray();
        $this->showDemandForm = true;
    }

    public function deleteDemand($id)
    {
        $demand = \App\Models\ProjectOffer::findOrFail($id);
        if (!$this->project->canManage(Auth::user())) return;

        $demand->delete();
        $this->project->refresh();
        session()->flash('success', 'Demande supprimée.');
    }

    public function addDemand()
    {
        if (!$this->project->canManage(Auth::user())) return;

        $this->validate([
            'demandTitle' => 'required|min:3',
            'demandDescription' => 'nullable',
        ]);

        $data = [
            'title' => $this->demandTitle,
            'description' => $this->demandDescription,
            'type' => 'demand',
        ];

        $isUpdate = (bool)$this->editingDemandId;
        if ($this->editingDemandId) {
            $projectDemand = \App\Models\ProjectOffer::findOrFail($this->editingDemandId);
            $projectDemand->update($data);
            $projectDemand->skills()->detach(); // Refresh skills
        } else {
            $projectDemand = $this->project->allOffers()->create($data);
        }
        
        $this->attachSkillsToOffer($projectDemand);

        $this->demandTitle = '';
        $this->demandDescription = '';
        $this->selectedSkills = [];
        $this->showDemandForm = false;
        $this->editingDemandId = null;
        $this->project->refresh();
        session()->flash('success', $isUpdate ? 'Demande mise à jour !' : 'Demande ajoutée !');
    }

    public function getTeamMembers()
    {
        return $this->project->activeMembers()
            ->with('memberable')
            ->get()
            ->map(function($member) {
                if ($member->memberable instanceof \App\Models\User) {
                    $member->trustPath = auth()->check() ? auth()->user()->getTrustPathTo($member->memberable) : [];
                    $member->degree = count($member->trustPath) <= 2 ? 1 : (count($member->trustPath) <= 4 ? 2 : 3);
                } else {
                    $member->trustPath = [];
                    $member->degree = 3;
                }
                return $member;
            });
    }

    public function getNetworkExperts()
    {
        if (!auth()->check()) return collect();

        $me = auth()->user();
        $projectSkills = $this->project->allSkills()->pluck('name')->toArray();
        if (empty($projectSkills)) return collect();

        // Reusing the "Network" logic from Profile search but tailored for project skills
        $myCircleIds = $me->activeJoinedCircles->pluck('id');
        $firstDegreeUserIds = \App\Models\CircleMember::whereIn('circle_id', $myCircleIds)
            ->where('status', 'active')
            ->pluck('user_id')
            ->unique();

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

        $networkUserIds = $firstDegreeUserIds->merge($secondDegreeUserIds)->unique()
            ->diff($this->project->activeMembers->pluck('memberable_id'));

        return \App\Models\User::whereIn('id', $networkUserIds)
            ->whereHas('achievements', function($q) use ($projectSkills) {
                $q->whereHas('skill', function($sq) use ($projectSkills) {
                    $sq->whereIn('name', $projectSkills);
                });
            })
            ->with(['achievements.skill', 'activeJoinedCircles'])
            ->get()
            ->map(function($u) use ($firstDegreeUserIds, $projectSkills) {
                $u->degree = $firstDegreeUserIds->contains($u->id) ? 1 : 2;
                $u->trustPath = auth()->user()->getTrustPathTo($u);
                
                // Find matching skill for reason
                $matchingSkill = $u->achievements->first(fn($a) => in_array($a->skill?->name, $projectSkills));
                $u->matchReason = $matchingSkill ? "Expertise: " . $matchingSkill->skill->name : "Compétences";
                
                return $u;
            })
            ->sortBy('degree');
    }

    public function toggleStatus()
    {
        if (!$this->project->canManage(Auth::user())) {
            session()->flash('error', 'Vous n\'avez pas la permission de modifier ce projet.');
            return;
        }

        $this->project->toggleStatus();
        session()->flash('success', 'Statut du projet mis à jour.');
        $this->project->refresh();
    }

    public function joinProject()
    {
        if ($this->project->isMember(Auth::user()) || $this->project->isPending(Auth::user())) {
            session()->flash('error', 'Vous avez déjà postulé ou êtes déjà membre.');
            return;
        }

        $this->project->addMember(Auth::user(), 'member', 'pending');
        session()->flash('success', 'Votre candidature a été envoyée !');
        $this->project->refresh();
    }

    public function inviteUser($userId)
    {
        if (!$this->project->canManage(Auth::user())) return;

        $user = \App\Models\User::findOrFail($userId);
        if ($this->project->isMember($user)) {
            session()->flash('error', 'Cet utilisateur est déjà membre.');
            return;
        }

        $this->project->addMember($user, 'member', 'invited');
        $this->userSearch = '';
        session()->flash('success', 'Invitation envoyée !');
        $this->project->refresh();
    }

    public function approveMember($memberId)
    {
        if (!$this->project->canManage(Auth::user())) return;

        $member = \App\Models\ProjectMember::findOrFail($memberId);
        $member->update(['status' => 'active']);
        session()->flash('success', 'Membre approuvé !');
        $this->project->refresh();
    }

    public function rejectMember($memberId)
    {
        if (!$this->project->canManage(Auth::user())) return;

        $member = \App\Models\ProjectMember::findOrFail($memberId);
        $member->delete();
        session()->flash('success', 'Demande refusée/annulée.');
        $this->project->refresh();
    }

    public function acceptInvitation()
    {
        $member = $this->project->members()
            ->where('memberable_type', \App\Models\User::class)
            ->where('memberable_id', Auth::id())
            ->where('status', 'invited')
            ->firstOrFail();

        $member->update(['status' => 'active']);
        session()->flash('success', 'Vous avez rejoint le projet !');
        $this->project->refresh();
    }

    public function getUserSuggestionsProperty()
    {
        if (strlen($this->userSearch) < 2) return collect();
        
        return \App\Models\User::where('name', 'like', '%' . $this->userSearch . '%')
            ->where('id', '!=', auth()->id())
            ->whereDoesntHave('projectMemberships', function($q) {
                $q->where('project_id', $this->project->id);
            })
            ->with('activeJoinedCircles')
            ->take(5)
            ->get();
    }

    public function leaveProject()
    {
        if ($this->project->isOwner(Auth::user())) {
            session()->flash('error', 'Le propriétaire ne peut pas quitter le projet.');
            return;
        }

        $this->project->removeMember(Auth::user());
        session()->flash('success', 'Vous avez quitté le projet.');
        $this->project->refresh();
    }

    public function submitReview()
    {
        $this->validate([
            'reviewComment' => 'required|min:10',
        ]);

        ProjectReview::create([
            'project_id' => $this->project->id,
            'user_id' => Auth::id(),
            'type' => $this->reviewType,
            'comment' => $this->reviewComment,
            'parent_id' => $this->replyTo,
        ]);

        $this->reviewComment = '';
        $this->replyTo = null;
        session()->flash('success', 'Avis publié !');
        $this->project->refresh();
    }

    public function setReplyTo($reviewId)
    {
        $this->replyTo = $reviewId;
        $this->activeTab = 'reviews';
    }

    public function sendMessage()
    {
        $this->validate(['message' => 'required|min:2|max:1000']);

        \App\Models\Message::create([
            'circle_id' => null,
            'project_id' => $this->project->id,
            'sender_id' => Auth::id(),
            'content' => $this->message,
            'type' => 'chat',
        ]);

        $this->message = '';
        $this->project->load('messages.sender');
    }

    public function render()
    {
        return view('livewire.project.show', [
            'skills' => \App\Models\Skill::orderBy('name')->get()
        ]);
    }
}
