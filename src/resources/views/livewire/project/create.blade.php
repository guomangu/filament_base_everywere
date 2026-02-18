<div class="min-h-screen bg-slate-50/50 pb-20 pt-10">
    <div class="max-w-4xl mx-auto px-6">
        {{-- Header --}}
        <div class="bg-slate-900 rounded-[3rem] p-10 md:p-16 text-white mb-10 relative overflow-hidden shadow-2xl shadow-slate-900/20">
            <div class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-6 mb-8">
                    <div class="w-16 h-16 bg-blue-600 rounded-3xl flex items-center justify-center text-2xl font-black shadow-xl rotate-3">
                        {{ $step }}
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-5xl font-black tracking-tighter leading-none uppercase">Lancer un Projet</h1>
                        <p class="text-slate-400 text-xs font-black uppercase tracking-[0.3em] mt-2 italic">Donnez vie à vos idées & collaborez</p>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="flex items-center gap-4">
                    <div class="flex-grow h-2 bg-white/10 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500 rounded-full transition-all duration-700 shadow-[0_0_20px_rgba(59,130,246,0.5)]" style="width: {{ ($step/4)*100 }}%"></div>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">{{ $step }} / 4</span>
                </div>
            </div>
        </div>

        <div class="bg-white/40 backdrop-blur-3xl border border-white/60 rounded-[3.5rem] p-8 md:p-12 shadow-2xl shadow-blue-500/5">
            {{-- STEP 1: BASIC INFO --}}
            @if($step === 1)
                <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4 block italic">Nom du projet</label>
                        <input wire:model="title" type="text" placeholder="ex: BaklavasEZ, Agence Web, Potager Commun..." 
                            class="w-full bg-white/60 border border-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 rounded-3xl p-6 text-xl font-black tracking-tight placeholder:text-slate-300 shadow-sm transition-all uppercase">
                        @error('title') <span class="text-red-500 text-[10px] font-black uppercase mt-2 block pl-2">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4 block italic">Description & Vision</label>
                        <textarea wire:model="description" rows="5" placeholder="Expliquez ce que vous souhaitez accomplir..." 
                            class="w-full bg-white/60 border border-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 rounded-3xl p-6 text-sm font-bold leading-relaxed placeholder:text-slate-300 shadow-sm transition-all italic"></textarea>
                        @error('description') <span class="text-red-500 text-[10px] font-black uppercase mt-2 block pl-2">{{ $message }}</span> @enderror
                    </div>

                    <div class="p-6 bg-blue-50/50 rounded-[2rem] border border-blue-100 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-black text-slate-900 uppercase tracking-tight block">Ouvrir immédiatement ?</span>
                            <span class="text-[9px] font-medium text-slate-400 uppercase tracking-widest">Le projet sera visible et prêt à l'interaction</span>
                        </div>
                        <button wire:click="$toggle('is_open')" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none {{ $is_open ? 'bg-blue-600' : 'bg-slate-200' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $is_open ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>
                </div>
            @endif

            {{-- STEP 2: OFFRES --}}
            @if($step === 2)
                <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight flex items-center gap-2 italic">
                             <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                             Nos Offres ({{ count($offers) }})
                        </h3>
                    </div>

                    {{-- List existing offers --}}
                    <div class="space-y-4 mb-10">
                        @foreach($offers as $index => $off)
                            <div class="p-6 bg-white/60 border border-white rounded-3xl flex justify-between items-start group hover:bg-white transition-all">
                                <div>
                                    <div class="text-sm font-black text-slate-900 uppercase">{{ $off['title'] }}</div>
                                    <p class="text-[10px] text-slate-500 font-medium mt-1 line-clamp-2 italic">{{ $off['description'] }}</p>
                                    @if(!empty($off['skills']))
                                        <div class="flex gap-1 mt-2">
                                            @foreach($off['skills'] as $sId)
                                                @php $s = $skills->find($sId); @endphp
                                                <span class="text-[7px] font-black bg-blue-50 text-blue-600 px-2 py-0.5 rounded-lg uppercase tracking-widest">{{ $s->name ?? 'Skill' }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <button wire:click="removeOffer({{ $index }})" class="text-slate-200 hover:text-red-500 transition-colors p-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    {{-- Form to add offer --}}
                    <div class="p-8 bg-slate-900 rounded-[2.5rem] text-white shadow-xl shadow-slate-900/20">
                        <h4 class="text-xs font-black uppercase tracking-[0.2em] mb-6 text-blue-400">Ajouter une Offre</h4>
                        <div class="space-y-6">
                            <input wire:model="offerTitle" type="text" placeholder="Titre de l'offre (ex: Vente de baklavas...)" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-sm font-bold placeholder:text-slate-600 focus:border-blue-500 transition-all uppercase tracking-tight">
                            <textarea wire:model="offerDescription" rows="2" placeholder="Détails de l'offre..." class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-xs font-medium placeholder:text-slate-600 focus:border-blue-500 transition-all italic"></textarea>
                            
                            <div>
                                <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-3 block">Compétences liées (Select multiple)</label>
                                <select multiple wire:model="offerSkills" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-xs font-black text-slate-400 focus:bg-slate-800 transition-all uppercase custom-scrollbar">
                                    @foreach($skills as $skill)
                                        <option value="{{ $skill->id }}">{{ $skill->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button wire:click="addOffer" class="w-full py-4 bg-blue-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                                Certifier cette offre
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- STEP 3: DEMANDES --}}
            @if($step === 3)
                <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight flex items-center gap-2 italic">
                             <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                             Nos Demandes ({{ count($demands) }})
                        </h3>
                    </div>

                    {{-- List existing demands --}}
                    <div class="space-y-4 mb-10">
                        @foreach($demands as $index => $dem)
                            <div class="p-6 bg-white/60 border border-white rounded-3xl flex justify-between items-start group hover:bg-white transition-all">
                                <div>
                                    <div class="text-sm font-black text-slate-900 uppercase">{{ $dem['title'] }}</div>
                                    <p class="text-[10px] text-slate-500 font-medium mt-1 line-clamp-2 italic">{{ $dem['description'] }}</p>
                                    @if(!empty($dem['skills']))
                                        <div class="flex gap-1 mt-2">
                                            @foreach($dem['skills'] as $sId)
                                                @php $s = $skills->find($sId); @endphp
                                                <span class="text-[7px] font-black bg-purple-50 text-purple-600 px-2 py-0.5 rounded-lg uppercase tracking-widest">{{ $s->name ?? 'Skill' }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <button wire:click="removeDemand({{ $index }})" class="text-slate-200 hover:text-red-500 transition-colors p-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    {{-- Form to add demand --}}
                    <div class="p-8 bg-slate-900 rounded-[2.5rem] text-white shadow-xl shadow-slate-900/20">
                        <h4 class="text-xs font-black uppercase tracking-[0.2em] mb-6 text-purple-400">Ajouter une Demande</h4>
                        <div class="space-y-6">
                            <input wire:model="demandTitle" type="text" placeholder="Ce que vous cherchez (ex: Livreur, Développeur...)" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-sm font-bold placeholder:text-slate-600 focus:border-purple-500 transition-all uppercase tracking-tight">
                            <textarea wire:model="demandDescription" rows="2" placeholder="Détails de la demande..." class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-xs font-medium placeholder:text-slate-600 focus:border-purple-500 transition-all italic"></textarea>
                            
                            <div>
                                <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-3 block">Compétences recherchées</label>
                                <select multiple wire:model="demandSkills" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-xs font-black text-slate-400 focus:bg-slate-800 transition-all uppercase custom-scrollbar">
                                    @foreach($skills as $skill)
                                        <option value="{{ $skill->id }}">{{ $skill->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button wire:click="addDemand" class="w-full py-4 bg-purple-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-purple-700 transition-all shadow-lg shadow-purple-500/20">
                                Certifier cette demande
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- STEP 4: REVIEW & CREATE --}}
            @if($step === 4)
                <div class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div class="text-center">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-6 border border-green-100">
                            Prêt pour le lancement
                        </div>
                        <h3 class="text-3xl font-black text-slate-900 uppercase tracking-tighter italic">Récapitulatif final</h3>
                    </div>

                    <div class="p-10 bg-slate-50/50 rounded-[3rem] border border-slate-100 space-y-8">
                        <div>
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">PROJET</span>
                            <div class="text-2xl font-black text-slate-900 uppercase italic">"{{ $title }}"</div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div class="bg-white p-6 rounded-3xl border border-slate-100">
                                <span class="text-2xl font-black text-blue-600 leading-none block mb-1">{{ count($offers) }}</span>
                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Offres certifiées</span>
                            </div>
                            <div class="bg-white p-6 rounded-3xl border border-slate-100">
                                <span class="text-2xl font-black text-purple-600 leading-none block mb-1">{{ count($demands) }}</span>
                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Demandes certifiées</span>
                            </div>
                        </div>

                        <div @class(['p-4 rounded-2xl text-[9px] font-black uppercase tracking-widest text-center border', 'bg-green-50 text-green-600 border-green-100' => $is_open, 'bg-slate-100 text-slate-500 border-slate-200' => !$is_open])>
                            Projet {{ $is_open ? 'OUVERT' : 'FERMÉ' }} au lancement
                        </div>
                    </div>

                    <button wire:click="create" class="w-full py-8 bg-slate-900 text-white rounded-[2.5rem] font-black text-sm tracking-[0.4em] uppercase hover:bg-blue-600 transition-all shadow-2xl shadow-blue-500/10 group">
                        <span class="inline-flex items-center gap-4 transition-transform group-hover:scale-110">
                            Lancer le projet définitif
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </span>
                    </button>
                </div>
            @endif

            {{-- NAVIGATION BUTTONS --}}
            <div class="mt-12 pt-8 border-t border-slate-100 flex items-center justify-between">
                @if($step > 1)
                    <button wire:click="previousStep" class="flex items-center gap-3 px-8 py-4 bg-slate-100 text-slate-500 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M11 19l-7-7 7-7"/></svg>
                        Retour
                    </button>
                @else
                    <div></div>
                @endif

                <div class="flex items-center gap-4">
                    @if($step < 4)
                        <button wire:click="create" class="flex-shrink-0 px-6 py-4 bg-white border border-slate-200 text-slate-400 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:border-slate-900 hover:text-slate-900 transition-all">
                            Lancer maintenant
                        </button>
                        <button wire:click="nextStep" class="flex items-center gap-3 px-8 md:px-10 py-4 bg-blue-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                            {{ $step === 1 ? 'Suivant : Offres' : ($step === 2 ? 'Suivant : Demandes' : 'Suivant : Récap') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.05); border-radius: 10px; }
    </style>
</div>
