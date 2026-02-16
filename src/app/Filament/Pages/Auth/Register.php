<?php
namespace App\Filament\Pages\Auth;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Facades\Filament;
use Filament\Events\Auth\Registered;
class Register extends BaseRegister
{
    public function register(): ?RegistrationResponse
    {
        $data = $this->form->getState();
        $user = $this->getUserModel()::create($data);
        event(new Registered($user));
        Filament::auth()->login($user);
        session()->regenerate();
        return app(RegistrationResponse::class);
    }
}
