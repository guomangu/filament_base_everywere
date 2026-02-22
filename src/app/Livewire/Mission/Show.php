<?php

namespace App\Livewire\Mission;

use Livewire\Component;
use App\Models\Skill;
use App\Models\Project;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class Show extends Component
{
    public Skill $skill;
    public $title = '';
    public $description = '';
    public string $status = 'actuelle';
    public $realizedAt = '';
    public string $infoLabel = '';
    public string $infoUrl = '';
    public string $infoImageUrl = '';

    public function mount(Skill $skill)
    {
        $this->skill = $skill;
    }

    public function createRealisation()
    {
        $this->validate([
            'title' => 'required|min:3|max:255',
            'description' => 'nullable|max:1000',
            'status' => 'required|in:actuelle,verrouillée,terminée',
            'realizedAt' => 'required_if:status,terminée|nullable|date',
            'infoLabel' => 'nullable|string|max:255',
            'infoUrl' => 'nullable|url|max:500',
            'infoImageUrl' => 'nullable|url|max:500',
        ]);

        $project = Project::create([
            'title' => $this->title,
            'description' => $this->description,
            'owner_id' => Auth::id(),
            'skill_id' => $this->skill->id,
            'status' => $this->status,
            'realized_at' => $this->status === 'terminée' ? $this->realizedAt : null,
            'metadata' => ['status' => $this->status],
        ]);

        $project->addMember(Auth::user(), 'admin');

        if (!empty($this->infoLabel) && !empty($this->infoUrl)) {
            $project->informations()->create([
                'type' => 'website',
                'label' => $this->infoLabel,
                'content' => $this->infoUrl,
                'is_verified' => false,
            ]);
        }
        
        if (!empty($this->infoImageUrl)) {
            $project->informations()->create([
                'type' => 'link',
                'label' => 'Image attachée',
                'content' => $this->infoImageUrl,
                'is_verified' => false,
            ]);
        }

        // Automatic creation of private conversation/first message
        Message::create([
            'project_id' => $project->id,
            'sender_id' => Auth::id(),
            'content' => "Début de la réalisation pour la mission : " . $this->skill->name . ". Définissons ensemble les termes.",
            'type' => 'chat',
        ]);

        session()->flash('success', 'Réalisation créée !');
        return redirect()->route('projects.show', $project);
    }

    public function render()
    {
        $currentRealisations = $this->skill->projects()
            ->where('status', 'actuelle')
            ->with(['owner', 'activeMembers.memberable'])
            ->latest()
            ->get();

        $finishedRealisations = $this->skill->projects()
            ->where('status', 'terminée')
            ->with(['owner', 'activeMembers.memberable', 'reviews'])
            ->latest()
            ->get();

        return view('livewire.mission.show', [
            'currentRealisations' => $currentRealisations,
            'finishedRealisations' => $finishedRealisations,
        ])->layoutData([
            'title' => 'Mission : ' . $this->skill->name . ' | TrustCircle',
        ]);
    }
}
