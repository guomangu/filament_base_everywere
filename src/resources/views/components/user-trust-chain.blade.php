@props(['path' => []])

@if(count($path) > 0)
    <div class="flex items-center gap-2 overflow-x-auto py-2 no-scrollbar">
        @foreach($path as $index => $node)
            <div class="flex items-center gap-2 shrink-0">
                @if($node['type'] === 'user')
                    <div class="flex items-center gap-2 bg-white/40 backdrop-blur-md border border-white/60 px-3 py-1.5 rounded-xl shadow-sm">
                        <img src="{{ $node['avatar'] }}" class="w-5 h-5 rounded-full object-cover border border-white shadow-inner">
                        <span class="text-[9px] font-black uppercase tracking-tight text-slate-900">{{ $node['name'] }}</span>
                    </div>
                @else
                    <div class="flex flex-col items-center bg-blue-600/10 border border-blue-200/50 px-3 py-1 rounded-xl">
                        <span class="text-[7px] font-black uppercase tracking-widest text-blue-600 leading-none mb-0.5">Cercle</span>
                        <span class="text-[8px] font-bold text-blue-900 truncate max-w-[80px]">{{ $node['name'] }}</span>
                    </div>
                @endif

                @if(!$loop->last)
                    <div class="text-slate-300">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif
