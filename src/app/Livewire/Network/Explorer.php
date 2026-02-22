<?php

namespace App\Livewire\Network;

use Livewire\Component;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Circle;
use App\Models\CircleMember;
use App\Models\Skill;
use Illuminate\Support\Collection;

class Explorer extends Component
{
    public Model $origin; // Can be User or Circle
    public string $search = '';
    public int $limit = 10;

    public function mount(Model $origin)
    {
        $this->origin = $origin;
    }

    protected function getBaseUser(): ?User
    {
        if ($this->origin instanceof User) {
            return $this->origin;
        }
        if ($this->origin instanceof Circle) {
            return $this->origin->owner;
        }
        return auth()->user();
    }

    public function getResultsProperty(): Collection
    {
        $baseUser = $this->getBaseUser();
        if (!$baseUser) return collect();

        $searchTerm = '%' . strtolower($this->search) . '%';
        $myCircleIds = $baseUser->activeJoinedCircles->pluck('id');
        
        // --- 1. FIND CIRCLE CANDIDATES ---
        $queryCircles = Circle::query()
            ->with(['owner', 'activeMembers.user.achievements.skill', 'achievements.skill'])
            ->where('id', '!=', ($this->origin instanceof Circle ? $this->origin->id : 0));

        if (!empty($this->search)) {
            $queryCircles->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  // Search through members
                  ->orWhereHas('activeMembers.user', function($uq) use ($searchTerm) {
                      $uq->where('name', 'like', $searchTerm);
                  })
                  // Search through member skills
                  ->orWhereHas('activeMembers.user.achievements.skill', function($sq) use ($searchTerm) {
                      $sq->where('name', 'like', $searchTerm);
                  })
                  // Search through member realizations (achievements)
                  ->orWhereHas('activeMembers.user.achievements', function($aq) use ($searchTerm) {
                      $aq->where('title', 'like', $searchTerm);
                  });
            });
        }

        $results = $queryCircles->get()->map(function($c) use ($myCircleIds, $baseUser) {
            $c->proximity_type = 'global';
            $c->proximity_level = 5;

            if ($myCircleIds->contains($c->id)) {
                $c->proximity_type = 'direct';
                $c->proximity_level = 1;
            } else {
                $myCities = $baseUser->activeJoinedCircles->pluck('city')->filter()->unique();
                if ($myCities->contains($c->city)) {
                    $c->proximity_type = 'city';
                    $c->proximity_level = 3;
                } else {
                    $myRegions = $baseUser->activeJoinedCircles->pluck('region')->filter()->unique();
                    if ($myRegions->contains($c->region)) {
                        $c->proximity_type = 'region';
                        $c->proximity_level = 4;
                    } else {
                        $myCountries = $baseUser->activeJoinedCircles->pluck('country')->filter()->unique();
                        if ($myCountries->contains($c->country)) {
                            $c->proximity_type = 'global'; // Same country
                            $c->proximity_level = 5;
                        } else {
                            $c->proximity_type = 'earth'; // Different country
                            $c->proximity_level = 6;
                        }
                    }
                }
            }
            return $c;
        });

        return $results
            ->sortBy('proximity_level')
            ->values()
            ->take($this->limit)
            ->map(function($item) use ($baseUser) {
                // For the trust path, prioritize the VIEWER's connection.
                // Fallback to the ORIGIN's connection if viewer has none (discovery mode).
                $viewer = auth()->user();
                $path = $viewer ? $viewer->getTrustPathTo($item) : [];
                
                if (empty($path)) {
                    $path = $baseUser->getTrustPathTo($item);
                }
                
                $item->trustPath = $path;
                return $item;
            });
    }

    public function selectSkill(string $skillName)
    {
        $existing = collect(explode(' ', $this->search))->map(fn($s) => strtolower(trim($s)))->filter();
        if (!$existing->contains(strtolower($skillName))) {
            $this->search = trim($this->search . ' ' . $skillName);
        }
    }

    public function render()
    {
        return view('livewire.network.explorer', [
            'results' => $this->results
        ]);
    }
}
