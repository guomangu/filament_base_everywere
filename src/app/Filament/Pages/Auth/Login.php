<?php
namespace App\Filament\Pages\Auth;
use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Facades\Filament;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (\Illuminate\Auth\AuthenticationException $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();
            return null;
        }
        $data = $this->form->getState();
        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }
        $user = Filament::auth()->user();
        // SKIPPING canAccessPanel check here because we want to redirect regular users to their profile.
        // Redirection is handled by the LoginResponse singleton.
        session()->regenerate();
        return app(LoginResponse::class);
    }
}
