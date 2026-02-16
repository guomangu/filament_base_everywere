<?php

use App\Livewire\Circle\Profile as CircleProfile;
use App\Livewire\Circle\Create as CircleCreate;
use App\Livewire\Circle\Edit as CircleEdit;
use App\Livewire\Achievement\Create as AchievementCreate;
use App\Livewire\User\Profile as UserProfile;
use App\Livewire\User\Edit as UserEdit;
use App\Livewire\Home;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class);

Route::middleware('auth')->group(function () {
    Route::get('/circles/create', CircleCreate::class)->name('circles.create');
    Route::get('/circles/{circle}/edit', CircleEdit::class)->name('circles.edit');
    Route::get('/achievements/create', AchievementCreate::class)->name('achievements.create');
    Route::get('/profile/edit', UserEdit::class)->name('profile.edit');
});

Route::get('/circles/{circle}', CircleProfile::class)->name('circles.show');
Route::get('/users/{user}', UserProfile::class)->name('users.show');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');
