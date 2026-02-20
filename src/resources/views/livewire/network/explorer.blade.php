<div class="bg-white/40 backdrop-blur-3xl border border-white/60 rounded-[3rem] p-8 shadow-2xl shadow-blue-500/5">
    <div class="flex flex-col md:flex-row items-center justify-between mb-8 gap-4">
        <div>
            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight flex items-center gap-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Réseaux
            </h3>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">Exploration de proximité et de confiance</p>
        </div>

        <div class="flex items-center gap-4 flex-grow md:flex-grow-0">
            @if($origin instanceof \App\Models\User && auth()->id() === $origin->id)
                <a href="{{ route('circles.create') }}" class="shrink-0 px-4 py-3 bg-slate-900 border-2 border-slate-900 text-white rounded-2xl font-black text-[10px] tracking-widest uppercase hover:bg-blue-600 hover:border-blue-600 transition-all flex items-center justify-center gap-2 shadow-lg shadow-blue-500/10 group/circle">
                    <svg class="w-4 h-4 group-hover/circle:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 0012 20a10.003 10.003 0 006.235-2.197m-2.322-9.047a7.334 7.334 0 011.129 3.125m-1.282-3.125a10 10 0 11-14.703 0m14.703 0c-1.347-1.625-3.323-2.651-5.547-2.651-2.224 0-4.2 1.026-5.547 2.651"/></svg>
                    Nouveau Cercle
                </a>
            @endif

            <div class="relative w-full md:w-64">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher..." 
                    class="w-full bg-slate-100/50 border-none rounded-2xl py-3 pl-10 pr-4 text-xs font-bold placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 transition-all">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <div wire:loading wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2">
                    <div class="w-3 h-3 border-2 border-blue-600/20 border-t-blue-600 rounded-full animate-spin"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($results as $result)
            @php 
                $url = route('circles.show', $result);
                $avatar = $result->owner->avatar ?? '';
                $name = $result->name;
                $score = $result->average_trust_score;
                $activeMembers = $result->activeMembers->take(6);
            @endphp
            <div class="bg-white/80 border border-slate-100 rounded-[2.5rem] p-5 hover:shadow-xl hover:shadow-blue-500/5 transition-all group overflow-hidden relative flex flex-col h-full">
                <div class="flex items-center gap-4 mb-4">
                    <a href="{{ $url }}" class="relative shrink-0 group/av hover:scale-105 transition-transform duration-300">
                        <div class="w-14 h-14 bg-gradient-to-tr from-blue-600 to-indigo-700 rounded-2xl flex items-center justify-center text-white shadow-lg group-hover/av:scale-110 transition-transform duration-500">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if(($result->type ?? '') === 'business')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                @endif
                            </svg>
                        </div>
                        <div class="absolute -bottom-1 -right-1 px-1.5 py-0.5 bg-slate-900 rounded-lg border-2 border-white">
                            <span class="text-[7px] font-black text-white leading-none">{{ $score }}%</span>
                        </div>
                    </a>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span @class([
                                'text-[7px] font-black uppercase px-2 py-0.5 rounded-md tracking-tighter shadow-sm',
                                'bg-blue-600 text-white' => $result->proximity_level <= 2,
                                'bg-slate-200 text-slate-600' => $result->proximity_level > 2
                            ])>
                                {{ match($result->proximity_type) {
                                    'direct' => 'Direct',
                                    'city' => 'Ville',
                                    'region' => 'Région',
                                    'global' => 'Pays',
                                    'earth' => 'Monde',
                                    default => 'Réseau'
                                } }}
                            </span>
                            <span class="text-[7px] font-black text-slate-400 uppercase tracking-widest">Cercle</span>
                        </div>
                        <a href="{{ $url }}" class="text-sm font-black text-slate-900 uppercase truncate block hover:text-blue-600 transition-colors">
                            {{ $name }}
                        </a>
                    </div>
                </div>

                <!-- Membres du cercle -->
                <div class="mb-4">
                    <div class="flex flex-wrap gap-2">
                        @foreach($activeMembers as $member)
                            <a href="{{ route('users.show', $member->user) }}" class="flex items-center gap-2 bg-slate-50 border border-slate-100 pl-1 pr-3 py-1 rounded-xl group/m hover:bg-white hover:border-blue-500 transition-all shadow-sm">
                                <img src="{{ $member->user->avatar }}" class="w-6 h-6 rounded-lg object-cover group-hover/m:scale-110 transition-transform shadow-sm">
                                <span class="text-[9px] font-black text-slate-700 uppercase tracking-tight group-hover/m:text-blue-600 truncate max-w-[80px]">{{ $member->user->name }}</span>
                            </a>
                        @endforeach
                    </div>
                    @if($result->activeMembers->count() > 6)
                        <div class="mt-2 text-center">
                            <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest pl-1">+{{ $result->activeMembers->count() - 6 }} autres membres</span>
                        </div>
                    @endif
                </div>

                <div class="mb-4 flex-1">
                    @php 
                        $allSkills = $result->activeMembers->flatMap(fn($m) => $m->user->achievements)->map(fn($a) => $a->skill->name)->unique()->take(6);
                    @endphp
                    <div class="flex flex-wrap gap-1">
                        @foreach($allSkills as $skill)
                            <button wire:click="selectSkill('{{ $skill }}')" class="px-2 py-0.5 bg-blue-50 text-blue-600 rounded-lg text-[8px] font-black uppercase tracking-tight hover:bg-blue-600 hover:text-white transition-colors">
                                {{ $skill }}
                            </button>
                        @endforeach
                    </div>
                </div>

                @if(!empty($result->trustPath))
                    <div class="pt-4 border-t border-slate-50 relative z-10">
                        <x-user-trust-chain :path="$result->trustPath" class="scale-90 origin-left" />
                    </div>
                @endif
            </div>
        @empty
            <div class="md:col-span-2 py-12 text-center">
                <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Aucun résultat trouvé dans votre réseau</p>
            </div>
        @endforelse
    </div>
</div>
