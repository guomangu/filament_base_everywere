<?php

namespace App\Livewire\Circle;

use Livewire\Component;

class Edit extends Component
{
    public \App\Models\Circle $circle;
    public $name;
    public $description;
    public $address;
    public $type;
    public $is_public;

    protected $rules = [
        'address' => 'required|string|max:255',
        'type' => 'required|in:business,event,place,project',
        'is_public' => 'boolean',
    ];

    public function mount(\App\Models\Circle $circle)
    {
        if ($circle->owner_id !== auth()->id()) {
            abort(403);
        }

        $this->circle = $circle;
        $this->name = $circle->name;
        $this->description = $circle->description;
        $this->address = $circle->address;
        $this->type = $circle->type;
        $this->is_public = (bool)$circle->is_public;
    }

    public function update()
    {
        $this->validate();

        $this->circle->update([
            'type' => $this->type,
            'address' => $this->address,
            'is_public' => $this->is_public,
        ]);

        return redirect()->route('circles.show', $this->circle);
    }

    public function render()
    {
        return view('livewire.circle.edit');
    }
}
