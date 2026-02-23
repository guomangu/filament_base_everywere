<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
    {{-- Achievement Header --}}
    <div class="relative overflow-hidden bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[3rem] p-8 md:p-12 shadow-2xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-4 py-1.5 bg-blue-500/10 text-blue-600 text-[10px] font-black uppercase tracking-widest rounded-full border border-blue-500/20">
                        Preuve d'Expertise
                    </span>
                    @if($achievement->skill)
                        <a href="{{ route('mission.show', $achievement->skill) }}" class="px-4 py-1.5 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-full hover:bg-blue-600 transition-all">
                            {{ $achievement->skill->name }}
                        </a>
                    @endif
                </div>
                <h1 class="text-4xl md:text-6xl font-black text-slate-930 leading-[0.9] uppercase tracking-tighter mb-6">
                    {{ $achievement->title }}
                </h1>
                <div class="flex items-center gap-4 mb-6">
                    <a href="{{ route('users.show', $achievement->user) }}" class="flex items-center gap-3 group">
                        <img src="{{ $achievement->user->avatar }}" class="w-12 h-12 rounded-2xl object-cover border-2 border-white shadow-lg group-hover:border-blue-500 transition-all">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Expert</p>
                            <p class="text-xs font-black text-slate-900 uppercase group-hover:text-blue-600 transition-colors">{{ $achievement->user->name }}</p>
                        </div>
                    </a>
                    @if($achievement->proche)
                        <div class="w-px h-8 bg-slate-200"></div>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Pour le proche</p>
                                <p class="text-xs font-black text-slate-900 uppercase">{{ $achievement->proche->name }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="shrink-0 flex flex-col items-center gap-4">
                <div @class([
                    'w-32 h-32 rounded-[2.5rem] flex flex-col items-center justify-center border-4 border-white shadow-2xl rotate-3 relative overflow-hidden group',
                    'bg-green-500 text-white' => $achievement->is_verified,
                    'bg-slate-100 text-slate-400' => !$achievement->is_verified
                ])>
                    @if($achievement->is_verified)
                        <svg class="w-12 h-12 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Vérifié</span>
                    @else
                        <svg class="w-12 h-12 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">En attente</span>
                    @endif
                </div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Classé le {{ $achievement->realized_at?->translatedFormat('d F Y') ?? $achievement->created_at->translatedFormat('d F Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2 space-y-12">
            {{-- Description --}}
            <section class="bg-white/40 backdrop-blur-xl border border-white/60 rounded-[2.5rem] p-10 shadow-xl">
                <h2 class="text-xl font-black uppercase tracking-tighter mb-6 flex items-center gap-3">
                    <span class="w-1.5 h-6 bg-blue-600 rounded-full"></span>
                    Détails de la réussite
                </h2>
                <p class="text-slate-600 text-lg font-medium leading-relaxed italic whitespace-pre-line">
                    {{ $achievement->description ?: 'Aucune description détaillée fournie.' }}
                </p>
            </section>

            {{-- Proofs / Information --}}
            @if($achievement->informations->count() > 0)
                <section>
                    <h2 class="text-xl font-black uppercase tracking-tighter mb-8 flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-slate-900 rounded-full"></span>
                        Preuves & Documents ({{ $achievement->informations->count() }})
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach($achievement->informations as $info)
                            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm hover:shadow-xl transition-all group">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                        @if($info->type === 'link')
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.828a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                        @elseif($info->type === 'video')
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        @else
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-tight">{{ $info->label ?: 'Document' }}</h4>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase truncate max-w-[150px]">{{ $info->title }}</p>
                                    </div>
                                </div>
                                <a href="{{ $info->url }}" target="_blank" class="w-full py-3 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all flex items-center justify-center gap-2">
                                    Consulter la preuve
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Validations --}}
            <section>
                <h2 class="text-xl font-black uppercase tracking-tighter mb-8 flex items-center gap-3">
                    <span @class(['w-1.5 h-6 rounded-full', 'bg-green-500' => $achievement->is_verified, 'bg-slate-300' => !$achievement->is_verified])></span>
                    Validations & Avis des Pairs ({{ $achievement->validations->count() }})
                </h2>
                
                @if($achievement->validations->isEmpty())
                    <div class="bg-slate-50 border border-dashed border-slate-200 rounded-[2.5rem] p-12 text-center">
                        <p class="text-slate-400 font-black uppercase tracking-[0.3em] text-[10px]">Aucune validation pour le moment</p>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach($achievement->validations as $val)
                            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex items-start gap-6">
                                <img src="{{ $val->user->avatar }}" class="w-12 h-12 rounded-2xl object-cover shrink-0">
                                <div class="flex-grow">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-black text-slate-900 uppercase">{{ $val->user->name }}</span>
                                            @if($val->type === 'validate')
                                                <span class="px-2 py-0.5 bg-green-50 text-green-600 text-[8px] font-black uppercase rounded-md border border-green-100">Approuve</span>
                                            @else
                                                <span class="px-2 py-0.5 bg-red-50 text-red-600 text-[8px] font-black uppercase rounded-md border border-red-100">Conteste</span>
                                            @endif
                                        </div>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $val->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-slate-500 text-sm italic leading-relaxed">{{ $val->comment ?: 'A validé cette expertise sans commentaire.' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-12">
            <div class="bg-slate-900 rounded-[3rem] p-10 text-white shadow-3xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 rounded-full blur-[100px]"></div>
                
                <h3 class="text-sm font-black uppercase tracking-[0.3em] mb-8 text-blue-400 relative z-10 flex items-center gap-3">
                    <span class="w-1.5 h-6 bg-blue-500 rounded-full"></span>
                    À propos de l'Expert
                </h3>
                
                <div class="flex flex-col items-center text-center relative z-10">
                    <div class="relative mb-6">
                        <a href="{{ route('users.show', $achievement->user) }}" class="block p-1 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-[2rem] hover:rotate-6 transition-all">
                            <img src="{{ $achievement->user->avatar }}" class="w-24 h-24 rounded-[1.8rem] object-cover border-4 border-slate-900 shadow-2xl">
                        </a>
                        <div class="absolute -bottom-2 -right-2 bg-white p-2 rounded-xl shadow-xl">
                            <span class="text-xs font-black text-blue-600">{{ $achievement->user->trust_score }}%</span>
                        </div>
                    </div>
                    
                    <h4 class="text-xl font-black uppercase tracking-tight mb-2">{{ $achievement->user->name }}</h4>
                    <p class="text-[10px] font-medium text-slate-400 italic mb-8 uppercase tracking-widest">{{ $achievement->user->location ?: 'Localisation non définie' }}</p>
                    
                    <div class="grid grid-cols-2 gap-4 w-full mb-8">
                        <div class="bg-white/5 rounded-2xl p-4 border border-white/10">
                            <div class="text-xl font-black text-white">{{ $achievement->user->achievements->count() }}</div>
                            <div class="text-[8px] font-black text-slate-500 uppercase tracking-widest">Expertises</div>
                        </div>
                        <div class="bg-white/5 rounded-2xl p-4 border border-white/10">
                            <div class="text-xl font-black text-white">{{ $achievement->user->validationsReceived()->count() }}</div>
                            <div class="text-[8px] font-black text-slate-500 uppercase tracking-widest">Avis reçus</div>
                        </div>
                    </div>

                    <a href="{{ route('users.show', $achievement->user) }}" class="w-full py-4 bg-white text-slate-900 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-blue-600 hover:text-white transition-all">
                        Voir le profil complet
                    </a>
                </div>
            </div>

            @if($achievement->skill)
                <div class="bg-white border border-slate-100 rounded-[3rem] p-10 shadow-xl relative overflow-hidden group">
                    <h3 class="text-[11px] font-black uppercase tracking-[0.4em] text-slate-400 mb-8 flex items-center gap-3">
                        <span class="w-2 h-4 bg-blue-600 rounded-full"></span>
                        Compétence Clé
                    </h3>
                    
                    <a href="{{ route('mission.show', $achievement->skill) }}" class="flex flex-col items-center text-center group/skill">
                        <div class="w-16 h-16 bg-slate-900 rounded-2xl flex items-center justify-center text-white shadow-xl mb-4 group-hover/skill:bg-blue-600 group-hover/skill:rotate-6 transition-all">
                            <span class="text-2xl font-black uppercase">{{ substr($achievement->skill->name, 0, 1) }}</span>
                        </div>
                        <h4 class="text-xl font-black text-slate-930 uppercase tracking-tight group-hover/skill:text-blue-600 transition-all">{{ $achievement->skill->name }}</h4>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-2">Dossier #{{ $achievement->skill->id }}</p>
                        
                        <div class="mt-8 pt-8 border-t border-slate-100 w-full">
                            <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-slate-500">
                                <span>Experts</span>
                                <span class="text-slate-900">{{ \App\Models\User::whereHas('achievements', fn($q) => $q->where('skill_id', $achievement->skill->id))->count() }}</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
