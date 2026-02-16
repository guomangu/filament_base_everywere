<?php

namespace App\Livewire\User;

use Livewire\Component;

class Edit extends Component
{
    public $name;
    public $bio;
    public $avatar_url;

    protected $rules = [
        'name' => 'required|string|max:255',
        'bio' => 'nullable|string|max:1000',
        'avatar_url' => 'nullable|url',
    ];

    public function mount()
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->bio = $user->bio;
        $this->avatar_url = $user->avatar_url;
    }

    public function update()
    {
        $this->validate();

        auth()->user()->update([
            'name' => $this->name,
            'bio' => $this->bio,
            'avatar_url' => $this->avatar_url,
        ]);

        return redirect()->route('users.show', auth()->user());
    }

    public function render()
    {
        return view('livewire.user.edit');
    }
}
