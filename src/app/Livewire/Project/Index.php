<?php

namespace App\Livewire\Project;

use App\Models\Project;
use App\Models\Skill;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = 'all'; // all, offers, demands
    public $filterStatus = 'open'; // all, open, closed
    public $selectedSkills = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => 'all'],
        'filterStatus' => ['except' => 'open'],
        'selectedSkills' => ['except' => []],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleSkill($skillId)
    {
        if (in_array($skillId, $this->selectedSkills)) {
            $this->selectedSkills = array_diff($this->selectedSkills, [$skillId]);
        } else {
            $this->selectedSkills[] = $skillId;
        }
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterType = 'all';
        $this->filterStatus = 'open';
        $this->selectedSkills = [];
        $this->resetPage();
    }

    public function render()
    {
        $query = Project::with(['owner', 'activeMembers', 'offers', 'demands']);

        // Search filter
        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm)
                  ->orWhere('address', 'like', $searchTerm)
                  // Search in owner
                  ->orWhereHas('owner', function($uq) use ($searchTerm) {
                      $uq->where('name', 'like', $searchTerm);
                  })
                  // Search in skills
                  ->orWhereHas('skills', function($sq) use ($searchTerm) {
                      $sq->where('name', 'like', $searchTerm);
                  })
                  // Search in offers & demands
                  ->orWhereHas('allOffers', function($oq) use ($searchTerm) {
                      $oq->where('title', 'like', $searchTerm)
                        ->orWhere('description', 'like', $searchTerm);
                  });
            });
        }

        // Status filter
        if ($this->filterStatus === 'open') {
            $query->where('is_open', true);
        } elseif ($this->filterStatus === 'closed') {
            $query->where('is_open', false);
        }

        // Type filter (offers/demands)
        if ($this->filterType === 'offers') {
            $query->whereHas('offers');
        } elseif ($this->filterType === 'demands') {
            $query->whereHas('demands');
        }

        if (!empty($this->selectedSkills)) {
            $query->whereHas('skills', function($sq) {
                $sq->whereIn('skills.id', $this->selectedSkills);
            });
        }

        $projects = $query->latest()->paginate(12);
        $skills = Skill::orderBy('name')->get();

        return view('livewire.project.index', [
            'projects' => $projects,
            'skills' => $skills,
        ]);
    }
}
