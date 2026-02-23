@props([
    'skillName',
    'achievements',
    'canEdit' => false,
])

@php
    $skill = \App\Models\Skill::where('name', $skillName)->first();
    $realizedDates = $achievements->filter(fn($i) => $i['type'] === 'achievement' && $i['model']->title !== '__SKELETON__')
        ->map(fn($i) => $i['model']->realized_at)
        ->filter();
    $minYear = $realizedDates->count() ? $realizedDates->min()->format('Y') : null;
    $maxYear = $realizedDates->count() ? $realizedDates->max()->format('Y') : null;
    
    $firstItem = $achievements->where('type', 'achievement')->first(); 
    $procheIdForBtn = $firstItem ? $firstItem['model']->proche_id : null;
    $proofCount = $achievements->filter(fn($i) => $i['type'] === 'achievement' && $i['model']->title !== '__SKELETON__')->count();
@endphp

<div class="flex flex-wrap items-center gap-4 md:gap-6 mb-8">
    @if($skill)
        <a href="{{ route('mission.show', $skill) }}" class="w-12 h-12 md:w-16 md:h-16 bg-slate-900 rounded-2xl md:rounded-[1.5rem] flex items-center justify-center text-white shadow-xl rotate-3 shrink-0 hover:bg-blue-600 transition-all hover:rotate-6">
            <span class="text-lg md:text-xl font-black uppercase">{{ substr($skillName, 0, 1) }}</span>
        </a>
    @else
        <div class="w-12 h-12 md:w-16 md:h-16 bg-slate-900 rounded-2xl md:rounded-[1.5rem] flex items-center justify-center text-white shadow-xl rotate-3 shrink-0">
            <span class="text-lg md:text-xl font-black uppercase">{{ substr($skillName, 0, 1) }}</span>
        </div>
    @endif

    <div class="flex-grow min-w-0">
        <div class="flex items-center gap-3 md:gap-4 mb-1 md:mb-2">
            @if($skill)
                <a href="{{ route('mission.show', $skill) }}" class="group/title">
                    <h3 class="text-xl md:text-2xl font-black text-slate-900 leading-none uppercase tracking-tight truncate group-hover/title:text-blue-600 transition-colors">{{ $skillName }}</h3>
                </a>
            @else
                <h3 class="text-xl md:text-2xl font-black text-slate-900 leading-none uppercase tracking-tight truncate">{{ $skillName }}</h3>
            @endif

            @if($canEdit)
                <button type="button" wire:click="addProofForSkill('{{ addslashes($skillName) }}', {{ $procheIdForBtn ?? 'null' }})" class="px-3 py-1.5 md:px-4 md:py-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl text-[9px] md:text-[10px] font-black uppercase tracking-widest transition-all shadow-sm flex items-center gap-2 shrink-0">
                    <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                    Réalisation
                </button>
            @endif
        </div>

        <div class="flex items-center gap-2">
            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
            <span class="text-[8px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest truncate">
                {{ $proofCount }} Preuve(s)
                @if($minYear && $maxYear)
                    • {{ $minYear === $maxYear ? $minYear : "{$minYear} - {$maxYear}" }}
                @endif
            </span>
        </div>
    </div>
</div>
