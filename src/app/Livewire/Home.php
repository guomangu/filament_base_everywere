<?php

namespace App\Livewire;

use Livewire\Component;

class Home extends Component
{
    public $search = '';
    public $lat;
    public $lng;
    public $locationName;

    public function resetLocation()
    {
        $this->reset(['lat', 'lng', 'locationName']);
    }

    public function render()
    {
        $circles = \App\Models\Circle::with(['owner', 'members.user', 'achievements.skill']);

        // 1. Accessibility Logic: Public circles OR private circles where user has mutual members
        $circles->where(function($q) {
            $q->where('is_public', true);
            
            if (auth()->check()) {
                $myCircleIds = auth()->user()->joinedCircles()->pluck('circles.id')->toArray();
                $q->orWhereHas('members', function($mq) use ($myCircleIds) {
                    $mq->whereIn('circle_id', $myCircleIds);
                });
                $q->orWhere('owner_id', auth()->id());
            }
        });

        // 2. Search Logic
        if (!empty($this->search)) {
            $circles->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhereHas('achievements', function($aq) {
                      $aq->where('title', 'like', '%' . $this->search . '%')
                        ->orWhereHas('skill', function($sq) {
                            $sq->where('name', 'like', '%' . $this->search . '%');
                        });
                  });
            });
        }

        // 3. Proximity Logic (Haversine if lat/lng present)
        if ($this->lat && $this->lng) {
            $circles->selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(JSON_EXTRACT(coordinates, '$.lat'))) * cos(radians(JSON_EXTRACT(coordinates, '$.lng')) - radians(?)) + sin(radians(?)) * sin(radians(JSON_EXTRACT(coordinates, '$.lat'))))) AS distance", [$this->lat, $this->lng, $this->lat])
                    ->orderBy('distance');
        } else {
            $circles->latest();
        }

        $results = $circles->take(20)->get();

        // 4. Transform results to include matching context
        $formattedResults = $results->map(function($circle) {
            $context = null;
            if (!empty($this->search)) {
                // Find why it matched
                $matchingAchievement = $circle->achievements()
                    ->where('title', 'like', '%' . $this->search . '%')
                    ->orWhereHas('skill', function($sq) {
                        $sq->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->first();
                
                if ($matchingAchievement) {
                    $context = "Réalisation : " . $matchingAchievement->title;
                } elseif (stripos($circle->name, $this->search) !== false) {
                    $context = "Cercle trouvé";
                }
            }
            $circle->matching_context = $context;
            return $circle;
        });

        return view('livewire.home', [
            'circles' => $formattedResults,
            'achievements' => \App\Models\Achievement::with(['user', 'skill', 'circle'])->where('is_verified', true)->latest()->take(6)->get(),
        ]);
    }
}
