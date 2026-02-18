<div 
    x-data="{ 
        activeTab: @entangle('activeTab'),
        showIndicator: false,
        playNotify() {
            let audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2358/2358-preview.mp3');
            audio.volume = 0.2;
            audio.play().catch(e => console.log('Audio play failed:', e));
        }
    }" 
    x-on:project-updated.window="showIndicator = true; playNotify(); setTimeout(() => showIndicator = false, 3000)"
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
                            <p class="text-xl text-slate-500 font-medium max-w-2xl leading-relaxed mb-8">
                                {{ $project->description }}
                            </p>
                        @endif

                        {{-- Stats Bar --}}
                        <div class="grid grid-cols-4 gap-4 md:gap-8 py-8 border-t border-slate-100">
                            <button @click="activeTab = 'team'" class="text-center md:text-left hover:opacity-80 transition-opacity">
                                <div class="text-xl md:text-3xl font-black text-slate-900 leading-none mb-1">{{ $project->activeMembers->count() }}</div>
                                <div class="text-[8px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest">Membres</div>
                            </button>
                            <button @click="activeTab = 'offers'" class="text-center md:text-left hover:opacity-80 transition-opacity">
                                <div class="text-xl md:text-3xl font-black text-blue-600 leading-none mb-1">{{ $project->offers->count() }}</div>
                                <div class="text-[8px] md:text-[10px] font-black text-blue-400 uppercase tracking-widest">Offres</div>
                            </button>
                            <button @click="activeTab = 'demands'" class="text-center md:text-left hover:opacity-80 transition-opacity">
                                <div class="text-xl md:text-3xl font-black text-purple-600 leading-none mb-1">{{ $project->demands->count() }}</div>
                                <div class="text-[8px] md:text-[10px] font-black text-purple-400 uppercase tracking-widest">Demandes</div>
                            </button>
                            <button @click="activeTab = 'reviews'" class="text-center md:text-left hover:opacity-80 transition-opacity">
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

    {{-- ===== TABS NAV ===== --}}
    <div class="max-w-7xl mx-auto px-6 mb-8">
        <div class="flex gap-2 overflow-x-auto pb-2 custom-scrollbar-h">
            @foreach([
                ['key' => 'overview',  'label' => 'Vue d\'ensemble', 'icon' => 'M4 6h16M4 12h16M4 18h7'],
                ['key' => 'offers',    'label' => 'Offres ('.$project->offers->count().')', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
                ['key' => 'demands',   'label' => 'Demandes ('.$project->demands->count().')', 'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['key' => 'reviews',   'label' => 'Avis ('.$project->reviews->count().')', 'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
                ['key' => 'team',      'label' => 'Équipe', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ] as $tab)
                <button
                    @click="activeTab = '{{ $tab['key'] }}'"
                    :class="activeTab === '{{ $tab['key'] }}' ? 'bg-slate-900 text-white shadow-xl' : 'bg-white/60 text-slate-500 hover:bg-white hover:text-slate-900'"
                    class="flex-shrink-0 flex items-center gap-2 px-5 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all backdrop-blur-xl border border-white/60"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/></svg>
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ===== TAB CONTENT ===== --}}
    <div class="max-w-7xl mx-auto px-6">

        {{-- ---- OVERVIEW ---- --}}
        <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Offres preview --}}
                <div class="bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[3rem] p-8 shadow-xl shadow-blue-500/5">
                    <h3 class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                        Ce que nous offrons
                    </h3>
                    @forelse($project->offers->take(3) as $offer)
                        <div class="mb-4 p-4 bg-blue-50/50 rounded-2xl border border-blue-100">
                            <div class="font-black text-sm text-slate-900 uppercase tracking-tight mb-1">{{ $offer->title }}</div>
                            @if($offer->description)
                                <p class="text-[10px] text-slate-500 font-medium leading-relaxed">{{ $offer->description }}</p>
                            @endif
                            @if($offer->skills->count() > 0)
                                <div class="flex flex-wrap gap-1 mt-2">
                                    @foreach($offer->skills as $skill)
                                        <a href="{{ route('projects.index', ['selectedSkills' => [$skill->id]]) }}" class="text-[8px] font-black text-blue-600 bg-blue-100 px-2 py-0.5 rounded-lg uppercase hover:bg-blue-600 hover:text-white transition-all">
                                            {{ $skill->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
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
                <div class="bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[3rem] p-8 shadow-xl shadow-purple-500/5">
                    <h3 class="text-[10px] font-black text-purple-600 uppercase tracking-[0.3em] mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                        Ce que nous cherchons
                    </h3>
                    @forelse($project->demands->take(3) as $demand)
                        <div class="mb-4 p-4 bg-purple-50/50 rounded-2xl border border-purple-100">
                            <div class="font-black text-sm text-slate-900 uppercase tracking-tight mb-1">{{ $demand->title }}</div>
                            @if($demand->description)
                                <p class="text-[10px] text-slate-500 font-medium leading-relaxed">{{ $demand->description }}</p>
                            @endif
                            @if($demand->skills->count() > 0)
                                <div class="flex flex-wrap gap-1 mt-2">
                                    @foreach($demand->skills as $skill)
                                        <a href="{{ route('projects.index', ['selectedSkills' => [$skill->id]]) }}" class="text-[8px] font-black text-purple-600 bg-purple-100 px-2 py-0.5 rounded-lg uppercase hover:bg-purple-600 hover:text-white transition-all">
                                            {{ $skill->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
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
                    <div class="md:col-span-2 bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[3rem] p-8 shadow-xl">
                        <h3 class="text-[10px] font-black text-green-600 uppercase tracking-[0.3em] mb-6 flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            Derniers Avis
                            <span class="ml-auto text-slate-300">{{ $project->getPositiveReviewsCount() }}✓ {{ $project->getNegativeReviewsCount() }}✗</span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($project->reviews->take(4) as $review)
                                <div @class(['p-4 rounded-2xl border', 'bg-green-50/50 border-green-100' => $review->type === 'validate', 'bg-red-50/50 border-red-100' => $review->type === 'reject'])>
                                    <div class="flex items-center gap-3 mb-2">
                                        <a href="{{ route('users.show', $review->user) }}" class="flex items-center gap-3 group/reviewer hover:opacity-80 transition-all">
                                            <img src="{{ $review->user->avatar }}" class="w-8 h-8 rounded-xl object-cover ring-1 ring-slate-100 group-hover/reviewer:ring-blue-500 transition-all">
                                            <div>
                                                <div class="text-[10px] font-black text-slate-900 uppercase group-hover/reviewer:text-blue-600 transition-colors">{{ $review->user->name }}</div>
                                                <span @class(['text-[7px] font-black uppercase px-2 py-0.5 rounded-full', 'bg-green-600 text-white' => $review->type === 'validate', 'bg-red-600 text-white' => $review->type === 'reject'])>
                                                    {{ $review->type === 'validate' ? '✓ Validé' : '✗ Rejeté' }}
                                                </span>
                                            </div>
                                        </a>
                                    </div>
                                    @if($review->comment)
                                        <p class="text-[10px] font-medium text-slate-600 italic leading-relaxed">"{{ $review->comment }}"</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ---- OFFRES ---- --}}
        <div x-show="activeTab === 'offers'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            @if($project->canManage(auth()->user()))
                <div class="mb-8">
                    @if(!$showOfferForm)
                        <button wire:click="$set('showOfferForm', true)" class="w-full py-6 bg-white border-2 border-dashed border-blue-200 text-blue-600 rounded-[2.5rem] font-black text-[10px] uppercase tracking-[0.3em] hover:bg-blue-50 transition-all flex items-center justify-center gap-4 group">
                            <span class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all">+</span>
                            Ajouter une Offre (Petit à Petit)
                        </button>
                    @else
                        <div class="bg-slate-900 rounded-[3rem] p-8 text-white shadow-2xl animate-in fade-in slide-in-from-top-4 duration-300 relative overflow-hidden">
                             <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/10 rounded-full blur-3xl"></div>
                             <h4 class="text-xs font-black uppercase tracking-[0.2em] mb-6 text-blue-400 relative z-10 flex items-center justify-between">
                                 <span>{{ $editingOfferId ? '🚀 Modifier l\'Offre' : '🚀 Nouvelle Offre' }}</span>
                                 @if($editingOfferId)
                                     <button wire:click="$set('editingOfferId', null); $set('showOfferForm', false)" class="text-[9px] text-slate-500 hover:text-white transition-colors">Annuler</button>
                                 @endif
                             </h4>
                             <div class="space-y-6 relative z-10">
                                 <input wire:model="offerTitle" type="text" placeholder="Titre de l'offre (ex: Maintenance logicielle...)" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-sm font-bold placeholder:text-slate-600 focus:border-blue-500 transition-all uppercase tracking-tight">
                                 <textarea wire:model="offerDescription" rows="3" placeholder="Description détaillée..." class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-xs font-medium placeholder:text-slate-600 focus:border-blue-500 transition-all italic"></textarea>
                                 
                                 <div x-data="{ 
                                     open: false,
                                     search: @entangle('skillSearch')
                                 }" class="relative">
                                     <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-3 block italic">Compétences (Saisie intelligente)</label>
                                     <div class="flex flex-wrap gap-2 p-4 bg-white/5 border border-white/10 rounded-2xl min-h-[60px] focus-within:border-blue-500 transition-all">
                                         @foreach($selectedSkills as $skillName)
                                             <span class="inline-flex items-center gap-2 bg-blue-600 text-white px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest animate-in scale-in-center duration-200">
                                                 {{ $skillName }}
                                                 <button wire:click="removeSkill('{{ $skillName }}')" class="hover:text-red-200">
                                                     <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                 </button>
                                             </span>
                                         @endforeach
                                         <input 
                                             type="text" 
                                             wire:model.live="skillSearch" 
                                             placeholder="{{ empty($selectedSkills) ? 'Ex: Marketing, SEO...' : 'Ajouter...' }}"
                                             @keydown.enter.prevent="$wire.addSkill()"
                                             @focus="open = true"
                                             class="flex-grow bg-transparent border-none focus:ring-0 text-xs font-black text-white p-0 uppercase placeholder:text-slate-700"
                                         >
                                     </div>

                                     {{-- Suggestions dropdown --}}
                                     @if($skillSearch && count($skills->filter(fn($s) => str_contains(strtolower($s->name), strtolower($skillSearch)))))
                                         <div x-show="open" @click.away="open = false" class="absolute z-50 w-full mt-2 bg-slate-800 border border-white/10 rounded-2xl shadow-2xl max-h-48 overflow-y-auto custom-scrollbar">
                                             @foreach($skills->filter(fn($s) => str_contains(strtolower($s->name), strtolower($skillSearch))) as $s)
                                                 <button wire:click="addSkill('{{ $s->name }}')" class="w-full text-left px-5 py-3 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:bg-white/10 hover:text-white transition-all">
                                                     {{ $s->name }}
                                                 </button>
                                             @endforeach
                                         </div>
                                     @endif
                                 </div>

                                 <div class="flex gap-4">
                                     <button wire:click="addOffer" class="flex-grow py-4 bg-blue-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 shadow-lg shadow-blue-500/20 transition-all">Enregistrer l'offre</button>
                                     <button wire:click="$set('showOfferForm', false)" class="px-8 py-4 bg-white/10 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-white/20 transition-all">Annuler</button>
                                 </div>
                             </div>
                        </div>
                    @endif
                </div>
            @endif
            <div class="space-y-4">
                @forelse($project->offers as $offer)
                    <div class="bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[2.5rem] p-8 shadow-xl shadow-blue-500/5 hover:shadow-2xl hover:shadow-blue-500/10 transition-all">
                        <div class="flex items-start gap-6">
                            <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center text-white font-black text-xl flex-shrink-0">
                                {{ substr($offer->title, 0, 1) }}
                            </div>
                            <div class="flex-grow">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">{{ $offer->title }}</h3>
                                    @if($project->canManage(auth()->user()))
                                        <div class="flex gap-2">
                                            <button wire:click="editOffer({{ $offer->id }})" class="p-2 bg-slate-100 rounded-xl hover:bg-blue-600 hover:text-white transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button wire:click="deleteOffer({{ $offer->id }})" wire:confirm="Supprimer cette offre ?" class="p-2 bg-slate-100 rounded-xl hover:bg-red-600 hover:text-white transition-all text-red-500">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                @if($offer->description)
                                    <p class="text-slate-500 font-medium leading-relaxed mb-4">{{ $offer->description }}</p>
                                @endif
                                @if($offer->skills->count() > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($offer->skills as $skill)
                                            <a href="{{ route('projects.index', ['selectedSkills' => [$skill->id]]) }}" class="text-[9px] font-black text-blue-600 bg-blue-50 border border-blue-100 px-3 py-1 rounded-xl uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all">
                                                {{ $skill->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-20 text-center bg-white/40 border-2 border-dashed border-slate-200 rounded-[3rem]">
                        <p class="text-slate-400 font-black uppercase tracking-[0.3em] text-sm italic">Aucune offre définie pour ce projet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ---- DEMANDES ---- --}}
        <div x-show="activeTab === 'demands'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            @if($project->canManage(auth()->user()))
                <div class="mb-8">
                    @if(!$showDemandForm)
                        <button wire:click="$set('showDemandForm', true)" class="w-full py-6 bg-white border-2 border-dashed border-purple-200 text-purple-600 rounded-[2.5rem] font-black text-[10px] uppercase tracking-[0.3em] hover:bg-purple-50 transition-all flex items-center justify-center gap-4 group">
                            <span class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-all">+</span>
                            Ajouter une Demande (Petit à Petit)
                        </button>
                    @else
                        <div class="bg-slate-900 rounded-[3rem] p-8 text-white shadow-2xl animate-in fade-in slide-in-from-top-4 duration-300 relative overflow-hidden">
                             <div class="absolute top-0 right-0 w-32 h-32 bg-purple-600/10 rounded-full blur-3xl"></div>
                             <h4 class="text-xs font-black uppercase tracking-[0.2em] mb-6 text-purple-400 relative z-10 flex items-center justify-between">
                                 <span>{{ $editingDemandId ? '🔍 Modifier la Demande' : '🔍 Nouvelle Demande' }}</span>
                                 @if($editingDemandId)
                                     <button wire:click="$set('editingDemandId', null); $set('showDemandForm', false)" class="text-[9px] text-slate-500 hover:text-white transition-colors">Annuler</button>
                                 @endif
                             </h4>
                             <div class="space-y-6 relative z-10">
                                 <input wire:model="demandTitle" type="text" placeholder="Ce que vous cherchez..." class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-sm font-bold placeholder:text-slate-600 focus:border-purple-500 transition-all uppercase tracking-tight">
                                 <textarea wire:model="demandDescription" rows="3" placeholder="Détails de la demande..." class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-xs font-medium placeholder:text-slate-600 focus:border-purple-500 transition-all italic"></textarea>
                                 
                                 <div x-data="{ 
                                     open: false,
                                     search: @entangle('skillSearch')
                                 }" class="relative">
                                     <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-3 block italic">Compétences recherchées (Saisie intelligente)</label>
                                     <div class="flex flex-wrap gap-2 p-4 bg-white/5 border border-white/10 rounded-2xl min-h-[60px] focus-within:border-purple-500 transition-all">
                                         @foreach($selectedSkills as $skillName)
                                             <span class="inline-flex items-center gap-2 bg-purple-600 text-white px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest animate-in scale-in-center duration-200">
                                                 {{ $skillName }}
                                                 <button wire:click="removeSkill('{{ $skillName }}')" class="hover:text-red-200">
                                                     <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                 </button>
                                             </span>
                                         @endforeach
                                         <input 
                                             type="text" 
                                             wire:model.live="skillSearch" 
                                             placeholder="{{ empty($selectedSkills) ? 'Ex: React, Design UI...' : 'Ajouter...' }}"
                                             @keydown.enter.prevent="$wire.addSkill()"
                                             @focus="open = true"
                                             class="flex-grow bg-transparent border-none focus:ring-0 text-xs font-black text-white p-0 uppercase placeholder:text-slate-700"
                                         >
                                     </div>

                                     {{-- Suggestions dropdown --}}
                                     @if($skillSearch && count($skills->filter(fn($s) => str_contains(strtolower($s->name), strtolower($skillSearch)))))
                                         <div x-show="open" @click.away="open = false" class="absolute z-50 w-full mt-2 bg-slate-800 border border-white/10 rounded-2xl shadow-2xl max-h-48 overflow-y-auto custom-scrollbar">
                                             @foreach($skills->filter(fn($s) => str_contains(strtolower($s->name), strtolower($skillSearch))) as $s)
                                                 <button wire:click="addSkill('{{ $s->name }}')" class="w-full text-left px-5 py-3 text-[10px] font-black uppercase tracking-widest text-slate-300 hover:bg-white/10 hover:text-white transition-all">
                                                     {{ $s->name }}
                                                 </button>
                                             @endforeach
                                         </div>
                                     @endif
                                 </div>

                                 <div class="flex gap-4">
                                     <button wire:click="addDemand" class="flex-grow py-4 bg-purple-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-purple-700 shadow-lg shadow-purple-500/20 transition-all">Enregistrer la demande</button>
                                     <button wire:click="$set('showDemandForm', false)" class="px-8 py-4 bg-white/10 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-white/20 transition-all">Annuler</button>
                                 </div>
                             </div>
                        </div>
                    @endif
                </div>
            @endif
            <div class="space-y-4">
                @forelse($project->demands as $demand)
                    <div class="bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[2.5rem] p-8 shadow-xl shadow-purple-500/5 hover:shadow-2xl hover:shadow-purple-500/10 transition-all">
                        <div class="flex items-start gap-6">
                            <div class="w-14 h-14 bg-purple-600 rounded-2xl flex items-center justify-center text-white font-black text-xl flex-shrink-0">
                                {{ substr($demand->title, 0, 1) }}
                            </div>
                            <div class="flex-grow">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">{{ $demand->title }}</h3>
                                    @if($project->canManage(auth()->user()))
                                        <div class="flex gap-2">
                                            <button wire:click="editDemand({{ $demand->id }})" class="p-2 bg-slate-100 rounded-xl hover:bg-purple-600 hover:text-white transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button wire:click="deleteDemand({{ $demand->id }})" wire:confirm="Supprimer cette demande ?" class="p-2 bg-slate-100 rounded-xl hover:bg-red-600 hover:text-white transition-all text-red-500">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                @if($demand->description)
                                    <p class="text-slate-500 font-medium leading-relaxed mb-4">{{ $demand->description }}</p>
                                @endif
                                @if($demand->skills->count() > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($demand->skills as $skill)
                                            <a href="{{ route('projects.index', ['selectedSkills' => [$skill->id]]) }}" class="text-[9px] font-black text-purple-600 bg-purple-50 border border-purple-100 px-3 py-1 rounded-xl uppercase tracking-widest hover:bg-purple-600 hover:text-white transition-all">
                                                {{ $skill->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-20 text-center bg-white/40 border-2 border-dashed border-slate-200 rounded-[3rem]">
                        <p class="text-slate-400 font-black uppercase tracking-[0.3em] text-sm italic">Aucune demande définie pour ce projet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ---- AVIS (REVIEWS) ---- --}}
        <div x-show="activeTab === 'reviews'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Review Form --}}
                @auth
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
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Votre retour (min. 10 caractères)</label>
                                <textarea wire:model="reviewComment" rows="4" placeholder="Partagez votre expérience avec ce projet..."
                                    class="w-full bg-white/5 border border-white/10 focus:border-blue-500 focus:ring-0 rounded-2xl p-4 text-sm text-slate-200 placeholder:text-slate-600 resize-none"></textarea>
                                @error('reviewComment') <span class="text-red-400 text-[9px] font-black uppercase mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <button wire:click="submitReview" @class(['w-full py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-lg', 'bg-green-600 hover:bg-green-700 shadow-green-500/20' => $reviewType === 'validate', 'bg-red-600 hover:bg-red-700 shadow-red-500/20' => $reviewType === 'reject'])>
                                Publier mon avis
                            </button>
                        </div>
                    </div>
                @endauth

                {{-- Reviews List --}}
                <div class="{{ auth()->check() ? 'lg:col-span-2' : 'lg:col-span-3' }} space-y-4">
                    @forelse($project->reviews as $review)
                        <div @class(['p-6 rounded-[2rem] border transition-all', 'bg-green-50/50 border-green-100' => $review->type === 'validate', 'bg-red-50/50 border-red-100' => $review->type === 'reject'])>
                            <div class="flex items-center gap-4 mb-4">
                                <a href="{{ route('users.show', $review->user) }}" class="hover:scale-110 transition-transform">
                                    <img src="{{ $review->user->avatar }}" class="w-12 h-12 rounded-2xl object-cover ring-4 ring-white shadow-md">
                                </a>
                                <div class="flex-grow">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('users.show', $review->user) }}" class="text-sm font-black text-slate-900 uppercase hover:text-blue-600 transition-colors">{{ $review->user->name }}</a>
                                        <span @class(['text-[8px] font-black uppercase px-2 py-0.5 rounded-full', 'bg-green-600 text-white' => $review->type === 'validate', 'bg-red-600 text-white' => $review->type === 'reject'])>
                                            {{ $review->type === 'validate' ? '✓ Validé' : '✗ Rejeté' }}
                                        </span>
                                    </div>
                                    <div class="text-[9px] font-black text-slate-400 uppercase mt-1">{{ $review->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            @if($review->comment)
                                <p class="text-sm font-medium text-slate-700 italic leading-relaxed">"{{ $review->comment }}"</p>
                            @endif

                            {{-- Replies --}}
                            @if($review->replies->count() > 0)
                                <div class="mt-4 pl-6 border-l-2 border-white/80 space-y-3">
                                    @foreach($review->replies as $reply)
                                        <div class="p-4 bg-white/60 rounded-2xl">
                                            <div class="flex items-center gap-2 mb-2">
                                                <a href="{{ route('users.show', $reply->user) }}" class="flex items-center gap-2 group/reply hover:opacity-80 transition-all">
                                                    <img src="{{ $reply->user->avatar }}" class="w-6 h-6 rounded-lg object-cover ring-1 ring-white/20 group-hover/reply:ring-blue-500 transition-all">
                                                    <span class="text-[9px] font-black text-slate-900 uppercase group-hover/reply:text-blue-600 transition-colors">{{ $reply->user->name }}</span>
                                                    <span class="text-[7px] font-black text-blue-600 uppercase tracking-widest">Réponse</span>
                                                </a>
                                            </div>
                                            @if($reply->comment)
                                                <p class="text-[10px] font-medium text-slate-600 italic">"{{ $reply->comment }}"</p>
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
    </style>
</div>
