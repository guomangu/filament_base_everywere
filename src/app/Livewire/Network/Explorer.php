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
        
        // --- 1. FIND CANDIDATES ---
        // We look for Users and Circles that match the search (or just general if empty)
        
        // Tier 1: Direct Network (Circle mates)
        $myCircleIds = $baseUser->activeJoinedCircles->pluck('id');
        $directUserIds = CircleMember::whereIn('circle_id', $myCircleIds)
            ->where('status', 'active')
            ->pluck('user_id')
            ->unique();
        
        // Tier 2: Secondary Network
        $secondaryCircleIds = CircleMember::whereIn('user_id', $directUserIds)
            ->where('status', 'active')
            ->pluck('circle_id')
            ->unique()
            ->diff($myCircleIds);
        
        $secondaryUserIds = CircleMember::whereIn('circle_id', $secondaryCircleIds)
            ->where('status', 'active')
            ->pluck('user_id')
            ->unique()
            ->diff($directUserIds)
            ->diff([$baseUser->id]);

        // Tier 3+: Local/Regional/Country Match
        // We'll fetch candidates based on location fields if network is small
        
        $queryUsers = User::query()
            ->with(['achievements.skill', 'activeJoinedCircles'])
            ->where('id', '!=', $baseUser->id);

        if (!empty($this->search)) {
            $queryUsers->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhereHas('achievements.skill', fn($sq) => $sq->where('name', 'like', $searchTerm));
            });
        }

        $candidates = $queryUsers->get()->map(function($u) use ($directUserIds, $secondaryUserIds, $baseUser) {
            $u->proximity_type = 'global';
            $u->proximity_level = 5;

            if ($directUserIds->contains($u->id)) {
                $u->proximity_type = 'direct';
                $u->proximity_level = 1;
            } elseif ($secondaryUserIds->contains($u->id)) {
                $u->proximity_type = 'contact';
                $u->proximity_level = 2;
            } else {
                // Check geographical tiers
                $myCities = $baseUser->activeJoinedCircles->pluck('city')->filter()->unique();
                $uCities = $u->activeJoinedCircles->pluck('city')->filter()->unique();
                if ($myCities->intersect($uCities)->isNotEmpty()) {
                    $u->proximity_type = 'city';
                    $u->proximity_level = 3;
                } else {
                    $myRegions = $baseUser->activeJoinedCircles->pluck('region')->filter()->unique();
                    $uRegions = $u->activeJoinedCircles->pluck('region')->filter()->unique();
                    if ($myRegions->intersect($uRegions)->isNotEmpty()) {
                        $u->proximity_type = 'region';
                        $u->proximity_level = 4;
                    }
                }
            }
            return $u;
        });

        // Same for Circles
        $queryCircles = Circle::query()
            ->with(['owner', 'activeMembers.user', 'achievements.skill'])
            ->where('id', '!=', ($this->origin instanceof Circle ? $this->origin->id : 0));

        if (!empty($this->search)) {
            $queryCircles->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhereHas('achievements.skill', fn($sq) => $sq->where('name', 'like', $searchTerm));
            });
        }

        $circleCandidates = $queryCircles->get()->map(function($c) use ($myCircleIds, $baseUser) {
            $c->proximity_type = 'global';
            $c->proximity_level = 5;

            if ($myCircleIds->contains($c->id)) {
                $c->proximity_type = 'direct';
                $c->proximity_level = 1;
            } else {
                // Determine proximity through members or geography
                $cMemberIds = $c->activeMembers->pluck('user_id');
                // Path check?
                
                // Geographical shortcuts
                $myCities = $baseUser->activeJoinedCircles->pluck('city')->filter()->unique();
                if ($myCities->contains($c->city)) {
                    $c->proximity_type = 'city';
                    $c->proximity_level = 3;
                } else {
                    $myRegions = $baseUser->activeJoinedCircles->pluck('region')->filter()->unique();
                    if ($myRegions->contains($c->region)) {
                        $c->proximity_type = 'region';
                        $c->proximity_level = 4;
                    }
                }
            }
            return $c;
        });

        return $candidates->concat($circleCandidates)
            ->sortBy('proximity_level')
            ->values()
            ->take($this->limit)
            ->map(function($item) use ($baseUser) {
                $item->trustPath = $baseUser->getTrustPathTo($item);
                return $item;
            });
    }

    public function render()
    {
        return view('livewire.network.explorer', [
            'results' => $this->results
        ]);
    }
}
