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
}
