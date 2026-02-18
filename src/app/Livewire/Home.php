<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;

class Home extends Component
{
    #[Url]
    public $search = '';
    public $lat;
    public $lng;
    public $locationName;

    public function mount()
    {
        if (auth()->check() && auth()->user()->coordinates && auth()->user()->location) {
            $this->lat = auth()->user()->coordinates['lat'];
            $this->lng = auth()->user()->coordinates['lng'];
            $this->locationName = auth()->user()->location;
        }
    }

    public function resetLocation()
    {
        $this->reset(['lat', 'lng', 'locationName']);
    }

    public function setLocation($lat, $lng, $name)
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->locationName = $name;

        if (auth()->check()) {
            auth()->user()->update([
                'coordinates' => ['lat' => $lat, 'lng' => $lng],
                'location' => $name
            ]);
        }
    }

    public function render()
    {
        // 1. Fetch Circles
        $circleQuery = \App\Models\Circle::with([
            'owner.achievements.skill', 
            'owner.proches.achievements.skill',
            'members.user.achievements.skill', 
            'members.user.proches.achievements.skill',
            'achievements.skill'
        ]);

        // Circle Accessibility Logic
        $circleQuery->where(function($q) {
            $q->where('is_public', true);
            if (auth()->check()) {
                $myCircleIds = auth()->user()->joinedCircles()->pluck('circles.id')->toArray();
                $q->orWhereHas('members', function($mq) use ($myCircleIds) {
                    $mq->whereIn('circle_id', $myCircleIds);
                });
                $q->orWhere('owner_id', auth()->id());
            }
        });

        // 2. Fetch Projects
        $projectQuery = \App\Models\Project::with(['owner', 'activeMembers', 'offers', 'demands', 'skills']);

        // Project Accessibility (Open projects only for now, or owned/member)
        $projectQuery->where(function($q) {
            $q->where('is_open', true);
            if (auth()->check()) {
                $q->orWhere('owner_id', auth()->id())
                  ->orWhereHas('activeMembers', fn($mq) => $mq->where('memberable_id', auth()->id())->where('memberable_type', \App\Models\User::class));
            }
        });

        // 3. Search Logic (Universal Smart Search)
        if (!empty($this->search)) {
            $searchTerm = '%' . mb_strtolower($this->search) . '%';
            
            // Filter Circles
            $circleQuery->where(function($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(address) LIKE ?', [$searchTerm])
                  ->orWhereHas('owner', function($uq) use ($searchTerm) {
                      $uq->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                        ->orWhereHas('achievements.skill', fn($sq) => $sq->whereRaw('LOWER(name) LIKE ?', [$searchTerm]));
                  })
                  ->orWhereHas('members.user.achievements.skill', fn($sq) => $sq->whereRaw('LOWER(name) LIKE ?', [$searchTerm]))
                  ->orWhereHas('achievements.skill', fn($sq) => $sq->whereRaw('LOWER(name) LIKE ?', [$searchTerm]));
            });

            // Filter Projects
            $projectQuery->where(function($q) use ($searchTerm) {
                $q->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(address) LIKE ?', [$searchTerm])
                  ->orWhereHas('owner', fn($uq) => $uq->whereRaw('LOWER(name) LIKE ?', [$searchTerm]))
                  ->orWhereHas('skills', fn($sq) => $sq->whereRaw('LOWER(name) LIKE ?', [$searchTerm]))
                  ->orWhereHas('allOffers', function($oq) use ($searchTerm) {
                      $oq->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                        ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm]);
                  });
            });
        }

        // 4. Proximity Logic
        if ($this->lat && $this->lng) {
            $distanceRaw = "(6371 * acos(cos(radians(?)) * cos(radians(JSON_EXTRACT(coordinates, '$.lat'))) * cos(radians(JSON_EXTRACT(coordinates, '$.lng')) - radians(?)) + sin(radians(?)) * sin(radians(JSON_EXTRACT(coordinates, '$.lat')))))";
            
            $circleQuery->selectRaw("*, $distanceRaw AS distance", [$this->lat, $this->lng, $this->lat]);
            $projectQuery->selectRaw("*, $distanceRaw AS distance", [$this->lat, $this->lng, $this->lat]);
        }

        // 5. Execute and Merge
        $circles = $circleQuery->get();
        $projects = $projectQuery->get();

        $allResults = $circles->concat($projects);

        // 6. Proximity Sorting
        if ($this->lat && $this->lng) {
            $allResults = $allResults->sortBy('distance');
        } else {
            $allResults = $allResults->sortByDesc('created_at');
        }

        // 7. Limit and Format
        $formattedResults = $allResults->take(20)->map(function($entity) {
            // Determine type
            $entity->is_circle = $entity instanceof \App\Models\Circle;
            $entity->is_project = $entity instanceof \App\Models\Project;

            // Smart Distance Label
            if (!empty($entity->coordinates) && (isset($entity->coordinates['lat']) && $entity->coordinates['lat'] != 0)) {
                if (isset($entity->distance)) {
                    $dist = $entity->distance;
                    if ($dist < 1) {
                        $entity->smart_distance = "À " . round($dist * 1000) . "m";
                    } elseif ($dist < 2) {
                        $entity->smart_distance = "Tout proche";
                    } else {
                        $entity->smart_distance = round($dist, 1) . " km";
                    }
                    
                    if ($this->locationName && stripos(mb_strtolower($entity->address), mb_strtolower(explode(',', $this->locationName)[0])) !== false) {
                        $entity->smart_distance = "Même ville • " . $entity->smart_distance;
                    }
                }
            } else {
                $entity->smart_distance = "Remote / Dématérialisé";
            }

            // Matching Context (Simplified for high speed merge)
            if (!empty($this->search)) {
                $search = mb_strtolower($this->search);
                if ($entity->is_circle) {
                    if (stripos(mb_strtolower($entity->name), $search) !== false) $entity->matching_context = "Cercle trouvé";
                    else $entity->matching_context = "Expertise associée";
                } else {
                    if (stripos(mb_strtolower($entity->title), $search) !== false) $entity->matching_context = "Projet trouvé";
                    else $entity->matching_context = "Besoins ou Offres";
                }
            }

            return $entity;
        });

        $dynamicTitle = 'TrustCircle | Découvrez les Cercles et Projets';
        $dynamicDesc = 'Rejoignez TrustCircle pour découvrir des cercles d\'expertises et des projets locaux basés sur la confiance.';

        if ($this->search) {
            $dynamicTitle = 'Résultats pour "' . $this->search . '" | TrustCircle';
            $dynamicDesc = 'Découvrez tous les cercles et projets correspondant à "' . $this->search . '" sur TrustCircle. Collaboration et proximité.';
        }

        return view('livewire.home', [
            'results' => $formattedResults,
            'achievements' => \App\Models\Achievement::with(['user', 'skill', 'circle'])->where('is_verified', true)->latest()->take(6)->get(),
        ])->layoutData([
            'title' => $dynamicTitle,
            'description' => $dynamicDesc,
        ]);
    }
}
