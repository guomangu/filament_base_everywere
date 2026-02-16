<?php

namespace App\Http\Controllers;

use App\Http\Requests\AchievementStoreRequest;
use App\Http\Requests\AchievementUpdateRequest;
use App\Models\Achievement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AchievementController extends Controller
{
    public function index(Request $request): View
    {
        $achievements = Achievement::all();

        return view('achievement.index', [
            'achievements' => $achievements,
        ]);
    }

    public function create(Request $request): View
    {
        return view('achievement.create');
    }

    public function store(AchievementStoreRequest $request): RedirectResponse
    {
        $achievement = Achievement::create($request->validated());

        $request->session()->flash('achievement.id', $achievement->id);

        return redirect()->route('achievements.index');
    }

    public function show(Request $request, Achievement $achievement): View
    {
        return view('achievement.show', [
            'achievement' => $achievement,
        ]);
    }

    public function edit(Request $request, Achievement $achievement): View
    {
        return view('achievement.edit', [
            'achievement' => $achievement,
        ]);
    }

    public function update(AchievementUpdateRequest $request, Achievement $achievement): RedirectResponse
    {
        $achievement->update($request->validated());

        $request->session()->flash('achievement.id', $achievement->id);

        return redirect()->route('achievements.index');
    }

    public function destroy(Request $request, Achievement $achievement): RedirectResponse
    {
        $achievement->delete();

        return redirect()->route('achievements.index');
    }
}
