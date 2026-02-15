<?php

namespace App\Livewire;

use Livewire\Component;

class ReactionPicker extends Component
{
    public \App\Models\Post $post;
    public $types;

    public function mount(\App\Models\Post $post)
    {
        $this->post = $post;
        $this->types = \App\Models\ReactionType::all();
    }

    public function toggle($type)
    {
        $existing = $this->post->reactions()
            ->where('user_id', auth()->id())
            ->where('type', $type)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            $this->post->reactions()->create([
                'user_id' => auth()->id(),
                'type' => $type,
            ]);
        }

        $this->dispatch('reaction-updated');
    }

    public function render()
    {
        return view('livewire.reaction-picker');
    }
}
