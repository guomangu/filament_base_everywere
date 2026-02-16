<?php

namespace App\Livewire;

use Livewire\Component;

class Home extends Component
{
    public $search = '';
    public $location = '';

    public function render()
    {
        $query = \App\Models\Circle::with(['owner', 'members']);

        // Trust Chain Logic: Public circles OR private circles where user has mutual members
        $query->where(function($q) {
            $q->where('is_public', true);
            
            if (auth()->check()) {
                $myCircleIds = auth()->user()->joinedCircles()->pluck('circles.id')->toArray();
                $q->orWhereHas('members', function($mq) use ($myCircleIds) {
                    $mq->whereIn('circle_id', $myCircleIds);
                });
                // Also show if I am the owner
                $q->orWhere('owner_id', auth()->id());
            }
        });

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('messages', function($mq) {
                      $mq->where('content', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('members.user.achievements.skill', function($sq) {
                      $sq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if (!empty($this->location)) {
            $query->where('address', 'like', '%' . $this->location . '%');
        }

        return view('livewire.home', [
            'circles' => $query->latest()->take(12)->get(),
            'achievements' => \App\Models\Achievement::with(['user', 'skill', 'circle'])->where('is_verified', true)->latest()->take(6)->get(),
        ]);
    }
}
