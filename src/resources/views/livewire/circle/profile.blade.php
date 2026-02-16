<div class="min-h-screen bg-slate-50/50 pb-20">
    <!-- Circle Hero Header -->
    <div class="relative pt-32 pb-16 overflow-hidden">
        <!-- Background Accents -->
        <div class="absolute inset-0 -z-10">
            <div class="absolute top-[-20%] right-[-10%] w-[50%] aspect-square bg-blue-500/5 rounded-full blur-[140px]"></div>
            <div class="absolute bottom-0 left-0 w-[40%] aspect-square bg-indigo-500/5 rounded-full blur-[120px]"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6">
            <div class="bg-white/40 backdrop-blur-3xl border border-white/60 rounded-[4rem] p-10 md:p-16 shadow-2xl shadow-blue-500/5 relative overflow-hidden group">
                <!-- Top Badge Status -->
                <div class="absolute top-10 right-10">
                    <div class="flex items-center gap-3">
                        <span class="px-4 py-1.5 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-green-100 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-green-600 rounded-full animate-pulse"></span>
                            Cercle Actif
                        </span>
                        @if($circle->is_public)
                            <span class="px-4 py-1.5 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-100">Public</span>
                        @else
                            <span class="px-4 py-1.5 bg-amber-50 text-amber-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-amber-100">Privé</span>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col lg:flex-row gap-12 relative z-10">
                    <!-- Icon / Identity -->
                    <div class="flex-shrink-0">
                        <div class="w-32 h-32 bg-gradient-to-tr from-blue-600 to-indigo-700 rounded-[2.5rem] flex items-center justify-center text-white shadow-2xl shadow-blue-600/30 rotate-3 group-hover:rotate-6 transition-transform duration-500">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($circle->type === 'business')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                @endif
                            </svg>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="flex-grow">
                        <div class="flex flex-wrap items-center gap-4 mb-4">
                            <span class="text-xs font-black text-blue-600 uppercase tracking-[0.3em]">Cercle de confiance</span>
                            <span class="text-slate-200">/</span>
                            <span class="text-xs font-black text-slate-400 uppercase tracking-[0.3em]">{{ $circle->type }}</span>
                        </div>
                        <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tighter leading-none mb-6">
                            {{ $circle->name }}
                        </h1>
                        <p class="text-xl text-slate-500 font-medium max-w-2xl leading-relaxed mb-8">
                            {{ $circle->description }}
                        </p>

                        <!-- Stats Bar -->
                        <div class="grid grid-cols-3 gap-8 py-8 border-t border-slate-100">
                            <div>
                                <div class="text-3xl font-black text-slate-900 leading-none mb-1">{{ $circle->members->count() }}</div>
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Membres</div>
                            </div>
                            <div>
                                <div class="text-3xl font-black text-slate-900 leading-none mb-1">{{ $circle->achievements->count() }}</div>
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Preuves</div>
                            </div>
                            <div>
                                <div class="text-3xl font-black text-slate-900 leading-none mb-1">98%</div>
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Score Confiance</div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="lg:w-72 space-y-4">
                        <div class="p-6 bg-slate-900 rounded-[2.5rem] text-white">
                            <a href="{{ route('users.show', $circle->owner) }}" class="flex items-center gap-4 mb-6 group/owner transition-all">
                                <img src="{{ $circle->owner->avatar }}" class="w-12 h-12 rounded-2xl ring-2 ring-white/10 group-hover/owner:ring-blue-500 transition-all">
                                <div>
                                    <div class="text-xs font-black text-blue-400 uppercase tracking-widest">Fondateur</div>
                                    <div class="font-bold group-hover/owner:text-blue-400 transition-colors">{{ $circle->owner->name }}</div>
                                </div>
                            </a>
                            
                            @auth
                                @php 
                                    $isMember = $circle->members->contains('user_id', auth()->id()); 
                                    $isOwner = $circle->owner_id === auth()->id();
                                @endphp

                                @if($isOwner)
                                    <a href="{{ route('circles.edit', $circle) }}" class="block text-center py-4 bg-white/10 hover:bg-white/20 rounded-2xl font-black text-sm tracking-widest uppercase transition-all mb-3 text-white border border-white/5">
                                        Éditer
                                    </a>
                                    <button class="w-full text-center py-4 bg-blue-600 hover:bg-blue-700 rounded-2xl font-black text-sm tracking-widest uppercase transition-all shadow-xl shadow-blue-500/20">
                                        Inviter
                                    </button>
                                @elseif($isMember)
                                    <div class="w-full text-center py-4 bg-green-500/10 rounded-2xl font-black text-sm tracking-widest uppercase text-green-400 border border-green-500/20">
                                        Membre Actif
                                    </div>
                                @else
                                    <button class="w-full text-center py-4 bg-blue-600 hover:bg-blue-700 rounded-2xl font-black text-sm tracking-widest uppercase transition-all shadow-xl shadow-blue-500/20">
                                        Rejoindre
                                    </button>
                                @endif
                            @else
                                <a href="/admin/login" class="block text-center py-4 bg-blue-600 hover:bg-blue-700 rounded-2xl font-black text-sm tracking-widest uppercase transition-all shadow-xl shadow-blue-500/20">
                                    Se Connecter
                                </a>
                            @endauth
                        </div>
                        <div class="flex items-center justify-between px-6 py-4 bg-white/60 backdrop-blur-xl border border-white/60 rounded-2xl text-slate-500 font-bold text-xs uppercase tracking-widest">
                            <span>Localisation</span>
                            <span class="text-slate-900">{{ $circle->address }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="max-w-7xl mx-auto px-6 mt-12 grid grid-cols-1 lg:grid-cols-3 gap-12">
        
        <!-- Main Content: Skill Directory -->
        <div class="lg:col-span-2 space-y-12">
            <div>
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                        Répertoire des Compétences
                        <span class="text-xs font-black text-slate-400 border border-slate-200 px-3 py-1 rounded-full">{{ $circleSkills->count() }}</span>
                    </h2>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    @forelse($circleSkills as $skillId => $achievements)
                        @php $latestAch = $achievements->first(); @endphp
                        <div class="group relative bg-white/60 backdrop-blur-2xl border border-white/60 p-6 rounded-[2.5rem] hover:bg-white hover:shadow-xl transition-all duration-500">
                            <div class="flex flex-col md:flex-row items-center gap-6">
                                <!-- Skill Icon & Info -->
                                <div class="flex items-center gap-4 flex-shrink-0 min-w-[200px]">
                                    <div class="w-12 h-12 bg-slate-900 rounded-2xl flex items-center justify-center text-white text-xs font-black shadow-lg uppercase">
                                        {{ substr($latestAch->skill->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <h3 class="font-black text-slate-900 uppercase tracking-tight">{{ $latestAch->skill->name }}</h3>
                                        <span class="text-[9px] font-black text-blue-500 uppercase tracking-widest">{{ $achievements->count() }} expert(s)</span>
                                    </div>
                                </div>

                                <!-- Latest Proof & User -->
                                <div class="flex-grow flex flex-col md:flex-row items-center gap-6 border-l border-slate-100 pl-6 w-full">
                                    <div class="flex-grow">
                                        <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 italic">Dernière réussite :</div>
                                        <h4 class="font-black text-slate-900 text-sm italic group-hover:text-blue-600 transition-colors">"{{ $latestAch->title }}"</h4>
                                    </div>

                                    <a href="{{ route('users.show', $latestAch->user) }}" class="flex items-center gap-3 bg-slate-50 px-4 py-2 rounded-2xl border border-slate-100 flex-shrink-0 hover:bg-white hover:border-blue-200 transition-all group/u">
                                        <img src="{{ $latestAch->user->avatar }}" class="w-6 h-6 rounded-lg object-cover shadow-sm group-hover/u:scale-110 transition-transform">
                                        <span class="text-[10px] font-black text-slate-600 uppercase tracking-tight group-hover/u:text-blue-600 transition-colors">{{ $latestAch->user->name }}</span>
                                    </a>
                                </div>

                                <!-- Detail Action -->
                                <div class="flex-shrink-0">
                                    <button class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-20 text-center bg-white/40 border border-white/60 rounded-[3rem]">
                            <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-sm italic">Aucune compétence encore indexée.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Compact Members Section at Bottom -->
            <div class="pt-12 border-t border-slate-100">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.3em] mb-6">Membres du Cercle</h3>
                <div class="flex flex-wrap gap-4">
                    @foreach($circle->members as $member)
                        <a href="{{ route('users.show', $member->user) }}" class="flex items-center gap-3 bg-white/60 p-2 pr-4 rounded-2xl border border-white/60 hover:border-blue-200 hover:bg-white transition-colors group">
                            <img src="{{ $member->user->avatar }}" class="w-8 h-8 rounded-xl object-cover shadow-sm group-hover:scale-110 transition-transform">
                            <div class="min-w-0">
                                <div class="text-[10px] font-black text-slate-900 truncate group-hover:text-blue-600 transition-colors">{{ $member->user->name }}</div>
                                <div class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter">{{ $member->role }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar: Messaging & Logistics -->
        <div class="space-y-12">
            <div class="bg-slate-900 rounded-[3.5rem] p-10 shadow-2xl shadow-slate-900/20 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-blue-600/10 to-transparent"></div>
                
                <h2 class="text-3xl font-black mb-10 tracking-tight flex items-center gap-4 relative z-10">
                    Le Board
                    <span class="px-3 py-1 bg-white/10 text-xs rounded-full border border-white/10 font-black">{{ $circle->messages->count() }}</span>
                </h2>

                <!-- Messages Feed -->
                <div class="space-y-6 max-h-[600px] overflow-y-auto pr-4 mb-10 custom-scrollbar relative z-10">
                    @forelse($circle->messages as $msg)
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('users.show', $msg->sender) }}" class="flex items-center gap-3 group/msg hover:opacity-80 transition-all">
                                <img src="{{ $msg->sender->avatar }}" class="w-6 h-6 rounded-lg ring-1 ring-white/20 group-hover/msg:ring-blue-500 transition-all">
                                <span class="text-xs font-black uppercase tracking-widest text-slate-400 group-hover/msg:text-blue-400 transition-colors">{{ $msg->sender->name }}</span>
                                <span class="text-[9px] font-black text-slate-600 uppercase">{{ $msg->created_at->diffForHumans() }}</span>
                            </a>
                            <div class="bg-white/5 border border-white/10 p-5 rounded-2xl rounded-tl-none text-slate-300 text-sm leading-relaxed italic">
                                {{ $msg->content }}
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-slate-600 font-black uppercase tracking-[0.3em] text-[10px]">Silence radio...</div>
                    @endforelse
                </div>

                <!-- Input area if member -->
                @auth
                    @if($circle->members->contains('user_id', auth()->id()) || $circle->owner_id === auth()->id())
                        <div class="relative z-10 p-4 bg-white/5 border border-white/10 rounded-[2.5rem]">
                            <textarea wire:model="message" 
                                placeholder="Partagez une info logistique ou chat..." 
                                class="w-full bg-transparent border-none focus:ring-0 text-sm text-slate-200 placeholder:text-slate-600 mb-4 resize-none"
                                rows="3"></textarea>
                            
                            <button wire:click="sendMessage" 
                                class="w-full py-4 bg-white text-slate-900 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-500 hover:text-white transition-all shadow-xl shadow-white/5">
                                Envoyer
                            </button>
                        </div>
                    @else
                        <div class="relative z-10 text-center p-8 bg-white/5 border border-dashed border-white/10 rounded-3xl">
                            <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.3em]">Réservé aux membres vérifiés</p>
                        </div>
                    @endif
                @else
                    <div class="relative z-10 text-center p-8 bg-white/5 border border-dashed border-white/10 rounded-3xl">
                        <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.3em]">Connectez-vous pour voir les échanges</p>
                    </div>
                @endauth
            </div>
            
            <!-- Information Grid -->
            <div class="bg-white/60 backdrop-blur-xl border border-white/60 rounded-[3rem] p-10 space-y-8">
                <div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4">Stack Verified</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($circle->achievements->pluck('skill.name')->unique() as $skill)
                            <span class="px-4 py-2 bg-slate-100 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-wider">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
                
                <div class="pt-8 border-t border-slate-100">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4">Galerie Preuve</h3>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($circle->achievements->take(4) as $ach)
                            <div class="relative group aspect-square overflow-hidden rounded-2xl bg-slate-100">
                                <img src="{{ $ach->media_url ?? 'https://picsum.photos/seed/'.$ach->id.'/300/300' }}" class="w-full h-full object-cover opacity-60 group-hover:opacity-100 group-hover:scale-110 transition-all duration-700">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.2);
        }
    </style>
</div>
