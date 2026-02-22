<div 
    x-data="{ showIndicator: false }"
    x-on:project-updated.window="showIndicator = true; window.playNotify && playNotify(); setTimeout(function() { showIndicator = false }, 3000)"
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
    {{-- ===== RÉALISATION HERO HEADER ===== --}}
    <div class="relative pt-6 pb-12 overflow-hidden">
        <div class="absolute inset-0 -z-10">
            <div class="absolute top-[-20%] right-[-10%] w-[50%] aspect-square bg-blue-500/5 rounded-full blur-[140px]"></div>
            <div class="absolute bottom-0 left-0 w-[40%] aspect-square bg-indigo-500/5 rounded-full blur-[120px]"></div>
        </div>

        <div class="max-w-7xl mx-auto">
            <div class="bg-white/40 backdrop-blur-3xl border border-white/60 rounded-[3rem] md:rounded-[4rem] p-6 md:p-10 shadow-2xl shadow-blue-500/5 relative overflow-hidden group">

                {{-- Status badges --}}
                <div class="flex flex-wrap items-center gap-2 md:gap-3 mb-8 md:absolute md:top-10 md:right-10 md:mb-0">
                    @php
                        $statusColors = [
                            'actuelle' => 'bg-blue-50 text-blue-600 border-blue-100',
                            'verrouillée' => 'bg-amber-50 text-amber-600 border-amber-100',
                            'terminée' => 'bg-green-50 text-green-600 border-green-100',
                            'annulée' => 'bg-red-50 text-red-600 border-red-100',
                        ];
                        $statusIcons = [
                            'actuelle' => 'animate-pulse',
                            'verrouillée' => 'fas fa-lock',
                            'terminée' => '',
                            'annulée' => '',
                        ];
                    @endphp
                    <span class="px-3 md:px-4 py-1.5 {{ $statusColors[$project->status] ?? 'bg-slate-50 text-slate-600' }} rounded-full text-[9px] md:text-[10px] font-black uppercase tracking-widest border flex items-center gap-2">
                        @if($project->status === 'actuelle')
                            <span class="w-1.5 h-1.5 bg-blue-600 rounded-full animate-pulse"></span>
                        @elseif($project->status === 'verrouillée')
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                        @endif
                        {{ ucfirst($project->status) }}
                    </span>
                    
                    @if($project->skill)
                        <a href="{{ route('mission.show', $project->skill) }}" class="px-3 md:px-4 py-1.5 bg-slate-900 text-white rounded-full text-[9px] md:text-[10px] font-black uppercase tracking-widest hover:scale-105 transition-all">
                            Mission: {{ $project->skill->name }}
                        </a>
                    @endif
                </div>

                <div class="flex flex-col lg:flex-row gap-12 relative z-10">
                    {{-- Icon --}}
                    <div class="flex-shrink-0 flex justify-center lg:block">
                        <div class="w-24 h-24 md:w-32 md:h-32 bg-gradient-to-tr from-slate-800 to-slate-930 rounded-[2rem] md:rounded-[2.5rem] flex items-center justify-center text-white shadow-2xl shadow-slate-900/30 rotate-3 group-hover:rotate-6 transition-transform duration-500">
                            <svg class="w-12 h-12 md:w-16 md:h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="flex-grow">
                        <div class="flex flex-wrap items-center gap-4 mb-2">
                            <span class="text-xs font-black text-blue-600 uppercase tracking-[0.3em]">Réalisation</span>
                            <span class="text-slate-200">/</span>
                            <a href="{{ route('users.show', $project->owner) }}" class="text-xs font-black text-slate-400 uppercase tracking-[0.3em] hover:text-blue-600 transition-colors">{{ $project->owner->name }}</a>
                        </div>
                        <h1 class="text-3xl md:text-7xl font-black text-slate-900 tracking-tighter leading-none mb-4 text-center md:text-left uppercase">
                            {{ $project->title }}
                        </h1>
                        @if($project->description)
                            <p class="text-xl text-slate-500 font-medium max-w-2xl leading-relaxed mb-6">
                                {{ $project->description }}
                            </p>
                        @endif

                        {{-- Skills Tags --}}
                        <div class="flex flex-wrap items-center gap-2 mb-8">
                            @foreach($project->skills as $skill)
                                <span class="px-3 py-1 bg-white border border-slate-200 rounded-full text-[9px] font-black uppercase tracking-widest text-slate-600">
                                    #{{ $skill->name }}
                                </span>
                            @endforeach
                            @if($project->canManage(auth()->user()) && $project->status === 'actuelle')
                                @if($showSkillTagForm)
                                    <div class="flex items-center gap-2 animate-in slide-in-from-left-2 duration-300">
                                        <input type="text" wire:model="additionalSkillName" wire:keydown.enter="addAdditionalSkill" placeholder="Nouvelle compétence..." class="bg-slate-100 border-none rounded-lg px-3 py-1 text-[9px] font-bold uppercase outline-none focus:ring-1 focus:ring-blue-500">
                                        <button wire:click="addAdditionalSkill" class="text-blue-500 hover:text-blue-600 transform hover:scale-110 transition-all font-black text-xs">+</button>
                                        <button wire:click="$set('showSkillTagForm', false)" class="text-slate-400 hover:text-slate-600 font-black text-xs">×</button>
                                    </div>
                                @else
                                    <button wire:click="$set('showSkillTagForm', true)" class="px-3 py-1 border border-dashed border-slate-300 rounded-full text-[9px] font-black uppercase tracking-widest text-slate-400 hover:border-blue-500 hover:text-blue-500 transition-all">
                                        + Ajouter un tag
                                    </button>
                                @endif
                            @endif
                        </div>

                        {{-- Participants Bar --}}
                        <div class="flex items-center gap-8 py-4 border-t border-slate-100">
                            <div class="flex flex-col">
                                <span class="text-xl font-black text-slate-900 leading-none">{{ $project->activeMembers->count() + 1 }}</span>
                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mt-1">Participants</span>
                            </div>
                            <div class="flex flex-col border-l border-slate-100 pl-8">
                                <span class="text-xl font-black text-slate-900 leading-none">{{ $project->messages->count() }}</span>
                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mt-1">Echanges</span>
                            </div>
                        </div>
                    </div>

                    {{-- Actions Sidebar --}}
                    <div class="lg:w-72 space-y-4">
                        <div class="p-6 bg-slate-900 rounded-[2.5rem] text-white">
                            {{-- State Transitions --}}
                            <div class="space-y-3 mb-6">
                                @if($project->canManage(auth()->user()))
                                    @if($project->status === 'actuelle')
                                        <button wire:click="lockRealisation" class="w-full py-4 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all shadow-lg shadow-amber-500/20">
                                            🔒 Verrouiller le contrat
                                        </button>
                                    @endif
                                    @if($project->status === 'verrouillée')
                                        <button wire:click="completeRealisation" class="w-full py-4 bg-green-600 hover:bg-green-700 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all shadow-lg shadow-green-500/20">
                                            ✅ Marquer comme Terminée
                                        </button>
                                    @endif
                                    @if(in_array($project->status, ['actuelle', 'verrouillée']))
                                        <button wire:click="cancelRealisation" onclick="confirm('Annuler cette réalisation ?') || event.stopImmediatePropagation()" class="w-full py-3 bg-white/5 hover:bg-red-500/20 text-red-400 rounded-2xl font-black text-[9px] uppercase tracking-widest border border-red-500/10 transition-all">
                                            Annuler
                                        </button>
                                    @endif
                                @endif
                                <a href="{{ route('cv.project', $project) }}" target="_blank" class="w-full py-4 bg-white/10 hover:bg-white/20 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all flex items-center justify-center gap-2 border border-white/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4"/></svg>
                                    Générer CV Papier
                                </a>
                            </div>

                            {{-- Owner & Participants list --}}
                            <div class="space-y-4">
                                <a href="{{ route('users.show', $project->owner) }}" class="flex items-center gap-3 group/owner">
                                    <img src="{{ $project->owner->avatar_url }}" class="w-10 h-10 rounded-xl ring-2 ring-blue-500">
                                    <div>
                                        <div class="text-[7px] font-black text-blue-400 uppercase tracking-widest leading-none mb-1">Responsable</div>
                                        <div class="text-xs font-bold">{{ $project->owner->name }}</div>
                                    </div>
                                </a>

                                @foreach($project->activeMembers as $member)
                                    <a href="{{ route('users.show', $member->memberable) }}" class="flex items-center gap-3">
                                        <img src="{{ $member->memberable->avatar_url }}" class="w-10 h-10 rounded-xl ring-2 ring-white/10 opacity-70 hover:opacity-100 transition-opacity">
                                        <div>
                                            <div class="text-[7px] font-black text-slate-500 uppercase tracking-widest leading-none mb-1">Participant</div>
                                            <div class="text-xs font-bold">{{ $member->memberable->name }}</div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>

                            {{-- Auth Join action --}}
                            @auth
                                @if(!$project->isMember(auth()->user()) && !$project->isOwner(auth()->user()) && $project->status === 'actuelle')
                                    <div class="mt-8 pt-8 border-t border-white/10">
                                        <button wire:click="joinProject" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all">
                                            📦 Rejoindre la réalisation
                                        </button>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="max-w-7xl mx-auto">
        {{-- ---- OFFRES (BOUTIQUE) ---- --}}
        <div>
                <div class="mb-12">
                    @if(!$showOfferForm)
                        <button wire:click="$set('showOfferForm', true)" class="w-full py-10 bg-white border-2 border-dashed border-blue-200 text-blue-600 rounded-[3rem] font-black text-xs uppercase tracking-[0.4em] hover:bg-blue-50 transition-all flex items-center justify-center gap-6 group">
                            <span class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white group-hover:rotate-12 transition-all shadow-lg shadow-blue-500/5">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            </span>
                            Nouvel Article en Boutique
                        </button>
                    @else
                        <div class="bg-slate-900 rounded-[3.5rem] p-10 text-white shadow-3xl animate-in fade-in slide-in-from-top-4 duration-500 relative overflow-hidden group/form">
                             <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 rounded-full blur-[100px]"></div>
                             <h4 class="text-sm font-black uppercase tracking-[0.3em] mb-10 text-blue-400 relative z-10 flex items-center justify-between">
                                 <span class="flex items-center gap-3">
                                     <span class="w-2 h-8 bg-blue-500 rounded-full"></span>
                                     {{ $editingOfferId ? 'Éditer l\'Article' : 'Ajouter à la Boutique' }}
                                 </span>
                                 @if($editingOfferId)
                                     <button wire:click="$set('editingOfferId', null); $set('showOfferForm', false)" class="text-[10px] text-slate-500 hover:text-white transition-colors uppercase tracking-widest bg-white/5 px-4 py-2 rounded-xl">Annuler</button>
                                 @endif
                             </h4>
                             
                             <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 relative z-10">
                                 <div class="space-y-6">
                                     <div class="space-y-2">
                                         <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest italic ml-2">Nom de l'article</label>
                                         <input wire:model="offerTitle" type="text" placeholder="Titre attractif..." class="w-full bg-white/5 border border-white/10 rounded-2xl p-5 text-sm font-bold placeholder:text-slate-600 focus:border-blue-500 transition-all outline-none">
                                     </div>
                                     <div class="space-y-2">
                                         <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest italic ml-2">Description principale</label>
                                         <textarea wire:model="offerDescription" rows="4" placeholder="Décrivez votre offre de manière concise..." class="w-full bg-white/5 border border-white/10 rounded-2xl p-5 text-xs font-medium placeholder:text-slate-600 focus:border-blue-500 transition-all italic outline-none"></textarea>
                                     </div>
                                 </div>

                                 <div class="space-y-6">
                                     <div class="space-y-2">
                                         <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest italic ml-2">Galerie Photos (Plusieurs possibles)</label>
                                         <div class="relative group/upload">
                                             <input type="file" wire:model="offerImages" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                                             <div class="w-full py-8 border-2 border-dashed border-white/10 rounded-2xl flex flex-col items-center justify-center gap-3 bg-white/5 group-hover/upload:border-blue-500/50 transition-all">
                                                 <svg class="w-8 h-8 text-slate-600 group-hover/upload:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                 <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Cliquez ou glissez vos images</span>
                                             </div>
                                         </div>
                                         @if($offerImages)
                                             <div class="flex flex-wrap gap-2 mt-2">
                                                 @foreach($offerImages as $image)
                                                     <div class="relative w-16 h-16 rounded-lg overflow-hidden border border-white/10">
                                                         <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                                                     </div>
                                                 @endforeach
                                             </div>
                                         @endif
                                     </div>
                                     <div class="space-y-4">
                                         <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest italic ml-2">Informations (Label : Détail)</label>
                                         @foreach($offerInfos as $index => $info)
                                             <div class="flex gap-2 animate-in slide-in-from-left-2 duration-200">
                                                 <input wire:model="offerInfos.{{ $index }}.label" type="text" placeholder="Ex: Prix" class="w-1/3 bg-white/5 border border-white/10 rounded-xl p-3 text-[10px] font-bold text-blue-300 placeholder:text-slate-700 focus:border-blue-500 outline-none">
                                                 <input wire:model="offerInfos.{{ $index }}.title" type="text" placeholder="Ex: 5 euros" class="flex-grow bg-white/5 border border-white/10 rounded-xl p-3 text-[10px] font-bold text-white placeholder:text-slate-700 focus:border-blue-500 outline-none">
                                                 @if(count($offerInfos) > 1)
                                                     <button wire:click="removeOfferInfo({{ $index }})" class="p-3 text-red-400 hover:text-red-300 transition-colors">
                                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                     </button>
                                                 @endif
                                             </div>
                                         @endforeach
                                         <button wire:click="addOfferInfo" class="text-[9px] font-black text-blue-500 uppercase tracking-widest hover:text-blue-400 transition-colors ml-2">+ Ajouter une info</button>
                                     </div>
                                 </div>
                             </div>

                             <div class="flex gap-4 mt-10 pt-10 border-t border-white/5 relative z-10">
                                 <button wire:click="addOffer" wire:loading.attr="disabled" class="flex-grow py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:from-blue-500 hover:to-indigo-500 shadow-2xl shadow-blue-500/20 transition-all disabled:opacity-50">
                                     <span wire:loading.remove>📦 Valider l'article</span>
                                     <span wire:loading>Traitement...</span>
                                 </button>
                                 <button wire:click="$set('showOfferForm', false)" class="px-10 py-5 bg-white/5 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:bg-white/10 transition-all">Annuler</button>
                             </div>
                        </div>
                    @endif
                </div>

            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-8">
                @forelse($project->offers as $offer)
                    <x-offer-card 
                        :offer="$offer" 
                        :project="$project"
                        :canManage="$project->canManage(auth()->user())"
                    />
                @empty
                    <div class="col-span-full py-20 text-center">
                        <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 border border-white/10 opacity-20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <p class="text-white/20 text-[10px] font-black uppercase tracking-widest italic">Aucun article dans la vitrine</p>
                    </div>
                @endforelse
            </div>
            </div>
        </div>



    {{-- ===== LE FORUM DU PROJET (Nouveau Layout Chat) ===== --}}
    <div class="max-w-7xl mx-auto px-6 mt-12 pt-12 border-t border-slate-100">
        <div class="bg-slate-900 rounded-[3.5rem] p-8 md:p-12 shadow-2xl shadow-slate-900/40 text-white relative flex flex-col h-[700px] overflow-hidden group/board mt-8">
            <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-blue-600/10 to-transparent pointer-events-none"></div>
            
            <div class="flex items-center justify-between mb-6 relative z-10 shrink-0">
                <div>
                    <h2 class="text-3xl md:text-4xl font-black tracking-tight flex items-center gap-4">
                        Le Forum
                        <span class="px-3 py-1 bg-blue-500/10 text-blue-400 text-[10px] rounded-full border border-blue-500/20 uppercase tracking-widest">
                            {{ $project->messages->count() }}
                        </span>
                    </h2>
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mt-2">Discussions du projet</p>
                </div>

                @auth
                @if($project->isMember(auth()->user()) || $project->isOwner(auth()->user()))
                    <div class="flex items-center gap-2 bg-white/5 py-1.5 px-3 rounded-full border border-white/10 shadow-inner">
                        <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(34,197,94,0.5)]"></div>
                        <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Live</span>
                    </div>
                @endif
                @endauth
            </div>

            <!-- Messages feed -->
            <div class="flex-grow overflow-y-auto pr-6 custom-scrollbar relative z-10 space-y-8 flex flex-col mb-6"
                 id="forum-scroller"
                 x-data
                 x-init="$nextTick(() => { $el.scrollTop = $el.scrollHeight }); $watch('showIndicator', () => { setTimeout(() => $el.scrollTop = $el.scrollHeight, 100) });"
                 x-on:message-sent.window="setTimeout(() => $el.scrollTop = $el.scrollHeight, 100)">
                @forelse($project->messages->reverse() as $msg)
                    @php
                        $isGuest = is_null($msg->sender_id);
                        $msgIsOwner = !$isGuest && $msg->sender_id === $project->owner_id;
                    @endphp
                    <div class="flex flex-col gap-3 group/msg shrink-0">
                        <div class="flex items-center gap-3">
                            @if($isGuest)
                                <div class="w-8 h-8 rounded-xl bg-slate-800 flex items-center justify-center border border-white/10 shadow-lg shrink-0">
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
                                    @endif
                                </div>
                                <span class="text-[8px] font-black text-slate-600 uppercase tracking-widest">{{ $msg->created_at->diffForHumans() }}</span>
                            </div>

                            @if($isGuest && isset($msg->metadata['guest']['contact']) && (auth()->id() === $project->owner_id || (auth()->check() && $project->isMember(auth()->user()))))
                                <div class="ml-auto px-2 py-1 bg-blue-500/10 border border-blue-500/20 rounded-lg flex items-center gap-2">
                                    <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span class="text-[9px] font-bold text-blue-300">{{ $msg->metadata['guest']['contact'] }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="relative">
                            <div @class([
                                'bg-white/5 border border-white/10 p-6 rounded-[2rem] rounded-tl-none text-slate-300 text-base leading-relaxed italic group-hover/msg:border-white/20 transition-all',
                                'border-l-4 border-l-blue-500' => $msg->sender_id === $project->owner_id
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
                        <p class="text-slate-600 font-black uppercase tracking-[0.4em] text-[10px]">Silence radio... soyez le premier à parler !</p>
                    </div>
                @endforelse
            </div>

            <!-- Input area -->
            @if(auth()->guest() || $project->isMember(auth()->user()) || $project->isOwner(auth()->user()))
                <div class="relative z-10 shrink-0">
                    @php
                    $mentionables = collect([
                        ['type' => 'projet', 'id' => $project->id, 'name' => $project->title],
                        ['type' => 'user', 'id' => $project->owner_id, 'name' => $project->owner->name],
                    ]);
                    foreach($project->activeMembers as $m) {
                        if($m->memberable instanceof \App\Models\User) {
                            $mentionables->push(['type' => 'user', 'id' => $m->memberable_id, 'name' => $m->memberable->name]);
                        }
                    }
                    foreach($project->offers as $o) {
                        $mentionables->push(['type' => 'offre', 'id' => $o->id, 'name' => $o->title]);
                    }
                    foreach($project->skills as $s) {
                        $mentionables->push(['type' => 'skill', 'id' => $s->id, 'name' => $s->name]);
                    }
                    $mentionables = $mentionables->unique(fn($o) => $o['type'].($o['id'] ?? $o['name']))->values();
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
                            const val = $refs.projMsgInput.value;
                            const cursor = $refs.projMsgInput.selectionStart;
                            const lastAt = val.lastIndexOf('@', cursor - 1);
                            $refs.projMsgInput.value = val.substring(0, lastAt) + val.substring(cursor);
                            @this.set('message', $refs.projMsgInput.value);
                            this.showMentions = false;
                            $refs.projMsgInput.focus();
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
                                        :class="item.type === 'skill' || item.type === 'offre' ? 'bg-blue-500/20 text-blue-400 group-hover:bg-white/20 group-hover:text-white' : 'bg-slate-700 text-slate-400 group-hover:bg-white/20 group-hover:text-white'">
                                        <svg x-show="item.type === 'skill' || item.type === 'offre'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        <svg x-show="item.type === 'user'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <svg x-show="item.type === 'projet'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
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
                                <input type="file" wire:model="upload" class="hidden" x-ref="projFileInput">
                                <button type="button" 
                                    @click="$refs.projFileInput.click()"
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
                                    x-ref="projMsgInput"
                                    @input="handleInput"
                                    @keydown.escape="showMentions = false"
                                    type="text"
                                    placeholder="{{ auth()->guest() ? 'Votre message au projet...' : ($project->isOwner(auth()->user()) ? 'Partager une info, une avancée...' : 'Posez une question, proposez une idée...') }}" 
                                    class="w-full bg-transparent border-none focus:ring-0 text-sm md:text-base text-slate-200 placeholder:text-slate-600 truncate"
                                >
                            </div>
                            <button type="submit" 
                                class="w-full md:w-auto shrink-0 px-6 py-3 bg-white text-slate-900 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all shadow-xl shadow-white/5 active:scale-95">
                                Envoyer
                            </button>
                        </div>
                    </form>
                    <p class="mt-3 px-4 text-[8px] font-black text-slate-600 uppercase tracking-widest">Astuce : Cliquez sur + ou tapez @ pour attacher un membre ou une offre</p>
                </div>
            @else
                <div class="relative z-10 shrink-0 text-center p-6 bg-white/5 border border-dashed border-white/10 rounded-[2rem] mb-4 group/join">
                    <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.3em]">
                        <a href="#" wire:click.prevent="joinProject" class="text-blue-400 group-hover/join:text-blue-300 transition-colors">Rejoignez le projet</a> pour participer aux discussions
                    </p>
                </div>
            @endif
        </div>

    </div>

    {{-- ===== OFFER ACTIONS MODALS (Quotes & Reviews) ===== --}}
    @include('livewire.offers.modals')

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
        .custom-scrollbar-h::-webkit-scrollbar { height: 3px; }
        .custom-scrollbar-h::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar-h::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
        .custom-scrollbar-modal::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar-modal::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar-modal::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.05); border-radius: 10px; }
        .back-to-overview {
            scroll-margin-top: 160px; /* Header (100px) + Comfortable Gap */
        }
    </style>
</div>
