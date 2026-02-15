<?php

namespace App\Livewire;

use Livewire\Component;

class Feed extends Component
{
    public function render()
    {
        return view('livewire.feed', [
            'posts' => \App\Models\Post::with(['user', 'comments', 'reactions'])->latest()->paginate(10),
        ]);
    }
}
