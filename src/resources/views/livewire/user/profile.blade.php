<div class="min-h-screen bg-slate-50/50 pb-12 pt-6">
    <!-- User Header Portfolio -->
    <div class="max-w-7xl mx-auto px-6 mb-12">
        <div class="bg-white/40 backdrop-blur-3xl border border-white/60 rounded-[4rem] p-8 md:p-12 shadow-2xl shadow-blue-500/5 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="flex flex-col md:flex-row items-center gap-8 md:gap-12">
                    <div class="relative flex-shrink-0">
                        <div class="absolute inset-0 bg-blue-600/20 rounded-[3rem] md:rounded-[3.5rem] blur-2xl group-hover:blur-3xl transition-all duration-700"></div>
                        <img src="{{ $user->avatar }}" class="relative w-40 h-40 md:w-64 md:h-64 rounded-[3rem] md:rounded-[3.5rem] object-cover border-4 border-white shadow-2xl transition-transform duration-700 group-hover:scale-[1.02]">
                        <div class="absolute -bottom-2 -right-2 md:-bottom-4 md:-right-4 w-16 h-16 md:w-20 md:h-20 bg-slate-900 rounded-2xl md:rounded-3xl flex flex-col items-center justify-center shadow-2xl border-4 border-white">
                            <span class="text-[8px] md:text-xs font-black text-blue-400 uppercase tracking-widest leading-none mb-1">Score</span>
                            <span class="text-2xl md:text-3xl font-black text-white leading-none">{{ $user->trust_score }}</span>
                        </div>
                    </div>

                    <div class="flex-grow text-center md:text-left">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-4 border border-blue-100">
                            Profil Vérifié
                        </div>
                        <div class="flex flex-col md:flex-row items-center justify-center md:justify-start gap-4 mb-4">
                            <h1 class="text-3xl md:text-7xl font-black text-slate-900 tracking-tighter leading-none">{{ $user->name }}</h1>
                            <x-project-transporter :project="$activeProject" />
                            <livewire:information.manager :model="$user" :key="'user-info-'.$user->id" />
                        </div>
                        <p class="text-slate-500 font-medium text-base md:text-xl max-w-2xl leading-relaxed italic px-4 md:px-0 mb-6">
                            "{{ $user->bio ?? 'Ce bâtisseur de confiance n\'a pas encore rédigé sa bio.' }}"
                        </p>
                        @auth
                            @php $trustPath = auth()->user()->getTrustPathTo($user); @endphp
                            @if(count($trustPath) > 0)
                                <div class="mt-4 mb-2">
                                    <x-user-trust-chain :path="$trustPath" />
                                </div>
                            @endif
                        @endauth
                        
                        <div class="mt-8">
                            @auth
                                @if(auth()->id() !== $user->id)
                                    <div class="flex gap-4">
                                        <button class="flex-grow px-6 py-4 bg-slate-900 text-white rounded-[2rem] font-black text-sm tracking-widest uppercase hover:bg-blue-600 transition-all shadow-xl">
                                            Message
                                        </button>
                                    </div>
                                @else
                                    <div class="space-y-4">
                                        <!-- Primary Platform Actions -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <a href="{{ route('circles.create') }}" class="px-6 py-5 bg-slate-900 border-2 border-slate-900 text-white rounded-[2.5rem] font-black text-xs tracking-[0.2em] uppercase hover:bg-blue-600 hover:border-blue-600 transition-all flex items-center justify-center gap-3 shadow-2xl shadow-blue-500/30 group/circle">
                                                <svg class="w-5 h-5 group-hover/circle:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 0012 20a10.003 10.003 0 006.235-2.197m-2.322-9.047a7.334 7.334 0 011.129 3.125m-1.282-3.125a10 10 0 11-14.703 0m14.703 0c-1.347-1.625-3.323-2.651-5.547-2.651-2.224 0-4.2 1.026-5.547 2.651"/></svg>
                                                Nouveau Cercle
                                            </a>
                                        </div>

                                        <!-- Secondary Profile Actions -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            @if($user->id === auth()->id())
                                                <button wire:click="openProcheModal" class="px-6 py-4 bg-slate-900 text-white rounded-[2rem] font-black text-xs tracking-widest uppercase hover:bg-blue-600 transition-all shadow-xl flex items-center justify-center gap-3">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                                    Nouveau Proche
                                                </button>
                                            @endif
                                            
                                            
                                            @if($user->id === auth()->id())
                                                <a href="{{ route('profile.edit') }}" class="px-6 py-4 bg-white border-2 border-slate-100 text-slate-400 rounded-[2rem] font-black text-[10px] tracking-widest uppercase hover:border-slate-300 hover:text-slate-900 transition-all flex items-center justify-center gap-3">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    Paramètres
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endauth
                        </div>

                    </div>
                </div>
            </div>
            <div class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-blue-400/10 to-transparent"></div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-12">
        <!-- Sidebar -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Stats -->
            <div class="bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[3.5rem] p-8 shadow-2xl shadow-blue-500/5">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-8">Métriques de Confiance</h3>
                <div class="space-y-8">
                    <div>
                        <div class="flex justify-between items-end mb-4">
                            <span class="text-xs font-black text-slate-900 uppercase tracking-widest">Trust Index</span>
                            <span class="text-4xl font-black text-slate-900 tracking-tighter">{{ $user->trust_score }}<span class="text-slate-200">%</span></span>
                        </div>
                        <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden p-0.5 border border-slate-50">
                            <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full shadow-lg shadow-blue-500/20" style="width: {{ $user->trust_score }}%"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4 pt-8 border-t border-slate-50">
                        <div class="p-6 bg-green-50/50 rounded-3xl border border-green-100/50">
                            <div class="text-2xl font-black text-green-600 leading-none mb-1">+{{ $valCount }}</div>
                            <div class="text-[9px] font-black text-green-600/60 uppercase tracking-widest">Validations</div>
                        </div>
                        <div class="p-6 bg-red-50/50 rounded-3xl border border-red-100/50">
                            <div class="text-2xl font-black text-red-600 leading-none mb-1">-{{ $rejCount }}</div>
                            <div class="text-[9px] font-black text-red-600/60 uppercase tracking-widest">Réfutations</div>
                        </div>
                        <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100">
                            <div class="text-2xl font-black text-slate-900 leading-none mb-1">{{ $user->joinedCircles->count() }}</div>
                            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Cercles</div>
                        </div>
                    </div>
                </div>

                <!-- CV Action Block -->
                <div class="mt-8">
                    <a href="{{ route('cv.user', $user) }}" target="_blank" class="flex items-center justify-between p-6 bg-slate-900 text-white rounded-[2.5rem] group hover:bg-blue-600 transition-all shadow-2xl shadow-slate-900/20">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center group-hover:bg-white group-hover:text-blue-600 transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4"/></svg>
                            </div>
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60">Expertise Export</div>
                                <div class="text-sm font-black uppercase">Générer mon CV</div>
                            </div>
                        </div>
                        <svg class="w-5 h-5 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>

            {{-- ===== MES PROJETS ===== --}}
            @if($userProjects->count() > 0)
                <div class="bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[3.5rem] p-8 shadow-2xl shadow-blue-500/5">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight flex items-center gap-3">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Projets
                                <span class="text-[10px] font-black text-slate-400 border border-slate-200 px-2 py-0.5 rounded-full">{{ $userProjects->count() }}</span>
                            </h3>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">Offres & Demandes de services</p>
                        </div>
                        @auth
                            @if(auth()->id() === $user->id)
                                <div class="flex items-center gap-2">
                                    <button wire:click="startCreatingProject('offer')" 
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20"
                                            title="Proposer une Offre">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                        Offre
                                    </button>
                                    <button wire:click="startCreatingProject('demand')" 
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg shadow-blue-500/20"
                                            title="Exprimer un Besoin">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        Besoin
                                    </button>
                                </div>
                            @endif
                        @endauth
                    </div>

                    @if($isCreatingProject)
                        <div class="mb-8 bg-white/80 backdrop-blur-xl border-2 border-blue-500 p-8 rounded-[3.5rem] shadow-2xl animate-in fade-in slide-in-from-top-4 duration-300">
                            <div class="flex items-center justify-between mb-4 px-2">
                                <span class="text-[12px] font-black text-blue-600 uppercase tracking-widest">
                                    {{ $projectType === 'offer' ? 'Proposer une Offre' : 'Exprimer un Besoin' }}
                                </span>
                                <button wire:click="cancelProjectCreation" class="text-slate-400 hover:text-red-500 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="relative">
                                <input wire:model="projectTitle" 
                                       wire:keydown.enter="confirmProjectCreation"
                                       type="text" 
                                       placeholder="Titre de votre {{ $projectType === 'offer' ? 'offre' : 'besoin' }}..." 
                                       autofocus
                                       class="w-full bg-white border-2 border-slate-100 focus:border-blue-500 focus:ring-0 rounded-3xl p-6 text-xl font-black tracking-tight placeholder:text-slate-300 shadow-inner">
                                <button wire:click="confirmProjectCreation" class="absolute right-3 top-3 bottom-3 px-8 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                                    Lancer le Projet
                                </button>
                            </div>
                            @error('projectTitle') <span class="text-red-500 text-[10px] font-black uppercase mt-3 block pl-6">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($userProjects as $project)
                            <div class="relative group">
                                <a href="{{ route('projects.show', $project) }}" class="block bg-gradient-to-br from-white/80 to-white/40 backdrop-blur-xl border border-white/60 p-5 rounded-3xl hover:shadow-2xl hover:shadow-blue-500/10 hover:scale-[1.02] transition-all duration-300">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-base font-black text-slate-900 uppercase tracking-tight truncate group-hover:text-blue-600 transition-colors">
                                                {{ $project->title }}
                                            </h4>
                                            @if($project->description)
                                                <p class="text-[10px] font-medium text-slate-500 mt-1 line-clamp-2">{{ $project->description }}</p>
                                            @endif
                                        </div>
                                        <div class="ml-3 flex-shrink-0">
                                            @if($project->is_open)
                                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-50 border border-green-200 rounded-lg">
                                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                                    <span class="text-[8px] font-black text-green-700 uppercase">Ouvert</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg">
                                                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                                                    <span class="text-[8px] font-black text-slate-500 uppercase">Fermé</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-4 gap-2 mb-4">
                                        <div class="bg-white/60 rounded-xl p-2 text-center border border-slate-100">
                                            <div class="text-xs font-black text-slate-900">{{ $project->activeMembers->count() }}</div>
                                            <div class="text-[7px] font-black text-slate-400 uppercase">Membres</div>
                                        </div>
                                        <div class="bg-blue-50/60 rounded-xl p-2 text-center border border-blue-100">
                                            <div class="text-xs font-black text-blue-600">{{ $project->offers->count() }}</div>
                                            <div class="text-[7px] font-black text-blue-400 uppercase">Offres</div>
                                        </div>
                                        <div class="bg-purple-50/60 rounded-xl p-2 text-center border border-purple-100">
                                            <div class="text-xs font-black text-purple-600">{{ $project->demands->count() }}</div>
                                            <div class="text-[7px] font-black text-purple-400 uppercase">Demandes</div>
                                        </div>
                                        <div class="bg-green-50/60 rounded-xl p-2 text-center border border-green-100">
                                            <div class="text-xs font-black text-green-600">+{{ $project->reviews->where('type', 'validate')->count() }}</div>
                                            <div class="text-[7px] font-black text-green-400 uppercase">Avis</div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 pt-3 border-t border-slate-100">
                                        <img src="{{ $project->owner->avatar }}" class="w-6 h-6 rounded-lg object-cover border border-slate-200">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-[9px] font-black text-slate-900 uppercase truncate">{{ $project->owner->name }}</div>
                                            <div class="text-[7px] font-black text-slate-400 uppercase">{{ $project->owner_id === $user->id ? 'Propriétaire' : 'Membre' }}</div>
                                        </div>
                                        <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </div>
                                </a>

                                {{-- Toggle button for owner/admin --}}
                                @auth
                                    @if($project->canManage(auth()->user()))
                                        <button wire:click="toggleProjectStatus({{ $project->id }})" 
                                            class="absolute top-3 left-3 px-2 py-1 text-[7px] font-black uppercase tracking-widest rounded-lg transition-all {{ $project->is_open ? 'bg-green-100 text-green-700 hover:bg-red-100 hover:text-red-700' : 'bg-slate-100 text-slate-500 hover:bg-green-100 hover:text-green-700' }}"
                                            title="{{ $project->is_open ? 'Fermer le projet' : 'Ouvrir le projet' }}">
                                            {{ $project->is_open ? '● Fermer' : '○ Ouvrir' }}
                                        </button>
                                    @endif
                                @endauth
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif(auth()->check() && auth()->id() === $user->id)
                <div class="bg-white/40 backdrop-blur-3xl border-2 border-dashed border-slate-200 rounded-[3.5rem] p-10 text-center">
                    @if($isCreatingProject)
                        <div class="max-w-md mx-auto">
                            <div class="flex items-center justify-between mb-4 px-2">
                                <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">
                                    {{ $projectType === 'offer' ? 'Nouvelle Offre' : 'Nouveau Besoin' }}
                                </span>
                                <button wire:click="cancelProjectCreation" class="text-slate-400 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="relative">
                                <input wire:model="projectTitle" 
                                       wire:keydown.enter="confirmProjectCreation"
                                       type="text" 
                                       placeholder="Titre de votre {{ $projectType === 'offer' ? 'offre' : 'besoin' }}..." 
                                       autofocus
                                       class="w-full bg-white border-2 border-blue-500 focus:ring-0 rounded-2xl p-4 text-lg font-black tracking-tight placeholder:text-slate-300 shadow-2xl">
                                <button wire:click="confirmProjectCreation" class="absolute right-2 top-2 bottom-2 px-6 bg-blue-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all">
                                    Lancer
                                </button>
                            </div>
                            @error('projectTitle') <span class="text-red-500 text-[10px] font-black uppercase mt-2 block pl-4">{{ $message }}</span> @enderror
                        </div>
                    @else
                        <div class="w-16 h-16 bg-blue-50 rounded-3xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-slate-400 font-black uppercase tracking-[0.3em] text-xs mb-4">Aucun projet encore lancé</p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <button wire:click="startCreatingProject('offer')" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                Proposer une Offre
                            </button>
                            <button wire:click="startCreatingProject('demand')" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg shadow-blue-500/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                Exprimer un Besoin
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Reseaux (Discovery) -->
            @auth
                @if(auth()->id() === $user->id)
                    <div class="mt-10">
                        <livewire:network.explorer :origin="$user" />
                    </div>
                @endif
            @endauth
            <!-- Mes Proches (Managed Proches) -->
            @if($user->id === auth()->id() && $user->proches->count() > 0)
                <div class="bg-blue-600 rounded-[3.5rem] p-10 text-white shadow-2xl shadow-blue-500/20 relative overflow-hidden mt-10">
                    <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-white/10 to-transparent"></div>
                    <div class="flex items-center justify-between mb-8 relative z-10">
                        <h3 class="text-2xl font-black tracking-tight">Mes Proches</h3>
                        <button wire:click="openProcheModal" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white text-white hover:text-blue-600 flex items-center justify-center transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                    <div class="space-y-4 relative z-10">
                        @foreach($user->proches as $proche)
                            <div class="p-5 bg-white/10 border border-white/10 rounded-3xl hover:bg-white/20 transition-all group/p">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-4 flex-grow min-w-0">
                                        <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center font-black text-xl border-2 border-white/20">
                                            {{ substr($proche->name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-black uppercase tracking-tight truncate">{{ $proche->name }}</div>
                                            <div class="text-[9px] font-bold text-blue-200 uppercase tracking-widest">{{ $proche->achievements->count() }} compétence(s)</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button wire:click="openCreateModal({{ $proche->id }})" class="p-3 bg-white/10 hover:bg-white text-white hover:text-blue-600 rounded-2xl transition-all shadow-sm" title="Ajouter une compétence">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                        </button>
                                        
                                        @if($proche->transfer_token)
                                            <div class="bg-white/90 px-3 py-1.5 rounded-xl flex flex-col items-center">
                                                <span class="text-[7px] font-black text-slate-500 uppercase leading-none mb-1">Code</span>
                                                <span class="text-xs font-black text-blue-600 leading-none">{{ $proche->transfer_code }}</span>
                                            </div>
                                        @else
                                            <button wire:click="generateTransfer({{ $proche->id }})" class="p-3 bg-white/10 hover:bg-white text-white hover:text-blue-600 rounded-2xl transition-all shadow-sm" title="Transférer le compte">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                @if($proche->transfer_token)
                                    <div class="mt-4 pt-4 border-t border-white/10">
                                        <p class="text-[8px] font-bold text-blue-100 uppercase tracking-widest leading-relaxed break-all">
                                            Transfert : <span class="select-all bg-blue-700/50 px-1 rounded">{{ url('/proches/claim/'.$proche->transfer_token) }}</span>
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Portfolio: Skill-Centric View -->
        <div class="lg:col-span-8 space-y-12">
            <div class="flex items-center justify-between mb-10">
                <h2 class="text-4xl font-black text-slate-900 tracking-tighter">Expertises & Réalisations</h2>
                @if($user->id === auth()->id())
                    <button wire:click="openCreateModal" class="px-6 py-3 bg-blue-600 text-white rounded-2xl font-black text-xs tracking-widest uppercase hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                        Ajouter une compétence
                    </button>
                @endif
            </div>
            
            <div class="space-y-16">
                @forelse($groupedAchievements as $skillName => $achievements)
                    <div class="relative">
                        <!-- Skill Header -->
                        <div class="flex flex-wrap items-center gap-4 md:gap-6 mb-8">
                            <div class="w-12 h-12 md:w-16 md:h-16 bg-slate-900 rounded-2xl md:rounded-[1.5rem] flex items-center justify-center text-white shadow-xl rotate-3 shrink-0">
                                <span class="text-lg md:text-xl font-black uppercase">{{ substr($skillName, 0, 1) }}</span>
                            </div>
                            <div class="flex-grow min-w-0">
                                <div class="flex items-center gap-3 md:gap-4 mb-1 md:mb-2">
                                    <h3 class="text-xl md:text-2xl font-black text-slate-900 leading-none uppercase tracking-tight truncate">{{ $skillName }}</h3>
                                    @auth
                                        @if($canEdit)
                                            @php 
                                                // Check if any achievement in this group belongs to a Proche we can edit
                                                $firstAch = $achievements->first(); 
                                                $procheIdForBtn = $firstAch->proche_id;
                                            @endphp
                                            <button wire:click="addProofForSkill('{{ $skillName }}', {{ $procheIdForBtn ?? 'null' }})" class="w-7 h-7 md:w-8 md:h-8 rounded-full bg-blue-500 text-white flex items-center justify-center hover:bg-slate-900 transition-all shadow-lg shadow-blue-500/20 group/btn shrink-0">
                                                <svg class="w-4 h-4 md:w-5 h-5 group-hover/btn:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                            </button>
                                        @endif
                                    @endauth
                                </div>
                                @php
                                    $realizedDates = $achievements->reject(fn($a) => $a->title === '__SKELETON__')->pluck('realized_at')->filter();
                                    $minYear = $realizedDates->count() ? $realizedDates->min()->format('Y') : null;
                                    $maxYear = $realizedDates->count() ? $realizedDates->max()->format('Y') : null;
                                @endphp
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                                    <span class="text-[8px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest truncate">
                                        {{ $achievements->reject(fn($a) => $a->title === '__SKELETON__')->count() }} Preuve(s)
                                        @if($minYear && $maxYear)
                                            • {{ $minYear === $maxYear ? $minYear : "{$minYear} - {$maxYear}" }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Proofs under this skill -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 pl-6 md:pl-20 relative">
                            <!-- Timeline Connector -->
                            <div class="absolute left-4 md:left-[4.5rem] top-0 bottom-0 w-px bg-gradient-to-b from-slate-200 via-slate-100 to-transparent"></div>
                            
                            @foreach($achievements->reject(fn($a) => $a->title === '__SKELETON__') as $achievement)
                                <div class="group relative">
                                    <!-- Point -->
                                    <div class="absolute -left-[2.75rem] md:-left-[5rem] top-8 w-3 h-3 md:w-4 md:h-4 rounded-full bg-white border-[3px] md:border-4 border-slate-900 shadow-lg z-10 group-hover:bg-blue-600 group-hover:border-blue-200 transition-all duration-500"></div>

                                    <div class="relative bg-white/60 backdrop-blur-2xl border border-white/60 p-8 rounded-[2.5rem] hover:bg-white transition-all duration-500 group-hover:shadow-[0_40px_80px_-15px_rgba(59,130,246,0.08)]">
                                        <div class="flex items-start justify-between mb-6">
                                            <div class="flex flex-col gap-1">
                                                <span class="text-[9px] font-black uppercase text-slate-300 tracking-widest">
                                                    {{ $achievement->realized_at ? $achievement->realized_at->format('M Y') : $achievement->created_at->format('M Y') }}
                                                </span>
                                                @if($achievement->proche_id)
                                                    <span class="text-[8px] font-black text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md uppercase tracking-tighter">Proche : {{ $achievement->proche->name }}</span>
                                                @endif
                                            </div>
                                            
                                            <div class="flex items-center gap-2">
                                                @auth
                                                    @if(auth()->id() !== $user->id)
                                                        @php 
                                                            $myValidation = $achievement->validations->where('user_id', auth()->id())->first();
                                                        @endphp
                                                        <div class="flex bg-slate-100 rounded-xl p-1 gap-1">
                                                            <button wire:click="initiateValidation({{ $achievement->id }}, 'validate')" @class([
                                                                'p-1.5 rounded-lg transition-all',
                                                                'bg-white text-green-600 shadow-sm' => $myValidation && $myValidation->type === 'validate',
                                                                'text-slate-400 hover:text-green-600' => !$myValidation || $myValidation->type !== 'validate'
                                                            ]) title="Valider">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                            </button>
                                                            <button wire:click="initiateValidation({{ $achievement->id }}, 'reject')" @class([
                                                                'p-1.5 rounded-lg transition-all',
                                                                'bg-white text-red-600 shadow-sm' => $myValidation && $myValidation->type === 'reject',
                                                                'text-slate-400 hover:text-red-600' => !$myValidation || $myValidation->type !== 'reject'
                                                            ]) title="Rejeter">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                            </button>
                                                        </div>
                                                    @endif
                                                @endauth
                                                
                                                <button wire:click="openValidationModal({{ $achievement->id }})" class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-100 rounded-xl hover:border-blue-200 transition-all group/vcount">
                                                    @php 
                                                        $valCount = $achievement->validations->where('type', 'validate')->count();
                                                        $rejCount = $achievement->validations->where('type', 'reject')->count();
                                                        $score = $valCount - $rejCount;
                                                    @endphp
                                                    <span @class([
                                                        'text-[10px] font-black',
                                                        'text-green-600' => $score > 0,
                                                        'text-red-600' => $score < 0,
                                                        'text-slate-400' => $score === 0
                                                    ])>{{ $score > 0 ? '+' : '' }}{{ $score }}</span>
                                                    <div class="flex -space-x-2">
                                                        @foreach($achievement->validations->take(3) as $v)
                                                            <a href="{{ route('users.show', $v->user) }}" class="hover:z-20 transition-transform hover:scale-125">
                                                                <img src="{{ $v->user->avatar }}" class="w-4 h-4 rounded-full border border-white shadow-sm">
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center gap-3 mb-4">
                                            <h4 class="text-xl font-black text-slate-900 tracking-tight leading-tight italic">"{{ $achievement->title }}"</h4>
                                            <livewire:information.manager :model="$achievement" :key="'ach-info-'.$achievement->id" />
                                        </div>
                                        <p class="text-slate-500 text-sm font-medium mb-8 leading-relaxed line-clamp-2 italic">{{ $achievement->description }}</p>

                                        <div class="pt-6 border-t border-slate-50 flex items-center gap-3">
                                            <div class="w-6 h-6 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            </div>
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">{{ $achievement->circle?->name ?? 'External Proof' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="py-20 text-center bg-white/40 border-2 border-dashed border-slate-200 rounded-[3rem]">
                        <p class="text-slate-400 font-black uppercase tracking-[0.3em] text-sm italic">Aucune expertise encore certifiée.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Create Success Modal (Two-Step) -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center px-6">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xl" wire:click="$set('showCreateModal', false)"></div>
            
            <div class="relative bg-white rounded-[4rem] shadow-2xl w-full max-w-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
                <!-- Progress Header -->
                <div class="bg-slate-900 p-12 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-blue-600/20 to-transparent"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-4 mb-8">
                            <span class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center text-xl font-black">{{ $step }}</span>
                            <div>
                                <h3 class="text-2xl font-black tracking-tight leading-none uppercase">
                                    {{ $step === 1 ? 'Quelle est votre expertise ?' : 'Certifier cette expertise' }}
                                </h3>
                                <p class="text-slate-400 text-xs font-bold mt-2 uppercase tracking-widest">
                                    {{ $step === 1 ? 'Ajout d\'une nouvelle compétence' : 'Dépôt de preuve factuelle' }}
                                </p>
                            </div>
                        </div>
                        <!-- Progress bar -->
                        <div class="w-full h-1.5 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full transition-all duration-500" style="width: {{ $step === 1 ? '50%' : '100%' }}"></div>
                        </div>
                    </div>
                </div>

                <div class="p-12">
                    @if($step === 1)
                        <!-- Step 1 Form -->
                        <div class="space-y-8">
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 block">Nom de la compétence</label>
                                <input wire:model="skillName" type="text" placeholder="ex: Cuisine Japonaise, Design UI, Charpente..." 
                                    class="w-full bg-slate-50 border-white focus:ring-blue-500 focus:border-blue-500 rounded-3xl p-6 text-xl font-black tracking-tight placeholder:text-slate-300">
                                @error('skillName') <span class="text-red-500 text-[10px] font-black uppercase mt-2 block">{{ $message }}</span> @enderror
                            </div>

                            <button wire:click="submitSkillOnly" class="w-full py-6 bg-slate-900 text-white rounded-[2.5rem] font-black text-sm tracking-[0.3em] uppercase hover:bg-blue-600 transition-all shadow-2xl shadow-slate-900/20">
                                Ajouter cette compétence
                            </button>
                        </div>
                    @else
                        <!-- Step 2 Form -->
                        <div class="space-y-6">
                            <div class="flex items-center gap-4 p-4 bg-blue-50 rounded-2xl border border-blue-100 mb-4">
                                <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Compétence :</span>
                                <span class="text-sm font-black text-slate-900 italic">"{{ $skillName }}"</span>
                                {{-- No back button here as we are adding proof to an existing skill context --}}
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">Titre de la réussite</label>
                                <input wire:model="proofTitle" type="text" placeholder="ex: Chef de partie au restaurant X..." 
                                    class="w-full bg-slate-50 border-white focus:ring-blue-500 rounded-2xl p-4 text-sm font-bold italic">
                                @error('proofTitle') <span class="text-red-500 text-[10px] font-black uppercase mt-2 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">Description / Contexte</label>
                                <textarea wire:model="proofDescription" rows="3" placeholder="Expliquez ce que vous avez accompli, le résultat concret..." 
                                    class="w-full bg-slate-50 border-white focus:ring-blue-500 rounded-2xl p-4 text-sm font-bold italic"></textarea>
                                @error('proofDescription') <span class="text-red-500 text-[10px] font-black uppercase mt-2 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">Date de réalisation</label>
                                <input wire:model="realizedAt" type="date"
                                    class="w-full bg-slate-50 border-white focus:ring-blue-500 rounded-2xl p-4 text-sm font-bold uppercase tracking-widest">
                                @error('realizedAt') <span class="text-red-500 text-[10px] font-black uppercase mt-2 block">{{ $message }}</span> @enderror
                            </div>

                            <button wire:click="submitProof" class="w-full py-6 bg-blue-600 text-white rounded-[2.5rem] font-black text-sm tracking-[0.3em] uppercase hover:bg-blue-700 transition-all shadow-2xl shadow-blue-500/20">
                                Certifier cette preuve
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
    <!-- Create Proche Modal -->
    @if($showProcheModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center px-6">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xl" wire:click="$set('showProcheModal', false)"></div>
            
            <div class="relative bg-white rounded-[4rem] shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-300">
                <div class="bg-slate-900 p-10 text-white">
                    <h3 class="text-2xl font-black uppercase tracking-tight">Nouveau Proche</h3>
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mt-2">Créez un réseau pour votre entourage</p>
                </div>
                
                <div class="p-10 space-y-8">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 block">Nom complet du proche</label>
                        <input wire:model="procheName" type="text" placeholder="ex: Jean Dupont, Marie Durant..." 
                            class="w-full bg-slate-50 border-white focus:ring-blue-500 rounded-3xl p-6 text-xl font-black tracking-tight">
                        @error('procheName') <span class="text-red-500 text-[10px] font-black uppercase mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    <button wire:click="createProche" class="w-full py-6 bg-blue-600 text-white rounded-[2rem] font-black text-sm tracking-[0.2em] uppercase hover:bg-blue-700 transition-all shadow-xl shadow-blue-500/20">
                        Créer le profil proche
                    </button>
                    
                    <p class="text-center text-[9px] font-medium text-slate-400 italic">
                        Le profil sera géré par vous jusqu'à son transfert définitif.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Achievement Validation Details Modal -->
    @if($showValidationModal && $selectedAchievement)
        <div class="fixed inset-0 z-[110] flex items-center justify-center px-6">
            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-xl" wire:click="$set('showValidationModal', false)"></div>
            
            <div class="relative bg-white rounded-[4rem] shadow-2xl w-full max-w-xl overflow-hidden animate-in fade-in zoom-in duration-300">
                <div class="bg-slate-900 p-10 text-white flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-black uppercase tracking-tight italic">"{{ $selectedAchievement->title }}"</h3>
                        <div class="flex items-center gap-4 mt-2">
                            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Score : {{ $selectedAchievement->validations->where('type', 'validate')->count() - $selectedAchievement->validations->where('type', 'reject')->count() }}</p>
                            @if($votingType)
                                <span @class([
                                    'px-3 py-1 rounded-full text-[8px] font-black uppercase',
                                    'bg-green-600 text-white' => $votingType === 'validate',
                                    'bg-red-600 text-white' => $votingType === 'reject',
                                ])>VOTE : {{ $votingType === 'validate' ? 'VALIDER' : 'REJETER' }}</span>
                            @endif
                        </div>
                    </div>
                    <button wire:click="$set('showValidationModal', false)" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                    @php 
                        $myV = $selectedAchievement->validations->where('user_id', auth()->id())->first();
                    @endphp

                    @if(($votingType || $myV) && !($myV?->reply))
                        <div class="bg-slate-50 p-8 rounded-[2.5rem] border border-slate-100 shadow-inner">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 block">
                                {{ $myV ? 'Modifier votre retour' : 'Pourquoi ' . ($votingType === 'validate' ? 'validez' : 'rejetez') . '-vous cette preuve ?' }}
                            </label>
                            <textarea wire:model="validationComment" rows="3" placeholder="Laissez un message (optionnel)..." 
                                class="w-full bg-white border-white focus:ring-blue-500 rounded-2xl p-4 text-sm font-bold italic shadow-sm"></textarea>
                            <button wire:click="confirmValidation" @class([
                                'w-full mt-6 py-4 rounded-2xl font-black text-xs uppercase tracking-widest text-white transition-all shadow-lg',
                                'bg-green-600 hover:bg-green-700 shadow-green-500/20' => ($votingType ?? ($myV?->type)) === 'validate',
                                'bg-red-600 hover:bg-red-700 shadow-red-500/20' => ($votingType ?? ($myV?->type)) === 'reject',
                            ])>
                                {{ $myV ? 'Mettre à jour mon feedback' : 'Confirmer mon vote' }}
                            </button>
                        </div>
                     @endif

                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block px-4 italic">Historique des retours</label>
                        @forelse($selectedAchievement->validations as $v)
                            <div @class([
                                'p-6 rounded-[2rem] flex gap-5 border transition-all',
                                'bg-green-50/50 border-green-100 text-green-900 italic' => $v->type === 'validate',
                                'bg-red-50/50 border-red-100 text-red-900 italic' => $v->type === 'reject',
                            ])>
                                <a href="{{ route('users.show', $v->user) }}" class="shrink-0 hover:scale-110 transition-transform">
                                    <img src="{{ $v->user->avatar }}" class="w-12 h-12 rounded-2xl object-cover ring-4 ring-white shadow-md">
                                </a>
                                <div class="flex-grow">
                                    <div class="flex items-center justify-between mb-1">
                                        <a href="{{ route('users.show', $v->user) }}" class="text-sm font-black uppercase hover:text-blue-600 transition-colors">{{ $v->user->name }}</a>
                                        <span @class([
                                            'text-[8px] font-black uppercase px-2 py-0.5 rounded-full',
                                            'bg-green-600 text-white' => $v->type === 'validate',
                                            'bg-red-600 text-white' => $v->type === 'reject',
                                        ])>{{ $v->type === 'validate' ? 'Validé' : 'Rejeté' }}</span>
                                    </div>
                                    <x-user-skills-tags :user="$v->user" limit="3" class="mb-3 scale-90 origin-left" />
                                    @if($v->comment)
                                        <p class="text-xs font-semibold leading-relaxed">"{{ $v->comment }}"</p>
                                    @endif

                                    @if($v->reply)
                                        <div class="mt-4 p-4 bg-white/60 rounded-2xl border border-white/80 shadow-sm relative pr-12">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-[8px] font-black uppercase text-blue-600 tracking-tighter">Réponse de l'expert</span>
                                            </div>
                                            <p class="text-[10px] font-bold italic text-slate-700">"{{ $v->reply }}"</p>
                                            <div class="absolute top-4 right-4 text-blue-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            </div>
                                        </div>
                                    @elseif(auth()->id() === $selectedAchievement->user_id && auth()->id() !== $v->user_id)
                                        <div class="mt-4 pt-4 border-t border-slate-100/50">
                                            <div class="flex gap-2">
                                                <input wire:model="replyText" type="text" placeholder="Répondre à ce retour..." 
                                                    class="flex-grow bg-white/50 border-none rounded-xl px-4 py-2 text-[10px] font-bold italic focus:ring-2 focus:ring-blue-500/20">
                                                <button wire:click="submitReply({{ $v->id }})" class="bg-blue-600 text-white p-2 rounded-xl hover:bg-blue-700 transition-all shadow-md">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center">
                                <p class="text-slate-400 font-black uppercase tracking-widest text-[10px]">Aucune validation pour le moment.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
