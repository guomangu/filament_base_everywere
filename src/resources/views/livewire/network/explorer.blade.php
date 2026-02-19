<div class="bg-white/40 backdrop-blur-3xl border border-white/60 rounded-[3rem] p-8 shadow-2xl shadow-blue-500/5">
    <div class="flex flex-col md:flex-row items-center justify-between mb-8 gap-4">
        <div>
            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight flex items-center gap-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Réseaux
            </h3>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">Exploration de proximité et de confiance</p>
        </div>

        <div class="relative w-full md:w-64">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher une compétence..." 
                class="w-full bg-slate-100/50 border-none rounded-2xl py-3 pl-10 pr-4 text-xs font-bold placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 transition-all">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <div wire:loading wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2">
                <div class="w-3 h-3 border-2 border-blue-600/20 border-t-blue-600 rounded-full animate-spin"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($results as $result)
            @php 
                $isUser = $result instanceof \App\Models\User;
                $url = $isUser ? route('users.show', $result) : route('circles.show', $result);
                $avatar = $isUser ? $result->avatar : ($result->owner->avatar ?? '');
                $name = $result->name;
                $score = $isUser ? $result->trust_score : $result->average_trust_score;
                $typeLabel = $isUser ? 'Expert' : 'Cercle';
            @endphp
            <div class="bg-white/80 border border-slate-100 rounded-[2.5rem] p-4 hover:shadow-xl hover:shadow-blue-500/5 transition-all group overflow-hidden relative">
                <div class="flex items-center gap-4 mb-4">
                    <a href="{{ $url }}" class="relative shrink-0 group/av hover:scale-105 transition-transform duration-300">
                        <img src="{{ $avatar }}" class="w-12 h-12 rounded-2xl object-cover ring-2 ring-slate-50 group-hover:ring-blue-500/20 transition-all">
                        <div class="absolute -bottom-1 -right-1 px-1.5 py-0.5 bg-slate-900 rounded-lg border-2 border-white">
                            <span class="text-[7px] font-black text-white leading-none">{{ $score }}</span>
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
                                    'contact' => 'Contact',
                                    'city' => 'Ville',
                                    'region' => 'Région',
                                    'global' => 'Pays',
                                    default => 'Réseau'
                                } }}
                            </span>
                            <span class="text-[7px] font-black text-slate-400 uppercase tracking-widest">{{ $typeLabel }}</span>
                        </div>
                        <a href="{{ $url }}" class="text-xs font-black text-slate-900 uppercase truncate block hover:text-blue-600 transition-colors">
                            {{ $name }}
                        </a>
                    </div>
                </div>

                <div class="mb-4">
                    @php 
                        $achievements = $result->achievements ?? collect();
                        if (!$isUser && $result->owner) {
                            $achievements = $result->owner->achievements;
                        }
                        $skills = collect($achievements)->pluck('skill.name')->unique()->take(3);
                    @endphp
                    <div class="flex flex-wrap gap-1">
                        @foreach($skills as $skill)
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
