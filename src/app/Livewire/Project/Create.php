<?php

namespace App\Livewire\Project;

use App\Models\Project;
use App\Models\ProjectOffer;
use App\Models\Skill;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    public $step = 1; // 1: Basic info, 2: Offers, 3: Demands, 4: Review
    
    // Basic info
    public $title = '';
    public $description = '';
    public $is_open = true;
    
    // Offers
    public $offers = [];
    public $offerTitle = '';
    public $offerDescription = '';
    public $offerSkills = [];
    
    // Demands
    public $demands = [];
    public $demandTitle = '';
    public $demandDescription = '';
    public $demandSkills = [];

    public function mount()
    {
        // Initialize with empty arrays
        $this->offers = [];
        $this->demands = [];
    }

    public function nextStep()
    {
        $this->validateCurrentStep();
        $this->step++;
    }

    public function previousStep()
    {
        $this->step--;
    }

    public function validateCurrentStep()
    {
        if ($this->step === 1) {
            $this->validate([
                'title' => 'required|min:3|max:255',
                'description' => 'nullable|max:1000',
            ]);
        }
    }

    public function addOffer()
    {
        $this->validate([
            'offerTitle' => 'required|min:3',
            'offerDescription' => 'nullable',
        ]);

        $this->offers[] = [
            'title' => $this->offerTitle,
            'description' => $this->offerDescription,
            'skills' => $this->offerSkills,
        ];

        $this->offerTitle = '';
        $this->offerDescription = '';
        $this->offerSkills = [];
    }

    public function removeOffer($index)
    {
        unset($this->offers[$index]);
        $this->offers = array_values($this->offers);
    }

    public function addDemand()
    {
        $this->validate([
            'demandTitle' => 'required|min:3',
            'demandDescription' => 'nullable',
        ]);

        $this->demands[] = [
            'title' => $this->demandTitle,
            'description' => $this->demandDescription,
            'skills' => $this->demandSkills,
        ];

        $this->demandTitle = '';
        $this->demandDescription = '';
        $this->demandSkills = [];
    }

    public function removeDemand($index)
    {
        unset($this->demands[$index]);
        $this->demands = array_values($this->demands);
    }

    public function create()
    {
        // Always validate Step 1 info (minimum required)
        $this->validate([
            'title' => 'required|min:3|max:255',
            'description' => 'nullable|max:1000',
        ]);

        // Create project
        $project = Project::create([
            'title' => $this->title,
            'description' => $this->description,
            'owner_id' => Auth::id(),
            'is_open' => $this->is_open,
        ]);

        // Add owner as admin member
        $project->addMember(Auth::user(), 'admin');

        // Create offers
        foreach ($this->offers as $offer) {
            $projectOffer = $project->allOffers()->create([
                'title' => $offer['title'],
                'description' => $offer['description'],
                'type' => 'offer',
            ]);
        }

        // Create demands
        foreach ($this->demands as $demand) {
            $projectDemand = $project->allOffers()->create([
                'title' => $demand['title'],
                'description' => $demand['description'],
                'type' => 'demand',
            ]);
        }

        // Attach all unique skills to the project level (Expertise)
        $allSkillIds = [];
        foreach ($this->offers as $o) {
            if (!empty($o['skills'])) $allSkillIds = array_merge($allSkillIds, $o['skills']);
        }
        foreach ($this->demands as $d) {
            if (!empty($d['skills'])) $allSkillIds = array_merge($allSkillIds, $d['skills']);
        }
        
        if (!empty($allSkillIds)) {
            $project->skills()->sync(array_unique($allSkillIds));
        }

        session()->flash('success', 'Projet créé avec succès !');
        return redirect()->route('projects.show', $project);
    }

    public function render()
    {
        $skills = Skill::orderBy('name')->get();
        
        return view('livewire.project.create', [
            'skills' => $skills,
        ]);
    }
}
