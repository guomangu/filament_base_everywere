<div 
    x-data="{ showIndicator: false }"
    x-on:project-updated.window="showIndicator = true; window.playNotify && playNotify(); setTimeout(function() { showIndicator = false }, 3000)"
    wire:poll.5s.visible="refresh" 
    class="min-h-screen bg-slate-50/50 pb-12"
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
    {{-- ===== PROJECT HERO HEADER ===== --}}
    <div class="relative pt-6 pb-12 overflow-hidden">
        <div class="absolute inset-0 -z-10">
            <div class="absolute top-[-20%] right-[-10%] w-[50%] aspect-square bg-blue-500/5 rounded-full blur-[140px]"></div>
            <div class="absolute bottom-0 left-0 w-[40%] aspect-square bg-indigo-500/5 rounded-full blur-[120px]"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6">
            <div class="bg-white/40 backdrop-blur-3xl border border-white/60 rounded-[3rem] md:rounded-[4rem] p-6 md:p-10 shadow-2xl shadow-blue-500/5 relative overflow-hidden group">

                {{-- Status badges --}}
                <div class="flex flex-wrap items-center gap-2 md:gap-3 mb-8 md:absolute md:top-10 md:right-10 md:mb-0">
                    @if($project->is_open)
                        <span class="px-3 md:px-4 py-1.5 bg-green-50 text-green-600 rounded-full text-[9px] md:text-[10px] font-black uppercase tracking-widest border border-green-100 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-green-600 rounded-full animate-pulse"></span>
                            Ouvert
                        </span>
                    @else
                        <span class="px-3 md:px-4 py-1.5 bg-slate-100 text-slate-500 rounded-full text-[9px] md:text-[10px] font-black uppercase tracking-widest border border-slate-200 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                            Fermé
                        </span>
                    @endif
                </div>

                <div class="flex flex-col lg:flex-row gap-12 relative z-10">
                    {{-- Icon --}}
                    <div class="flex-shrink-0 flex justify-center lg:block">
                        <div class="w-24 h-24 md:w-32 md:h-32 bg-gradient-to-tr from-blue-600 to-indigo-700 rounded-[2rem] md:rounded-[2.5rem] flex items-center justify-center text-white shadow-2xl shadow-blue-600/30 rotate-3 group-hover:rotate-6 transition-transform duration-500">
                            <svg class="w-12 h-12 md:w-16 md:h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="flex-grow">
                        <div class="flex flex-wrap items-center gap-4 mb-2">
                            <span class="text-xs font-black text-blue-600 uppercase tracking-[0.3em]">Projet</span>
                            <span class="text-slate-200">/</span>
                            <a href="{{ route('users.show', $project->owner) }}" class="text-xs font-black text-slate-400 uppercase tracking-[0.3em] hover:text-blue-600 transition-colors">{{ $project->owner->name }}</a>
                        </div>
                        <h1 class="text-3xl md:text-7xl font-black text-slate-900 tracking-tighter leading-none mb-4 text-center md:text-left">
                            {{ $project->title }}
                        </h1>
                        {{-- Boutique Info --}}
                        @if($project->description)
                            <p class="text-xl text-slate-500 font-medium max-w-2xl leading-relaxed mb-6">
                                {{ $project->description }}
                            </p>
                        @endif

                        {{-- Stats Bar (Compact) --}}
                        <div class="flex items-center gap-8 py-4 border-t border-slate-100">
                            <div class="flex flex-col">
                                <span class="text-xl font-black text-slate-900 leading-none">{{ $project->offers->count() }}</span>
                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mt-1">Produits</span>
                            </div>
                            <div class="flex flex-col border-l border-slate-100 pl-8">
                                <span class="text-xl font-black text-green-600 leading-none">+{{ $project->getPositiveReviewsCount() }}</span>
                                <span class="text-[8px] font-black text-green-400 uppercase tracking-widest mt-1">Avis Positifs</span>
                            </div>
                            <div class="flex flex-col border-l border-slate-100 pl-8" @click="document.getElementById('members-section').scrollIntoView({behavior: 'smooth'})">
                                <span class="text-xl font-black text-slate-900 leading-none">{{ $project->activeMembers->count() }}</span>
                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mt-1">Membres</span>
                            </div>
                        </div>
                    </div>

                    {{-- Actions Sidebar --}}
                    <div class="lg:w-72 space-y-4">
                        <div class="p-6 bg-slate-900 rounded-[2.5rem] text-white">
                            {{-- Owner --}}
                            <a href="{{ route('users.show', $project->owner) }}" class="flex items-center gap-4 mb-3 group/owner transition-all">
                                <img src="{{ $project->owner->avatar }}" class="w-12 h-12 rounded-2xl ring-2 ring-white/10 group-hover/owner:ring-blue-500 transition-all">
                                <div>
                                    <div class="text-[8px] font-black text-blue-400 uppercase tracking-widest leading-none mb-1">Fondateur</div>
                                    <div class="text-sm font-bold group-hover/owner:text-blue-400 transition-colors">{{ $project->owner->name }}</div>
                                </div>
                            </a>

                            {{-- Members avatars --}}
                            @if($project->activeMembers->count() > 0)
                                <div class="flex flex-wrap gap-2 mb-4 pt-4 border-t border-white/10">
                                    @foreach($project->activeMembers->take(8) as $member)
                                        @if($member->memberable)
                                            <a href="{{ $member->memberable instanceof \App\Models\User ? route('users.show', $member->memberable) : ($member->memberable instanceof \App\Models\Circle ? route('circles.show', $member->memberable) : '#') }}" class="hover:scale-110 transition-transform">
                                                <img src="{{ $member->memberable->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($member->memberable->name ?? '?').'&background=1e293b&color=fff' }}" class="w-8 h-8 rounded-xl ring-2 ring-white/10 object-cover" title="{{ $member->memberable->name ?? '?' }}">
                                            </a>
                                        @endif
                                    @endforeach
                                    @if($project->activeMembers->count() > 8)
                                        <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center text-[8px] font-black text-white">+{{ $project->activeMembers->count() - 8 }}</div>
                                    @endif
                                </div>
                            @endif

                            {{-- Address --}}
                            <div class="mb-6 pt-4 border-t border-white/10">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[8px] font-black text-slate-500 uppercase tracking-[0.2em]">Localisation</span>
                                    @if($project->canManage(auth()->user()))
                                        <button wire:click="$toggle('isEditingAddress')" class="text-[8px] font-black text-blue-400 uppercase tracking-widest hover:text-blue-300 transition-colors">
                                            {{ $isEditingAddress ? 'Annuler' : 'Modifier' }}
                                        </button>
                                    @endif
                                </div>
                                
                                @if($isEditingAddress)
                                    <div class="space-y-2">
                                        <input 
                                            type="text" 
                                            wire:model="address" 
                                            wire:keydown.enter="updateAddress"
                                            placeholder="Ex: Paris, France"
                                            class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:border-blue-500 focus:ring-0 transition-all uppercase font-bold"
                                        >
                                        <button wire:click="updateAddress" class="w-full py-2 bg-blue-600 hover:bg-blue-700 rounded-xl text-[9px] font-black uppercase tracking-widest text-white transition-all">
                                            Enregistrer
                                        </button>
                                    </div>
                                @else
                                    <div class="flex items-start gap-2">
                                        <svg class="w-3.5 h-3.5 text-slate-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        @if($project->address)
                                            <div class="flex flex-col gap-2">
                                                @if($project->circle && $project->circle->address_tags)
                                                    <div class="flex flex-wrap items-center gap-1 mt-1">
                                                        @foreach($project->circle->address_tags as $tag)
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-white/10 text-[8px] font-black text-white/60 uppercase tracking-tighter">
                                                                {{ $tag }}
                                                            </span>
                                                            @if(!$loop->last)
                                                                <span class="text-[8px] text-white/20 font-bold">&lt;</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-xs font-black text-slate-900 uppercase tracking-tight leading-tight">
                                                        {{ $project->address }}
                                                    </span>
                                                @endif
                                                @if($project->city)
                                                    <div class="flex">
                                                        <a href="{{ url('/?search=' . urlencode($project->city)) }}" class="px-3 py-1 bg-blue-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                                                            {{ $project->city }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-xs font-bold text-slate-600 italic uppercase tracking-tight">
                                                Non définie
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>


                            <!-- CV Component Action -->
                            <div class="mt-6 pt-6 border-t border-white/10 mb-6">
                                <a href="{{ route('cv.project', $project) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-3 px-6 py-4 bg-blue-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 transition-all shadow-xl shadow-blue-500/20 group">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Générer CV du Projet
                                </a>
                            </div>

                            {{-- Auth actions --}}
                            @auth
                                <div class="space-y-3">
                                    @if($project->canManage(auth()->user()))
                                        <button wire:click="toggleStatus" class="w-full text-center py-4 rounded-2xl font-black text-sm tracking-widest uppercase transition-all border {{ $project->is_open ? 'bg-red-500/10 text-red-400 border-red-500/20 hover:bg-red-500 hover:text-white' : 'bg-green-500/10 text-green-400 border-green-500/20 hover:bg-green-500 hover:text-white' }}">
                                            {{ $project->is_open ? '⏸ Fermer le projet' : '▶ Ouvrir le projet' }}
                                        </button>
                                    @elseif($project->isMember(auth()->user()))
                                        <div class="w-full text-center py-4 bg-blue-600/10 rounded-2xl font-black text-sm tracking-widest uppercase text-blue-400 border border-blue-500/20">
                                            Membre Actif
                                        </div>
                                        <button wire:click="leaveProject" class="w-full text-center py-2 text-slate-500 hover:text-red-400 font-bold text-[10px] uppercase tracking-widest transition-colors">
                                            Quitter le projet
                                        </button>
                                    @elseif($project->isPending(auth()->user()))
                                        <div class="w-full text-center py-4 bg-orange-500/10 rounded-2xl font-black text-[10px] tracking-widest uppercase text-orange-400 border border-orange-500/20">
                                            Candidature en cours...
                                        </div>
                                        <button wire:click="leaveProject" class="w-full text-center py-2 text-slate-500 hover:text-red-400 font-bold text-[10px] uppercase tracking-widest transition-colors">
                                            Annuler la demande
                                        </button>
                                    @elseif($project->isInvited(auth()->user()))
                                        <div class="space-y-2">
                                            <div class="w-full text-center py-3 bg-purple-500/10 rounded-2xl font-black text-[10px] tracking-widest uppercase text-purple-400 border border-purple-500/20">
                                                Invitation reçue 💌
                                            </div>
                                            <div class="flex gap-2">
                                                <button wire:click="acceptInvitation" class="flex-grow py-3 bg-blue-600 hover:bg-blue-700 rounded-xl font-black text-[9px] uppercase tracking-widest text-white transition-all">Accepter</button>
                                                <button wire:click="leaveProject" class="px-4 py-3 bg-white/10 hover:bg-red-600 rounded-xl font-black text-[9px] uppercase tracking-widest text-white transition-all">Refuser</button>
                                            </div>
                                        </div>
                                    @elseif($project->is_open)
                                        <button wire:click="joinProject" class="w-full text-center py-4 bg-blue-600 hover:bg-blue-700 rounded-2xl font-black text-sm tracking-widest uppercase transition-all shadow-xl shadow-blue-500/20">
                                            Postuler au projet
                                        </button>
                                    @else
                                        <div class="w-full text-center py-4 bg-slate-800 rounded-2xl font-black text-sm tracking-widest uppercase text-slate-500 border border-slate-700">
                                            Projet fermé
                                        </div>
                                    @endif
                                </div>
                            @else
                                <a href="/admin/login" class="block text-center py-4 bg-blue-600 hover:bg-blue-700 rounded-2xl font-black text-sm tracking-widest uppercase transition-all shadow-xl shadow-blue-500/20">
                                    Se Connecter
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="max-w-7xl mx-auto px-6">
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
                    <div wire:key="offer-{{ $offer->id }}" class="bg-white/60 backdrop-blur-3xl border border-white/60 rounded-[3rem] overflow-hidden shadow-xl shadow-blue-500/5 hover:shadow-2xl hover:shadow-blue-500/10 hover:translate-y-[-4px] transition-all group/item">
                        {{-- Photo Gallery --}}
                        <div class="relative h-40 md:h-64 bg-slate-100 overflow-hidden">
                            @if($offer->images && count($offer->images) > 0)
                                <div class="flex overflow-x-auto snap-x snap-mandatory h-full no-scrollbar custom-scrollbar-h">
                                    @foreach($offer->images as $img)
                                        <div class="min-w-full h-full snap-start">
                                            <img src="{{ Storage::url($img) }}" class="w-full h-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                                @if(count($offer->images) > 1)
                                    <div class="absolute bottom-2 right-2 px-2 py-1 bg-black/60 backdrop-blur-md rounded-full text-[6px] md:text-[8px] font-black text-white uppercase tracking-widest pointer-events-none">
                                        {{ count($offer->images) }} Photos
                                    </div>
                                @endif
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-slate-50 text-slate-200">
                                    <svg class="w-8 h-8 md:w-16 md:h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif

                            @if($project->canManage(auth()->user()))
                                <div class="absolute top-2 right-2 flex gap-1.5 opacity-0 group-hover/item:opacity-100 transition-opacity z-30">
                                    <button wire:click.stop="editOffer({{ $offer->id }})" class="p-2 md:p-3 bg-white/90 backdrop-blur shadow-lg rounded-lg md:rounded-xl text-blue-600 hover:bg-blue-600 hover:text-white transition-all scale-75 md:scale-100">
                                        <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click.stop="deleteOffer({{ $offer->id }})" wire:confirm="Supprimer cette offre ?" class="p-2 md:p-3 bg-white/90 backdrop-blur shadow-lg rounded-lg md:rounded-xl text-red-600 hover:bg-red-600 hover:text-white transition-all scale-75 md:scale-100">
                                        <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            @endif
                            <div wire:click="setQuoteOffer({{ $offer->id }})" class="absolute inset-0 z-20 cursor-pointer group-hover/item:bg-blue-600/5 transition-colors"></div>
                        </div>

                        <div class="p-4 md:p-6 lg:p-8 relative">
                            <h3 class="text-xs md:text-sm lg:text-lg font-black text-slate-900 uppercase tracking-tight mb-2 md:mb-4 group-hover/item:text-blue-600 transition-colors line-clamp-2 min-h-[2.5em] md:min-h-0">{{ $offer->title }}</h3>
                            @if($offer->description)
                                <p class="text-[9px] md:text-xs text-slate-500 font-medium leading-relaxed mb-4 md:mb-6 line-clamp-2 italic">"{{ $offer->description }}"</p>
                            @endif

                            @if($offer->informations->count() > 0)
                                <div class="pt-3 md:pt-6 border-t border-slate-100 flex flex-wrap gap-1.5 md:gap-2">
                                    @foreach($offer->informations as $info)
                                        <div class="flex items-center gap-1 bg-blue-50/50 border border-blue-100/50 px-1.5 md:px-2.5 py-0.5 md:py-1 rounded-md md:rounded-lg text-[7px] md:text-[8px] font-black uppercase tracking-widest shadow-sm shadow-blue-500/5">
                                            @if($info->label)
                                                <span class="text-blue-400 italic">{{ $info->label }}:</span>
                                            @endif
                                            <span class="text-blue-600">{{ $info->title }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Buttons: View Reviews & Rate --}}
                            <div class="mt-4 md:mt-6 pt-4 md:pt-6 border-t border-slate-100 flex gap-2 relative z-30">
                                <button wire:click.stop="setReviewOffer({{ $activeOfferIdForReview === $offer->id && !$showReviewModal ? 'null' : $offer->id }})" class="flex-grow py-2 md:py-3 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-lg md:rounded-xl font-black text-[8px] md:text-[10px] uppercase tracking-widest transition-colors flex items-center justify-center gap-1.5 md:gap-2">
                                    <svg class="w-3 h-3 md:w-4 md:h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Avis ({{ $offer->reviews->count() }})</span>
                                </button>
                                <button wire:click.stop="setReviewOffer({{ $offer->id }}, true)" class="px-4 py-2 md:py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg md:rounded-xl font-black text-[8px] md:text-[10px] uppercase tracking-widest transition-colors flex items-center justify-center gap-1.5 md:gap-2 shadow-lg shadow-blue-500/20">
                                    <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    <span>Évaluer</span>
                                </button>
                            </div>

                            <div wire:click="setQuoteOffer({{ $offer->id }})" class="absolute inset-0 z-20 cursor-pointer"></div>
                        @if($activeOfferIdForReview === $offer->id && !$showReviewModal)
                                <div class="mt-4 border-t border-slate-100 pt-6 animate-in slide-in-from-top-2 duration-300">
                                    {{-- Reviews List Only --}}
                                    <div class="space-y-4">
                                        @forelse($offer->reviews as $review)
                                            <div @class(['p-4 rounded-[1.5rem] border transition-all', 'bg-green-50/50 border-green-100' => $review->type === 'validate', 'bg-red-50/50 border-red-100' => $review->type === 'reject'])>
                                                <div class="flex items-center gap-3 mb-3">
                                                    <img src="{{ $review->user->avatar }}" class="w-8 h-8 rounded-xl object-cover ring-2 ring-white shadow-sm">
                                                    <div class="flex flex-col">
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-[11px] font-black text-slate-900 uppercase">{{ $review->user->name }}</span>
                                                            <span @class(['text-[7px] font-black uppercase px-1.5 py-0.5 rounded-md', 'bg-green-600 text-white' => $review->type === 'validate', 'bg-red-600 text-white' => $review->type === 'reject'])>
                                                                {{ $review->type === 'validate' ? '✓' : '✗' }}
                                                            </span>
                                                        </div>
                                                        <div class="text-[8px] font-black text-slate-400 uppercase mt-0.5">{{ $review->created_at->diffForHumans() }}</div>
                                                    </div>
                                                    
                                                    @auth
                                                        @if(($review->user_id === auth()->id() || $project->canManage(auth()->user())) && $review->replies->count() === 0)
                                                            <button wire:click="deleteReview({{ $review->id }})" wire:confirm="Supprimer cet avis ?" class="ml-auto text-slate-300 hover:text-red-500 transition-colors">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                            </button>
                                                        @endif
                                                    @endauth
                                                </div>

                                                @if($review->comment)
                                                    <p @class(['text-xs font-medium leading-relaxed italic', 'text-slate-700' => $review->replies->count() === 0, 'text-slate-400' => $review->replies->count() > 0])>"{{ $review->comment }}"</p>
                                                @endif

                                                @if($review->replies->count() > 0)
                                                    <div class="mt-3 pl-4 border-l-2 border-white space-y-2">
                                                        @foreach($review->replies as $reply)
                                                            <div class="p-3 bg-white/80 rounded-xl shadow-sm border border-slate-100">
                                                                <div class="flex items-center gap-2 mb-1.5">
                                                                    <img src="{{ $reply->user->avatar }}" class="w-5 h-5 rounded-md object-cover ring-1 ring-blue-500/20 shadow-sm">
                                                                    <div>
                                                                        <span class="text-[8px] font-black text-slate-900 uppercase">{{ $reply->user->name }}</span>
                                                                        <span class="text-[6px] font-black text-blue-600 bg-blue-50 px-1 py-0.5 rounded-md uppercase tracking-widest ml-1">Office</span>
                                                                    </div>
                                                                </div>
                                                                <p class="text-[9px] font-bold text-slate-700">"{{ $reply->comment }}"</p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                @auth
                                                    @if($project->canManage(auth()->user()) && $review->replies->count() === 0)
                                                        <button wire:click="setReplyTo({{ $review->id }})" class="mt-2 text-[8px] font-black text-blue-600 uppercase tracking-widest hover:text-blue-800 transition-colors">
                                                            ↩ Répondre
                                                        </button>
                                                    @endif
                                                @endauth
                                            </div>
                                        @empty
                                            <p class="text-[9px] text-slate-400 font-black uppercase tracking-[0.2em] italic text-center py-4">Aucun avis.</p>
                                        @endforelse

                                        @if($replyTo)
                                            <div class="bg-blue-50 border border-blue-200 rounded-[1.5rem] p-4 mt-4">
                                                <div class="flex items-center justify-between mb-3">
                                                    <span class="text-[9px] font-black text-blue-600 uppercase tracking-widest">Répondre</span>
                                                    <button wire:click="$set('replyTo', null)" class="text-slate-400 hover:text-red-500 transition-colors">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </div>
                                                <textarea wire:model="reviewComment" rows="2" placeholder="Votre réponse..."
                                                    class="w-full bg-white border-white focus:ring-blue-500 rounded-xl p-3 text-xs font-medium italic mb-3"></textarea>
                                                <button wire:click="submitReview" class="w-full py-2 bg-blue-600 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-blue-700 transition-all">
                                                    Publier
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-32 text-center bg-white/40 border-2 border-dashed border-slate-200 rounded-[4rem]">
                        <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-6 text-slate-200">
                             <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <p class="text-slate-400 font-black uppercase tracking-[0.4em] text-xs italic">La boutique est vide pour le moment.</p>
                    </div>
                @endforelse
            </div>
            </div>
        </div>



    {{-- ===== DETACHED FORUM (BOTTOM) ===== --}}
    <div class="max-w-7xl mx-auto px-6 mt-12 pt-12 border-t border-slate-100">
        <div class="bg-slate-900 rounded-[3.5rem] p-10 shadow-2xl shadow-slate-900/20 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-blue-600/10 to-transparent"></div>

            <h2 class="text-3xl font-black mb-10 tracking-tight flex items-center gap-4 relative z-10">
                Le Forum
                <span class="px-3 py-1 bg-white/10 text-xs rounded-full border border-white/10 font-black">{{ $project->messages->count() }}</span>
            </h2>

            {{-- Message input --}}
            @auth
                <div class="relative z-10 p-4 bg-white/5 border border-white/10 rounded-[2.5rem] mb-10 group-focus-within:border-blue-500 transition-all">
                    <textarea wire:model="message" placeholder="Posez une question, partagez une info..."
                        class="w-full bg-transparent border-none focus:ring-0 text-sm text-slate-200 placeholder:text-slate-600 mb-4 resize-none"
                        rows="3"></textarea>
                    <button wire:click="sendMessage" class="w-full py-4 bg-white text-slate-900 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all shadow-xl shadow-white/5">
                        Envoyer au projet
                    </button>
                </div>
            @else
                <div class="relative z-10 text-center p-8 bg-white/5 border border-dashed border-white/10 rounded-3xl mb-10">
                    <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.3em]">Connectez-vous pour participer au forum</p>
                </div>
            @endauth

            {{-- Messages feed --}}
            <div class="space-y-6 max-h-[600px] overflow-y-auto pr-4 custom-scrollbar relative z-10">
                @forelse($project->messages as $msg)
                    <div class="flex flex-col gap-2 animate-in fade-in slide-in-from-bottom-2 duration-300">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('users.show', $msg->sender) }}" class="flex items-center gap-3 group/msg hover:opacity-80 transition-all">
                                <img src="{{ $msg->sender->avatar }}" class="w-6 h-6 rounded-lg ring-1 ring-white/20 group-hover/msg:ring-blue-500 transition-all">
                                <span class="text-xs font-black uppercase tracking-widest text-slate-400 group-hover/msg:text-blue-400 transition-colors">{{ $msg->sender->name }}</span>
                                <span class="text-[9px] font-black text-slate-600 uppercase">{{ $msg->created_at->diffForHumans() }}</span>
                            </a>
                        </div>
                        <div class="bg-white/5 border border-white/10 p-5 rounded-2xl rounded-tl-none text-slate-300 text-sm leading-relaxed italic">
                            {{ $msg->content }}
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-600 font-black uppercase tracking-[0.3em] text-[10px]">Silence radio... soyez le premier à parler !</div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ===== GLOBAL REVIEW MODAL ===== --}}
    @if($showReviewModal && $activeOfferIdForReview)
        @php $offerForModal = $project->offers->firstWhere('id', $activeOfferIdForReview); @endphp
        @if($offerForModal)
            <div class="fixed inset-0 z-[200] flex items-center justify-center px-4 md:px-6 py-6 md:py-12" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                {{-- Backdrop --}}
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xl" wire:click="setReviewOffer(null, false)"></div>

                {{-- Modal Card --}}
                <div class="bg-white rounded-[2rem] md:rounded-[3rem] shadow-3xl w-full max-w-2xl relative overflow-hidden animate-in zoom-in-95 duration-300 flex flex-col md:flex-row max-h-[95vh] md:max-h-[90vh]">
                    {{-- Left side: Product Info --}}
                    <div class="w-full md:w-1/3 bg-slate-50 p-6 md:p-8 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-slate-100 shrink-0">
                        @if($offerForModal->images && count($offerForModal->images) > 0)
                            <img src="{{ Storage::url($offerForModal->images[0]) }}" class="w-24 h-24 md:w-40 md:h-40 rounded-2xl md:rounded-3xl object-cover shadow-2xl mb-4 md:mb-6">
                        @else
                            <div class="w-24 h-24 md:w-40 md:h-40 bg-white rounded-2xl md:rounded-3xl flex items-center justify-center text-slate-200 mb-4 md:mb-6 shadow-xl">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                        @endif
                        <h4 class="text-[10px] md:text-xs font-black text-slate-900 uppercase tracking-tight text-center line-clamp-2 px-2">{{ $offerForModal->title }}</h4>
                        <div class="mt-3 flex flex-wrap justify-center gap-1">
                            @foreach($offerForModal->informations->take(2) as $info)
                                <span class="bg-white px-2 py-0.5 md:py-1 rounded md:rounded-md text-[6px] md:text-[7px] font-black uppercase text-blue-600 border border-slate-100 shadow-sm">{{ $info->title }}</span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Right side: Form --}}
                    <div class="flex-grow p-6 md:p-10 relative overflow-y-auto custom-scrollbar-modal">
                        <button wire:click="setReviewOffer(null, false)" class="absolute top-6 right-6 text-slate-300 hover:text-slate-900 transition-colors z-10">
                            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        <div class="mb-6 md:mb-8">
                            <span class="text-[8px] md:text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] mb-1 md:mb-2 block">Nouvel Avis</span>
                            <h2 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight leading-tight">Votre expérience client</h2>
                        </div>

                        @auth
                            @if(!$project->reviews()->where('user_id', auth()->id())->where('project_offer_id', $offerForModal->id)->whereNull('parent_id')->exists())
                                <div class="space-y-4 md:space-y-6">
                                    {{-- Quality Toggle --}}
                                    <div class="flex p-1 bg-slate-100 rounded-xl md:rounded-2xl gap-1">
                                        <button wire:click="$set('reviewType', 'validate')" @class(['flex-1 py-2 md:py-3 rounded-lg md:rounded-xl font-black text-[9px] md:text-[10px] uppercase tracking-widest transition-all flex items-center justify-center gap-2', 'bg-white text-green-600 shadow-xl shadow-green-500/10' => $reviewType === 'validate', 'text-slate-500 hover:bg-white/50' => $reviewType !== 'validate'])>
                                            <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            Satisfait
                                        </button>
                                        <button wire:click="$set('reviewType', 'reject')" @class(['flex-1 py-2 md:py-3 rounded-lg md:rounded-xl font-black text-[9px] md:text-[10px] uppercase tracking-widest transition-all flex items-center justify-center gap-2', 'bg-white text-red-600 shadow-xl shadow-red-500/10' => $reviewType === 'reject', 'text-slate-500 hover:bg-white/50' => $reviewType !== 'reject'])>
                                            <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Déçu
                                        </button>
                                    </div>

                                    {{-- Comment --}}
                                    <div class="space-y-1.5 md:space-y-2">
                                        <label class="text-[8px] md:text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Commentaire détaillé</label>
                                        <textarea wire:model="reviewComment" rows="4" placeholder="Décrivez votre expérience avec cet article..."
                                            class="w-full bg-slate-50 border border-slate-100 focus:border-blue-500 focus:ring-0 rounded-xl md:rounded-2xl p-4 md:p-5 text-sm font-medium text-slate-800 placeholder:text-slate-400 resize-none transition-all outline-none italic"></textarea>
                                        @error('reviewComment') <span class="text-red-500 text-[8px] md:text-[9px] font-black uppercase mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <button wire:click="submitReview" class="w-full py-4 md:py-5 bg-slate-900 hover:bg-blue-600 text-white rounded-xl md:rounded-2xl font-black text-xs uppercase tracking-[0.2em] transition-all shadow-2xl shadow-slate-900/20 active:scale-95">
                                        Publier mon avis
                                    </button>
                                </div>
                            @else
                                <div class="bg-blue-50 border border-blue-100 rounded-2xl md:rounded-3xl p-6 md:p-10 text-center">
                                    <div class="w-12 h-12 md:w-16 md:h-16 bg-blue-600 text-white rounded-xl md:rounded-2xl flex items-center justify-center mx-auto mb-4 md:mb-6 shadow-xl shadow-blue-500/20">
                                        <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <h3 class="text-base md:text-lg font-black text-slate-900 uppercase tracking-tight mb-2">Avis déjà publié</h3>
                                    <p class="text-[9px] md:text-[10px] text-slate-500 font-black uppercase tracking-widest leading-relaxed">Vous avez déjà partagé votre opinion sur cet article. Merci de votre contribution !</p>
                                    <button wire:click="setReviewOffer(null, false)" class="mt-6 md:mt-8 px-6 md:px-8 py-2 md:py-3 bg-white border border-slate-100 rounded-lg md:rounded-xl text-[9px] md:text-[10px] font-black uppercase tracking-widest text-slate-900 hover:bg-slate-50 transition-all">Fermer</button>
                                </div>
                            @endif
                        @else
                            <div class="bg-slate-50 border border-slate-100 rounded-2xl md:rounded-3xl p-6 md:p-10 text-center">
                                <p class="text-[10px] md:text-xs font-black text-slate-500 uppercase tracking-widest mb-4 md:mb-6">Connectez-vous pour évaluer cet article</p>
                                <a href="/admin/login" class="inline-block px-6 md:px-8 py-3 md:py-4 bg-blue-600 text-white rounded-xl md:rounded-2xl font-black text-[9px] md:text-[10px] uppercase tracking-[0.2em] hover:bg-blue-700 transition-all shadow-xl shadow-blue-500/20">Se Connecter</a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- ===== QUOTE REQUEST MODAL ===== --}}
    @if($showQuoteModal && $quoteOfferId)
        @php $offerForQuote = $project->offers->firstWhere('id', $quoteOfferId); @endphp
        @if($offerForQuote)
            <div class="fixed inset-0 z-[200] flex items-center justify-center px-4 md:px-6 py-6 md:py-12" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                {{-- Backdrop --}}
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xl" wire:click="setQuoteOffer(null, false)"></div>

                {{-- Modal Card --}}
                <div class="bg-white rounded-[2rem] md:rounded-[3rem] shadow-3xl w-full max-w-2xl relative overflow-hidden animate-in zoom-in-95 duration-300 flex flex-col md:flex-row max-h-[95vh] md:max-h-[90vh]">
                    {{-- Left side: Product Info --}}
                    <div class="w-full md:w-1/3 bg-blue-50 p-6 md:p-8 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-blue-100 shrink-0">
                        @if($offerForQuote->images && count($offerForQuote->images) > 0)
                            <img src="{{ Storage::url($offerForQuote->images[0]) }}" class="w-24 h-24 md:w-40 md:h-40 rounded-2xl md:rounded-3xl object-cover shadow-2xl mb-4 md:mb-6 ring-4 ring-white">
                        @else
                            <div class="w-24 h-24 md:w-40 md:h-40 bg-white rounded-2xl md:rounded-3xl flex items-center justify-center text-blue-200 mb-4 md:mb-6 shadow-xl">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                        @endif
                        <h4 class="text-[10px] md:text-xs font-black text-slate-900 uppercase tracking-tight text-center line-clamp-2 px-2">{{ $offerForQuote->title }}</h4>
                    </div>

                    {{-- Right side: Form --}}
                    <div class="flex-grow p-6 md:p-10 relative overflow-y-auto custom-scrollbar-modal">
                        <button wire:click="setQuoteOffer(null, false)" class="absolute top-6 right-6 text-slate-300 hover:text-slate-900 transition-colors z-10">
                            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        <div class="mb-6 md:mb-8">
                            <span class="text-[8px] md:text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] mb-1 md:mb-2 block">Demande de Devis</span>
                            <h2 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight leading-tight">Envoyer un message privé</h2>
                        </div>

                        @auth
                            <div class="space-y-4 md:space-y-6">
                                <div class="p-4 bg-orange-50 border border-orange-100 rounded-xl">
                                    <p class="text-[9px] font-bold text-orange-700 leading-relaxed uppercase tracking-widest">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Votre demande sera envoyée directement en privé au propriétaire du projet.
                                    </p>
                                </div>

                                {{-- Quote Message --}}
                                <div class="space-y-1.5 md:space-y-2">
                                    <label class="text-[8px] md:text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Votre message</label>
                                    <textarea wire:model="quoteMessage" rows="6" placeholder="Posez vos questions ou précisez votre demande..."
                                        class="w-full bg-slate-50 border border-slate-100 focus:border-blue-500 focus:ring-0 rounded-xl md:rounded-2xl p-4 md:p-5 text-sm font-medium text-slate-800 placeholder:text-slate-400 resize-none transition-all outline-none"></textarea>
                                    @error('quoteMessage') <span class="text-red-500 text-[8px] md:text-[9px] font-black uppercase mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <button wire:click="submitQuoteRequest" class="w-full py-4 md:py-5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl md:rounded-2xl font-black text-xs uppercase tracking-[0.2em] transition-all shadow-2xl shadow-blue-500/20 active:scale-95 flex items-center justify-center gap-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    Envoyer le devis
                                </button>
                            </div>
                        @else
                            <div class="bg-slate-50 border border-slate-100 rounded-2xl md:rounded-3xl p-6 md:p-10 text-center">
                                <p class="text-[10px] md:text-xs font-black text-slate-500 uppercase tracking-widest mb-4 md:mb-6">Connectez-vous pour demander un devis</p>
                                <a href="/admin/login" class="inline-block px-6 md:px-8 py-3 md:py-4 bg-blue-600 text-white rounded-xl md:rounded-2xl font-black text-[9px] md:text-[10px] uppercase tracking-[0.2em] hover:bg-blue-700 transition-all shadow-xl shadow-blue-500/20">Se Connecter</a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        @endif
    @endif

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
