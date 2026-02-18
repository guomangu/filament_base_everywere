<div 
    x-data="projectShowData(@entangle('activeTab'))"
    x-on:project-updated.window="showIndicator = true; playNotify(); setTimeout(function() { showIndicator = false }, 3000)"
    wire:poll.5s.visible="refresh" 
    class="min-h-screen bg-slate-50/50 pb-20"
>
    <!-- Top-right Loading Indicator (Only on change detection) -->
    <div x-show="showIndicator" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-[-20px]"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-[-20px]"
        class="fixed top-6 right-6 z-[100]"
    >
        <div class="flex items-center gap-3 bg-white border border-blue-100 px-5 py-3 rounded-2xl shadow-2xl shadow-blue-500/10 transition-all border-b-4 border-b-blue-500">
            <div class="relative">
                <div class="w-2 h-2 bg-blue-600 rounded-full animate-ping absolute -top-1 -right-1"></div>
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <span class="text-[11px] font-black text-slate-900 uppercase tracking-widest">Nouveau contenu !</span>
        </div>
    </div>
    {{-- ===== PROJECT HERO HEADER ===== --}}
    <div class="relative pt-10 pb-16 overflow-hidden">
        <div class="absolute inset-0 -z-10">
            <div class="absolute top-[-20%] right-[-10%] w-[50%] aspect-square bg-blue-500/5 rounded-full blur-[140px]"></div>
            <div class="absolute bottom-0 left-0 w-[40%] aspect-square bg-indigo-500/5 rounded-full blur-[120px]"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6">
            <div class="bg-white/40 backdrop-blur-3xl border border-white/60 rounded-[3rem] md:rounded-[4rem] p-8 md:p-16 shadow-2xl shadow-blue-500/5 relative overflow-hidden group">

                {{-- Status badges --}}
                <div class="flex flex-wrap items-center gap-2 md:gap-3 mb-8 md:absolute md:top-10 md:right-10 md:mb-0">
                    @if($project->is_open)
                        <span class="px-3 md:px-4 py-1.5 bg-green-50 text-green-600 rounded-full text-[9px] md:text-[10px] font-black uppercase tracking-widest border border-green-100 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-green-600 rounded-full animate-pulse"></span>
                            Ouvert
                        </span>
                    @else
                        <span class="px-3 md:px-4 py-1.5 bg-slate-100 text-slate-500 rounded-full text-[9px] md:text-[10px] font-black uppercase tracking-widest border border-slate-200 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                            Fermé
                        </span>
                    @endif
                </div>

                <div class="flex flex-col lg:flex-row gap-12 relative z-10">
                    {{-- Icon --}}
                    <div class="flex-shrink-0 flex justify-center lg:block">
                        <div class="w-24 h-24 md:w-32 md:h-32 bg-gradient-to-tr from-blue-600 to-indigo-700 rounded-[2rem] md:rounded-[2.5rem] flex items-center justify-center text-white shadow-2xl shadow-blue-600/30 rotate-3 group-hover:rotate-6 transition-transform duration-500">
                            <svg class="w-12 h-12 md:w-16 md:h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="flex-grow">
                        <div class="flex flex-wrap items-center gap-4 mb-4">
                            <span class="text-xs font-black text-blue-600 uppercase tracking-[0.3em]">Projet</span>
                            <span class="text-slate-200">/</span>
                            <a href="{{ route('users.show', $project->owner) }}" class="text-xs font-black text-slate-400 uppercase tracking-[0.3em] hover:text-blue-600 transition-colors">{{ $project->owner->name }}</a>
                        </div>
                        <h1 class="text-3xl md:text-7xl font-black text-slate-900 tracking-tighter leading-none mb-6 text-center md:text-left">
                            {{ $project->title }}
                        </h1>
                        @if($project->description)
                            <p class="text-xl text-slate-500 font-medium max-w-2xl leading-relaxed mb-6">
                                {{ $project->description }}
                            </p>
                        @endif

                        {{-- Project Expertise (Skills) --}}
                        <div class="mb-8 p-6 bg-blue-50/50 rounded-[2rem] border border-blue-100/50 relative group/skills">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] flex items-center gap-2">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                                    Expertise du Projet
                                </h3>
                                @if($project->canManage(auth()->user()))
                                    <button wire:click="$toggle('showProjectSkillForm')" class="p-2 hover:bg-blue-100 rounded-xl transition-colors text-blue-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    </button>
                                @endif
                            </div>

                            @if($showProjectSkillForm)
                                <div class="mb-6 animate-fadeIn">
                                    <div class="relative" x-data="{ open: true }">
                                        <div class="flex gap-2 mb-4">
                                            <input 
                                                wire:model.live="skillSearch" 
                                                wire:keydown.enter.prevent="addSkill" 
                                                @focus="open = true"
                                                type="text" 
                                                placeholder="Rechercher ou créer une expertise..." 
                                                class="flex-grow bg-white border border-blue-100 px-5 py-3 rounded-xl text-xs font-bold focus:ring-2 focus:ring-blue-500 outline-none"
                                            >
                                        </div>

                                        {{-- Suggestions Dropdown --}}
                                        @if($this->skillSuggestions->count() > 0)
                                            <div x-show="open" @click.away="open = false" class="absolute z-50 w-full mt-[-10px] mb-4 bg-white border border-blue-100 rounded-2xl shadow-2xl overflow-hidden animate-in fade-in slide-in-from-top-2 duration-200">
                                                @foreach($this->skillSuggestions as $suggestion)
                                                    <button 
                                                        wire:click="addSkill('{{ $suggestion->name }}')" 
                                                        @click="open = false"
                                                        class="w-full text-left px-5 py-4 text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-all flex items-center justify-between group"
                                                    >
                                                        <span>{{ $suggestion->name }}</span>
                                                        <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                    </button>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="flex flex-wrap gap-2 mb-6">
                                            @foreach($selectedSkills as $skillName)
                                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest shadow-lg shadow-blue-500/10">
                                                    {{ $skillName }}
                                                    <button wire:click="removeSkill('{{ $skillName }}')" class="hover:text-red-200"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button wire:click="saveProjectSkills" class="flex-grow py-3 bg-slate-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all">Enregistrer l'expertise</button>
                                        <button wire:click="$set('showProjectSkillForm', false)" class="px-5 py-3 bg-slate-100 text-slate-500 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">Annuler</button>
                                    </div>
                                </div>
                            @endif

                            <div class="flex flex-wrap gap-2">
                                @forelse($project->skills as $skill)
                                    <a href="{{ route('projects.index', ['selectedSkills' => [$skill->id]]) }}" class="px-4 py-2 bg-white border border-blue-100 text-blue-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 hover:text-white hover:shadow-lg hover:shadow-blue-500/20 transition-all">
                                        {{ $skill->name }}
                                    </a>
                                @empty
                                    <p class="text-[10px] font-black text-slate-300 uppercase italic">Aucune compétence d'expertise définie pour ce projet.</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Stats Bar --}}
                        <div class="grid grid-cols-4 gap-4 md:gap-8 py-8 border-t border-slate-100" id="project-stats-bar">
                            <button @click="switchTab('team')" class="text-center md:text-left hover:opacity-80 transition-opacity">
                                <div class="text-xl md:text-3xl font-black text-slate-900 leading-none mb-1">{{ $project->activeMembers->count() }}</div>
                                <div class="text-[8px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest">Membres</div>
                            </button>
                            <button @click="switchTab('offers')" class="text-center md:text-left hover:opacity-80 transition-opacity">
                                <div class="text-xl md:text-3xl font-black text-blue-600 leading-none mb-1">{{ $project->offers->count() }}</div>
                                <div class="text-[8px] md:text-[10px] font-black text-blue-400 uppercase tracking-widest">Offres</div>
                            </button>
                            <button @click="switchTab('demands')" class="text-center md:text-left hover:opacity-80 transition-opacity">
                                <div class="text-xl md:text-3xl font-black text-purple-600 leading-none mb-1">{{ $project->demands->count() }}</div>
                                <div class="text-[8px] md:text-[10px] font-black text-purple-400 uppercase tracking-widest">Demandes</div>
                            </button>
                            <button @click="switchTab('reviews')" class="text-center md:text-left hover:opacity-80 transition-opacity">
                                <div class="text-xl md:text-3xl font-black text-green-600 leading-none mb-1">+{{ $project->getPositiveReviewsCount() }}</div>
                                <div class="text-[8px] md:text-[10px] font-black text-green-400 uppercase tracking-widest">Avis +</div>
                            </button>
                        </div>
                    </div>

                    {{-- Actions Sidebar --}}
                    <div class="lg:w-72 space-y-4">
                        <div class="p-6 bg-slate-900 rounded-[2.5rem] text-white">
                            {{-- Owner --}}
                            <a href="{{ route('users.show', $project->owner) }}" class="flex items-center gap-4 mb-4 group/owner transition-all">
                                <img src="{{ $project->owner->avatar }}" class="w-12 h-12 rounded-2xl ring-2 ring-white/10 group-hover/owner:ring-blue-500 transition-all">
                                <div>
                                    <div class="text-[8px] font-black text-blue-400 uppercase tracking-widest leading-none mb-1">Fondateur</div>
                                    <div class="text-sm font-bold group-hover/owner:text-blue-400 transition-colors">{{ $project->owner->name }}</div>
                                </div>
                            </a>

                            {{-- Members avatars --}}
                            @if($project->activeMembers->count() > 0)
                                <div class="flex flex-wrap gap-2 mb-6 pt-4 border-t border-white/10">
                                    @foreach($project->activeMembers->take(8) as $member)
                                        @if($member->memberable)
                                            <a href="{{ $member->memberable instanceof \App\Models\User ? route('users.show', $member->memberable) : ($member->memberable instanceof \App\Models\Circle ? route('circles.show', $member->memberable) : '#') }}" class="hover:scale-110 transition-transform">
                                                <img src="{{ $member->memberable->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($member->memberable->name ?? '?').'&background=1e293b&color=fff' }}" class="w-8 h-8 rounded-xl ring-2 ring-white/10 object-cover" title="{{ $member->memberable->name ?? '?' }}">
                                            </a>
                                        @endif
                                    @endforeach
                                    @if($project->activeMembers->count() > 8)
                                        <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center text-[8px] font-black text-white">+{{ $project->activeMembers->count() - 8 }}</div>
                                    @endif
                                </div>
                            @endif

                            {{-- Address --}}
                            <div class="mb-6 pt-4 border-t border-white/10">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[8px] font-black text-slate-500 uppercase tracking-[0.2em]">Localisation</span>
                                    @if($project->canManage(auth()->user()))
                                        <button wire:click="$toggle('isEditingAddress')" class="text-[8px] font-black text-blue-400 uppercase tracking-widest hover:text-blue-300 transition-colors">
                                            {{ $isEditingAddress ? 'Annuler' : 'Modifier' }}
                                        </button>
                                    @endif
                                </div>
                                
                                @if($isEditingAddress)
                                    <div class="space-y-2">
                                        <input 
                                            type="text" 
                                            wire:model="address" 
                                            wire:keydown.enter="updateAddress"
                                            placeholder="Ex: Paris, France"
                                            class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:border-blue-500 focus:ring-0 transition-all uppercase font-bold"
                                        >
                                        <button wire:click="updateAddress" class="w-full py-2 bg-blue-600 hover:bg-blue-700 rounded-xl text-[9px] font-black uppercase tracking-widest text-white transition-all">
                                            Enregistrer
                                        </button>
                                    </div>
                                @else
                                    <div class="flex items-start gap-2">
                                        <svg class="w-3.5 h-3.5 text-slate-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        @if($project->address)
                                            <div class="flex flex-col gap-1">
                                                <div class="flex flex-wrap items-center gap-1.5">
                                                    @if($project->neighborhood)
                                                        <a href="{{ url('/?search=' . urlencode($project->neighborhood)) }}" class="text-xs font-black text-blue-400 hover:text-white transition-colors uppercase tracking-tight">
                                                            {{ $project->neighborhood }}
                                                        </a>
                                                        <span class="text-slate-600 text-[10px]">•</span>
                                                    @endif
                                                    @if($project->city)
                                                        <a href="{{ url('/?search=' . urlencode($project->city)) }}" class="text-xs font-black text-slate-200 hover:text-blue-400 transition-colors uppercase tracking-tight">
                                                            {{ $project->city }}
                                                        </a>
                                                    @endif
                                                </div>
                                                <span class="text-[10px] font-bold text-slate-600 uppercase tracking-tight leading-tight">
                                                    {{ $project->address }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-xs font-bold text-slate-600 italic uppercase tracking-tight">
                                                Non définie
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>


                            {{-- Auth actions --}}
                            @auth
                                <div class="space-y-3">
                                    @if($project->canManage(auth()->user()))
                                        <button wire:click="toggleStatus" class="w-full text-center py-4 rounded-2xl font-black text-sm tracking-widest uppercase transition-all border {{ $project->is_open ? 'bg-red-500/10 text-red-400 border-red-500/20 hover:bg-red-500 hover:text-white' : 'bg-green-500/10 text-green-400 border-green-500/20 hover:bg-green-500 hover:text-white' }}">
                                            {{ $project->is_open ? '⏸ Fermer le projet' : '▶ Ouvrir le projet' }}
                                        </button>
                                    @elseif($project->isMember(auth()->user()))
                                        <div class="w-full text-center py-4 bg-blue-600/10 rounded-2xl font-black text-sm tracking-widest uppercase text-blue-400 border border-blue-500/20">
                                            Membre Actif
                                        </div>
                                        <button wire:click="leaveProject" class="w-full text-center py-2 text-slate-500 hover:text-red-400 font-bold text-[10px] uppercase tracking-widest transition-colors">
                                            Quitter le projet
                                        </button>
                                    @elseif($project->isPending(auth()->user()))
                                        <div class="w-full text-center py-4 bg-orange-500/10 rounded-2xl font-black text-[10px] tracking-widest uppercase text-orange-400 border border-orange-500/20">
                                            Candidature en cours...
                                        </div>
                                        <button wire:click="leaveProject" class="w-full text-center py-2 text-slate-500 hover:text-red-400 font-bold text-[10px] uppercase tracking-widest transition-colors">
                                            Annuler la demande
                                        </button>
                                    @elseif($project->isInvited(auth()->user()))
                                        <div class="space-y-2">
                                            <div class="w-full text-center py-3 bg-purple-500/10 rounded-2xl font-black text-[10px] tracking-widest uppercase text-purple-400 border border-purple-500/20">
                                                Invitation reçue 💌
                                            </div>
                                            <div class="flex gap-2">
                                                <button wire:click="acceptInvitation" class="flex-grow py-3 bg-blue-600 hover:bg-blue-700 rounded-xl font-black text-[9px] uppercase tracking-widest text-white transition-all">Accepter</button>
                                                <button wire:click="leaveProject" class="px-4 py-3 bg-white/10 hover:bg-red-600 rounded-xl font-black text-[9px] uppercase tracking-widest text-white transition-all">Refuser</button>
                                            </div>
                                        </div>
                                    @elseif($project->is_open)
                                        <button wire:click="joinProject" class="w-full text-center py-4 bg-blue-600 hover:bg-blue-700 rounded-2xl font-black text-sm tracking-widest uppercase transition-all shadow-xl shadow-blue-500/20">
                                            Postuler au projet
                                        </button>
                                    @else
                                        <div class="w-full text-center py-4 bg-slate-800 rounded-2xl font-black text-sm tracking-widest uppercase text-slate-500 border border-slate-700">
                                            Projet fermé
                                        </div>
                                    @endif
                                </div>
                            @else
                                <a href="/admin/login" class="block text-center py-4 bg-blue-600 hover:bg-blue-700 rounded-2xl font-black text-sm tracking-widest uppercase transition-all shadow-xl shadow-blue-500/20">
                                    Se Connecter
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="max-w-7xl mx-auto px-6" id="tab-content-start">


        {{-- ---- OVERVIEW ---- --}}
        <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Offres preview --}}
                <div @click="switchTab('offers')" class="bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[3rem] p-8 shadow-xl shadow-blue-500/5 cursor-pointer hover:shadow-blue-500/10 hover:scale-[1.01] transition-all group/card">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            Ce que nous offrons
                        </h3>
                        <svg class="w-4 h-4 text-slate-300 group-hover/card:text-blue-500 group-hover/card:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5-5 5M6 7l5 5-5 5"/></svg>
                    </div>
                    @forelse($project->offers->take(3) as $offer)
                        <div class="mb-4 p-4 bg-blue-50/50 rounded-2xl border border-blue-100 flex gap-4 items-center">
                            @if($offer->images && count($offer->images) > 0)
                                <img src="{{ Storage::url($offer->images[0]) }}" class="w-12 h-12 rounded-xl object-cover shadow-sm bg-white">
                            @else
                                <div class="w-12 h-12 rounded-xl bg-white border border-blue-100 flex items-center justify-center text-blue-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                            <div class="flex-grow">
                                <div class="font-black text-sm text-slate-900 uppercase tracking-tight mb-0.5">{{ $offer->title }}</div>
                                @if($offer->description)
                                    <p class="text-[9px] text-slate-500 font-medium leading-relaxed line-clamp-1">{{ $offer->description }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-[10px] font-black text-slate-300 uppercase text-center py-8">Aucune offre définie</p>
                    @endforelse
                    @if($project->offers->count() > 3)
                        <button @click="activeTab = 'offers'" class="w-full text-center text-[9px] font-black text-blue-600 uppercase tracking-widest hover:text-blue-800 transition-colors mt-2">
                            Voir toutes les offres →
                        </button>
                    @endif
                </div>

                {{-- Demandes preview --}}
                <div @click="switchTab('demands')" class="bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[3rem] p-8 shadow-xl shadow-purple-500/5 cursor-pointer hover:shadow-purple-500/10 hover:scale-[1.01] transition-all group/card">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-[10px] font-black text-purple-600 uppercase tracking-[0.3em] flex items-center gap-2">
                            <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                            Ce que nous cherchons
                        </h3>
                        <svg class="w-4 h-4 text-slate-300 group-hover/card:text-purple-500 group-hover/card:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5-5 5M6 7l5 5-5 5"/></svg>
                    </div>
                    @forelse($project->demands->take(3) as $demand)
                        <div class="mb-4 p-4 bg-purple-50/50 rounded-2xl border border-purple-100 flex gap-4 items-center">
                            @if($demand->images && count($demand->images) > 0)
                                <img src="{{ Storage::url($demand->images[0]) }}" class="w-12 h-12 rounded-xl object-cover shadow-sm bg-white">
                            @else
                                <div class="w-12 h-12 rounded-xl bg-white border border-purple-100 flex items-center justify-center text-purple-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                            <div class="flex-grow">
                                <div class="font-black text-sm text-slate-900 uppercase tracking-tight mb-0.5">{{ $demand->title }}</div>
                                @if($demand->description)
                                    <p class="text-[9px] text-slate-500 font-medium leading-relaxed line-clamp-1">{{ $demand->description }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-[10px] font-black text-slate-300 uppercase text-center py-8">Aucune demande définie</p>
                    @endforelse
                    @if($project->demands->count() > 3)
                        <button @click="activeTab = 'demands'" class="w-full text-center text-[9px] font-black text-purple-600 uppercase tracking-widest hover:text-purple-800 transition-colors mt-2">
                            Voir toutes les demandes →
                        </button>
                    @endif
                </div>

                {{-- Avis preview --}}
                @if($project->reviews->count() > 0)
                    <div @click="switchTab('reviews')" class="md:col-span-1 bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[3rem] p-8 shadow-xl cursor-pointer hover:shadow-green-500/5 hover:scale-[1.01] transition-all group/card h-full">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-[10px] font-black text-green-600 uppercase tracking-[0.3em] flex items-center gap-2">
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                Derniers Avis
                                <span class="ml-2 text-slate-300">{{ $project->getPositiveReviewsCount() }}✓ {{ $project->getNegativeReviewsCount() }}✗</span>
                            </h3>
                            <svg class="w-4 h-4 text-slate-300 group-hover/card:text-green-500 group-hover/card:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5-5 5M6 7l5 5-5 5"/></svg>
                        </div>
                        <div class="space-y-4">
                            @foreach($project->reviews->take(2) as $review)
                                <div @class(['p-4 rounded-2xl border', 'bg-green-50/50 border-green-100' => $review->type === 'validate', 'bg-red-50/50 border-red-100' => $review->type === 'reject'])>
                                    <div class="flex items-center gap-3 mb-2">
                                        <img src="{{ $review->user->avatar }}" class="w-6 h-6 rounded-lg object-cover ring-1 ring-slate-100">
                                        <div class="text-[9px] font-black text-slate-900 uppercase">{{ $review->user->name }}</div>
                                    </div>
                                    @if($review->comment)
                                        <p class="text-[9px] font-medium text-slate-600 italic leading-tight line-clamp-2">"{{ $review->comment }}"</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Équipe preview --}}
                <div @click="switchTab('team')" class="md:col-span-1 bg-slate-900 rounded-[3rem] p-8 shadow-2xl shadow-blue-500/10 cursor-pointer hover:scale-[1.01] transition-all group/card relative overflow-hidden h-full">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/10 rounded-full blur-3xl"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-[10px] font-black text-blue-400 uppercase tracking-[0.3em] flex items-center gap-2">
                                <span class="w-2 h-2 bg-blue-400 rounded-full"></span>
                                Le Noyau Dur
                            </h3>
                            <svg class="w-4 h-4 text-white/20 group-hover/card:text-blue-400 group-hover/card:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5-5 5M6 7l5 5-5 5"/></svg>
                        </div>
                        
                        <div class="flex -space-x-4 mb-6">
                            @foreach($project->activeMembers->take(6) as $member)
                                <img src="{{ $member->memberable->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($member->memberable->name ?? '?') }}" class="w-12 h-12 rounded-2xl border-4 border-slate-900 shadow-xl object-cover ring-1 ring-white/10 group-hover/card:-translate-y-1 transition-all" style="transition-delay: {{ $loop->index * 50 }}ms">
                            @endforeach
                            @if($project->activeMembers->count() > 6)
                                <div class="w-12 h-12 rounded-2xl bg-slate-800 border-4 border-slate-900 flex items-center justify-center text-[10px] font-black text-white">+{{ $project->activeMembers->count() - 6 }}</div>
                            @endif
                        </div>

                        <div class="bg-white/5 rounded-2xl p-4 border border-white/10">
                            <div class="text-[8px] font-black text-slate-500 uppercase tracking-widest leading-none mb-1">Dernière activité</div>
                            <div class="text-[10px] text-white font-bold">{{ $project->activeMembers->count() }} experts collaborent activement sur ce projet.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ---- OFFRES ---- --}}
        <div x-show="activeTab === 'offers'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            {{-- Back button --}}
            <button @click="switchTab('overview')" class="back-to-overview mb-8 flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-red-600 to-rose-600 border-b-4 border-red-800 rounded-2xl text-[10px] font-black text-white uppercase tracking-[0.2em] hover:from-red-500 hover:to-rose-500 hover:translate-y-[-2px] hover:shadow-2xl hover:shadow-red-500/40 active:translate-y-[2px] active:border-b-0 transition-all group">
                <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Retour à l'aperçu
            </button>
            @if($project->canManage(auth()->user()))
                <div class="mb-12">
                    @if(!$showOfferForm)
                        <button wire:click="$set('showOfferForm', true)" class="w-full py-10 bg-white border-2 border-dashed border-blue-200 text-blue-600 rounded-[3rem] font-black text-xs uppercase tracking-[0.4em] hover:bg-blue-50 transition-all flex items-center justify-center gap-6 group">
                            <span class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white group-hover:rotate-12 transition-all shadow-lg shadow-blue-500/5">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            </span>
                            Nouvel Article en Boutique
                        </button>
                    @else
                        <div class="bg-slate-900 rounded-[3.5rem] p-10 text-white shadow-3xl animate-in fade-in slide-in-from-top-4 duration-500 relative overflow-hidden group/form">
                             <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 rounded-full blur-[100px]"></div>
                             <h4 class="text-sm font-black uppercase tracking-[0.3em] mb-10 text-blue-400 relative z-10 flex items-center justify-between">
                                 <span class="flex items-center gap-3">
                                     <span class="w-2 h-8 bg-blue-500 rounded-full"></span>
                                     {{ $editingOfferId ? 'Éditer l\'Article' : 'Ajouter à la Boutique' }}
                                 </span>
                                 @if($editingOfferId)
                                     <button wire:click="$set('editingOfferId', null); $set('showOfferForm', false)" class="text-[10px] text-slate-500 hover:text-white transition-colors uppercase tracking-widest bg-white/5 px-4 py-2 rounded-xl">Annuler</button>
                                 @endif
                             </h4>
                             
                             <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 relative z-10">
                                 <div class="space-y-6">
                                     <div class="space-y-2">
                                         <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest italic ml-2">Nom de l'article</label>
                                         <input wire:model="offerTitle" type="text" placeholder="Titre attractif..." class="w-full bg-white/5 border border-white/10 rounded-2xl p-5 text-sm font-bold placeholder:text-slate-600 focus:border-blue-500 transition-all outline-none">
                                     </div>
                                     <div class="space-y-2">
                                         <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest italic ml-2">Description principale</label>
                                         <textarea wire:model="offerDescription" rows="4" placeholder="Décrivez votre offre de manière concise..." class="w-full bg-white/5 border border-white/10 rounded-2xl p-5 text-xs font-medium placeholder:text-slate-600 focus:border-blue-500 transition-all italic outline-none"></textarea>
                                     </div>
                                 </div>

                                 <div class="space-y-6">
                                     <div class="space-y-2">
                                         <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest italic ml-2">Galerie Photos (Plusieurs possibles)</label>
                                         <div class="relative group/upload">
                                             <input type="file" wire:model="offerImages" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                                             <div class="w-full py-8 border-2 border-dashed border-white/10 rounded-2xl flex flex-col items-center justify-center gap-3 bg-white/5 group-hover/upload:border-blue-500/50 transition-all">
                                                 <svg class="w-8 h-8 text-slate-600 group-hover/upload:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                 <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Cliquez ou glissez vos images</span>
                                             </div>
                                         </div>
                                         @if($offerImages)
                                             <div class="flex flex-wrap gap-2 mt-2">
                                                 @foreach($offerImages as $image)
                                                     <div class="relative w-16 h-16 rounded-lg overflow-hidden border border-white/10">
                                                         <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                                                     </div>
                                                 @endforeach
                                             </div>
                                         @endif
                                     </div>
                                     <div class="space-y-4">
                                         <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest italic ml-2">Informations (Label : Détail)</label>
                                         @foreach($offerInfos as $index => $info)
                                             <div class="flex gap-2 animate-in slide-in-from-left-2 duration-200">
                                                 <input wire:model="offerInfos.{{ $index }}.label" type="text" placeholder="Ex: Prix" class="w-1/3 bg-white/5 border border-white/10 rounded-xl p-3 text-[10px] font-bold text-blue-300 placeholder:text-slate-700 focus:border-blue-500 outline-none">
                                                 <input wire:model="offerInfos.{{ $index }}.title" type="text" placeholder="Ex: 5 euros" class="flex-grow bg-white/5 border border-white/10 rounded-xl p-3 text-[10px] font-bold text-white placeholder:text-slate-700 focus:border-blue-500 outline-none">
                                                 @if(count($offerInfos) > 1)
                                                     <button wire:click="removeOfferInfo({{ $index }})" class="p-3 text-red-400 hover:text-red-300 transition-colors">
                                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                     </button>
                                                 @endif
                                             </div>
                                         @endforeach
                                         <button wire:click="addOfferInfo" class="text-[9px] font-black text-blue-500 uppercase tracking-widest hover:text-blue-400 transition-colors ml-2">+ Ajouter une info</button>
                                     </div>
                                 </div>
                             </div>

                             <div class="flex gap-4 mt-10 pt-10 border-t border-white/5 relative z-10">
                                 <button wire:click="addOffer" wire:loading.attr="disabled" class="flex-grow py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:from-blue-500 hover:to-indigo-500 shadow-2xl shadow-blue-500/20 transition-all disabled:opacity-50">
                                     <span wire:loading.remove>📦 Valider l'article</span>
                                     <span wire:loading>Traitement...</span>
                                 </button>
                                 <button wire:click="$set('showOfferForm', false)" class="px-10 py-5 bg-white/5 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:bg-white/10 transition-all">Annuler</button>
                             </div>
                        </div>
                    @endif
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($project->offers as $offer)
                    <div class="bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[3rem] overflow-hidden shadow-xl shadow-blue-500/5 hover:shadow-2xl hover:shadow-blue-500/10 hover:translate-y-[-4px] transition-all group/item">
                        {{-- Photo Gallery --}}
                        <div class="relative h-64 bg-slate-100 overflow-hidden">
                            @if($offer->images && count($offer->images) > 0)
                                <div class="flex overflow-x-auto snap-x snap-mandatory h-full no-scrollbar custom-scrollbar-h">
                                    @foreach($offer->images as $img)
                                        <div class="min-w-full h-full snap-start">
                                            <img src="{{ Storage::url($img) }}" class="w-full h-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                                @if(count($offer->images) > 1)
                                    <div class="absolute bottom-4 right-4 px-3 py-1.5 bg-black/60 backdrop-blur-md rounded-full text-[8px] font-black text-white uppercase tracking-widest pointer-events-none">
                                        {{ count($offer->images) }} Photos
                                    </div>
                                @endif
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-slate-50 text-slate-200">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif

                            @if($project->canManage(auth()->user()))
                                <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover/item:opacity-100 transition-opacity">
                                    <button wire:click="editOffer({{ $offer->id }})" class="p-3 bg-white/90 backdrop-blur shadow-lg rounded-xl text-blue-600 hover:bg-blue-600 hover:text-white transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="deleteOffer({{ $offer->id }})" wire:confirm="Supprimer cette offre ?" class="p-3 bg-white/90 backdrop-blur shadow-lg rounded-xl text-red-600 hover:bg-red-600 hover:text-white transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            @endif
                        </div>

                        <div class="p-8">
                            <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight mb-4 group-hover/item:text-blue-600 transition-colors">{{ $offer->title }}</h3>
                            @if($offer->description)
                                <p class="text-xs text-slate-500 font-medium leading-relaxed mb-6 line-clamp-3 italic">"{{ $offer->description }}"</p>
                            @endif

                            @if($offer->informations->count() > 0)
                                <div class="pt-6 border-t border-slate-100 flex flex-wrap gap-2">
                                    @foreach($offer->informations as $info)
                                        <div class="flex items-center gap-1.5 bg-blue-50/50 border border-blue-100/50 px-2.5 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest shadow-sm shadow-blue-500/5">
                                            @if($info->label)
                                                <span class="text-blue-400 italic">{{ $info->label }}:</span>
                                            @endif
                                            <span class="text-blue-600">{{ $info->title }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-32 text-center bg-white/40 border-2 border-dashed border-slate-200 rounded-[4rem]">
                        <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-6 text-slate-200">
                             <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <p class="text-slate-400 font-black uppercase tracking-[0.4em] text-xs italic">La boutique est vide pour le moment.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ---- DEMANDES ---- --}}
        <div x-show="activeTab === 'demands'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            {{-- Back button --}}
            <button @click="switchTab('overview')" class="back-to-overview mb-8 flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-red-600 to-rose-600 border-b-4 border-red-800 rounded-2xl text-[10px] font-black text-white uppercase tracking-[0.2em] hover:from-red-500 hover:to-rose-500 hover:translate-y-[-2px] hover:shadow-2xl hover:shadow-red-500/40 active:translate-y-[2px] active:border-b-0 transition-all group">
                <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Retour à l'aperçu
            </button>
            @if($project->canManage(auth()->user()))
                <div class="mb-12">
                    @if(!$showDemandForm)
                        <button wire:click="$set('showDemandForm', true)" class="w-full py-10 bg-white border-2 border-dashed border-purple-200 text-purple-600 rounded-[3rem] font-black text-xs uppercase tracking-[0.4em] hover:bg-purple-50 transition-all flex items-center justify-center gap-6 group">
                            <span class="w-12 h-12 rounded-2xl bg-purple-100 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white group-hover:rotate-12 transition-all shadow-lg shadow-purple-500/5">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </span>
                            Nouvelle Demande d'Expertise
                        </button>
                    @else
                        <div class="bg-slate-900 rounded-[3.5rem] p-10 text-white shadow-3xl animate-in fade-in slide-in-from-top-4 duration-500 relative overflow-hidden group/form">
                             <div class="absolute top-0 right-0 w-64 h-64 bg-purple-600/10 rounded-full blur-[100px]"></div>
                             <h4 class="text-sm font-black uppercase tracking-[0.3em] mb-10 text-purple-400 relative z-10 flex items-center justify-between">
                                 <span class="flex items-center gap-3">
                                     <span class="w-2 h-8 bg-purple-500 rounded-full"></span>
                                     {{ $editingDemandId ? 'Éditer la Demande' : 'Rechercher une Expertise' }}
                                 </span>
                                 @if($editingDemandId)
                                     <button wire:click="$set('editingDemandId', null); $set('showDemandForm', false)" class="text-[10px] text-slate-500 hover:text-white transition-colors uppercase tracking-widest bg-white/5 px-4 py-2 rounded-xl">Annuler</button>
                                 @endif
                             </h4>
                             
                             <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 relative z-10">
                                 <div class="space-y-6">
                                     <div class="space-y-2">
                                         <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest italic ml-2">Objet de la recherche</label>
                                         <input wire:model="demandTitle" type="text" placeholder="Ex: Développeur Senior React..." class="w-full bg-white/5 border border-white/10 rounded-2xl p-5 text-sm font-bold placeholder:text-slate-600 focus:border-purple-500 transition-all outline-none">
                                     </div>
                                     <div class="space-y-2">
                                         <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest italic ml-2">Détails de la mission</label>
                                         <textarea wire:model="demandDescription" rows="4" placeholder="Décrivez le besoin technique ou créatif..." class="w-full bg-white/5 border border-white/10 rounded-2xl p-5 text-xs font-medium placeholder:text-slate-600 focus:border-purple-500 transition-all italic outline-none"></textarea>
                                     </div>
                                 </div>

                                 <div class="space-y-6">
                                     <div class="space-y-2">
                                         <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest italic ml-2">Images de référence (Optionnel)</label>
                                         <div class="relative group/upload">
                                             <input type="file" wire:model="demandImages" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                                             <div class="w-full py-8 border-2 border-dashed border-white/10 rounded-2xl flex flex-col items-center justify-center gap-3 bg-white/5 group-hover/upload:border-purple-500/50 transition-all">
                                                 <svg class="w-8 h-8 text-slate-600 group-hover/upload:text-purple-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                 <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Illustrer le besoin</span>
                                             </div>
                                         </div>
                                         @if($demandImages)
                                             <div class="flex flex-wrap gap-2 mt-2">
                                                 @foreach($demandImages as $image)
                                                     <div class="relative w-16 h-16 rounded-lg overflow-hidden border border-white/10">
                                                         <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                                                     </div>
                                                 @endforeach
                                             </div>
                                         @endif
                                     </div>
                                     <div class="space-y-4">
                                         <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest italic ml-2">Informations cruciales (Label : Détail)</label>
                                         @foreach($demandInfos as $index => $info)
                                             <div class="flex gap-2 animate-in slide-in-from-left-2 duration-200">
                                                 <input wire:model="demandInfos.{{ $index }}.label" type="text" placeholder="Ex: Budget" class="w-1/3 bg-white/5 border border-white/10 rounded-xl p-3 text-[10px] font-bold text-purple-300 placeholder:text-slate-700 focus:border-purple-500 outline-none">
                                                 <input wire:model="demandInfos.{{ $index }}.title" type="text" placeholder="Ex: 500€" class="flex-grow bg-white/5 border border-white/10 rounded-xl p-3 text-[10px] font-bold text-white placeholder:text-slate-700 focus:border-purple-500 outline-none">
                                                 @if(count($demandInfos) > 1)
                                                     <button wire:click="removeDemandInfo({{ $index }})" class="p-3 text-red-400 hover:text-red-300 transition-colors">
                                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                     </button>
                                                 @endif
                                             </div>
                                         @endforeach
                                         <button wire:click="addDemandInfo" class="text-[9px] font-black text-purple-500 uppercase tracking-widest hover:text-purple-400 transition-colors ml-2">+ Ajouter une info</button>
                                     </div>
                                 </div>
                             </div>

                             <div class="flex gap-4 mt-10 pt-10 border-t border-white/5 relative z-10">
                                 <button wire:click="addDemand" wire:loading.attr="disabled" class="flex-grow py-5 bg-gradient-to-r from-purple-600 to-fuchsia-600 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:from-purple-500 hover:to-fuchsia-500 shadow-2xl shadow-purple-500/20 transition-all disabled:opacity-50">
                                     <span wire:loading.remove>🔮 Publier la demande</span>
                                     <span wire:loading>Traitement...</span>
                                 </button>
                                 <button wire:click="$set('showDemandForm', false)" class="px-10 py-5 bg-white/5 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:bg-white/10 transition-all">Annuler</button>
                             </div>
                        </div>
                    @endif
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($project->demands as $demand)
                    <div class="bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[3rem] overflow-hidden shadow-xl shadow-purple-500/5 hover:shadow-2xl hover:shadow-purple-500/10 hover:translate-y-[-4px] transition-all group/item">
                        {{-- Photo Gallery --}}
                        <div class="relative h-64 bg-slate-100 overflow-hidden">
                            @if($demand->images && count($demand->images) > 0)
                                <div class="flex overflow-x-auto snap-x snap-mandatory h-full no-scrollbar custom-scrollbar-h">
                                    @foreach($demand->images as $img)
                                        <div class="min-w-full h-full snap-start">
                                            <img src="{{ Storage::url($img) }}" class="w-full h-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                                @if(count($demand->images) > 1)
                                    <div class="absolute bottom-4 right-4 px-3 py-1.5 bg-black/60 backdrop-blur-md rounded-full text-[8px] font-black text-white uppercase tracking-widest pointer-events-none">
                                        {{ count($demand->images) }} Photos
                                    </div>
                                @endif
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-slate-50 text-slate-200">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                            @endif

                            @if($project->canManage(auth()->user()))
                                <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover/item:opacity-100 transition-opacity">
                                    <button wire:click="editDemand({{ $demand->id }})" class="p-3 bg-white/90 backdrop-blur shadow-lg rounded-xl text-purple-600 hover:bg-purple-600 hover:text-white transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="deleteDemand({{ $demand->id }})" wire:confirm="Supprimer cette demande ?" class="p-3 bg-white/90 backdrop-blur shadow-lg rounded-xl text-red-600 hover:bg-red-600 hover:text-white transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            @endif
                        </div>

                        <div class="p-8">
                            <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight mb-4 group-hover/item:text-purple-600 transition-colors">{{ $demand->title }}</h3>
                            @if($demand->description)
                                <p class="text-xs text-slate-500 font-medium leading-relaxed mb-6 line-clamp-3 italic">"{{ $demand->description }}"</p>
                            @endif

                            @if($demand->informations->count() > 0)
                                <div class="pt-6 border-t border-slate-100 flex flex-wrap gap-2">
                                    @foreach($demand->informations as $info)
                                        <div class="flex items-center gap-1.5 bg-purple-50/50 border border-purple-100/50 px-2.5 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest shadow-sm shadow-purple-500/5">
                                            @if($info->label)
                                                <span class="text-purple-400 italic">{{ $info->label }}:</span>
                                            @endif
                                            <span class="text-purple-600">{{ $info->title }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-32 text-center bg-white/40 border-2 border-dashed border-slate-200 rounded-[4rem]">
                        <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-6 text-slate-200">
                             <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <p class="text-slate-400 font-black uppercase tracking-[0.4em] text-xs italic">Aucune expertise n'est recherchée pour le moment.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ---- AVIS (REVIEWS) ---- --}}
        <div x-show="activeTab === 'reviews'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            {{-- Back button --}}
            <button @click="switchTab('overview')" class="back-to-overview mb-8 flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-red-600 to-rose-600 border-b-4 border-red-800 rounded-2xl text-[10px] font-black text-white uppercase tracking-[0.2em] hover:from-red-500 hover:to-rose-500 hover:translate-y-[-2px] hover:shadow-2xl hover:shadow-red-500/40 active:translate-y-[2px] active:border-b-0 transition-all group">
                <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Retour à l'aperçu
            </button>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Review Form --}}
                @auth
                    @if(!$project->reviews()->where('user_id', auth()->id())->whereNull('parent_id')->exists())
                        <div class="bg-slate-900 rounded-[3rem] p-8 text-white shadow-2xl shadow-slate-900/20 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-blue-600/10 to-transparent"></div>
                            <div class="relative z-10">
                                <h3 class="text-xl font-black uppercase tracking-tight mb-6">Laisser un Avis</h3>

                                {{-- Type selector --}}
                                <div class="flex gap-3 mb-6">
                                    <button wire:click="$set('reviewType', 'validate')" @class(['flex-1 py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition-all', 'bg-green-600 text-white shadow-lg shadow-green-500/20' => $reviewType === 'validate', 'bg-white/10 text-slate-400 hover:bg-white/20' => $reviewType !== 'validate'])>
                                        ✓ Valider
                                    </button>
                                    <button wire:click="$set('reviewType', 'reject')" @class(['flex-1 py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition-all', 'bg-red-600 text-white shadow-lg shadow-red-500/20' => $reviewType === 'reject', 'bg-white/10 text-slate-400 hover:bg-white/20' => $reviewType !== 'reject'])>
                                        ✗ Rejeter
                                    </button>
                                </div>

                                <div class="mb-4">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Votre retour (un seul avis possible)</label>
                                    <textarea wire:model="reviewComment" rows="4" placeholder="Partagez votre expérience avec ce projet..."
                                        class="w-full bg-white/5 border border-white/10 focus:border-blue-500 focus:ring-0 rounded-2xl p-4 text-sm text-slate-200 placeholder:text-slate-600 resize-none"></textarea>
                                    @error('reviewComment') <span class="text-red-400 text-[9px] font-black uppercase mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <button wire:click="submitReview" @class(['w-full py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-lg', 'bg-green-600 hover:bg-green-700 shadow-green-500/20' => $reviewType === 'validate', 'bg-red-600 hover:bg-red-700 shadow-red-500/20' => $reviewType === 'reject'])>
                                    Publier mon avis
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="bg-blue-900/20 backdrop-blur-xl border border-blue-500/20 rounded-[3rem] p-10 text-center flex flex-col items-center justify-center">
                            <div class="w-20 h-20 bg-blue-600/20 rounded-3xl flex items-center justify-center mb-6 ring-1 ring-blue-500/30 shadow-2xl shadow-blue-500/10">
                                <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-xl font-black uppercase tracking-tight text-white mb-2">Avis Enregistré</h3>
                            <p class="text-[9px] text-blue-300 font-black uppercase tracking-[0.2em] leading-relaxed max-w-[200px]">Merci pour votre retour ! L'avis est désormais ancré dans l'ADN du projet.</p>
                        </div>
                    @endif
                @endauth

                {{-- Reviews List --}}
                <div class="{{ auth()->check() ? 'lg:col-span-2' : 'lg:col-span-3' }} space-y-4">
                    @forelse($project->reviews as $review)
                        <div @class(['p-6 rounded-[2rem] border transition-all', 'bg-green-50/50 border-green-100' => $review->type === 'validate', 'bg-red-50/50 border-red-100' => $review->type === 'reject'])>
                            <div class="flex items-center gap-4 mb-4">
                                <a href="{{ route('users.show', $review->user) }}" class="hover:scale-110 transition-transform">
                                    <img src="{{ $review->user->avatar }}" class="w-12 h-12 rounded-2xl object-cover ring-4 ring-white shadow-md">
                                </a>
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('users.show', $review->user) }}" class="text-sm font-black text-slate-900 uppercase hover:text-blue-600 transition-colors">{{ $review->user->name }}</a>
                                        <span @class(['text-[8px] font-black uppercase px-2 py-0.5 rounded-full', 'bg-green-600 text-white' => $review->type === 'validate', 'bg-red-600 text-white' => $review->type === 'reject'])>
                                            {{ $review->type === 'validate' ? '✓ Validé' : '✗ Rejeté' }}
                                        </span>
                                        @if($review->replies->count() > 0)
                                            <span class="bg-slate-900 text-white text-[7px] font-black uppercase px-2 py-0.5 rounded-full flex items-center gap-1 shadow-lg shadow-slate-900/10">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                Verrouillé
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[9px] font-black text-slate-400 uppercase mt-1">{{ $review->created_at->diffForHumans() }}</div>
                                </div>
                                
                                {{-- Delete button --}}
                                @auth
                                    @if(($review->user_id === auth()->id() || $project->canManage(auth()->user())) && $review->replies->count() === 0)
                                        <button wire:click="deleteReview({{ $review->id }})" wire:confirm="Supprimer cet avis ?" class="text-slate-300 hover:text-red-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endif
                                @endauth

                            @if($review->comment)
                                <p @class(['text-sm font-medium leading-relaxed italic', 'text-slate-700' => $review->replies->count() === 0, 'text-slate-400' => $review->replies->count() > 0])>"{{ $review->comment }}"</p>
                            @endif

                            {{-- Replies --}}
                            @if($review->replies->count() > 0)
                                <div class="mt-4 pl-6 border-l-2 border-white space-y-3">
                                    @foreach($review->replies as $reply)
                                        <div class="p-4 bg-white/80 rounded-2xl shadow-sm border border-slate-100 animate-in slide-in-from-top-2 duration-300">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="flex items-center gap-2 group/reply">
                                                    <img src="{{ $reply->user->avatar }}" class="w-6 h-6 rounded-lg object-cover ring-2 ring-blue-500/20 shadow-sm">
                                                    <div>
                                                        <span class="text-[9px] font-black text-slate-900 uppercase">{{ $reply->user->name }}</span>
                                                        <span class="text-[7px] font-black text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded-md uppercase tracking-widest ml-1">Réponse Officielle</span>
                                                    </div>
                                                </div>
                                            </div>
                                            @if($reply->comment)
                                                <p class="text-[10px] font-bold text-slate-700">"{{ $reply->comment }}"</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Reply button for project owner --}}
                            @auth
                                @if($project->canManage(auth()->user()) && $review->replies->count() === 0)
                                    <button wire:click="setReplyTo({{ $review->id }})" class="mt-3 text-[9px] font-black text-blue-600 uppercase tracking-widest hover:text-blue-800 transition-colors">
                                        ↩ Répondre
                                    </button>
                                @endif
                            @endauth
                        </div>
                    @empty
                        <div class="py-20 text-center bg-white/40 border-2 border-dashed border-slate-200 rounded-[3rem]">
                            <p class="text-slate-400 font-black uppercase tracking-[0.3em] text-sm italic">Aucun avis pour le moment.</p>
                        </div>
                    @endforelse

                    {{-- Reply form (if setReplyTo was called) --}}
                    @if($replyTo)
                        <div class="bg-blue-50 border border-blue-200 rounded-[2rem] p-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Répondre à cet avis</span>
                                <button wire:click="$set('replyTo', null)" class="text-slate-400 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <textarea wire:model="reviewComment" rows="3" placeholder="Votre réponse..."
                                class="w-full bg-white border-white focus:ring-blue-500 rounded-2xl p-4 text-sm font-medium italic mb-4"></textarea>
                            <button wire:click="submitReview" class="w-full py-3 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all">
                                Publier la réponse
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ---- ÉQUIPE (TEAM) ---- --}}
    <div class="max-w-7xl mx-auto px-6 mb-12" x-show="activeTab === 'team'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        {{-- Back button --}}
        <button @click="switchTab('overview')" class="back-to-overview mb-8 flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-red-600 to-rose-600 border-b-4 border-red-800 rounded-2xl text-[10px] font-black text-white uppercase tracking-[0.2em] hover:from-red-500 hover:to-rose-500 hover:translate-y-[-2px] hover:shadow-2xl hover:shadow-red-500/40 active:translate-y-[2px] active:border-b-0 transition-all group">
            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour à l'aperçu
        </button>
        <div class="space-y-12">
            {{-- Admin Management --}}
            @if($project->canManage(auth()->user()))
                <section class="bg-slate-900 rounded-[3rem] p-8 text-white shadow-2xl relative">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/5 rounded-full blur-3xl"></div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 relative z-10">
                        {{-- Pending Applications --}}
                        <div>
                            <h3 class="text-xs font-black uppercase tracking-[0.2em] mb-6 text-blue-400 flex items-center justify-between">
                                <span>📩 Candidatures en attente</span>
                                <span class="bg-blue-600 text-white px-2 py-0.5 rounded-lg text-[9px]">{{ $project->members()->where('status', 'pending')->count() }}</span>
                            </h3>
                            <div class="space-y-3">
                                @forelse($project->members()->where('status', 'pending')->with('memberable')->get() as $pending)
                                    <div class="flex items-center justify-between bg-white/5 border border-white/10 rounded-2xl p-4 group hover:bg-white/10 transition-all">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $pending->memberable->avatar }}" class="w-10 h-10 rounded-xl object-cover">
                                            <div>
                                                <div class="text-sm font-bold uppercase tracking-tight">{{ $pending->memberable->name }}</div>
                                                <div class="text-[9px] text-slate-500 font-black uppercase">Souhaite rejoindre</div>
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <button wire:click="approveMember({{ $pending->id }})" class="p-2 bg-blue-600 rounded-xl hover:bg-blue-700 transition-all" title="Approuver">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                            <button wire:click="rejectMember({{ $pending->id }})" class="p-2 bg-white/10 rounded-xl hover:bg-red-600 transition-all text-slate-400 hover:text-white" title="Refuser">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest italic py-4">Aucune candidature en attente.</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Invite Search --}}
                        <div x-data="{ open: false }">
                            <h3 class="text-xs font-black uppercase tracking-[0.2em] mb-6 text-purple-400">🚀 Inviter des experts</h3>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    wire:model.live="userSearch" 
                                    @focus="open = true"
                                    placeholder="Rechercher par nom..." 
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-sm font-bold placeholder:text-slate-600 focus:border-purple-500 transition-all uppercase tracking-tight"
                                >
                                
                                @if(count($this->userSuggestions))
                                    <div x-show="open" @click.away="open = false" class="absolute z-50 w-full mt-2 bg-slate-800/95 backdrop-blur-xl border border-white/20 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] max-h-60 overflow-y-auto custom-scrollbar">
                                        @foreach($this->userSuggestions as $suggestion)
                                            <button wire:click="inviteUser({{ $suggestion->id }})" class="w-full text-left px-4 py-3 border-b border-white/5 last:border-0 hover:bg-white/10 transition-all group/s">
                                                <div class="flex items-center gap-3">
                                                    <img src="{{ $suggestion->avatar }}" class="w-10 h-10 rounded-xl object-cover group-hover/s:ring-2 group-hover/s:ring-purple-500 transition-all">
                                                    <div class="flex-grow">
                                                        <div class="text-[11px] font-black uppercase tracking-tight text-white group-hover/s:text-purple-400 transition-colors">{{ $suggestion->name }}</div>
                                                        <div class="flex items-center gap-2">
                                                            <div class="text-[8px] text-slate-500 font-black uppercase tracking-widest">{{ $suggestion->trust_score }} TRUST SCORE</div>
                                                            @if($suggestion->activeJoinedCircles->count() > 0)
                                                                <span class="text-[7px] font-bold text-blue-400 bg-blue-400/10 px-1.5 py-0.5 rounded-md uppercase tracking-tighter">{{ $suggestion->activeJoinedCircles->count() }} Cercles</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <svg class="w-4 h-4 text-slate-600 group-hover/s:text-purple-500 group-hover/s:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Sent Invitations --}}
                            @php $invited = $project->members()->where('status', 'invited')->with('memberable')->get(); @endphp
                            @if($invited->count() > 0)
                                <div class="mt-6">
                                    <h4 class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-3">Invitations envoyées</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($invited as $inv)
                                            <div class="flex items-center gap-2 bg-white/5 border border-white/10 rounded-xl px-3 py-1.5 group">
                                                <span class="text-[9px] font-bold text-slate-300">{{ $inv->memberable->name }}</span>
                                                <button wire:click="rejectMember({{ $inv->id }})" class="text-slate-500 hover:text-red-400">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </section>
            @endif
            {{-- Team Members --}}
            <section>
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-6 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                    Le Noyau (Équipe)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($this->getTeamMembers() as $member)
                        <div class="bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[2.5rem] p-6 shadow-xl shadow-blue-500/5 group transition-all hover:shadow-2xl">
                            <div class="flex items-start gap-4">
                                <div class="relative">
                                    <a href="{{ $member->memberable instanceof \App\Models\User ? route('users.show', $member->memberable) : ($member->memberable instanceof \App\Models\Circle ? route('circles.show', $member->memberable) : '#') }}" class="block hover:opacity-80 transition-opacity">
                                        <img src="{{ $member->memberable->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($member->memberable->name ?? '?').'&background=1e293b&color=fff' }}" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-blue-100">
                                        <div @class(['absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white flex items-center justify-center text-[8px] font-black text-white', 'bg-blue-600' => $member->degree == 1, 'bg-purple-600' => $member->degree == 2, 'bg-slate-400' => $member->degree == 3])>
                                            {{ $member->degree }}
                                        </div>
                                    </a>
                                </div>
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <a href="{{ $member->memberable instanceof \App\Models\User ? route('users.show', $member->memberable) : ($member->memberable instanceof \App\Models\Circle ? route('circles.show', $member->memberable) : '#') }}" class="min-w-0">
                                            <h4 class="text-sm font-black text-slate-900 uppercase truncate hover:text-blue-600 transition-colors">
                                                {{ $member->memberable->name ?? 'Membre inconnu' }}
                                            </h4>
                                        </a>
                                        <span @class(['text-[7px] font-black uppercase px-2 py-0.5 rounded-lg border', $member->role === 'admin' ? 'bg-blue-600 text-white border-blue-600' : 'bg-slate-100 text-slate-500 border-slate-200'])>
                                            {{ $member->role }}
                                        </span>
                                    </div>
                                    
                                    @if($member->memberable instanceof \App\Models\User || $member->memberable instanceof \App\Models\Circle)
                                        <div class="flex flex-wrap gap-1 mb-3">
                                            @php
                                                $uniqueAchievements = $member->memberable->achievements->unique(fn($a) => $a->skill_id ?? $a->title)->filter()->take(3);
                                            @endphp
                                            @foreach($uniqueAchievements as $ach)
                                                <a href="{{ $ach->skill_id ? route('projects.index', ['selectedSkills' => [$ach->skill_id]]) : '#' }}" class="text-[8px] font-black text-blue-500 bg-blue-50 px-2 py-0.5 rounded-md uppercase tracking-tighter hover:bg-blue-600 hover:text-white transition-all">
                                                    {{ $ach->skill->name ?? $ach->title }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Trust Path --}}
                                    @if(!empty($member->trustPath))
                                        <div class="flex items-center gap-1.5 pt-3 border-t border-slate-100 overflow-x-auto custom-scrollbar-h pb-1">
                                            @foreach($member->trustPath as $index => $node)
                                                @if($index > 0)
                                                    <svg class="w-2 h-2 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                                                @endif
                                                <div class="flex items-center gap-1 flex-shrink-0">
                                                    @if($node['type'] === 'user')
                                                        <a href="{{ route('users.show', $node['id']) }}" class="flex items-center gap-1 hover:opacity-75 transition-opacity">
                                                            <img src="{{ $node['avatar'] }}" class="w-4 h-4 rounded-full ring-1 ring-slate-200">
                                                            <span class="text-[7px] font-black text-slate-600 uppercase">{{ $node['name'] }}</span>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('circles.show', $node['id']) }}" class="flex items-center gap-1 hover:opacity-75 transition-opacity">
                                                            <div class="w-4 h-4 bg-slate-100 rounded-full flex items-center justify-center">
                                                                <svg class="w-2 h-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            </div>
                                                            <span class="text-[7px] font-black text-slate-400 uppercase">{{ $node['name'] }}</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Network Experts Suggestions --}}
            @if(auth()->check())
                <section>
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-[10px] font-black text-purple-600 uppercase tracking-[0.3em] flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-purple-500 rounded-full"></span>
                            Réseau Proche (Experts suggérés)
                        </h3>
                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest italic">Basé sur les besoins du projet</span>
                    </div>
                    @php $experts = $this->getNetworkExperts(); @endphp
                    @if($experts->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($experts as $expert)
                                <div class="bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[2.5rem] p-6 shadow-xl shadow-purple-500/5 group border-dashed hover:border-solid hover:border-purple-300 transition-all hover:shadow-2xl">
                                    <div class="flex items-start gap-4">
                                        <div class="relative">
                                            <a href="{{ route('users.show', $expert) }}" class="block hover:opacity-80 transition-opacity">
                                                <img src="{{ $expert->avatar }}" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-purple-100">
                                                <div @class(['absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white flex items-center justify-center text-[8px] font-black text-white', 'bg-blue-600' => $expert->degree == 1, 'bg-purple-600' => $expert->degree == 2])>
                                                    {{ $expert->degree }}
                                                </div>
                                            </a>
                                        </div>
                                        <div class="flex-grow min-w-0">
                                            <div class="flex items-center justify-between gap-2 mb-1">
                                                <a href="{{ route('users.show', $expert) }}" class="min-w-0">
                                                    <h4 class="text-sm font-black text-slate-900 uppercase truncate hover:text-purple-600 transition-colors">
                                                        {{ $expert->name }}
                                                    </h4>
                                                </a>
                                                @if($project->canManage(auth()->user()))
                                                    @if($project->isInvited($expert))
                                                        <span class="text-[7px] font-black text-purple-600 bg-purple-50 px-2 py-0.5 rounded-lg border border-purple-100 uppercase italic">Invité</span>
                                                    @elseif($project->isPending($expert))
                                                        <span class="text-[7px] font-black text-orange-600 bg-orange-50 px-2 py-0.5 rounded-lg border border-orange-100 uppercase italic">Postulant</span>
                                                    @else
                                                        <button wire:click="inviteUser({{ $expert->id }})" class="text-[7px] font-black text-white bg-purple-600 hover:bg-purple-700 px-3 py-1 rounded-lg uppercase tracking-widest transition-all shadow-lg shadow-purple-500/20">
                                                            Inviter +
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                            
                                            <p class="text-[9px] font-black text-purple-700 uppercase tracking-tighter mb-2">{{ $expert->matchReason }}</p>

                                            <div class="flex flex-wrap gap-1 mb-3">
                                                @php
                                                    $expertAchievements = $expert->achievements->unique(fn($a) => $a->skill_id ?? $a->title)->take(3);
                                                @endphp
                                                @foreach($expertAchievements as $ach)
                                                    <a href="{{ $ach->skill_id ? route('projects.index', ['selectedSkills' => [$ach->skill_id]]) : '#' }}" class="text-[8px] font-black text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md uppercase tracking-tighter hover:bg-slate-900 hover:text-white transition-all">
                                                        {{ $ach->skill->name ?? $ach->title }}
                                                    </a>
                                                @endforeach
                                            </div>

                                            {{-- Trust Path --}}
                                            @if(!empty($expert->trustPath))
                                                <div class="flex items-center gap-1.5 pt-3 border-t border-slate-100 overflow-x-auto custom-scrollbar-h pb-1">
                                                    @foreach($expert->trustPath as $index => $node)
                                                        @if($index > 0)
                                                            <svg class="w-2 h-2 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                                                        @endif
                                                        <div class="flex items-center gap-1 flex-shrink-0">
                                                            @if($node['type'] === 'user')
                                                                <a href="{{ route('users.show', $node['id']) }}" class="flex items-center gap-1 hover:opacity-75 transition-opacity">
                                                                    <img src="{{ $node['avatar'] }}" class="w-4 h-4 rounded-full ring-1 ring-slate-200">
                                                                    <span class="text-[7px] font-black text-slate-600 uppercase">{{ $node['name'] }}</span>
                                                                </a>
                                                            @else
                                                                <a href="{{ route('circles.show', $node['id']) }}" class="flex items-center gap-1 hover:opacity-75 transition-opacity">
                                                                    <div class="w-4 h-4 bg-slate-100 rounded-full flex items-center justify-center">
                                                                        <svg class="w-2 h-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                    </div>
                                                                    <span class="text-[7px] font-black text-slate-400 uppercase">{{ $node['name'] }}</span>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 text-center bg-white/40 border-2 border-dashed border-slate-200 rounded-[3rem]">
                            <p class="text-slate-400 font-black uppercase tracking-[0.3em] text-[10px] italic">Aucun expert trouvé dans votre réseau proche pour les besoins de ce projet.</p>
                        </div>
                    @endif
                </section>
            @endif
        </div>
    </div>

    {{-- ===== DETACHED FORUM (BOTTOM) ===== --}}
    <div class="max-w-7xl mx-auto px-6 mt-12 pt-12 border-t border-slate-100">
        <div class="bg-slate-900 rounded-[3.5rem] p-10 shadow-2xl shadow-slate-900/20 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-blue-600/10 to-transparent"></div>

            <h2 class="text-3xl font-black mb-10 tracking-tight flex items-center gap-4 relative z-10">
                Le Forum
                <span class="px-3 py-1 bg-white/10 text-xs rounded-full border border-white/10 font-black">{{ $project->messages->count() }}</span>
            </h2>

            {{-- Message input --}}
            @auth
                <div class="relative z-10 p-4 bg-white/5 border border-white/10 rounded-[2.5rem] mb-10 group-focus-within:border-blue-500 transition-all">
                    <textarea wire:model="message" placeholder="Posez une question, partagez une info..."
                        class="w-full bg-transparent border-none focus:ring-0 text-sm text-slate-200 placeholder:text-slate-600 mb-4 resize-none"
                        rows="3"></textarea>
                    <button wire:click="sendMessage" class="w-full py-4 bg-white text-slate-900 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all shadow-xl shadow-white/5">
                        Envoyer au projet
                    </button>
                </div>
            @else
                <div class="relative z-10 text-center p-8 bg-white/5 border border-dashed border-white/10 rounded-3xl mb-10">
                    <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.3em]">Connectez-vous pour participer au forum</p>
                </div>
            @endauth

            {{-- Messages feed --}}
            <div class="space-y-6 max-h-[600px] overflow-y-auto pr-4 custom-scrollbar relative z-10">
                @forelse($project->messages as $msg)
                    <div class="flex flex-col gap-2 animate-in fade-in slide-in-from-bottom-2 duration-300">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('users.show', $msg->sender) }}" class="flex items-center gap-3 group/msg hover:opacity-80 transition-all">
                                <img src="{{ $msg->sender->avatar }}" class="w-6 h-6 rounded-lg ring-1 ring-white/20 group-hover/msg:ring-blue-500 transition-all">
                                <span class="text-xs font-black uppercase tracking-widest text-slate-400 group-hover/msg:text-blue-400 transition-colors">{{ $msg->sender->name }}</span>
                                <span class="text-[9px] font-black text-slate-600 uppercase">{{ $msg->created_at->diffForHumans() }}</span>
                            </a>
                        </div>
                        <div class="bg-white/5 border border-white/10 p-5 rounded-2xl rounded-tl-none text-slate-300 text-sm leading-relaxed italic">
                            {{ $msg->content }}
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-600 font-black uppercase tracking-[0.3em] text-[10px]">Silence radio... soyez le premier à parler !</div>
                @endforelse
            </div>
        </div>

    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
        .custom-scrollbar-h::-webkit-scrollbar { height: 3px; }
        .custom-scrollbar-h::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar-h::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
        .back-to-overview {
            scroll-margin-top: 160px; /* Header (100px) + Comfortable Gap */
        }
    </style>
    <script>
        function projectShowData(entangledTab) {
            return {
                activeTab: entangledTab,
                lastScrollPos: 0,
                showIndicator: false,
                switchTab(tab) {
                    if (tab === 'overview') {
                        this.activeTab = 'overview';
                        this.$nextTick(() => {
                            window.scrollTo({ 
                                top: this.lastScrollPos, 
                                behavior: 'smooth' 
                            });
                        });
                    } else {
                        this.lastScrollPos = window.pageYOffset;
                        this.activeTab = tab;
                        this.$nextTick(() => {
                            const el = document.querySelector('[x-show="activeTab === \'' + tab + '\'"] .back-to-overview');
                            if (el) {
                                el.scrollIntoView({ behavior: 'smooth' });
                            } else {
                                const fallBack = document.getElementById('tab-content-start');
                                if (fallBack) fallBack.scrollIntoView({ behavior: 'smooth' });
                            }
                        });
                    }
                },
                playNotify() {
                    let audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2358/2358-preview.mp3');
                    audio.volume = 0.2;
                    audio.play().catch(e => console.log('Audio play failed:', e));
                }
            }
        }
    </script>
</div>
