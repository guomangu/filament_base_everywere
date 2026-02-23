@props([
    'achievement',
])

@php
    $valCount = $achievement->validations->where('type', 'validate')->count();
    $rejCount = $achievement->validations->where('type', 'reject')->count();
    $score = $valCount - $rejCount;
@endphp

<button wire:click="openValidationModal({{ $achievement->id }})" class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-100 rounded-xl hover:border-blue-200 transition-all group/vcount pointer-events-auto relative z-30">
    <span @class([
        'text-[10px] font-black',
        'text-green-600' => $score > 0,
        'text-red-600' => $score < 0,
        'text-slate-400' => $score === 0
    ])>{{ $score > 0 ? '+' : '' }}{{ $score }}</span>
    <div class="flex -space-x-2">
        @foreach($achievement->validations->take(3) as $v)
            <div class="hover:z-20 transition-transform hover:scale-125 pointer-events-auto">
                <img src="{{ $v->user->avatar }}" class="w-4 h-4 rounded-full border border-white shadow-sm">
            </div>
        @endforeach
    </div>
</button>
