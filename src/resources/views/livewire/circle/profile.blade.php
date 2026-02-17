<div 
    x-data="{ 
        showIndicator: false,
        playNotify() {
            let audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2358/2358-preview.mp3');
            audio.volume = 0.2;
            audio.play().catch(e => console.log('Audio play failed:', e));
        }
    }" 
    x-on:circle-updated.window="showIndicator = true; playNotify(); setTimeout(() => showIndicator = false, 3000)"
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
    <!-- Circle Hero Header -->
    <div class="relative pt-32 pb-16 overflow-hidden">
        <!-- Background Accents -->
        <div class="absolute inset-0 -z-10">
            <div class="absolute top-[-20%] right-[-10%] w-[50%] aspect-square bg-blue-500/5 rounded-full blur-[140px]"></div>
            <div class="absolute bottom-0 left-0 w-[40%] aspect-square bg-indigo-500/5 rounded-full blur-[120px]"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6">
            <div class="bg-white/40 backdrop-blur-3xl border border-white/60 rounded-[3rem] md:rounded-[4rem] p-8 md:p-16 shadow-2xl shadow-blue-500/5 relative overflow-hidden group">
                <!-- Top Badge Status -->
                <div class="flex flex-wrap items-center gap-2 md:gap-3 mb-8 md:absolute md:top-10 md:right-10 md:mb-0">
                    <span class="px-3 md:px-4 py-1.5 bg-green-50 text-green-600 rounded-full text-[9px] md:text-[10px] font-black uppercase tracking-widest border border-green-100 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-green-600 rounded-full animate-pulse"></span>
                        Cercle Actif
                    </span>
                    @if($circle->is_public)
                        <span class="px-3 md:px-4 py-1.5 bg-blue-50 text-blue-600 rounded-full text-[9px] md:text-[10px] font-black uppercase tracking-widest border border-blue-100">Public</span>
                    @else
                        <span class="px-3 md:px-4 py-1.5 bg-amber-50 text-amber-600 rounded-full text-[9px] md:text-[10px] font-black uppercase tracking-widest border border-amber-100">Privé</span>
                    @endif
                </div>

                <div class="flex flex-col lg:flex-row gap-12 relative z-10">
                    <!-- Icon / Identity -->
                    <div class="flex-shrink-0 flex justify-center lg:block">
                        <div class="w-24 h-24 md:w-32 md:h-32 bg-gradient-to-tr from-blue-600 to-indigo-700 rounded-[2rem] md:rounded-[2.5rem] flex items-center justify-center text-white shadow-2xl shadow-blue-600/30 rotate-3 group-hover:rotate-6 transition-transform duration-500">
                            <svg class="w-12 h-12 md:w-16 md:h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <div class="flex flex-col md:flex-row items-center gap-4 mb-6">
                            <h1 class="text-3xl md:text-7xl font-black text-slate-900 tracking-tighter leading-none text-center md:text-left">
                                {{ $circle->name }}
                            </h1>
                            <livewire:information.manager :model="$circle" :key="'circle-info-'.$circle->id" />
                        </div>
                        <p class="text-xl text-slate-500 font-medium max-w-2xl leading-relaxed mb-8">
                            {{ $circle->description }}
                        </p>

                        <!-- Stats Bar -->
                        <div class="grid grid-cols-3 gap-4 md:gap-8 py-8 border-t border-slate-100">
                            <div class="text-center md:text-left">
                                <div class="text-xl md:text-3xl font-black text-slate-900 leading-none mb-1">{{ $circle->activeMembers->count() }}</div>
                                <div class="text-[8px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest">Membres</div>
                            </div>
                            <div class="text-center md:text-left">
                                <div class="text-xl md:text-3xl font-black text-slate-900 leading-none mb-1">{{ $circle->achievements->count() }}</div>
                                <div class="text-[8px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest">Preuves</div>
                            </div>
                            <div class="text-center md:text-left">
                                <div class="text-xl md:text-3xl font-black text-slate-900 leading-none mb-1">98%</div>
                                <div class="text-[8px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Confiance</div>
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
                                    <div class="space-y-3">
                                        <a href="{{ route('circles.edit', $circle) }}" class="block text-center py-4 bg-white/10 hover:bg-white/20 rounded-2xl font-black text-sm tracking-widest uppercase transition-all text-white border border-white/5">
                                            Configuration
                                        </a>
                                        
                                        @php $pendingRequests = $circle->members()->where('status', 'pending')->with('user')->get(); @endphp
                                        @if($pendingRequests->count() > 0)
                                            <div class="pt-4 border-t border-white/10">
                                                <div class="text-[9px] font-black text-blue-400 uppercase tracking-widest mb-4">Demandes d'accès ({{ $pendingRequests->count() }})</div>
                                                <div class="space-y-3">
                                                    @foreach($pendingRequests as $req)
                                                        <div class="flex items-center justify-between bg-white/5 p-3 rounded-xl border border-white/5">
                                                            <div class="flex items-center gap-2">
                                                                <img src="{{ $req->user->avatar }}" class="w-6 h-6 rounded-lg">
                                                                <span class="text-[10px] font-bold truncate max-w-[80px]">{{ $req->user->name }}</span>
                                                            </div>
                                                            <div class="flex gap-1">
                                                                <button wire:click="toggleApprove({{ $req->id }}, 'active')" class="p-1.5 bg-green-500/20 text-green-400 rounded-lg hover:bg-green-500 transition-colors">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                                </button>
                                                                <button wire:click="toggleApprove({{ $req->id }}, 'rejected')" class="p-1.5 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500 transition-colors">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    @php 
                                        $membership = $circle->members()->where('user_id', auth()->id())->first();
                                    @endphp

                                    @if($membership && $membership->status === 'active')
                                        <div class="space-y-3">
                                            <div class="w-full text-center py-4 bg-green-500/10 rounded-2xl font-black text-sm tracking-widest uppercase text-green-400 border border-green-500/20">
                                                Membre Actif
                                            </div>
                                            <button wire:click="leaveCircle" class="w-full text-center py-2 text-slate-500 hover:text-red-400 font-bold text-[10px] uppercase tracking-widest transition-colors">
                                                Quitter le cercle
                                            </button>
                                        </div>
                                    @elseif($membership && $membership->status === 'pending')
                                        <div class="w-full text-center py-4 bg-amber-500/10 rounded-2xl font-black text-sm tracking-widest uppercase text-amber-400 border border-amber-500/20">
                                            Demande en attente
                                        </div>
                                    @else
                                        <button wire:click="joinCircle" class="w-full text-center py-4 bg-blue-600 hover:bg-blue-700 rounded-2xl font-black text-sm tracking-widest uppercase transition-all shadow-xl shadow-blue-500/20">
                                            Demander à rejoindre
                                        </button>
                                    @endif
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
                        <span class="text-xs font-black text-slate-400 border border-slate-200 px-3 py-1 rounded-full">{{ $allSkills->count() }}</span>
                    </h2>
                </div>

                <div class="flex flex-wrap gap-3">
                    @forelse($allSkills as $item)
                        <a href="{{ route('users.show', $item['expert_id']) }}" @class([
                            'group/tag flex items-center gap-2 px-4 py-2 rounded-2xl border transition-all shadow-sm',
                            'bg-white border-slate-100 hover:border-blue-500 hover:shadow-md' => !$item['is_extended'],
                            'bg-slate-50 border-dashed border-slate-200 hover:bg-white hover:border-blue-300' => $item['is_extended'],
                        ])>
                            <div @class([
                                'w-2 h-2 rounded-full',
                                'bg-blue-500' => !$item['is_extended'],
                                'bg-slate-300 group-hover/tag:bg-blue-300' => $item['is_extended'],
                            ])></div>
                            <span class="text-xs font-black uppercase tracking-tight text-slate-900">{{ $item['skill'] }}</span>
                            <span class="text-[9px] font-bold text-slate-400 group-hover/tag:text-blue-500 transition-colors italic">· {{ $item['expert'] }}</span>
                            @if($item['is_extended'])
                                <svg class="w-3 h-3 text-slate-300 group-hover/tag:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            @endif
                        </a>
                    @empty
                        <div class="w-full py-20 text-center bg-white/40 border-2 border-dashed border-slate-200 rounded-[3rem]">
                            <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-sm italic">Aucune expertise encore indexée.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Compact Members Section at Bottom -->
            <div class="pt-12 border-t border-slate-100">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.3em] mb-6">Membres Actifs</h3>
                <div class="flex flex-wrap gap-3">
                    @foreach($circle->activeMembers as $member)
                        <a href="{{ route('users.show', $member->user) }}" class="flex items-center gap-2 md:gap-3 bg-white/60 p-1.5 md:p-2 pr-3 md:pr-4 rounded-xl md:rounded-2xl border border-white/60 hover:border-blue-200 hover:bg-white transition-colors group">
                            <img src="{{ $member->user->avatar }}" class="w-6 h-6 md:w-8 md:h-8 rounded-lg md:rounded-xl object-cover shadow-sm group-hover:scale-110 transition-transform">
                            <div class="min-w-0">
                                <div class="text-[9px] md:text-[10px] font-black text-slate-900 truncate group-hover:text-blue-600 transition-colors max-w-[80px] md:max-w-none">{{ $member->user->name }}</div>
                                <div class="text-[7px] md:text-[8px] font-bold text-slate-400 uppercase tracking-tighter">{{ $member->role }}</div>
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

                <!-- Input area if authenticated -->
                @auth
                    @php 
                        $isActiveMember = $circle->activeMembers->contains('user_id', auth()->id());
                        $isOwner = $circle->owner_id === auth()->id();
                    @endphp

                    <div class="relative z-10 p-4 bg-white/5 border border-white/10 rounded-[2.5rem] mb-10">
                        <textarea wire:model="message" 
                            placeholder="{{ ($isActiveMember || $isOwner) ? 'Partagez une info logistique...' : 'Posez une question en tant qu\'invité...' }}" 
                            class="w-full bg-transparent border-none focus:ring-0 text-sm text-slate-200 placeholder:text-slate-600 mb-4 resize-none"
                            rows="3"></textarea>
                        
                        <button wire:click="sendMessage" 
                            class="w-full py-4 bg-white text-slate-900 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-500 hover:text-white transition-all shadow-xl shadow-white/5">
                            Envoyer {{ ($isActiveMember || $isOwner) ? '' : '(Invité)' }}
                        </button>
                    </div>
                @else
                    <div class="relative z-10 text-center p-8 bg-white/5 border border-dashed border-white/10 rounded-3xl mb-10">
                        <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.3em]">Connectez-vous pour participer</p>
                    </div>
                @endauth

                <!-- Messages Feed -->
                @php
                    $activeMemberIds = $circle->activeMembers->pluck('user_id')->toArray();
                @endphp
                <div class="space-y-6 max-h-[600px] overflow-y-auto pr-4 custom-scrollbar relative z-10">
                    @forelse($circle->messages as $msg)
                        @php
                            $isOwner = $msg->sender_id === $circle->owner_id;
                            $isMember = in_array($msg->sender_id, $activeMemberIds);
                            $isGuest = !$isOwner && !$isMember;
                        @endphp
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center justify-between">
                                <a href="{{ route('users.show', $msg->sender) }}" class="flex items-center gap-3 group/msg hover:opacity-80 transition-all">
                                    <img src="{{ $msg->sender->avatar }}" class="w-6 h-6 rounded-lg ring-1 ring-white/20 group-hover/msg:ring-blue-500 transition-all">
                                    <span class="text-xs font-black uppercase tracking-widest text-slate-400 group-hover/msg:text-blue-400 transition-colors">{{ $msg->sender->name }}</span>
                                    @if($isGuest)
                                        <span class="px-2 py-0.5 bg-blue-500/10 text-blue-400 rounded-md text-[8px] font-black uppercase tracking-widest border border-blue-500/20">Invité</span>
                                    @endif
                                    <span class="text-[9px] font-black text-slate-600 uppercase">{{ $msg->created_at->diffForHumans() }}</span>
                                </a>
                            </div>
                            <div class="bg-white/5 border border-white/10 p-5 rounded-2xl rounded-tl-none text-slate-300 text-sm leading-relaxed italic">
                                {{ $msg->content }}
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-slate-600 font-black uppercase tracking-[0.3em] text-[10px]">Silence radio...</div>
                    @endforelse
                </div>
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
