<?php

namespace App\Livewire\Achievement;

use App\Models\Achievement;
use Livewire\Component;

class Show extends Component
{
    public Achievement $achievement;

    public function mount(Achievement $achievement)
    {
        $this->achievement = $achievement->load(['user', 'skill', 'proche', 'validations.user', 'informations']);
        
        $this->dispatchContext();
    }

    public function dispatchContext()
    {
        $items = collect([
            ['type' => 'achievement', 'id' => $this->achievement->id, 'name' => $this->achievement->title],
            ['type' => 'user', 'id' => $this->achievement->user_id, 'name' => $this->achievement->user->name],
            ['type' => 'skill', 'id' => $this->achievement->skill_id, 'name' => $this->achievement->skill->name ?? 'Expertise'],
        ]);

        if ($this->achievement->proche_id) {
            $items->push(['type' => 'user', 'id' => $this->achievement->proche->id, 'name' => $this->achievement->proche->name]);
        }

        $this->dispatch('updateMessagingContext', items: $items->unique(fn($o) => $o['type'].$o['id'])->values()->toArray())->to(\App\Livewire\GlobalMessaging::class);
    }

    public function render()
    {
        $breadcrumbUser = $this->achievement->user ?? ($this->achievement->proche ? $this->achievement->proche->parent : null);

        return view('livewire.achievement.show')->layoutData([
            'title' => 'Preuve : ' . $this->achievement->title . ' | TrustCircle',
            'description' => \Illuminate\Support\Str::limit($this->achievement->description, 160),
            'breadcrumbCircle' => $breadcrumbUser ? $breadcrumbUser->activeJoinedCircles->first() : null,
            'breadcrumbUser' => $breadcrumbUser,
            'breadcrumbSkill' => $this->achievement->skill,
            'breadcrumbAchievement' => $this->achievement,
        ]);
    }
}
