<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;

class Home extends Component
{
    use \App\Traits\HandlesOfferActions;
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

        // 2. Fetch Offers
        $offerQuery = \App\Models\ProjectOffer::where('type', 'offer')
            ->join('projects', 'project_offers.project_id', '=', 'projects.id')
            ->select('project_offers.*', 'projects.coordinates', 'projects.address', 'projects.owner_id')
            ->with(['project.owner', 'project.activeMembers', 'informations', 'reviews']);

        // Offer Accessibility (Link to open projects)
        $offerQuery->where('projects.is_open', true);

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

            // Filter Offers
            $offerQuery->where(function($q) use ($searchTerm) {
                $q->whereRaw('LOWER(project_offers.title) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(project_offers.description) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(projects.title) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(projects.address) LIKE ?', [$searchTerm]);
            });
        }

        // 4. Proximity Logic
        if ($this->lat && $this->lng) {
            $distanceRaw = "(6371 * acos(cos(radians(?)) * cos(radians(JSON_EXTRACT(projects.coordinates, '$.lat'))) * cos(radians(JSON_EXTRACT(projects.coordinates, '$.lng')) - radians(?)) + sin(radians(?)) * sin(radians(JSON_EXTRACT(projects.coordinates, '$.lat')))))";
            $circleDistanceRaw = "(6371 * acos(cos(radians(?)) * cos(radians(JSON_EXTRACT(coordinates, '$.lat'))) * cos(radians(JSON_EXTRACT(coordinates, '$.lng')) - radians(?)) + sin(radians(?)) * sin(radians(JSON_EXTRACT(coordinates, '$.lat')))))";
            
            $circleQuery->selectRaw("*, $circleDistanceRaw AS distance", [$this->lat, $this->lng, $this->lat]);
            $offerQuery->selectRaw("$distanceRaw AS distance", [$this->lat, $this->lng, $this->lat]);
        }

        // 5. Execute and Merge
        $circles = $circleQuery->get();
        $offers = $offerQuery->get();

        $allResults = $circles->concat($offers);

        // 6. Proximity Sorting
        if ($this->lat && $this->lng) {
            $allResults = $allResults->sortBy('distance');
        } else {
            $allResults = $allResults->sortByDesc('created_at');
        }

        // 7. Limit and Format
        $formattedResults = $allResults->take(32)->map(function($entity) {
            // Determine type
            $entity->is_circle = $entity instanceof \App\Models\Circle;
            $entity->is_offer = $entity instanceof \App\Models\ProjectOffer;

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
                    if (stripos(mb_strtolower($entity->title), $search) !== false) $entity->matching_context = "Offre trouvée";
                    else $entity->matching_context = "Projet associé";
                }
            }

            return $entity;
        });

        $dynamicTitle = 'TrustCircle | Réseau de Confiance & Projets de Proximité';
        $dynamicDesc = 'Trouvez des experts vérifiés et soutenez des initiatives locales. TrustCircle connecte les talents via des cercles de confiance pour une collaboration transparente.';

        if ($this->search) {
            $dynamicTitle = '🔍 ' . ucfirst($this->search) . ' : Experts et Offres de Confiance | TrustCircle';
            $dynamicDesc = 'Recherche en cours pour "' . $this->search . '". Découvrez les meilleurs talents et opportunités locales correspondant à votre besoin sur TrustCircle.';
        } elseif ($this->locationName) {
            $dynamicTitle = '📍 À Proximité de ' . $this->locationName . ' | Initiatives & Talents | TrustCircle';
            $dynamicDesc = 'Explorez les cercles de confiance et offres de services à ' . $this->locationName . '. Connectez-vous avec votre communauté locale pour bâtir demain.';
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
