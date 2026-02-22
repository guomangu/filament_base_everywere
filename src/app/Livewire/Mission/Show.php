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

    public function mount(Skill $skill)
    {
        $this->skill = $skill;
    }

    public function createRealisation()
    {
        $this->validate([
            'title' => 'required|min:3|max:255',
            'description' => 'nullable|max:1000',
        ]);

        $project = Project::create([
            'title' => $this->title,
            'description' => $this->description,
            'owner_id' => Auth::id(),
            'skill_id' => $this->skill->id,
            'status' => 'actuelle',
        ]);

        $project->addMember(Auth::user(), 'admin');

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
