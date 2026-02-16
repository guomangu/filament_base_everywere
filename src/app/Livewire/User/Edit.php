<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public $name;
    public $bio;
    public $avatar;
    public $avatar_url;

    protected $rules = [
        'name' => 'required|string|max:255',
        'bio' => 'nullable|string|max:1000',
        'avatar' => 'nullable|image|max:1024',
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

        $data = [
            'name' => $this->name,
            'bio' => $this->bio,
        ];

        if ($this->avatar) {
            $path = $this->avatar->store('avatars', 'public');
            $data['avatar_url'] = '/storage/' . $path;
        }

        auth()->user()->update($data);

        return redirect()->route('users.show', auth()->user());
    }

    public function render()
    {
        return view('livewire.user.edit');
    }
}
