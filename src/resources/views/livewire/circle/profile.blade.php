<div 
    x-data="{ 
        showIndicator: false,
        timer: null,
        playNotify() {
            let audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2358/2358-preview.mp3');
            audio.volume = 0.2;
            audio.play().catch(e => {});
        }
    }" 
    x-on:circle-updated.window="
        showIndicator = true; 
        playNotify(); 
        if(timer) clearTimeout(timer);
        timer = setTimeout(() => showIndicator = false, 5000)
    "
    wire:poll.5s.visible="refresh" 
    class="min-h-screen pb-12"
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
    <div class="relative pt-6 pb-12 overflow-hidden">
        <!-- Background Accents -->
        <div class="absolute inset-0 -z-10">
            <div class="absolute top-[-20%] right-[-10%] w-[50%] aspect-square bg-blue-500/5 rounded-full blur-[140px]"></div>
            <div class="absolute bottom-0 left-0 w-[40%] aspect-square bg-indigo-500/5 rounded-full blur-[120px]"></div>
        </div>

        <div class="max-w-7xl mx-auto">
            <div class="bg-white/40 backdrop-blur-3xl border border-white/60 rounded-[3rem] md:rounded-[4rem] p-6 md:p-10 shadow-2xl shadow-blue-500/5 relative overflow-hidden group">
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
                            <span class="text-xs font-black text-slate-400 uppercase tracking-[0.3em]">CERCLE</span>
                        </div>
                        <div class="flex flex-col md:flex-row items-center gap-4 mb-4">
                            <h1 class="text-3xl md:text-7xl font-black text-slate-900 tracking-tighter leading-none text-center md:text-left">
                                {{ $circle->name }}
                            </h1>
                            <livewire:information.manager :model="$circle" :key="'circle-info-'.$circle->id" />
                        </div>
                        <p class="text-xl text-slate-500 font-medium max-w-2xl leading-relaxed mb-6">
                            {{ $circle->description }}
                        </p>

                        <!-- Stats Bar -->
                        <div class="grid grid-cols-3 gap-4 md:gap-8 py-6 border-t border-slate-100">
                            <div class="text-center md:text-left">
                                <div class="text-xl md:text-3xl font-black text-slate-900 leading-none mb-1">{{ $circle->activeMembers->count() }}</div>
                                <div class="text-[8px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest">Membres</div>
                            </div>
                            <div class="text-center md:text-left">
                                <div class="text-xl md:text-3xl font-black text-slate-900 leading-none mb-1">{{ $circle->getValidatedAchievementsCount() }}</div>
                                <div class="text-[8px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest">Preuves</div>
                            </div>
                            <div class="text-center md:text-left">
                                <div class="text-xl md:text-3xl font-black text-slate-900 leading-none mb-1">{{ $circle->getAverageTrustScore() }}%</div>
                                <div class="text-[8px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Confiance</div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="lg:w-72 space-y-4">
                        <div class="p-6 bg-white/60 backdrop-blur-xl border border-white/60 rounded-[2.5rem] shadow-xl">
                            <div class="flex items-center justify-between mb-6 px-2">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Membres du Cercle</span>
                                <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full ring-1 ring-blue-100">{{ $circle->activeMembers->count() + 1 }}</span>
                            </div>

                            <div class="space-y-3 mb-8 max-h-[320px] overflow-y-auto pr-2 custom-scrollbar">
                                {{-- Owner (Fondateur) --}}
                                <div class="flex items-center gap-3 p-3 bg-slate-900 rounded-2xl text-white shadow-lg relative group/m">
                                    <a href="{{ route('users.show', $circle->owner) }}" class="shrink-0 relative">
                                        <img src="{{ $circle->owner->avatar }}" class="w-9 h-9 rounded-xl object-cover ring-2 ring-white/10 group-hover/m:ring-blue-400 transition-all">
                                        <div class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-blue-600 rounded-full border-2 border-slate-900 flex items-center justify-center" title="Fondateur">
                                            <span class="text-[7px] font-black uppercase">F</span>
                                        </div>
                                    </a>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-[11px] font-black truncate">{{ $circle->owner->name }}</div>
                                        <div class="flex flex-wrap gap-1.5 mt-1">
                                            @foreach($circle->owner->achievements->pluck('skill')->unique('id')->take(2) as $skill)
                                                <span class="text-[8px] font-black text-blue-400 uppercase tracking-tight">{{ $skill->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Active Members --}}
                                @foreach($circle->activeMembers as $member)
                                    @if($member->user_id !== $circle->owner_id)
                                        <div class="flex items-center gap-3 p-3 bg-white/50 border border-slate-100 rounded-2xl group/m hover:bg-white hover:border-blue-500/30 transition-all">
                                            <a href="{{ route('users.show', $member->user) }}" class="shrink-0">
                                                <img src="{{ $member->user->avatar }}" class="w-9 h-9 rounded-xl object-cover ring-2 ring-transparent group-hover/m:ring-blue-500 transition-all">
                                            </a>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-[11px] font-black text-slate-900 truncate group-hover/m:text-blue-600 transition-colors">{{ $member->user->name }}</div>
                                                <div class="flex flex-wrap gap-1.5 mt-1">
                                                    @foreach($member->user->achievements->pluck('skill')->unique('id')->take(2) as $skill)
                                                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-tight">{{ $skill->name }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                           
                           @auth
                                @php 
                                    $isMember = $circle->members->contains('user_id', auth()->id()); 
                                    $isOwner = $circle->owner_id === auth()->id();
                                @endphp

                                @if($isOwner)
                                    <div class="space-y-3">
                                        <a href="{{ route('circles.edit', $circle) }}" class="block text-center py-4 bg-slate-900 hover:bg-blue-600 rounded-2xl font-black text-xs tracking-widest uppercase transition-all text-white shadow-xl shadow-slate-900/10">
                                            Configuration
                                        </a>
                                        
                                        @php $pendingRequests = $circle->members()->where('status', 'pending')->with('user')->get(); @endphp
                                        @if($pendingRequests->count() > 0)
                                            <div class="pt-4 border-t border-slate-100">
                                                <div class="text-[9px] font-black text-blue-600 uppercase tracking-widest mb-4">Demandes ({{ $pendingRequests->count() }})</div>
                                                <div class="space-y-2">
                                                    @foreach($pendingRequests as $req)
                                                        <div class="flex items-center justify-between bg-white p-2 rounded-xl border border-slate-100 shadow-sm">
                                                            <div class="flex items-center gap-2">
                                                                <img src="{{ $req->user->avatar }}" class="w-6 h-6 rounded-lg object-cover">
                                                                <span class="text-[9px] font-black text-slate-900 truncate max-w-[70px]">{{ $req->user->name }}</span>
                                                            </div>
                                                            <div class="flex gap-1">
                                                                <button wire:click="toggleApprove({{ $req->id }}, 'active')" class="p-1.5 bg-green-50 text-green-600 rounded-lg hover:bg-green-600 hover:text-white transition-all">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                                </button>
                                                                <button wire:click="toggleApprove({{ $req->id }}, 'rejected')" class="p-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-50 hover:text-white transition-all">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
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
                                            <div class="w-full text-center py-4 bg-green-50 text-green-600 rounded-2xl font-black text-[10px] tracking-widest uppercase border border-green-100">
                                                Vous êtes membre
                                            </div>
                                            <button wire:click="leaveCircle" class="w-full text-center py-2 text-slate-400 hover:text-red-500 font-black text-[9px] uppercase tracking-widest transition-colors">
                                                Quitter le cercle
                                            </button>
                                        </div>
                                    @elseif($membership && $membership->status === 'pending')
                                        <div class="w-full text-center py-4 bg-amber-50 text-amber-600 rounded-2xl font-black text-[10px] tracking-widest uppercase border border-amber-100">
                                            Demande en attente
                                        </div>
                                    @else
                                        <button wire:click="joinCircle" class="w-full text-center py-4 bg-blue-600 hover:bg-blue-700 rounded-2xl font-black text-xs tracking-widest uppercase transition-all shadow-xl shadow-blue-500/20 text-white">
                                            Rejoindre le Cercle
                                        </button>
                                    @endif
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="block text-center py-4 bg-slate-900 hover:bg-blue-600 rounded-2xl font-black text-xs tracking-widest uppercase transition-all shadow-xl shadow-slate-900/10 text-white">
                                    Se Connecter
                                </a>
                            @endauth
                        </div>
                        <div class="px-6 py-4 bg-white/60 backdrop-blur-xl border border-white/60 rounded-2xl">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-slate-500 font-bold text-[10px] uppercase tracking-widest">Localisation</span>
                                @if(!$circle->address)
                                    <span class="text-slate-400 italic text-[10px] uppercase font-bold tracking-widest">Non définie</span>
                                @endif
                            </div>
                            @if($circle->address)
                                <div class="flex flex-wrap items-center gap-1">
                                    @if($circle->address_tags)
                                        @foreach($circle->address_tags as $tag)
                                            <a href="{{ url('/?search=' . urlencode($tag)) }}" class="inline-flex items-center px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-900/30 text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-tighter hover:bg-blue-100 dark:hover:bg-blue-800/50 transition-colors">
                                                {{ $tag }}
                                            </a>
                                            @if(!$loop->last)
                                                <span class="text-[8px] text-gray-300 dark:text-gray-600 font-bold">&lt;</span>
                                            @endif
                                        @endforeach
                                    @elseif($circle->address)
                                        <a href="{{ url('/?search=' . urlencode($circle->address)) }}" class="text-[10px] font-black uppercase hover:text-blue-600 transition-colors">
                                            {{ $circle->address }}
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="max-w-7xl mx-auto mt-12 grid grid-cols-1 lg:grid-cols-3 gap-12">
        
        <!-- Main Content: Skill Directory -->
        <div class="lg:col-span-2 space-y-16">
            
            {{-- ===== LE BOARD (Nouveau Layout Chat) ===== --}}
            <div class="bg-slate-900 rounded-[3.5rem] p-8 md:p-12 shadow-2xl shadow-slate-900/40 text-white relative flex flex-col h-[700px] group/board overflow-hidden">
                <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-blue-600/10 to-transparent pointer-events-none"></div>
                
                <div class="flex items-center justify-between mb-6 relative z-10 shrink-0">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-black tracking-tight flex items-center gap-4">
                            Le Board
                            <span class="px-4 py-1.5 bg-blue-500/10 text-blue-400 text-xs rounded-full border border-blue-500/20 font-black">
                                {{ $circle->messages->count() }} Messages
                            </span>
                        </h2>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mt-2">Logistique & Discussions du cercle</p>
                    </div>

                    @auth
                        <div class="hidden md:flex items-center gap-2">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-ping"></div>
                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Live</span>
                        </div>
                    @endauth
                </div>

                <!-- Messages Feed -->
                @php
                    $activeMemberIds = $circle->activeMembers->pluck('user_id')->toArray();
                @endphp
                <div class="flex-grow overflow-y-auto pr-6 custom-scrollbar relative z-10 space-y-8 flex flex-col mb-6"
                     id="board-scroller"
                     x-data
                     x-init="$nextTick(() => { $el.scrollTop = $el.scrollHeight }); $watch('showIndicator', () => { setTimeout(() => $el.scrollTop = $el.scrollHeight, 100) });"
                     x-on:message-sent.window="setTimeout(() => $el.scrollTop = $el.scrollHeight, 100)">
                        @forelse($circle->messages->reverse() as $msg)
                            @php
                                $isGuest = is_null($msg->sender_id);
                                $msgIsOwner = !$isGuest && $msg->sender_id === $circle->owner_id;
                                $msgIsMember = !$isGuest && in_array($msg->sender_id, $activeMemberIds);
                            @endphp
                            <div class="flex flex-col gap-3 group/msg shrink-0">
                                <div class="flex items-center gap-3">
                                    @if($isGuest)
                                        <div class="w-8 h-8 rounded-xl bg-slate-800 flex items-center justify-center border border-white/10 shadow-lg">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        </div>
                                    @else
                                        <a href="{{ route('users.show', $msg->sender) }}" class="shrink-0">
                                            <img src="{{ $msg->sender->avatar }}" class="w-8 h-8 rounded-xl ring-2 ring-white/5 group-hover/user:ring-blue-500 transition-all object-cover">
                                        </a>
                                    @endif
                                    
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-black text-white uppercase tracking-widest">
                                                {{ $isGuest ? ($msg->metadata['guest']['name'] ?? 'Invité') : $msg->sender->name }}
                                            </span>
                                            @if($isGuest)
                                                <span class="px-1.5 py-0.5 bg-slate-800 text-slate-400 text-[8px] font-black uppercase rounded-md border border-white/5">Invité</span>
                                            @elseif($msgIsOwner)
                                                <span class="px-1.5 py-0.5 bg-blue-500/20 text-blue-400 text-[8px] font-black uppercase rounded-md border border-blue-500/20">Fondateur</span>
                                            @elseif($msgIsMember)
                                                <span class="px-1.5 py-0.5 bg-white/10 text-slate-400 text-[8px] font-black uppercase rounded-md border border-white/5">Membre</span>
                                            @endif
                                        </div>
                                        <span class="text-[8px] font-black text-slate-600 uppercase tracking-widest">{{ $msg->created_at->diffForHumans() }}</span>
                                    </div>

                                    @if($isGuest && isset($msg->metadata['guest']['contact']) && (auth()->id() === $circle->owner_id || (auth()->check() && in_array(auth()->id(), $activeMemberIds))))
                                        <div class="ml-auto px-2 py-1 bg-blue-500/10 border border-blue-500/20 rounded-lg flex items-center gap-2">
                                            <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            <span class="text-[9px] font-bold text-blue-300">{{ $msg->metadata['guest']['contact'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            <div class="relative">
                                <div @class([
                                    'bg-white/5 border border-white/10 p-6 rounded-[2rem] rounded-tl-none text-slate-300 text-base leading-relaxed italic group-hover/msg:border-white/20 transition-all',
                                    'border-l-4 border-l-blue-500' => $msgIsOwner
                                ])>
                                    @if(isset($msg->metadata['type']) && $msg->metadata['type'] === 'quote_request')
                                        <div class="mb-4 p-4 bg-blue-500/10 border border-blue-500/20 rounded-2xl flex items-center gap-4 not-italic">
                                            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white shrink-0 shadow-lg shadow-blue-500/20">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Demande de mission</div>
                                                <div class="text-sm font-black text-white truncate">{{ $msg->metadata['offer_title'] ?? 'Sans titre' }}</div>
                                            </div>
                                            @if(isset($msg->metadata['offer_id']))
                                                <a href="#offer-{{ $msg->metadata['offer_id'] }}" class="ml-auto p-2 hover:bg-white/10 rounded-lg transition-colors text-blue-400" title="Voir l'offre">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                            @endif
                                        </div>
                                    @endif

                                    {{ $msg->content }}

                                    {{-- File Attachment Render --}}
                                    @if(isset($msg->metadata['file']))
                                        @php $file = $msg->metadata['file']; @endphp
                                        <div class="mt-4">
                                            @if(Str::startsWith($file['type'] ?? '', 'image/'))
                                                <div class="relative group/file overflow-hidden rounded-2xl border border-white/10 shadow-2xl">
                                                    <img src="{{ Storage::url($file['path']) }}" alt="{{ $file['name'] }}" class="w-full max-h-[400px] object-cover hover:scale-105 transition-transform duration-500 cursor-pointer" onclick="window.open('{{ Storage::url($file['path']) }}', '_blank')">
                                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/file:opacity-100 transition-opacity flex items-center justify-center gap-4">
                                                        <a href="{{ Storage::url($file['path']) }}" download="{{ $file['name'] }}" class="p-3 bg-white/20 backdrop-blur-md rounded-full text-white hover:bg-white/40 transition-all">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            @else
                                                <a href="{{ Storage::url($file['path']) }}" download="{{ $file['name'] }}" class="flex items-center gap-4 p-4 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 transition-all group/file">
                                                    <div class="w-12 h-12 bg-red-500/10 rounded-xl flex items-center justify-center text-red-500 shadow-inner">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">{{ strtoupper(explode('/', $file['type'])[1] ?? 'FILE') }}</div>
                                                        <div class="text-sm font-black text-white truncate">{{ $file['name'] }}</div>
                                                        <div class="text-[8px] font-bold text-slate-600 mt-1">{{ number_format(($file['size'] ?? 0) / 1024 / 1024, 2) }} MB</div>
                                                    </div>
                                                    <svg class="w-5 h-5 text-slate-600 group-hover/file:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div class="absolute -left-2 top-0 w-4 h-4 bg-white/5 border-t border-l border-white/10 rotate-[-15deg] invisible group-hover/msg:visible"></div>
                            </div>
                        </div>
                    @empty
                        <div class="py-20 text-center flex flex-col items-center justify-center gap-6 flex-grow">
                            <div class="w-20 h-20 rounded-[2.5rem] bg-white/5 flex items-center justify-center border border-white/10 text-slate-700 shadow-inner">
                                <svg class="w-10 h-10" viewBox="0 0 256 256" fill="none" stroke="currentColor" stroke-width="8"><path d="M200,48H56A16,16,0,0,0,40,64V184a16,16,0,0,0,16,16h81.37l33.32,29.15a4,4,0,0,0,5.31,0L200l.06,0a16,16,0,0,0,15.94-16V64A16,16,0,0,0,200,48ZM88,128a8,8,0,1,1,8,8A8,8,0,0,1,88,128Zm40,8a8,8,0,1,1,8-8A8,8,0,0,1,128,136Zm48-8a8,8,0,1,1-8,8A8,8,0,0,1,176,128Z" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                            </div>
                            <p class="text-slate-600 font-black uppercase tracking-[0.4em] text-[10px]">Silence radio sur le board...</p>
                        </div>
                    @endforelse
                </div>

                <!-- Input area if authenticated -->
                @auth
                    @php 
                        $isActiveMember = $circle->activeMembers->contains('user_id', auth()->id());
                        $isOwner = $circle->owner_id === auth()->id();
                    @endphp

                    @php
                        $mentionables = collect([
                            ['type' => 'circle', 'id' => $circle->id, 'name' => $circle->name],
                            ['type' => 'user', 'id' => $circle->owner_id, 'name' => $circle->owner->name],
                        ]);
                        foreach($circle->activeMembers as $m) {
                            $mentionables->push(['type' => 'user', 'id' => $m->user_id, 'name' => $m->user->name]);
                        }
                        foreach($circle->achievements as $a) {
                            $mentionables->push(['type' => 'skill', 'id' => $a->skill_id, 'name' => $a->skill->name]);
                        }
                        $mentionables = $mentionables->unique(fn($o) => $o['type'].$o['id'])->values();
                    @endphp

                    <div class="relative z-10 shrink-0" 
                        x-data="{ 
                            showMentions: false, 
                            mentionSearch: '',
                            items: {{ $mentionables->toJson() }},
                            get filteredItems() {
                                if (!this.mentionSearch) return this.items;
                                return this.items.filter(i => i.name.toLowerCase().includes(this.mentionSearch.toLowerCase()));
                            },
                            handleInput(e) {
                                const val = e.target.value;
                                const cursor = e.target.selectionStart;
                                const lastAt = val.lastIndexOf('@', cursor - 1);
                                if (lastAt !== -1 && (lastAt === 0 || val[lastAt - 1] === ' ')) {
                                    this.showMentions = true;
                                    this.mentionSearch = val.substring(lastAt + 1, cursor);
                                } else {
                                    this.showMentions = false;
                                }
                            },
                            selectItem(item) {
                                @this.selectAttachment(item.type, item.id, item.name);
                                const val = $refs.msgInput.value;
                                const cursor = $refs.msgInput.selectionStart;
                                const lastAt = val.lastIndexOf('@', cursor - 1);
                                $refs.msgInput.value = val.substring(0, lastAt) + val.substring(cursor);
                                @this.set('message', $refs.msgInput.value);
                                this.showMentions = false;
                                $refs.msgInput.focus();
                            }
                        }"
                    >
                        <!-- Mentions Dropdown -->
                        <div x-show="showMentions && filteredItems.length > 0" 
                             x-transition
                             class="absolute bottom-full left-0 mb-4 w-64 bg-slate-800 border border-white/10 rounded-2xl shadow-2xl overflow-hidden z-[100]"
                             style="display: none;">
                            <div class="p-2 border-b border-white/5 bg-slate-900/50">
                                <span class="text-[8px] font-black text-slate-500 uppercase tracking-widest px-2">Attacher un objet</span>
                            </div>
                            <div class="max-h-48 overflow-y-auto custom-scrollbar">
                                <template x-for="item in filteredItems" :key="item.type + item.id">
                                    <button @click="selectItem(item)" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-blue-600 transition-colors text-left group">
                                        <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0"
                                            :class="item.type === 'skill' ? 'bg-blue-500/20 text-blue-400 group-hover:bg-white/20 group-hover:text-white' : 'bg-slate-700 text-slate-400 group-hover:bg-white/20 group-hover:text-white'">
                                            <svg x-show="item.type === 'skill'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            <svg x-show="item.type === 'user'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            <svg x-show="item.type === 'circle'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-[7px] font-black uppercase tracking-widest opacity-50 text-white" x-text="item.type"></div>
                                            <div class="text-[10px] font-bold text-white truncate" x-text="item.name"></div>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <form wire:submit.prevent="sendMessage" class="p-4 bg-white/5 border border-white/10 rounded-[2rem] focus-within:border-blue-500 transition-all shadow-inner relative flex flex-col gap-4">
                            @guest
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-2 border-b border-white/5">
                                    <div class="relative">
                                        <input wire:model="guestName" type="text" placeholder="Votre Nom / Pseudo" class="w-full bg-transparent border-none focus:ring-0 text-sm text-slate-200 placeholder:text-slate-600">
                                        @error('guestName') <span class="absolute -bottom-4 left-0 text-[8px] font-black text-red-500 uppercase">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="relative">
                                        <input wire:model="guestContact" type="text" placeholder="E-mail ou Téléphone" class="w-full bg-transparent border-none focus:ring-0 text-sm text-slate-200 placeholder:text-slate-600">
                                        @error('guestContact') <span class="absolute -bottom-4 left-0 text-[8px] font-black text-red-500 uppercase">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            @endguest

                            <div class="flex flex-col md:flex-row items-center gap-4">
                                @auth
                                    <div class="hidden md:flex items-center gap-3 shrink-0 px-2">
                                        <img src="{{ auth()->user()->avatar }}" class="w-8 h-8 rounded-xl border border-white/10 shadow-lg object-cover">
                                    </div>
                                @endauth

                                <!-- Mobile Plus Button for Mentions -->
                                <button type="button" 
                                    @click="showMentions = !showMentions"
                                    class="shrink-0 w-8 h-8 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-blue-400 hover:border-blue-500/50 hover:bg-blue-500/5 transition-all active:scale-95"
                                    title="Attacher un objet">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                </button>

                                <!-- File Upload Button -->
                                <div class="relative flex items-center">
                                    <input type="file" wire:model="upload" class="hidden" x-ref="fileInput">
                                    <button type="button" 
                                        @click="$refs.fileInput.click()"
                                        class="shrink-0 w-8 h-8 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-blue-400 hover:border-blue-500/50 hover:bg-blue-500/5 transition-all active:scale-95"
                                        title="Envoyer une image ou un PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    </button>
                                    
                                    <div wire:loading wire:target="upload" class="absolute -top-1 -right-1">
                                        <div class="w-3 h-3 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                                    </div>
                                </div>

                                <div class="flex-1 w-full relative">
                                    <!-- Mentions Attachment Badge -->
                                    @if($attachment)
                                        <div class="absolute left-0 -top-10 flex items-center gap-2 px-3 py-1.5 bg-blue-600 rounded-xl shadow-lg shadow-blue-500/20 animate-in slide-in-from-bottom-2 duration-300 z-20">
                                            <span class="text-[8px] font-black text-white uppercase tracking-widest">OBJET : {{ $attachment['name'] }}</span>
                                            <button type="button" wire:click="removeAttachment" class="p-0.5 hover:bg-white/20 rounded-md transition-colors text-white">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    @endif

                                    <!-- File Preview Badge -->
                                    @if($upload)
                                        <div class="absolute left-0 -top-10 flex items-center gap-2 px-3 py-1.5 bg-blue-600 rounded-xl shadow-lg z-20 animate-in slide-in-from-bottom-2 duration-300">
                                            <span class="text-[8px] font-black text-white uppercase tracking-widest truncate max-w-[150px]">FICHIER : {{ $upload->getClientOriginalName() }}</span>
                                            <button type="button" wire:click="$set('upload', null)" class="p-0.5 hover:bg-white/20 rounded-md transition-colors text-white">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    @endif

                                    <input wire:model="message" 
                                        x-ref="msgInput"
                                        @input="handleInput"
                                        @keydown.escape="showMentions = false"
                                        type="text"
                                        placeholder="{{ auth()->guest() ? 'Votre message aux membres du cercle...' : (($isActiveMember || $isOwner) ? 'Partagez une info logistique, une annonce...' : 'Posez une question en tant qu\'invité...') }}" 
                                        class="w-full bg-transparent border-none focus:ring-0 text-sm md:text-base text-slate-200 placeholder:text-slate-600 truncate"
                                    >
                                </div>
                                
                                <button type="submit" 
                                    class="w-full md:w-auto shrink-0 px-6 py-3 bg-white text-slate-900 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all shadow-xl shadow-white/5 active:scale-95">
                                    Envoyer
                                </button>
                            </div>
                        </form>
                        <p class="mt-3 px-4 text-[8px] font-black text-slate-600 uppercase tracking-widest">Astuce : Cliquez sur + ou tapez @ pour attacher un membre ou une expertise</p>
                    </div>
                @else
                    <div class="relative z-10 shrink-0 text-center p-6 bg-white/5 border border-dashed border-white/10 rounded-[2rem] group/join cursor-pointer hover:bg-white/10 transition-all">
                        <p class="text-slate-500 text-xs font-black uppercase tracking-[0.3em] group-hover/join:text-blue-400">Connectez-vous pour participer</p>
                    </div>
                @endauth
            </div>
        </div>

        <!-- Sidebar: Messaging & Logistics -->
        <div class="space-y-12">
            @auth
                @if(auth()->id() == $circle->owner_id || $circle->isMember(auth()->user()) || auth()->user()->is_admin)
                    <div class="space-y-6">
                        <livewire:network.explorer :origin="$circle" />
                    </div>
                @endif
            @endauth
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
    @include('livewire.offers.modals')
</div>
