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
                <a href="#create-realisation" 
                   class="group relative inline-flex items-center justify-center px-8 py-5 font-black text-white transition-all duration-300 ease-in-out bg-slate-900 rounded-[2rem] hover:scale-[1.05] active:scale-95 shadow-xl hover:shadow-slate-500/25">
                    <span class="relative z-10 uppercase tracking-tighter text-lg">Démarrer une réalisation</span>
                    <div class="absolute inset-0 rounded-[2rem] bg-gradient-to-r from-blue-600 to-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </a>
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
                <h2 class="text-2xl font-black uppercase tracking-tighter mb-8 flex items-center gap-3">
                    <span class="w-2 h-8 bg-blue-500 rounded-full"></span>
                    Réalisations Actuelles
                </h2>
                
                @if($currentRealisations->isEmpty())
                    <div class="bg-white/40 backdrop-blur-xl border border-dashed border-slate-300 rounded-[2.5rem] p-12 text-center">
                        <p class="text-slate-400 font-bold uppercase tracking-widest text-sm">Aucune réalisation en cours</p>
                    </div>
                @else
                    <div class="grid gap-6">
                        @foreach($currentRealisations as $realisation)
                            <a href="{{ route('projects.show', $realisation) }}" class="group block bg-white/60 backdrop-blur-2xl border border-white/60 rounded-[2.5rem] p-6 hover:scale-[1.02] transition-all duration-300 hover:shadow-2xl">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="px-2 py-0.5 bg-green-500/10 text-green-600 text-[8px] font-black uppercase rounded-full">En cours</span>
                                            <span class="text-slate-400 text-[8px] font-bold">{{ $realisation->created_at->diffForHumans() }}</span>
                                        </div>
                                        <h3 class="text-xl font-black text-slate-900 group-hover:text-blue-600 transition-colors uppercase leading-[0.9] mb-2">{{ $realisation->title }}</h3>
                                        <p class="text-slate-500 text-sm line-clamp-2">{{ Str::limit($realisation->description, 150) }}</p>
                                    </div>
                                    <div class="flex -space-x-2">
                                        <img src="{{ $realisation->owner->avatar_url }}" class="w-10 h-10 rounded-full border-2 border-white shadow-sm" title="{{ $realisation->owner->name }}">
                                        @foreach($realisation->activeMembers->take(3) as $member)
                                            <img src="{{ $member->memberable->avatar_url }}" class="w-10 h-10 rounded-full border-2 border-white shadow-sm" title="{{ $member->memberable->name }}">
                                        @endforeach
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>

            {{-- Finished Realisations --}}
            <section>
                <h2 class="text-2xl font-black uppercase tracking-tighter mb-8 flex items-center gap-3">
                    <span class="w-2 h-8 bg-slate-900 rounded-full"></span>
                    Réalisations Terminées
                </h2>
                
                @if($finishedRealisations->isEmpty())
                    <div class="bg-white/40 backdrop-blur-xl border border-dashed border-slate-300 rounded-[2.5rem] p-12 text-center">
                        <p class="text-slate-400 font-bold uppercase tracking-widest text-sm">Aucune réalisation passée</p>
                    </div>
                @else
                    <div class="grid gap-6">
                        @foreach($finishedRealisations as $realisation)
                            <a href="{{ route('projects.show', $realisation) }}" class="group block bg-slate-50/50 backdrop-blur-2xl border border-slate-200/50 rounded-[2.5rem] p-6 grayscale hover:grayscale-0 transition-all duration-300">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="px-2 py-0.5 bg-slate-500/10 text-slate-600 text-[8px] font-black uppercase rounded-full">Terminée</span>
                                            <span class="text-slate-400 text-[8px] font-bold">{{ $realisation->realized_at?->translatedFormat('M Y') }}</span>
                                        </div>
                                        <h3 class="text-xl font-black text-slate-900 uppercase leading-[0.9] mb-2">{{ $realisation->title }}</h3>
                                        <div class="flex items-center gap-4">
                                            <div class="flex items-center gap-1.5">
                                                <div class="flex text-amber-400">
                                                    @for($i=0; $i<5; $i++)
                                                        <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                                    @endfor
                                                </div>
                                                <span class="text-[10px] font-black text-slate-900 mt-0.5">5.0</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex -space-x-2">
                                        <img src="{{ $realisation->owner->avatar_url }}" class="w-10 h-10 rounded-full border-2 border-white grayscale shadow-sm">
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        {{-- Sidebar Form --}}
        <div class="space-y-8" id="create-realisation">
            <div class="sticky top-24">
                <div class="bg-white/80 backdrop-blur-3xl border border-white rounded-[2.5rem] p-8 shadow-2xl">
                    <h2 class="text-xl font-black uppercase tracking-tighter mb-6">Nouvelle Réalisation</h2>
                    
                    @auth
                    <div x-data="{ isCreating: false }">
                        <button @click="if(!isCreating) { $wire.initDraft() }; isCreating = !isCreating" 
                                class="w-full py-4 rounded-[2rem] border-2 border-dashed border-slate-200 text-slate-500 font-bold text-xs uppercase tracking-widest hover:border-blue-500 hover:text-blue-500 hover:bg-blue-50 transition-all flex items-center justify-center gap-2 group">
                            <svg class="w-4 h-4 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Ajouter une réalisation
                        </button>

                        <div x-show="isCreating" x-collapse>
                            <div class="mt-4 p-5 bg-white rounded-[2rem] border-2 border-slate-100 relative">
                                <button @click="isCreating = false; $wire.cancelDraft()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition-colors z-20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                                
                                <form wire:submit.prevent="createRealisation" class="space-y-6 pt-4">
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Titre du contrat / projet</label>
                                        <input type="text" wire:model="title" 
                                            class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 font-bold text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 transition-all"
                                            placeholder="ex: Vidéo de mariage, Refonte site...">
                                        @error('title') <span class="text-[10px] text-red-500 font-bold uppercase mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Description rapide</label>
                                        <textarea wire:model="description" rows="4"
                                                class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 font-bold text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 transition-all"
                                                placeholder="Quels sont les objectifs ?"></textarea>
                                        @error('description') <span class="text-[10px] text-red-500 font-bold uppercase mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">État actuel</label>
                                        <div class="flex flex-col space-y-2">
                                            <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all" :class="$wire.status === 'actuelle' ? 'bg-blue-50 border-blue-500 shadow-sm' : 'bg-white border-transparent hover:border-blue-300'">
                                                <input type="radio" wire:model.live="status" value="actuelle" class="w-4 h-4 text-blue-600 focus:ring-blue-500 bg-white">
                                                <span class="text-xs font-bold text-slate-700">⏳ Actuelle (En cours)</span>
                                            </label>
                                            <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all" :class="$wire.status === 'verrouillée' ? 'bg-blue-50 border-blue-500 shadow-sm' : 'bg-white border-transparent hover:border-blue-300'">
                                                <input type="radio" wire:model.live="status" value="verrouillée" class="w-4 h-4 text-blue-600 focus:ring-blue-500 bg-white">
                                                <span class="text-xs font-bold text-slate-700">🔒 Verrouillée (Suspendue)</span>
                                            </label>
                                            <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all" :class="$wire.status === 'terminée' ? 'bg-blue-50 border-blue-500 shadow-sm' : 'bg-white border-transparent hover:border-blue-300'">
                                                <input type="radio" wire:model.live="status" value="terminée" class="w-4 h-4 text-blue-600 focus:ring-blue-500 bg-white">
                                                <span class="text-xs font-bold text-slate-700">✅ Terminée (Achevée)</span>
                                            </label>
                                        </div>
                                        @error('status') <span class="text-[10px] text-red-500 font-bold uppercase mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    @if($status === 'terminée')
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Date de réalisation</label>
                                        <input type="date" wire:model="realizedAt" 
                                            class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 font-bold text-slate-900 focus:ring-2 focus:ring-blue-500/20 transition-all uppercase tracking-widest">
                                        @error('realizedAt') <span class="text-[10px] text-red-500 font-bold uppercase mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    @endif

                                    @if($draftProject)
                                        <div class="mt-4 border border-slate-100 rounded-2xl p-4 bg-slate-50/50">
                                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Informations & Médias (Optionnel)</span>
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

                                    <button type="submit" 
                                            class="w-full py-5 bg-slate-900 text-white rounded-[2rem] font-black uppercase tracking-widest text-xs hover:bg-slate-800 transition-all active:scale-95 shadow-lg mt-4">
                                        Ouvrir le dossier
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @else
                        <div class="text-center py-6">
                            <p class="text-slate-500 font-medium mb-4">Vous devez être connecté pour proposer une réalisation.</p>
                            <a href="/login" class="inline-block px-6 py-3 bg-slate-100 rounded-2xl font-black uppercase tracking-widest text-[10px]">Connexion</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
