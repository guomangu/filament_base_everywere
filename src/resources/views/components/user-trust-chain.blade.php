@props(['path' => []])

@if(count($path) > 0)
    <div class="flex items-center gap-2 overflow-x-auto py-2 no-scrollbar">
        @foreach($path as $index => $node)
            <div class="flex items-center gap-2 shrink-0">
                @if($node['type'] === 'user')
                    <a href="{{ $node['name'] === 'Vous' ? '#' : route('users.show', $node['id']) }}" class="flex items-center gap-2 bg-white/40 backdrop-blur-md border border-white/60 px-3 py-1.5 rounded-xl shadow-sm hover:bg-white hover:border-blue-500 transition-all group">
                        <img src="{{ $node['avatar'] }}" class="w-5 h-5 rounded-full object-cover border border-white shadow-inner group-hover:scale-110 transition-transform">
                        <span class="text-[9px] font-black uppercase tracking-tight text-slate-900 group-hover:text-blue-600">{{ $node['name'] }}</span>
                    </a>
                @else
                    <a href="{{ route('circles.show', $node['id']) }}" class="flex flex-col items-center bg-blue-600/10 border border-blue-200/50 px-3 py-1 rounded-xl hover:bg-white hover:border-blue-500 transition-all group">
                        <span class="text-[7px] font-black uppercase tracking-widest text-blue-600 leading-none mb-0.5">Cercle</span>
                        <span class="text-[8px] font-bold text-blue-900 truncate max-w-[80px] group-hover:text-blue-600">{{ $node['name'] }}</span>
                    </a>
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
