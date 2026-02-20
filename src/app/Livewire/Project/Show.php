<?php

namespace App\Livewire\Project;

use App\Models\Project;
use App\Models\ProjectReview;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class Show extends Component
{
    use WithFileUploads;

    public Project $project;
    
    // Review form
    public $reviewType = 'validate';
    public $reviewComment = '';
    public $replyTo = null;

    // Quote request form
    public $showQuoteModal = false;
    public $quoteOfferId = null;
    public $quoteMessage = '';

    // Message form
    public $message = '';
    public $lastMessageCount = 0;

    // Membership management
    public $userSearch = '';
    public $lastPendingCount = 0;
    public $showMemberManager = false;

    // Progressive management (Petit à Petit)
    public $offerTitle = '';
    public $offerDescription = '';
    public $offerImages = [];
    public $offerInfos = [['label' => '', 'title' => '']];
    
    // Ratings & Reviews
    public $reviewRating = 5;
    public $activeOfferIdForReview = null;
    public $showReviewModal = false;
    
    // Editing states
    public $showOfferForm = false;
    public $editingOfferId = null;
    
    // Smart Skill Selector (Now for Project)
    public $selectedSkills = []; // Format: ['Skill name 1', 'Skill name 2']
    public $skillSearch = '';
    public $showProjectSkillForm = false;
    
    // Address Management
    public $address = '';
    public $isEditingAddress = false;

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->lastMessageCount = $this->project->messages()->count();
        $this->lastPendingCount = $this->project->members()->where('status', 'pending')->count();
        $this->refresh();
        $this->selectedSkills = $this->project->skills->pluck('name')->toArray();
        $this->address = $this->project->address;
    }

    public function refresh()
    {
        try {
            $this->project->load([
                'owner',
                'activeMembers.memberable',
                'members.memberable',
                'offers' => fn($q) => $q->with(['skills', 'reviews.user', 'reviews.replies.user', 'informations']),
                'reviews.user',
                'reviews.replies.user',
                'messages' => fn($q) => $q->with('sender')->latest()->take(30),
                'informations'
            ]);

            $currentMessageCount = $this->project->messages()->count();
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
        
        // Prevent duplicates (case-insensitive check)
        $existing = collect($this->selectedSkills)->map(fn($s) => mb_strtolower($s))->contains(mb_strtolower($name));
        
        if (!$existing) {
            $this->selectedSkills[] = $name;
        }
        $this->skillSearch = '';
    }

    public function removeSkill($name)
    {
        $this->selectedSkills = array_filter($this->selectedSkills, fn($s) => $s !== $name);
    }

    public function saveProjectSkills()
    {
        if (!$this->project->canManage(Auth::user())) return;

        $skillIds = [];
        foreach ($this->selectedSkills as $name) {
            $skill = \App\Models\Skill::firstOrCreate(
                ['name' => $name],
                ['slug' => \Illuminate\Support\Str::slug($name)]
            );
            $skillIds[] = $skill->id;
        }

        $this->project->skills()->sync($skillIds);
        $this->showProjectSkillForm = false;
        $this->project->refresh();
        session()->flash('success', 'Compétences du projet mises à jour.');
    }

    public function editOffer($id)
    {
        $offer = \App\Models\ProjectOffer::with('informations')->findOrFail($id);
        if (!$this->project->canManage(Auth::user())) return;

        $this->editingOfferId = $id;
        $this->offerTitle = $offer->title;
        $this->offerDescription = $offer->description;
        
        $this->offerInfos = $offer->informations->map(fn($info) => [
            'label' => $info->label,
            'title' => $info->title
        ])->toArray();
        
        if (empty($this->offerInfos)) {
            $this->offerInfos = [['label' => '', 'title' => '']];
        }

        $this->showOfferForm = true;
    }

    public function addOfferInfo()
    {
        $this->offerInfos[] = ['label' => '', 'title' => ''];
    }

    public function removeOfferInfo($index)
    {
        unset($this->offerInfos[$index]);
        $this->offerInfos = array_values($this->offerInfos);
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
            'offerImages.*' => 'image|max:2048',
            'offerInfos.*.label' => 'nullable|string|max:50',
            'offerInfos.*.title' => 'nullable|required_with:offerInfos.*.label|string|max:255',
        ]);

        $imagePaths = [];
        if (!empty($this->offerImages)) {
            foreach ($this->offerImages as $image) {
                $imagePaths[] = $image->store('project-offers', 'public');
            }
        }

        $data = [
            'title' => $this->offerTitle,
            'description' => $this->offerDescription,
            'type' => 'offer',
        ];

        if (!empty($imagePaths)) {
            $data['images'] = $imagePaths;
        }

        $isUpdate = (bool)$this->editingOfferId;
        if ($this->editingOfferId) {
            $projectOffer = \App\Models\ProjectOffer::findOrFail($this->editingOfferId);
            if (!isset($data['images']) && $projectOffer->images) {
                unset($data['images']); 
            }
            $projectOffer->update($data);
        } else {
            $projectOffer = $this->project->allOffers()->create($data);
        }

        // Sync Information
        $projectOffer->informations()->delete();
        foreach ($this->offerInfos as $info) {
            if (!empty($info['title'])) {
                $projectOffer->informations()->create([
                    'label' => $info['label'],
                    'title' => $info['title'],
                    'author_id' => Auth::id(),
                ]);
            }
        }
        
        $this->offerTitle = '';
        $this->offerDescription = '';
        $this->offerInfos = [['label' => '', 'title' => '']];
        $this->offerImages = [];
        $this->showOfferForm = false;
        $this->editingOfferId = null;
        $this->project->refresh();
        session()->flash('success', $isUpdate ? 'Offre mise à jour !' : 'Offre ajoutée !');
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

    public function updateAddress()
    {
        if (!$this->project->canManage(Auth::user())) return;

        $this->validate([
            'address' => 'nullable|string|max:255',
        ]);

        $this->project->update([
            'address' => $this->address,
        ]);

        $this->isEditingAddress = false;
        $this->project->refresh();
        session()->flash('success', 'Localisation mise à jour.');
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

    public function setReviewOffer($offerId, $showModal = false)
    {
        // If the offerId is the same as the currently active one AND we are not asking for a modal, toggle it off.
        if ($this->activeOfferIdForReview === $offerId && !$showModal) {
            $this->activeOfferIdForReview = null;
        } else {
            $this->activeOfferIdForReview = $offerId;
        }
        
        $this->showReviewModal = $showModal;
    }

    public function setQuoteOffer($offerId, $showModal = true)
    {
        $this->quoteOfferId = $offerId;
        $this->showQuoteModal = $showModal;
        
        if ($showModal && $offerId) {
            $offer = \App\Models\ProjectOffer::find($offerId);
            $this->quoteMessage = "Bonjour,\n\nJe souhaiterais obtenir un devis pour l'article : " . ($offer->title ?? 'votre offre') . ".\n\nMerci par avance !";
        }
    }

    public function submitQuoteRequest()
    {
        $this->validate([
            'quoteMessage' => 'required|min:5|max:2000',
            'quoteOfferId' => 'required|exists:project_offers,id',
        ]);

        $offer = \App\Models\ProjectOffer::findOrFail($this->quoteOfferId);

        \App\Models\Message::create([
            'project_id' => $this->project->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $this->project->owner_id,
            'content' => $this->quoteMessage,
            'type' => 'chat',
            'metadata' => [
                'type' => 'quote_request',
                'offer_id' => $this->quoteOfferId,
                'offer_title' => $offer->title,
            ],
        ]);

        $this->quoteMessage = '';
        $this->quoteOfferId = null;
        $this->showQuoteModal = false;
        session()->flash('success', 'Votre demande de devis a été envoyée en message privé au propriétaire du projet !');
    }


    public function submitReview()
    {
        $this->validate([
            'reviewComment' => 'required|min:5',
            'reviewRating' => 'required|integer|min:1|max:5',
        ]);

        // If it's a new review (not a reply)
        if (!$this->replyTo) {
            $query = $this->project->reviews()
                ->where('user_id', Auth::id())
                ->whereNull('parent_id');
            
            if ($this->activeOfferIdForReview) {
                $query->where('project_offer_id', $this->activeOfferIdForReview);
            }

            if ($query->exists()) {
                session()->flash('error', 'Vous avez déjà laissé un avis sur cet article.');
                return;
            }
        } else {
            // If it's a reply, only admins can reply
            if (!$this->project->canManage(Auth::user())) {
                session()->flash('error', 'Seuls les administrateurs du projet peuvent répondre aux avis.');
                return;
            }
        }

        ProjectReview::create([
            'project_id' => $this->project->id,
            'project_offer_id' => $this->activeOfferIdForReview,
            'user_id' => Auth::id(),
            'type' => $this->reviewType,
            'rating' => $this->reviewRating,
            'comment' => $this->reviewComment,
            'parent_id' => $this->replyTo,
        ]);

        $this->reviewComment = '';
        $this->reviewRating = 5;
        $this->replyTo = null;
        $this->activeOfferIdForReview = null;
        $this->showReviewModal = false;
        session()->flash('success', $this->replyTo ? 'Réponse publiée !' : 'Avis publié !');
        $this->project->refresh();
    }

    public function deleteReview($reviewId)
    {
        $review = ProjectReview::findOrFail($reviewId);
        
        // Only owner or project admin can delete
        if ($review->user_id !== Auth::id() && !$this->project->canManage(Auth::user())) {
            session()->flash('error', 'Action non autorisée.');
            return;
        }

        // Lock check: if it's a top-level review and has replies, it's locked
        if ($review->isTopLevel() && $review->replies()->exists()) {
            session()->flash('error', 'Cet avis est verrouillé car un administrateur y a répondu.');
            return;
        }

        $review->delete();
        session()->flash('success', 'Avis supprimé.');
        $this->project->refresh();
    }

    public function setReplyTo($reviewId)
    {
        $this->replyTo = $reviewId;
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

    public function getSkillSuggestionsProperty()
    {
        if (strlen($this->skillSearch) < 1) return collect();

        $searchTerm = '%' . $this->skillSearch . '%';

        return \App\Models\Skill::where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhereHas('achievements', function($aq) use ($searchTerm) {
                      $aq->where('title', 'like', $searchTerm);
                  });
            })
            ->whereNotIn('name', $this->selectedSkills)
            ->orderBy('name')
            ->take(8)
            ->get();
    }

    public function render()
    {
        return view('livewire.project.show')->layoutData([
            'title' => $this->project->title . ($this->project->city ? ' - ' . $this->project->city : '') . ' | Projet sur TrustCircle',
            'description' => \Illuminate\Support\Str::limit(strip_tags($this->project->description), 160, '...'),
            'og_image' => $this->project->owner->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($this->project->owner->name),
        ]);
    }
}
