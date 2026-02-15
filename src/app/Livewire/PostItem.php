<?php

namespace App\Livewire;

use Livewire\Component;

class PostItem extends Component
{
    public \App\Models\Post $post;

    public function mount(\App\Models\Post $post)
    {
        $this->post = $post;
    }

    public function render()
    {
        return view('livewire.post-item');
    }
}
