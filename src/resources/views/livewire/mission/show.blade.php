<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
    {{-- Mission Header --}}
    <div class="relative overflow-hidden bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[3rem] p-8 md:p-12 shadow-2xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-4 py-1.5 bg-blue-500/10 text-blue-600 text-[10px] font-black uppercase tracking-widest rounded-full border border-blue-500/20">
                        Mission (Compétence)
                    </span>
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">ID: #{{ $skill->id }}</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-black text-slate-930 leading-[0.9] uppercase tracking-tighter mb-6">
                    {{ $skill->name }}
                </h1>
                <p class="text-slate-500 text-lg max-w-2xl font-medium leading-relaxed">
                    Découvrez les réalisations liées à cette expertise et lancez votre propre projet collaboratif.
                </p>
            </div>
            
            <div class="shrink-0">
                @auth
                    <button wire:click="initDraft" 
                       class="group relative inline-flex items-center justify-center px-8 py-5 font-black text-white transition-all duration-300 ease-in-out bg-slate-900 rounded-[2rem] hover:scale-[1.05] active:scale-95 shadow-xl hover:shadow-slate-500/25">
                        <span class="relative z-10 uppercase tracking-tighter text-lg">Démarrer une réalisation</span>
                        <div class="absolute inset-0 rounded-[2rem] bg-gradient-to-r from-blue-600 to-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </button>
                @else
                    <a href="{{ route('login') }}" 
                       class="group relative inline-flex items-center justify-center px-8 py-5 font-black text-white transition-all duration-300 ease-in-out bg-slate-900 rounded-[2rem] hover:scale-[1.05] active:scale-95 shadow-xl hover:shadow-slate-500/25">
                        <span class="relative z-10 uppercase tracking-tighter text-lg">Démarrer une réalisation</span>
                        <div class="absolute inset-0 rounded-[2rem] bg-gradient-to-r from-blue-600 to-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </a>
                @endauth
            </div>
        </div>
        
        {{-- Stats Abstract --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-12 pt-8 border-t border-slate-200/50">
            <div>
                <p class="text-slate-400 text-[9px] font-black uppercase tracking-widest mb-1">Actuelles</p>
                <p class="text-2xl font-black text-slate-900">{{ $currentRealisations->count() }}</p>
            </div>
            <div>
                <p class="text-slate-400 text-[9px] font-black uppercase tracking-widest mb-1">Terminées</p>
                <p class="text-2xl font-black text-slate-900">{{ $finishedRealisations->count() }}</p>
            </div>
            <div class="md:col-span-2 flex justify-end">
                <a href="{{ route('cv.mission', $skill) }}" target="_blank" class="px-6 py-3 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4"/></svg>
                    Générer CV Papier
                </a>
            </div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2 space-y-12">
            {{-- Current Realisations --}}
            <section>
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-black uppercase tracking-tighter flex items-center gap-3">
                        <span class="w-2 h-8 bg-blue-500 rounded-full"></span>
                        Réalisations Actuelles
                    </h2>

                    @auth
                        <button wire:click="initDraft" 
                                class="px-6 py-3 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl flex items-center gap-2 group">
                            <svg class="w-4 h-4 group-hover:rotate-180 transition-transform duration-500 {{ $showCreationForm ? 'rotate-45' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            {{ $showCreationForm ? 'Fermer le formulaire' : 'Nouvelle Réalisation' }}
                        </button>
                    @endauth
                </div>

                {{-- Realisation Form --}}
                @auth
                    <div x-show="$wire.showCreationForm" 
                         x-collapse 
                         x-cloak 
                         class="mb-8 overflow-hidden">
                            <div class="p-8 bg-white/80 backdrop-blur-3xl rounded-[2.5rem] border-2 border-blue-100 relative shadow-2xl shadow-blue-500/5 overflow-hidden">
                            {{-- Loading Overlay --}}
                            <div wire:loading wire:target="initDraft" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-50 flex items-center justify-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                                    <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Initialisation du dossier...</p>
                                </div>
                            </div>

                            <button wire:click="cancelDraft" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600 transition-colors z-20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            
                            <h3 class="text-xl font-black uppercase tracking-tighter mb-8 flex items-center gap-3">
                                <span class="w-1.5 h-6 bg-blue-600 rounded-full"></span>
                                Ouvrir un Dossier de Réalisation
                            </h3>

                            <form wire:submit.prevent="createRealisation" class="space-y-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="space-y-6">
                                        <div>
                                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Titre du contrat / projet</label>
                                            <input type="text" wire:model="title" 
                                                class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 font-bold text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 transition-all italic text-sm"
                                                placeholder="ex: Vidéo de mariage, Refonte site...">
                                            @error('title') <span class="text-[10px] text-red-500 font-bold uppercase mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Description rapide</label>
                                            <textarea wire:model="description" rows="4"
                                                    class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 font-bold text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 transition-all italic text-sm"
                                                    placeholder="Quels sont les objectifs ?"></textarea>
                                            @error('description') <span class="text-[10px] text-red-500 font-bold uppercase mt-1">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="space-y-6">
                                        <div>
                                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">État de départ</label>
                                            <div class="grid grid-cols-1 gap-2">
                                                <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all" :class="$wire.status === 'actuelle' ? 'bg-blue-50 border-blue-500 shadow-sm' : 'bg-white border-transparent hover:border-blue-300'">
                                                    <input type="radio" wire:model.live="status" value="actuelle" class="w-4 h-4 text-blue-600 focus:ring-blue-500 bg-white">
                                                    <span class="text-[11px] font-black text-slate-900 uppercase tracking-widest">⏳ Actuelle</span>
                                                </label>
                                                <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all" :class="$wire.status === 'verrouillée' ? 'bg-amber-50 border-amber-500 shadow-sm' : 'bg-white border-transparent hover:border-blue-300'">
                                                    <input type="radio" wire:model.live="status" value="verrouillée" class="w-4 h-4 text-blue-600 focus:ring-blue-500 bg-white">
                                                    <span class="text-[11px] font-black text-slate-900 uppercase tracking-widest">🔒 Verrouillée</span>
                                                </label>
                                                <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all" :class="$wire.status === 'terminée' ? 'bg-green-50 border-green-500 shadow-sm' : 'bg-white border-transparent hover:border-blue-300'">
                                                    <input type="radio" wire:model.live="status" value="terminée" class="w-4 h-4 text-blue-600 focus:ring-blue-500 bg-white">
                                                    <span class="text-[11px] font-black text-slate-900 uppercase tracking-widest">✅ Terminée</span>
                                                </label>
                                            </div>
                                        </div>

                                        @if($status === 'terminée')
                                        <div class="animate-in slide-in-from-top-2 duration-300">
                                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Date d'achèvement</label>
                                            <input type="date" wire:model="realizedAt" 
                                                class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 font-bold text-slate-900 focus:ring-2 focus:ring-blue-500/20 transition-all uppercase tracking-widest text-xs">
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-4">Compétences secondaires (Optionnel)</label>
                                    <div class="flex flex-wrap gap-2 p-6 bg-slate-50 rounded-[2.5rem] border border-slate-100 max-h-48 overflow-y-auto custom-scrollbar">
                                        @foreach($availableSkills as $as)
                                            <label class="cursor-pointer">
                                                <input type="checkbox" wire:model.live="selectedSkillIds" value="{{ $as->id }}" class="hidden">
                                                <span @class([
                                                    'px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-tight border transition-all',
                                                    'bg-blue-600 border-blue-600 text-white shadow-lg shadow-blue-500/10' => in_array($as->id, $selectedSkillIds),
                                                    'bg-white border-slate-200 text-slate-400 hover:border-blue-300 hover:text-blue-600' => !in_array($as->id, $selectedSkillIds)
                                                ])>
                                                    {{ $as->name }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="mt-4">
                                        <input type="text" wire:model="newSkillName" 
                                               class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 font-bold text-slate-600 placeholder:text-slate-300 focus:ring-2 focus:ring-blue-500/20 transition-all italic text-xs"
                                               placeholder="Ajouter d'autres compétences (séparées par des virgules)...">
                                    </div>
                                </div>

                                @if($draftProject)
                                    <div class="mt-4 border border-slate-100 rounded-[2.5rem] p-8 bg-slate-50/50">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-6">Fichiers, Vidéos & Liens de preuve</span>
                                        <livewire:information.manager :model="$draftProject" :key="'info-manager-draft-mission-'.$draftProject->id" />
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="bg-red-50 text-red-500 p-4 rounded-xl text-xs font-bold mt-4 mb-2 shadow-sm border border-red-100">
                                        <ul class="list-disc pl-5">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="flex gap-4">
                                    <button type="submit" 
                                            class="flex-grow py-5 bg-blue-600 text-white rounded-[2rem] font-black uppercase tracking-[0.2em] text-xs hover:bg-blue-700 transition-all active:scale-95 shadow-2xl shadow-blue-500/20">
                                        Lancer la Réalisation
                                    </button>
                                    <button type="button" wire:click="cancelDraft"
                                            class="px-10 py-5 bg-slate-100 text-slate-400 rounded-[2rem] font-black uppercase tracking-[0.2em] text-xs hover:bg-slate-200 hover:text-slate-600 transition-all">
                                        Annuler
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endauth
                
                @if($currentRealisations->isEmpty())
                    <div class="bg-white/40 backdrop-blur-xl border border-dashed border-slate-300 rounded-[2.5rem] p-12 text-center group">
                        <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6 border border-slate-100 text-slate-300 group-hover:scale-110 transition-transform duration-500">
                             <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </div>
                        <p class="text-slate-400 font-black uppercase tracking-[0.3em] text-[10px]">Aucune réalisation en cours</p>
                        @auth
                            <button wire:click="initDraft" class="mt-6 text-[10px] font-black text-blue-600 uppercase tracking-widest hover:text-blue-700 transition-colors">Soyez le premier à contribuer</button>
                        @endauth
                    </div>
                @else
                    <div class="grid gap-6">
                        @foreach($currentRealisations as $realisation)
                            <div class="group relative bg-white/60 backdrop-blur-2xl border border-white/60 rounded-[2.5rem] p-6 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 hover:shadow-2xl hover:border-blue-200/50 overflow-hidden">
                                {{-- Big Link Overlay --}}
                                <a href="{{ route('projects.show', $realisation) }}" class="absolute inset-0 z-10" title="Voir la réalisation"></a>
                                
                                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <div class="flex items-start justify-between relative z-20">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="px-2 py-0.5 bg-blue-500/10 text-blue-600 text-[8px] font-black uppercase rounded-full group-hover:bg-blue-600 group-hover:text-white transition-colors">Actuelle</span>
                                            <span class="text-slate-400 text-[8px] font-bold uppercase tracking-widest">{{ $realisation->created_at->diffForHumans() }}</span>
                                        </div>
                                        <h3 class="text-xl font-black text-slate-930 group-hover:text-blue-600 transition-colors uppercase leading-[0.9] mb-4 italic truncate max-w-[400px]">"{{ $realisation->title }}"</h3>
                                        @if($realisation->skills && $realisation->skills->count() > 0)
                                            <div class="flex flex-wrap gap-1 mb-4 relative z-30">
                                                @foreach($realisation->skills as $s)
                                                    <a href="{{ route('mission.show', $s) }}" class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[8px] font-black uppercase rounded-lg border border-slate-200/50 hover:bg-blue-600 hover:text-white transition-colors relative z-30">
                                                        {{ $s->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                        <p class="text-slate-500 text-sm line-clamp-2 italic font-medium max-w-lg leading-relaxed">{{ Str::limit($realisation->description, 150) }}</p>
                                    </div>
                                    <div class="flex -space-x-3 pointer-events-none">
                                        <div class="relative group/avatar">
                                            <img src="{{ $realisation->owner->avatar }}" class="w-12 h-12 rounded-[1.2rem] border-2 border-white shadow-xl object-cover relative z-10">
                                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-blue-500 rounded-full border-2 border-white z-20 flex items-center justify-center">
                                                <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                                            </div>
                                        </div>
                                        @foreach($realisation->activeMembers->take(3) as $member)
                                            <img src="{{ $member->memberable->avatar }}" class="w-12 h-12 rounded-[1.2rem] border-2 border-white shadow-lg object-cover opacity-80 group-hover:opacity-100 transition-opacity">
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            {{-- Finished Realisations --}}
            <section>
                <h2 class="text-2xl font-black uppercase tracking-tighter mb-8 flex items-center gap-3">
                    <span class="w-2 h-8 bg-slate-930 rounded-full"></span>
                    Honneurs & Succès
                </h2>
                
                @if($finishedRealisations->isEmpty())
                    <div class="bg-white/40 backdrop-blur-xl border border-dashed border-slate-300 rounded-[2.5rem] p-12 text-center">
                        <p class="text-slate-400 font-black uppercase tracking-[0.3em] text-[10px]">Aucune réussite archivée</p>
                    </div>
                @else
                    <div class="grid gap-6">
                        @foreach($finishedRealisations as $realisation)
                            <div class="group relative bg-slate-50/50 backdrop-blur-2xl border border-slate-200/50 rounded-[2.5rem] p-8 hover:bg-white hover:shadow-2xl hover:-translate-y-1 transition-all duration-500 overflow-hidden">
                                {{-- Big Link Overlay --}}
                                <a href="{{ route('projects.show', $realisation) }}" class="absolute inset-0 z-10" title="Voir la réalisation"></a>
                                
                                <div class="flex items-start justify-between relative z-20">
                                    <div class="space-y-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-2xl bg-green-500/10 flex items-center justify-center text-green-600">
                                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            </div>
                                            <div>
                                                <h3 class="text-2xl font-black text-slate-930 group-hover:text-green-600 transition-colors uppercase leading-none tracking-tight">"{{ $realisation->title }}"</h3>
                                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1">Classé {{ $realisation->realized_at?->translatedFormat('F Y') }}</p>
                                            </div>
                                        </div>

                                        @if($realisation->skills && $realisation->skills->count() > 0)
                                            <div class="flex flex-wrap gap-2 relative z-30">
                                                @foreach($realisation->skills as $s)
                                                    <a href="{{ route('mission.show', $s) }}" class="px-3 py-1 bg-slate-100 text-slate-500 text-[9px] font-black uppercase rounded-lg border border-slate-200/50 hover:bg-blue-600 hover:text-white transition-all relative z-30">
                                                        #{{ $s->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                        
                                        <p class="text-slate-500 text-sm font-medium italic leading-relaxed max-w-xl line-clamp-2">{{ Str::limit($realisation->description, 200) }}</p>
                                        
                                        <div class="flex items-center gap-6 pt-2">
                                            <div class="flex items-center gap-2">
                                                <div class="flex text-amber-500">
                                                    @for($i=0; $i<5; $i++)
                                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                                    @endfor
                                                </div>
                                                <span class="text-xs font-black text-slate-930">Top Score</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end gap-6 px-2 pointer-events-none">
                                        <div class="flex -space-x-3">
                                            <img src="{{ $realisation->owner->avatar }}" class="w-14 h-14 rounded-2xl border-4 border-white shadow-2xl object-cover relative z-10">
                                            @foreach($realisation->activeMembers->take(2) as $member)
                                                <img src="{{ $member->memberable->avatar }}" class="w-14 h-14 rounded-2xl border-4 border-white shadow-xl object-cover opacity-60">
                                            @endforeach
                                        </div>
                                        @if($realisation->reviews->count() > 0)
                                            <span class="px-4 py-2 bg-white rounded-2xl border border-slate-100 text-[10px] font-black text-slate-900 uppercase tracking-widest shadow-xl">Certifié par Peers</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-12">
            @if($topExpert)
                <div class="relative group">
                    {{-- Decorative Background --}}
                    <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-[3rem] blur opacity-10 group-hover:opacity-20 transition duration-1000"></div>
                    
                    <div class="relative bg-white border border-slate-100 rounded-[3rem] p-10 shadow-2xl overflow-hidden">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-blue-500/5 rounded-full -mr-20 -mt-20 group-hover:scale-110 transition-transform duration-700"></div>
                        
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-[11px] font-black uppercase tracking-[0.4em] text-slate-400 flex items-center gap-3">
                                <span class="w-2 h-4 bg-blue-600 rounded-full"></span>
                                Référence
                            </h3>
                            <div class="px-4 py-2 bg-blue-50 rounded-2xl border border-blue-100">
                                <span class="text-[10px] font-black text-blue-600 uppercase">Top Expert</span>
                            </div>
                        </div>
                        
                        <div class="flex flex-col items-center text-center">
                            <div class="relative mb-6">
                                <a href="{{ route('users.show', $topExpert) }}" class="block p-1 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-[2.5rem] hover:rotate-6 transition-transform duration-500">
                                    <img src="{{ $topExpert->avatar }}" class="w-32 h-32 rounded-[2.2rem] object-cover border-4 border-white shadow-2xl">
                                </a>
                                <div class="absolute -bottom-2 -right-2 bg-white p-3 rounded-2xl shadow-xl border border-slate-50 flex flex-col items-center">
                                    <span class="text-[16px] font-black text-blue-600 leading-none">{{ $topExpert->trust_score }}%</span>
                                    <span class="text-[7px] font-black text-slate-400 uppercase tracking-widest">Trust</span>
                                </div>
                            </div>
                            
                            <h4 class="text-2xl font-black text-slate-930 uppercase tracking-tight mb-2">{{ $topExpert->name }}</h4>
                            <p class="text-xs font-medium text-slate-500 italic mb-8 max-w-[200px] leading-relaxed">Expert certifié par le réseau avec {{ $topExpert->achievements->count() }} validations réussies.</p>
                            
                            <div class="w-full space-y-4 mb-10">
                                <x-user-skills-tags :user="$topExpert" limit="5" class="justify-center flex-wrap" />
                            </div>

                            <a href="{{ route('users.show', $topExpert) }}" class="w-full py-5 bg-slate-930 rounded-[2rem] font-black uppercase tracking-widest text-xs hover:bg-blue-600 shadow-xl transition-all active:scale-95">
                                Voir le dossier complet
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-slate-900 rounded-[3.5rem] p-10 text-white shadow-3xl relative overflow-hidden group/mcard">
                <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 rounded-full blur-[100px]"></div>
                
                <h3 class="text-sm font-black uppercase tracking-[0.3em] mb-10 text-blue-400 relative z-10 flex items-center gap-3">
                    <span class="w-1.5 h-6 bg-blue-500 rounded-full"></span>
                    À propos de la Mission
                </h3>
                
                <div class="space-y-8 relative z-10">
                    <p class="text-slate-400 text-sm font-medium leading-relaxed italic">
                        La mission de type <strong class="text-white">{{ $skill->name }}</strong> regroupe les experts capables de délivrer des prestations certifiées dans ce domaine.
                    </p>
                    
                    <div class="space-y-4">
                        <div class="flex items-center gap-4 group/item">
                            <div class="w-10 h-10 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-blue-400 group-hover/item:bg-blue-600 group-hover/item:text-white transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest">Confiance Maximale</p>
                                <p class="text-xs text-slate-500">Transaction sécurisée via le réseau de confiance.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 group/item">
                            <div class="w-10 h-10 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-green-400 group-hover/item:bg-green-600 group-hover/item:text-white transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest">Preuves Certifiées</p>
                                <p class="text-xs text-slate-500">Chaque résultat est soumis à la validation des pairs.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
