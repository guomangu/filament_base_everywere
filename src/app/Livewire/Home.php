<?php

namespace App\Livewire;

use Livewire\Component;

class Home extends Component
{
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
        $circles = \App\Models\Circle::with([
            'owner.achievements.skill', 
            'owner.proches.achievements.skill',
            'members.user.achievements.skill', 
            'members.user.proches.achievements.skill',
            'achievements.skill'
        ]);

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

        // 2. Search Logic (Universal Smart Search - Case Insensitive)
        if (!empty($this->search)) {
            $searchTerm = '%' . mb_strtolower($this->search) . '%';
            $circles->where(function($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(address) LIKE ?', [$searchTerm])
                  // 1. Search in Owner (Name + Profile Skills + Bio)
                  ->orWhereHas('owner', function($uq) use ($searchTerm) {
                      $uq->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                        ->orWhereRaw('LOWER(bio) LIKE ?', [$searchTerm])
                        ->orWhereHas('achievements', function($aq) use ($searchTerm) {
                            $aq->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                              ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm])
                              ->orWhereHas('skill', function($sq) use ($searchTerm) {
                                  $sq->whereRaw('LOWER(name) LIKE ?', [$searchTerm]);
                              });
                        });
                  })
                  // 2. Search in Members (Name + Profile Skills)
                  ->orWhereHas('members.user', function($uq) use ($searchTerm) {
                      $uq->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                        ->orWhereRaw('LOWER(bio) LIKE ?', [$searchTerm])
                        ->orWhereHas('achievements', function($aq) use ($searchTerm) {
                            $aq->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                              ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm])
                              ->orWhereHas('skill', function($sq) use ($searchTerm) {
                                  $sq->whereRaw('LOWER(name) LIKE ?', [$searchTerm]);
                              });
                        });
                  })
                  // 3. Search in Direct Circle Achievements
                  ->orWhereHas('achievements', function($aq) use ($searchTerm) {
                      $aq->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                        ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm])
                        ->orWhereHas('skill', function($sq) use ($searchTerm) {
                            $sq->whereRaw('LOWER(name) LIKE ?', [$searchTerm]);
                        });
                  })
                  // 4. Search in Proches of Owner/Members
                  ->orWhereHas('owner.proches', function($pq) use ($searchTerm) {
                      $pq->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                        ->orWhereHas('achievements', function($aq) use ($searchTerm) {
                            $aq->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                              ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm])
                              ->orWhereHas('skill', function($sq) use ($searchTerm) {
                                  $sq->whereRaw('LOWER(name) LIKE ?', [$searchTerm]);
                              });
                        });
                  })
                  ->orWhereHas('members.user.proches', function($pq) use ($searchTerm) {
                      $pq->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                        ->orWhereHas('achievements', function($aq) use ($searchTerm) {
                            $aq->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                              ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm])
                              ->orWhereHas('skill', function($sq) use ($searchTerm) {
                                  $sq->whereRaw('LOWER(name) LIKE ?', [$searchTerm]);
                              });
                        });
                  })
                   // 5. Search in Board Messages
                  ->orWhereHas('messages', function($mq) use ($searchTerm) {
                      $mq->whereRaw('LOWER(content) LIKE ?', [$searchTerm]);
                  })
                  // 6. Search in Projects (associated via Circle members)
                  ->orWhereHas('members.user.ownedProjects', function($pq) use ($searchTerm) {
                      $pq->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                        ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm])
                        ->orWhereRaw('LOWER(address) LIKE ?', [$searchTerm])
                        ->orWhereHas('skills', function($sq) use ($searchTerm) {
                            $sq->whereRaw('LOWER(name) LIKE ?', [$searchTerm]);
                        })
                        ->orWhereHas('allOffers', function($oq) use ($searchTerm) {
                            $oq->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                              ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm]);
                        });
                  })
                  ->orWhereHas('owner.ownedProjects', function($pq) use ($searchTerm) {
                    $pq->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                      ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm])
                      ->orWhereRaw('LOWER(address) LIKE ?', [$searchTerm])
                      ->orWhereHas('skills', function($sq) use ($searchTerm) {
                          $sq->whereRaw('LOWER(name) LIKE ?', [$searchTerm]);
                      })
                      ->orWhereHas('allOffers', function($oq) use ($searchTerm) {
                          $oq->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                            ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm]);
                      });
                });
            });
        }

        // 3. Proximity Logic
        if ($this->lat && $this->lng) {
            $circles->selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(JSON_EXTRACT(coordinates, '$.lat'))) * cos(radians(JSON_EXTRACT(coordinates, '$.lng')) - radians(?)) + sin(radians(?)) * sin(radians(JSON_EXTRACT(coordinates, '$.lat'))))) AS distance", [$this->lat, $this->lng, $this->lat])
                    ->orderBy('distance');
        } else {
            $circles->latest();
        }

        $results = $circles->take(10)->get();

        // 4. Transform results with Smart Matching Context (Case Insensitive)
        $formattedResults = $results->map(function($circle) {
            // Smart Distance Label
            if (!empty($circle->coordinates) && ($circle->coordinates['lat'] != 0 || $circle->coordinates['lng'] != 0)) {
                if (isset($circle->distance)) {
                    $dist = $circle->distance;
                    if ($dist < 1) {
                        $circle->smart_distance = "À " . round($dist * 1000) . "m";
                    } elseif ($dist < 2) {
                        $circle->smart_distance = "Tout proche";
                    } else {
                        $circle->smart_distance = round($dist, 1) . " km";
                    }
                    
                    // Same City Logic
                    if ($this->locationName && stripos(mb_strtolower($circle->address), mb_strtolower(explode(',', $this->locationName)[0])) !== false) {
                        $circle->smart_distance = "Même ville • " . $circle->smart_distance;
                    }
                }
            } else {
                $circle->smart_distance = "Remote / Dématérialisé";
            }

            if (empty($this->search)) return $circle;

            $search = mb_strtolower($this->search);
            
            // Priority 1: Circle Identity
            if (stripos(mb_strtolower($circle->name), $search) !== false) {
                return $circle->setAttribute('matching_context', "Cercle trouvé")
                              ->setAttribute('matched_object', null);
            }

            // Priority 2: Direct Circle Skills/Achievements
            $match = $circle->achievements->filter(fn($a) => 
                stripos(mb_strtolower($a->title), $search) !== false || 
                stripos(mb_strtolower($a->description), $search) !== false || 
                stripos(mb_strtolower($a->skill->name), $search) !== false
            )->first();

            if ($match) {
                return $circle->setAttribute('matching_context', "Activités & Preuves")
                              ->setAttribute('matched_object', [
                                  'type' => 'achievement',
                                  'name' => $match->skill->name ?? $match->title,
                                  'image' => null,
                                  'icon' => 'sparkles',
                                  'trustPath' => auth()->check() ? auth()->user()->getTrustPathTo($match) : []
                              ]);
            }

            // Priority 3: Owner Expertise
            $ownerMatch = $circle->owner->achievements->filter(fn($a) => 
                stripos(mb_strtolower($a->title), $search) !== false || 
                stripos(mb_strtolower($a->description), $search) !== false || 
                stripos(mb_strtolower($a->skill->name), $search) !== false
            )->first();
            
            if ($ownerMatch || stripos(mb_strtolower($circle->owner->bio), $search) !== false) {
                return $circle->setAttribute('matching_context', "Expertise Fondateur")
                              ->setAttribute('matched_object', [
                                  'type' => 'user',
                                  'id' => $circle->owner->id,
                                  'name' => $circle->owner->name,
                                  'image' => $circle->owner->avatar,
                                  'detail' => $ownerMatch ? ($ownerMatch->skill->name ?? $ownerMatch->title) : 'Profil vérifié',
                                  'trustPath' => auth()->check() ? auth()->user()->getTrustPathTo($ownerMatch ?? $circle->owner) : []
                              ]);
            }

            // Priority 4: Member Expertise
            foreach($circle->members as $member) {
                // Member Direct Match
                $memberMatch = $member->user->achievements->filter(fn($a) => 
                    stripos(mb_strtolower($a->title), $search) !== false || 
                    stripos(mb_strtolower($a->description), $search) !== false || 
                    stripos(mb_strtolower($a->skill->name), $search) !== false
                )->first();

                if ($memberMatch || stripos(mb_strtolower($member->user->bio), $search) !== false) {
                    return $circle->setAttribute('matching_context', "Expertise Membre")
                                  ->setAttribute('matched_object', [
                                      'type' => 'user',
                                      'id' => $member->user->id,
                                      'name' => $member->user->name,
                                      'image' => $member->user->avatar,
                                      'detail' => $memberMatch ? ($memberMatch->skill->name ?? $memberMatch->title) : 'Profil vérifié',
                                      'trustPath' => auth()->check() ? auth()->user()->getTrustPathTo($memberMatch ?? $member->user) : []
                                  ]);
                }

                // Member Proche Match
                foreach($member->user->proches as $proche) {
                    $procheMatch = $proche->achievements->filter(fn($a) => 
                        stripos(mb_strtolower($a->title), $search) !== false || 
                        stripos(mb_strtolower($a->description), $search) !== false || 
                        stripos(mb_strtolower($a->skill->name), $search) !== false
                    )->first();

                    if ($procheMatch || stripos(mb_strtolower($proche->name), $search) !== false) {
                        return $circle->setAttribute('matching_context', "Expertise Proche de Membre")
                                      ->setAttribute('matched_object', [
                                          'type' => 'user',
                                          'name' => $proche->name,
                                          'image' => null,
                                          'detail' => "(Géré par {$member->user->name})",
                                          'trustPath' => auth()->check() ? auth()->user()->getTrustPathTo($procheMatch ?? $proche) : []
                                      ]);
                    }
                }
            }

            // Priority 5: Owner Proche Match
            foreach($circle->owner->proches as $proche) {
                $procheMatch = $proche->achievements->filter(fn($a) => 
                    stripos(mb_strtolower($a->title), $search) !== false || 
                    stripos(mb_strtolower($a->description), $search) !== false || 
                    stripos(mb_strtolower($a->skill->name), $search) !== false
                )->first();

                if ($procheMatch || stripos(mb_strtolower($proche->name), $search) !== false) {
                    return $circle->setAttribute('matching_context', "Expertise Proche de Fondateur")
                                  ->setAttribute('matched_object', [
                                      'type' => 'user',
                                      'name' => $proche->name,
                                      'image' => null,
                                      'detail' => "(Géré par {$circle->owner->name})",
                                      'trustPath' => auth()->check() ? auth()->user()->getTrustPathTo($procheMatch ?? $proche) : []
                                  ]);
                }
            }

            // Priority 5: User Names
            if (stripos(mb_strtolower($circle->owner->name), $search) !== false) {
                return $circle->setAttribute('matching_context', "Propriétaire")
                              ->setAttribute('matched_object', [
                                  'type' => 'user',
                                  'id' => $circle->owner->id,
                                  'name' => $circle->owner->name,
                                  'image' => $circle->owner->avatar,
                                  'trustPath' => auth()->check() ? auth()->user()->getTrustPathTo($circle->owner) : []
                              ]);
            }
            
            $matchingMember = $circle->members->filter(fn($m) => stripos(mb_strtolower($m->user->name), $search) !== false)->first();
            if ($matchingMember) {
                return $circle->setAttribute('matching_context', "Membre actif")
                              ->setAttribute('matched_object', [
                                  'type' => 'user',
                                  'id' => $matchingMember->user->id,
                                  'name' => $matchingMember->user->name,
                                  'image' => $matchingMember->user->avatar,
                                  'trustPath' => auth()->check() ? auth()->user()->getTrustPathTo($matchingMember->user) : []
                              ]);
            }

            // Priority 6: Project Match
            $projectMatch = null;
            
            // Check owner projects
            $projectMatch = $circle->owner->ownedProjects->filter(fn($p) => 
                stripos(mb_strtolower($p->title), $search) !== false || 
                stripos(mb_strtolower($p->description), $search) !== false ||
                $p->skills->contains(fn($s) => stripos(mb_strtolower($s->name), $search) !== false) ||
                $p->allOffers->contains(fn($o) => stripos(mb_strtolower($o->title), $search) !== false || stripos(mb_strtolower($o->description), $search) !== false)
            )->first();

            if ($projectMatch) {
                return $circle->setAttribute('matching_context', "Projet du Fondateur")
                              ->setAttribute('matched_object', [
                                  'type' => 'project',
                                  'id' => $projectMatch->id,
                                  'name' => $projectMatch->title,
                                  'detail' => "Regardez ce projet !",
                                  'trustPath' => []
                              ]);
            }

            // Check member projects
            foreach($circle->members as $member) {
                $projectMatch = $member->user->ownedProjects->filter(fn($p) => 
                    stripos(mb_strtolower($p->title), $search) !== false || 
                    stripos(mb_strtolower($p->description), $search) !== false ||
                    $p->skills->contains(fn($s) => stripos(mb_strtolower($s->name), $search) !== false) ||
                    $p->allOffers->contains(fn($o) => stripos(mb_strtolower($o->title), $search) !== false || stripos(mb_strtolower($o->description), $search) !== false)
                )->first();

                if ($projectMatch) {
                    return $circle->setAttribute('matching_context', "Projet de Membre")
                                  ->setAttribute('matched_object', [
                                      'type' => 'project',
                                      'id' => $projectMatch->id,
                                      'name' => $projectMatch->title,
                                      'detail' => "(Par {$member->user->name})",
                                      'trustPath' => []
                                  ]);
                }
            }

            return $circle->setAttribute('matching_context', "Correspondance")
                          ->setAttribute('matched_object', null);
        });

        return view('livewire.home', [
            'circles' => $formattedResults,
            'achievements' => \App\Models\Achievement::with(['user', 'skill', 'circle'])->where('is_verified', true)->latest()->take(6)->get(),
        ]);
    }
}
