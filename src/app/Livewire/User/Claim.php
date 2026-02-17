<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class Claim extends Component
{
    public string $token;
    public string $code = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    
    public ?\App\Models\Proche $proche = null;
    public bool $verified = false;

    public function mount(string $token)
    {
        $this->token = $token;
        $this->proche = \App\Models\Proche::where('transfer_token', $token)->first();

        if (!$this->proche) {
            abort(404, 'Lien de transfert invalide ou expiré.');
        }
    }

    public function verifyCode()
    {
        $this->validate([
            'code' => 'required|string|min:6|max:6',
        ]);

        if (strtoupper($this->code) === $this->proche->transfer_code) {
            $this->verified = true;
        } else {
            $this->addError('code', 'Le code de sécurité est incorrect.');
        }
    }

    public function claimAccount()
    {
        $this->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $this->proche->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'parent_id' => $this->proche->parent_id,
        ]);

        // Transfer achievements
        \App\Models\Achievement::where('proche_id', $this->proche->id)
            ->update([
                'user_id' => $user->id,
                'proche_id' => null,
            ]);

        // Delete Proche
        $this->proche->delete();

        Auth::login($user);

        return redirect()->route('users.show', $user)->with('notify', [
            'message' => 'Compte récupéré avec succès ! Bienvenue.',
            'type' => 'success'
        ]);
    }

    public function render()
    {
        return view('livewire.user.claim')->layout('components.layouts.app');
    }
}
