<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            \Filament\Http\Responses\Auth\Contracts\LoginResponse::class,
            \App\Http\Responses\LoginResponse::class
        );
        $this->app->singleton(
            \Filament\Http\Responses\Auth\Contracts\RegistrationResponse::class,
            \App\Http\Responses\RegistrationResponse::class
        );
    }

    public function boot(): void
    {
        \Livewire\Livewire::component('app.filament.pages.auth.login', \App\Filament\Pages\Auth\Login::class);
        \Livewire\Livewire::component('app.filament.pages.auth.register', \App\Filament\Pages\Auth\Register::class);

        if (class_exists(\App\Models\CircleMember::class)) {
            \App\Models\CircleMember::observe(\App\Observers\CircleMemberObserver::class);
        }

        if (class_exists(\App\Models\Circle::class)) {
            \App\Models\Circle::observe(\App\Observers\CircleObserver::class);
        }
    }
}
