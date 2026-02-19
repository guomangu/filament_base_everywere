@props(['path' => []])

@if(count($path) > 0)
    @php
        $nodes = collect($path);
        $results = [];
        $lastNodeKey = null;

        foreach ($nodes as $node) {
            $isVous = ($node['type'] === 'user' && ($node['name'] === 'Vous' || ($node['id'] ?? 0) === auth()->id()));
            $isOtherUser = ($node['type'] === 'user' && !$isVous);

            if ($isOtherUser) continue;

            $nodeKey = $node['type'] . '_' . ($node['id'] ?? $node['name']);
            if ($nodeKey === $lastNodeKey) continue;
            
            $results[] = $node;
            $lastNodeKey = $nodeKey;
        }
        $filteredPath = collect($results);
    @endphp

    <div class="flex items-center gap-2 overflow-x-auto py-2 no-scrollbar">
        @foreach($filteredPath as $index => $node)
            <div class="flex items-center gap-2 shrink-0">
                @if($node['type'] === 'user')
                    <a href="#" class="flex items-center gap-2 bg-white/40 backdrop-blur-md border border-white/60 px-3 py-1.5 rounded-xl shadow-sm hover:bg-white hover:border-blue-500 transition-all group">
                        <img src="{{ $node['avatar'] }}" class="w-5 h-5 rounded-full object-cover border border-white shadow-inner group-hover:scale-110 transition-transform">
                        <span class="text-[9px] font-black uppercase tracking-tight text-slate-900 group-hover:text-blue-600">{{ $node['name'] }}</span>
                    </a>
                @elseif($node['type'] === 'circle')
                    <a href="{{ route('circles.show', $node['id']) }}" class="flex flex-col items-center bg-blue-600/10 border border-blue-200/50 px-3 py-1 rounded-xl hover:bg-white hover:border-blue-500 transition-all group">
                        <span class="text-[7px] font-black uppercase tracking-widest text-blue-600 leading-none mb-0.5">Cercle</span>
                        <span class="text-[8px] font-bold text-blue-900 truncate max-w-[80px] group-hover:text-blue-600">{{ $node['name'] }}</span>
                    </a>
                @elseif($node['type'] === 'proche')
                    <a href="{{ route('users.show', $node['user_id']) }}" class="flex flex-col items-center bg-indigo-600/10 border border-indigo-200/50 px-3 py-1 rounded-xl hover:bg-white hover:border-indigo-500 transition-all group">
                        <span class="text-[7px] font-black uppercase tracking-widest text-indigo-500 leading-none mb-0.5">Proche</span>
                        <span class="text-[8px] font-bold text-indigo-900 truncate max-w-[80px] group-hover:text-indigo-600">{{ $node['name'] }}</span>
                    </a>
                @elseif($node['type'] === 'skill')
                    <a href="{{ route('users.show', $node['user_id']) }}" class="flex flex-col items-center bg-amber-600/10 border border-amber-200/50 px-3 py-1 rounded-xl hover:bg-white hover:border-amber-500 transition-all group">
                        <span class="text-[7px] font-black uppercase tracking-widest text-amber-500 leading-none mb-0.5">Compétence</span>
                        <span class="text-[8px] font-bold text-amber-900 truncate max-w-[80px] group-hover:text-amber-600">{{ $node['name'] }}</span>
                    </a>
                @elseif($node['type'] === 'achievement')
                    <a href="{{ route('users.show', $node['user_id']) }}" class="flex flex-col items-center bg-emerald-600/10 border border-emerald-200/50 px-3 py-1 rounded-xl hover:bg-white hover:border-emerald-500 transition-all group">
                        <span class="text-[7px] font-black uppercase tracking-widest text-emerald-500 leading-none mb-0.5">Preuve</span>
                        <span class="text-[8px] font-bold text-emerald-900 truncate max-w-[80px] italic group-hover:text-emerald-600">"{{ $node['name'] }}"</span>
                    </a>
                @elseif($node['type'] === 'earth')
                    <div class="flex flex-col items-center bg-blue-900 border border-blue-700 px-3 py-1 rounded-xl shadow-lg animate-pulse group">
                        <span class="text-[7px] font-black uppercase tracking-widest text-blue-200 leading-none mb-0.5">Global</span>
                        <div class="flex items-center gap-1">
                            <svg class="w-2.5 h-2.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                            <span class="text-[8px] font-black text-white truncate max-w-[80px]">{{ $node['name'] }}</span>
                        </div>
                    </div>
                @elseif($node['type'] === 'region')
                    <div class="flex flex-col items-center bg-orange-600/10 border border-orange-200/50 px-3 py-1 rounded-xl group">
                        <span class="text-[7px] font-black uppercase tracking-widest text-orange-500 leading-none mb-0.5">Région</span>
                        <span class="text-[8px] font-bold text-orange-900 truncate max-w-[80px]">{{ $node['name'] }}</span>
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
