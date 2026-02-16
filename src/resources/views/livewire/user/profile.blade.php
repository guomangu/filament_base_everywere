<div class="min-h-screen bg-slate-50/50 pb-20 pt-32">
    <!-- User Header Portfolio -->
    <div class="max-w-7xl mx-auto px-6 mb-16">
        <div class="bg-white/40 backdrop-blur-3xl border border-white/60 rounded-[4rem] p-10 md:p-16 shadow-2xl shadow-blue-500/5 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="flex flex-col md:flex-row items-center gap-12">
                    <div class="relative flex-shrink-0">
                        <div class="absolute inset-0 bg-blue-600/20 rounded-[3.5rem] blur-2xl group-hover:blur-3xl transition-all duration-700"></div>
                        <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.$user->name }}" class="relative w-48 h-48 md:w-64 md:h-64 rounded-[3.5rem] object-cover border-4 border-white shadow-2xl transition-transform duration-700 group-hover:scale-[1.02]">
                        <div class="absolute -bottom-4 -right-4 w-20 h-20 bg-slate-900 rounded-3xl flex flex-col items-center justify-center shadow-2xl border-4 border-white">
                            <span class="text-xs font-black text-blue-400 uppercase tracking-widest leading-none mb-1">Score</span>
                            <span class="text-3xl font-black text-white leading-none">{{ $user->trust_score }}</span>
                        </div>
                    </div>

                    <div class="flex-grow text-center md:text-left">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-6 border border-blue-100">
                            Profil Vérifié
                        </div>
                        <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tighter leading-none mb-6">{{ $user->name }}</h1>
                        <p class="text-slate-500 font-medium text-xl max-w-2xl leading-relaxed italic">
                            "{{ $user->bio ?? 'Ce bâtisseur de confiance n\'a pas encore rédigé sa bio.' }}"
                        </p>
                        
                        <div class="flex flex-wrap justify-center md:justify-start gap-4 mt-10">
                            @auth
                                @if(auth()->id() !== $user->id)
                                    <button class="px-10 py-4 bg-slate-900 text-white rounded-[2rem] font-black text-sm tracking-widest uppercase hover:bg-blue-600 transition-all shadow-xl shadow-slate-900/10">
                                        Se Porter Garant
                                    </button>
                                    <button class="px-10 py-4 bg-white border-2 border-slate-100 text-slate-900 rounded-[2rem] font-black text-sm tracking-widest uppercase hover:border-blue-600 hover:text-blue-600 transition-all">
                                        Message
                                    </button>
                                @else
                                    <button wire:click="openCreateModal" class="px-10 py-4 bg-blue-600 text-white rounded-[2rem] font-black text-sm tracking-widest uppercase hover:bg-blue-700 transition-all shadow-xl shadow-blue-500/20 flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                        Ajouter un Succès
                                    </button>
                                    <a href="{{ route('profile.edit') }}" class="px-10 py-4 bg-white border-2 border-slate-100 text-slate-900 rounded-[2rem] font-black text-sm tracking-widest uppercase hover:border-slate-900 transition-all">
                                        Éditer Profil
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-blue-400/10 to-transparent"></div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-12">
        <!-- Sidebar -->
        <div class="lg:col-span-4 space-y-12">
            <!-- Stats -->
            <div class="bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[3.5rem] p-10 shadow-2xl shadow-blue-500/5">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-10">Métriques de Confiance</h3>
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
                    <div class="grid grid-cols-2 gap-4 pt-8 border-t border-slate-50">
                        <div class="p-6 bg-slate-50 rounded-3xl">
                            <div class="text-2xl font-black text-slate-900 leading-none mb-1">{{ $totalVouchs }}</div>
                            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Garants</div>
                        </div>
                        <div class="p-6 bg-slate-50 rounded-3xl">
                            <div class="text-2xl font-black text-slate-900 leading-none mb-1">{{ $user->joinedCircles->count() }}</div>
                            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Cercles</div>
                        </div>
                    </div>
                </div>
            </div>
 
            <!-- Circles Joined -->
            <div class="bg-slate-900 rounded-[3.5rem] p-10 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-blue-600/10 to-transparent"></div>
                <h3 class="text-2xl font-black mb-8 tracking-tight relative z-10">Cercles de Confiance</h3>
                <div class="space-y-3 relative z-10">
                    @forelse($user->joinedCircles as $circle)
                        <a href="{{ route('circles.show', $circle) }}" class="flex items-center gap-4 p-4 bg-white/5 border border-white/5 hover:bg-white/10 hover:border-white/10 rounded-2xl transition-all group">
                            <div class="w-10 h-10 bg-blue-600/20 rounded-xl flex items-center justify-center text-blue-400 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <div class="min-w-0 flex-grow">
                                <h4 class="font-bold text-slate-200 truncate text-sm">{{ $circle->name }}</h4>
                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-500 italic">{{ $circle->type }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="py-8 text-center text-slate-600 text-[10px] font-black uppercase tracking-widest">Aucun cercle rejoint...</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Portfolio: Skill-Centric View -->
        <div class="lg:col-span-8 space-y-12">
            <div class="flex items-center justify-between mb-10">
                <h2 class="text-4xl font-black text-slate-900 tracking-tighter">Expertises & Réalisations</h2>
            </div>
            
            <div class="space-y-16">
                @forelse($groupedAchievements as $skillName => $achievements)
                    <div class="relative">
                        <!-- Skill Header -->
                        <div class="flex items-center gap-6 mb-8">
                            <div class="w-16 h-16 bg-slate-900 rounded-[1.5rem] flex items-center justify-center text-white shadow-xl rotate-3">
                                <span class="text-xl font-black uppercase">{{ substr($skillName, 0, 1) }}</span>
                            </div>
                            <div class="flex-grow">
                                <div class="flex items-center gap-4 mb-2">
                                    <h3 class="text-2xl font-black text-slate-900 leading-none uppercase tracking-tight">{{ $skillName }}</h3>
                                    @auth
                                        @if(auth()->id() === $user->id)
                                            <button wire:click="addProofForSkill('{{ $skillName }}')" class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center hover:bg-slate-900 transition-all shadow-lg shadow-blue-500/20 group/btn">
                                                <svg class="w-5 h-5 group-hover/btn:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                            </button>
                                        @endif
                                    @endauth
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $achievements->count() }} Preuve(s) certifiée(s)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Proofs under this skill -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pl-10 md:pl-20 relative">
                            <!-- Timeline Connector -->
                            <div class="absolute left-8 md:left-[4.5rem] top-0 bottom-0 w-px bg-gradient-to-b from-slate-200 via-slate-100 to-transparent"></div>
                            
                            @foreach($achievements as $achievement)
                                <div class="group relative">
                                    <!-- Point -->
                                    <div class="absolute -left-[4.5rem] md:-left-[5rem] top-8 w-4 h-4 rounded-full bg-white border-4 border-slate-900 shadow-lg z-10 group-hover:bg-blue-600 group-hover:border-blue-200 transition-all duration-500"></div>

                                    <div class="relative bg-white/60 backdrop-blur-2xl border border-white/60 p-8 rounded-[2.5rem] hover:bg-white transition-all duration-500 group-hover:shadow-[0_40px_80px_-15px_rgba(59,130,246,0.08)]">
                                        <div class="flex items-start justify-between mb-6">
                                            <span class="text-[9px] font-black uppercase text-slate-300 tracking-widest">{{ $achievement->created_at->format('M Y') }}</span>
                                            @if($achievement->is_verified)
                                                <div class="w-6 h-6 bg-green-50 text-green-500 rounded-lg flex items-center justify-center border border-green-100">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <h4 class="text-xl font-black text-slate-900 mb-4 tracking-tight leading-tight italic">"{{ $achievement->title }}"</h4>
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
                                    {{ $step === 1 ? 'Quelle est votre expertise ?' : 'Partagez une réalisation' }}
                                </h3>
                                <p class="text-slate-400 text-xs font-bold mt-2 uppercase tracking-widest">
                                    {{ $step === 1 ? 'Étape 1 sur 2 : La compétence' : 'Étape 2 sur 2 : La preuve factuelle' }}
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

                            <button wire:click="goToStep2" class="w-full py-6 bg-slate-900 text-white rounded-[2.5rem] font-black text-sm tracking-[0.3em] uppercase hover:bg-blue-600 transition-all shadow-2xl shadow-slate-900/20">
                                Continuer vers la preuve
                            </button>
                        </div>
                    @else
                        <!-- Step 2 Form -->
                        <div class="space-y-6">
                            <div class="flex items-center gap-4 p-4 bg-blue-50 rounded-2xl border border-blue-100 mb-4">
                                <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Compétence :</span>
                                <span class="text-sm font-black text-slate-900 italic">"{{ $skillName }}"</span>
                                <button wire:click="$set('step', 1)" class="ml-auto text-[9px] font-black text-blue-600 uppercase border-b border-blue-600">Modifier</button>
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">Titre de la réussite</label>
                                <input wire:model="proofTitle" type="text" placeholder="ex: Chef de partie au restaurant X..." 
                                    class="w-full bg-slate-50 border-white focus:ring-blue-500 rounded-2xl p-4 text-sm font-bold italic">
                                @error('proofTitle') <span class="text-red-500 text-[10px] font-black uppercase mt-2 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">Description / Contexte</label>
                                <textarea wire:model="proofDescription" rows="4" placeholder="Expliquez ce que vous avez accompli, le résultat concret..." 
                                    class="w-full bg-slate-50 border-white focus:ring-blue-500 rounded-2xl p-4 text-sm font-bold italic"></textarea>
                                @error('proofDescription') <span class="text-red-500 text-[10px] font-black uppercase mt-2 block">{{ $message }}</span> @enderror
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
</div>
