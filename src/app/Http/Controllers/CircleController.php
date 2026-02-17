<?php

namespace App\Http\Controllers;

use App\Http\Requests\CircleStoreRequest;
use App\Http\Requests\CircleUpdateRequest;
use App\Models\Circle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CircleController extends Controller
{
    public function index(Request $request): View
    {
        $circles = Circle::all();

        return view('circle.index', [
            'circles' => $circles,
        ]);
    }
}
