<div class="min-h-screen bg-slate-50/50 pb-32">
    {{-- ===== HERO SEARCH SECTION ===== --}}
    <div class="relative pt-24 pb-16 px-6 overflow-hidden">
        <div class="absolute inset-0 -z-10 overflow-hidden">
            <div class="absolute top-[-10%] left-[-5%] w-[40%] aspect-square bg-blue-400/10 rounded-full blur-[120px] animate-pulse"></div>
            <div class="absolute bottom-[-10%] right-[-5%] w-[40%] aspect-square bg-purple-400/10 rounded-full blur-[120px] animate-pulse" style="animation-delay: 2s"></div>
        </div>

        <div class="max-w-5xl mx-auto text-center relative z-10">
            <h1 class="text-4xl sm:text-6xl md:text-7xl font-black text-slate-900 tracking-tighter mb-8 leading-[0.9]">
                RÉPERTOIRE DES <br/>
                <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent uppercase">PROJETS & TALENTS.</span>
            </h1>
            
            <div class="max-w-3xl mx-auto mt-12">
                <div class="relative p-2 bg-white/60 backdrop-blur-3xl rounded-[3rem] border border-white/60 shadow-2xl shadow-blue-500/10 transition-all duration-500 focus-within:shadow-blue-500/20 focus-within:border-blue-200">
                    <div class="relative flex items-center">
                        <div class="absolute left-6 text-blue-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" 
                            wire:model.live.debounce.300ms="search" 
                            placeholder="Rechercher un projet, une expertise..." 
                            class="w-full bg-transparent border-none focus:ring-0 text-xl md:text-2xl font-black placeholder:text-slate-300 py-6 md:py-8 pl-18 pr-8 text-slate-900">
                        
                        <div wire:loading wire:target="search" class="absolute right-8">
                            <div class="w-6 h-6 border-4 border-blue-600/30 border-t-blue-600 rounded-full animate-spin"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col lg:flex-row gap-12">
            {{-- ===== SIDEBAR FILTERS ===== --}}
            <aside class="lg:w-80 space-y-8">
                {{-- Status & Type Filters --}}
                <div class="bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[2.5rem] p-8 shadow-xl shadow-blue-500/5">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-6 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                        Filtres actifs
                    </h3>

                    <div class="space-y-6">
                        <div>
                            <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-3 block">Statut du projet</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button wire:click="$set('filterStatus', 'open')" @class(['px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all border', $filterStatus === 'open' ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-500/20' : 'bg-white text-slate-500 border-slate-100 hover:border-blue-200'])>
                                    Ouvert
                                </button>
                                <button wire:click="$set('filterStatus', 'all')" @class(['px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all border', $filterStatus === 'all' ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-500/20' : 'bg-white text-slate-500 border-slate-100 hover:border-blue-200'])>
                                    Tous
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-3 block">Type d'échange</label>
                            <div class="space-y-2">
                                <button wire:click="$set('filterType', 'all')" @class(['w-full px-4 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest text-left transition-all border flex items-center justify-between group', $filterType === 'all' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-500 border-slate-100 hover:border-blue-200'])>
                                    <span>Global</span>
                                    <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </button>
                                <button wire:click="$set('filterType', 'offers')" @class(['w-full px-4 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest text-left transition-all border flex items-center justify-between group', $filterType === 'offers' ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-500/20' : 'bg-white text-slate-500 border-slate-100 hover:border-blue-200'])>
                                    <span>Offres</span>
                                    <div class="w-2 h-2 rounded-full bg-blue-400"></div>
                                </button>
                                <button wire:click="$set('filterType', 'demands')" @class(['w-full px-4 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest text-left transition-all border flex items-center justify-between group', $filterType === 'demands' ? 'bg-purple-600 text-white border-purple-600 shadow-lg shadow-purple-500/20' : 'bg-white text-slate-500 border-slate-100 hover:border-purple-200'])>
                                    <span>Demandes</span>
                                    <div class="w-2 h-2 rounded-full bg-purple-400"></div>
                                </button>
                            </div>
                        </div>

                        @if($search || $filterType !== 'all' || $filterStatus !== 'open' || !empty($selectedSkills))
                            <button wire:click="clearFilters" class="w-full py-4 text-[9px] font-black text-red-500 uppercase tracking-widest hover:text-red-600 transition-colors">
                                Réinitialiser les filtres
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Skill Cloud Filter --}}
                <div class="bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[2.5rem] p-8 shadow-xl shadow-blue-500/5">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-6 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full"></span>
                        Compétences
                    </h3>

                    <div class="flex flex-wrap gap-2">
                        @foreach($skills as $skill)
                            <button 
                                wire:click="toggleSkill({{ $skill->id }})"
                                @class([
                                    'px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-tighter transition-all border',
                                    'bg-indigo-600 text-white border-indigo-600 shadow-lg shadow-indigo-500/20' => in_array($skill->id, $selectedSkills),
                                    'bg-white text-slate-500 border-slate-100 hover:border-indigo-200 hover:text-indigo-600' => !in_array($skill->id, $selectedSkills)
                                ])
                            >
                                {{ $skill->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </aside>

            {{-- ===== PROJECT GRID ===== --}}
            <main class="flex-grow">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-[11px] font-black text-slate-900 uppercase tracking-[0.3em]">
                        {{ $projects->total() }} PROJETS TROUVÉS
                    </h2>
                    
                    @auth
                        <a href="{{ route('projects.create') }}" class="flex items-center gap-3 px-6 py-3 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-900/10 group">
                            <span>Lancer un projet</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        </a>
                    @endauth
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($projects as $project)
                        <div class="group relative">
                            <div class="absolute -inset-2 bg-gradient-to-br from-blue-600/10 to-purple-600/10 rounded-[3rem] blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            
                            <div class="relative bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[2.5rem] p-8 shadow-xl shadow-blue-500/5 hover:shadow-2xl transition-all duration-500 flex flex-col h-full border-b-4 border-b-transparent hover:border-b-blue-600">
                                {{-- Card Link Overlay --}}
                                <a href="{{ route('projects.show', $project) }}" class="absolute inset-0 z-0"></a>

                                <div class="relative z-10 pointer-events-none flex-grow">
                                    <div class="flex items-start justify-between mb-6">
                                        <div @class(['px-3 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest border', $project->is_open ? 'bg-green-50 text-green-600 border-green-100' : 'bg-slate-50 text-slate-400 border-slate-100'])>
                                            <span class="flex items-center gap-1.5">
                                                <span @class(['w-1.5 h-1.5 rounded-full', $project->is_open ? 'bg-green-500 animate-pulse' : 'bg-slate-300'])></span>
                                                {{ $project->is_open ? 'Ouvert' : 'Fermé' }}
                                            </span>
                                        </div>
                                        <div class="flex -space-x-3">
                                            @foreach($project->activeMembers->take(3) as $member)
                                                <img src="{{ $member->memberable->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($member->memberable->name ?? '?') }}" class="w-8 h-8 rounded-xl border-4 border-white shadow-sm ring-1 ring-slate-100">
                                            @endforeach
                                            @if($project->activeMembers->count() > 3)
                                                <div class="w-8 h-8 rounded-xl bg-slate-50 border-4 border-white shadow-sm flex items-center justify-center text-[8px] font-black text-slate-400">+{{ $project->activeMembers->count() - 3 }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <h3 class="text-2xl font-black text-slate-900 tracking-tight leading-none mb-3 group-hover:text-blue-600 transition-colors">
                                        {{ $project->title }}
                                    </h3>
                                    
                                    <p class="text-[13px] text-slate-500 font-medium leading-relaxed line-clamp-2 mb-6 pointer-events-auto">
                                        {{ $project->description }}
                                    </p>

                                    {{-- Stats --}}
                                    <div class="grid grid-cols-3 gap-3 mb-6">
                                        <div class="bg-blue-50/50 rounded-2xl p-3 border border-blue-50">
                                            <div class="text-xs font-black text-blue-600 leading-none mb-1">{{ $project->offers->count() }}</div>
                                            <div class="text-[7px] font-black text-blue-400 uppercase tracking-widest">Offres</div>
                                        </div>
                                        <div class="bg-purple-50/50 rounded-2xl p-3 border border-purple-50">
                                            <div class="text-xs font-black text-purple-600 leading-none mb-1">{{ $project->demands->count() }}</div>
                                            <div class="text-[7px] font-black text-purple-400 uppercase tracking-widest">Demandes</div>
                                        </div>
                                        <div class="bg-slate-50/50 rounded-2xl p-3 border border-slate-50">
                                            <div class="text-xs font-black text-slate-900 leading-none mb-1">{{ $project->activeMembers->count() }}</div>
                                            <div class="text-[7px] font-black text-slate-400 uppercase tracking-widest">Noyau</div>
                                        </div>
                                    </div>

                                    {{-- Top Skills Tags --}}
                                    <div class="flex flex-wrap gap-1.5 mb-8">
                                        @foreach($project->allSkills()->take(4) as $skill)
                                            <span class="text-[8px] font-black text-slate-500 bg-slate-100/50 px-2 py-1 rounded-lg uppercase tracking-tight">
                                                {{ $skill->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="relative z-10 pt-6 border-t border-slate-50 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $project->owner->avatar }}" class="w-8 h-8 rounded-xl ring-2 ring-slate-100">
                                        <div>
                                            <div class="text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Fondateur</div>
                                            <div class="text-[10px] font-bold text-slate-900">{{ $project->owner->name }}</div>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-slate-300 group-hover:text-blue-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5-5 5M6 7l5 5-5 5"/></svg>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-20 text-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6 text-slate-300">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <h3 class="text-2xl font-black text-slate-900 mb-2">Aucun projet</h3>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Ajustez vos filtres pour explorer davantage.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-12">
                    {{ $projects->links() }}
                </div>
            </main>
        </div>
    </div>
</div>
