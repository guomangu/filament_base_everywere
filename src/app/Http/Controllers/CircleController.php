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

    public function create(Request $request): View
    {
        return view('circle.create');
    }

    public function store(CircleStoreRequest $request): RedirectResponse
    {
        $circle = Circle::create($request->validated());

        $request->session()->flash('circle.id', $circle->id);

        return redirect()->route('circles.index');
    }

    public function show(Request $request, Circle $circle): View
    {
        return view('circle.show', [
            'circle' => $circle,
        ]);
    }

    public function edit(Request $request, Circle $circle): View
    {
        return view('circle.edit', [
            'circle' => $circle,
        ]);
    }

    public function update(CircleUpdateRequest $request, Circle $circle): RedirectResponse
    {
        $circle->update($request->validated());

        $request->session()->flash('circle.id', $circle->id);

        return redirect()->route('circles.index');
    }

    public function destroy(Request $request, Circle $circle): RedirectResponse
    {
        $circle->delete();

        return redirect()->route('circles.index');
    }
}
