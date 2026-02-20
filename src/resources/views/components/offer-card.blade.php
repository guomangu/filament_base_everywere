@props([
    'offer',
    'project' => null,
    'canManage' => false,
    'showProjectLink' => false,
    'quoteAction' => true,
    'reviewAction' => true,
])

<div wire:key="offer-card-{{ $offer->id }}" class="bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[3rem] overflow-hidden shadow-xl shadow-blue-500/5 hover:shadow-2xl hover:shadow-blue-500/10 hover:translate-y-[-4px] transition-all group/item flex flex-col h-full">
    {{-- Photo Gallery --}}
    <div class="relative h-44 md:h-64 bg-slate-100 overflow-hidden shrink-0">
        @if($offer->images && count($offer->images) > 0)
            <div class="flex overflow-x-auto snap-x snap-mandatory h-full no-scrollbar">
                @foreach($offer->images as $img)
                    <div class="min-w-full h-full snap-start">
                        <img src="{{ Storage::url($img) }}" class="w-full h-full object-cover">
                    </div>
                @endforeach
            </div>
            @if(count($offer->images) > 1)
                <div class="absolute bottom-3 right-3 px-3 py-1.5 bg-black/60 backdrop-blur-md rounded-full text-[8px] font-black text-white uppercase tracking-widest pointer-events-none border border-white/10">
                    {{ count($offer->images) }} Photos
                </div>
            @endif
        @else
            <div class="w-full h-full flex items-center justify-center bg-slate-50 text-slate-200">
                <svg class="w-12 h-12 md:w-16 md:h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        @endif

        @if($canManage)
            <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover/item:opacity-100 transition-opacity z-30">
                <button wire:click.stop="editOffer({{ $offer->id }})" class="p-3 bg-white/90 backdrop-blur shadow-lg rounded-xl text-blue-600 hover:bg-blue-600 hover:text-white transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <button wire:click.stop="deleteOffer({{ $offer->id }})" wire:confirm="Supprimer cette offre ?" class="p-3 bg-white/90 backdrop-blur shadow-lg rounded-xl text-red-600 hover:bg-red-600 hover:text-white transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        @endif
        
        @if($quoteAction)
            <div wire:click="setQuoteOffer({{ $offer->id }})" class="absolute inset-0 z-10 cursor-pointer group-hover/item:bg-blue-600/5 transition-colors"></div>
        @endif
    </div>

    <div class="p-6 md:p-8 flex flex-col flex-grow relative">
        @if($showProjectLink && $offer->project)
            <a href="{{ route('projects.show', $offer->project) }}" class="relative z-20 text-[8px] md:text-[9px] font-black text-blue-500 uppercase tracking-[0.2em] mb-3 hover:underline">
                {{ $offer->project->title }}
            </a>
        @endif

        <h3 class="text-sm md:text-lg font-black text-slate-900 uppercase tracking-tight mb-3 group-hover/item:text-blue-600 transition-colors line-clamp-2">
            {{ $offer->title }}
        </h3>
        
        @if($offer->description)
            <p class="text-[10px] md:text-sm text-slate-500 font-medium leading-relaxed mb-6 line-clamp-2 italic">
                "{{ $offer->description }}"
            </p>
        @endif

        <div class="mt-auto">
            @if($offer->informations->count() > 0)
                <div class="pt-4 border-t border-slate-100 flex flex-wrap gap-2 mb-6">
                    @foreach($offer->informations as $info)
                        <div class="flex items-center gap-1.5 bg-blue-50/50 border border-blue-100/50 px-2.5 py-1 rounded-lg text-[8px] md:text-[9px] font-black uppercase tracking-widest shadow-sm shadow-blue-500/5">
                            @if($info->label)
                                <span class="text-blue-400 italic opacity-60">{{ $info->label }}:</span>
                            @endif
                            <span class="text-blue-600">{{ $info->title }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($reviewAction)
                <div class="flex gap-2 relative z-30">
                    <button wire:click.stop="setReviewOffer({{ $offer->id }})" class="flex-grow py-3 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl font-black text-[9px] md:text-[10px] uppercase tracking-widest transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span>Avis ({{ $offer->reviews->count() }})</span>
                    </button>
                    <button wire:click.stop="setReviewOffer({{ $offer->id }}, true)" class="px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-black text-[9px] md:text-[10px] uppercase tracking-widest transition-colors flex items-center justify-center gap-2 shadow-lg shadow-blue-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        <span>Évaluer</span>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
