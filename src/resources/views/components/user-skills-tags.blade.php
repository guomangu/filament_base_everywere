@props(['user', 'limit' => 5])

@php
    // Get direct achievements with unique skills
    $directSkills = $user->achievements
        ->filter(fn($a) => $a->skill !== null)
        ->unique('skill_id')
        ->map(fn($a) => [
            'id' => $a->skill->id,
            'name' => $a->skill->name,
            'type' => 'direct'
        ]);

    // Get proche achievements with unique skills
    $procheSkills = $user->proches->flatMap->achievements
        ->filter(fn($a) => $a->skill !== null)
        ->unique('skill_id')
        ->map(fn($a) => [
            'id' => $a->skill->id,
            'name' => $a->skill->name,
            'type' => 'proche'
        ]);

    $allSkills = $directSkills->merge($procheSkills)->unique('name')->take($limit);
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-wrap gap-1.5']) }}>
    @foreach($allSkills as $skill)
        <a href="{{ route('projects.index', ['selectedSkills' => [$skill['id']]]) }}" 
           @class([
               'px-2 py-1 rounded-lg text-[8px] font-black uppercase tracking-tight transition-all hover:scale-105 active:scale-95 border',
               'bg-slate-900/5 text-slate-600 border-slate-900/5 hover:bg-white hover:border-slate-200 shadow-sm' => $skill['type'] === 'direct',
               'bg-blue-50 text-blue-600 border-blue-100/50 hover:bg-white hover:border-blue-200' => $skill['type'] === 'proche'
           ])>
            {{ $skill['name'] }}
        </a>
    @endforeach
</div>
