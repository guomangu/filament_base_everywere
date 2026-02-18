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
    
    // Project routes
    Route::get('/projects', \App\Livewire\Project\Index::class)->name('projects.index');
    Route::get('/projects/create', \App\Livewire\Project\Create::class)->name('projects.create');
    Route::get('/projects/{project}', \App\Livewire\Project\Show::class)->name('projects.show');
});

Route::get('/proches/claim/{token}', \App\Livewire\User\Claim::class)->name('proches.claim');

Route::get('/circles/{circle}', CircleProfile::class)->name('circles.show');
Route::get('/users/{user}', UserProfile::class)->name('users.show');

Route::get('/cv/u/{user}', \App\Livewire\Cv\Viewer::class)->name('cv.user');
Route::get('/cv/c/{circle}', \App\Livewire\Cv\Viewer::class)->name('cv.circle');
Route::get('/cv/p/{project}', \App\Livewire\Cv\Viewer::class)->name('cv.project');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');


Route::resource('circles', App\Http\Controllers\CircleController::class)->except(['create', 'show', 'edit']);

Route::resource('achievements', App\Http\Controllers\AchievementController::class)->except(['create', 'show', 'edit']);
