<div x-data="{ isOpen: @entangle('isOpen') }" class="relative">
    {{-- Full-screen Glassmorphism Overlay --}}
    <div 
        x-show="isOpen" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 backdrop-blur-0"
        x-transition:enter-end="opacity-100 backdrop-blur-3xl"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 backdrop-blur-3xl"
        x-transition:leave-end="opacity-0 backdrop-blur-0"
        class="fixed inset-0 z-[100] bg-white/10 flex flex-col pt-24"
        style="backdrop-filter: blur(40px) saturate(150%);"
    >
        {{-- Close Sidebar/Click Outside --}}
        <div class="absolute inset-0 z-0" @click="isOpen = false"></div>

        {{-- Main Container --}}
        <div class="relative z-10 max-w-6xl mx-auto w-full flex-grow flex flex-col px-4 md:px-8 pb-8 overflow-hidden">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-8 md:mb-12">
                <div class="flex items-center gap-6">
                    <div class="w-12 h-12 md:w-16 md:h-16 bg-blue-600 rounded-2xl md:rounded-3xl flex items-center justify-center shadow-2xl shadow-blue-500/40">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-2xl md:text-4xl font-black text-slate-900 tracking-tighter uppercase">Messagerie <span class="text-blue-600">Globale</span></h2>
                        <p class="text-[10px] md:text-xs font-black text-slate-500 uppercase tracking-[0.3em] mt-1">Vos conversations & notifications en temps réel</p>
                    </div>
                </div>
                <button @click="isOpen = false" class="w-12 h-12 md:w-16 md:h-16 rounded-2xl md:rounded-3xl bg-white/50 border border-white/20 hover:bg-white transition-all flex items-center justify-center group shadow-xl">
                    <svg class="w-6 h-6 text-slate-400 group-hover:text-slate-900 group-hover:rotate-90 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Navigation Tabs --}}
            <div class="flex p-1.5 bg-slate-900/5 backdrop-blur-xl rounded-2xl md:rounded-3xl border border-white/20 w-full mb-8">
                <button wire:click="selectTab('private')" @class(['flex-1 py-3 md:py-4 rounded-xl md:rounded-2xl font-black text-[10px] md:text-xs uppercase tracking-widest transition-all flex items-center justify-center gap-3', 'bg-white text-blue-600 shadow-xl shadow-blue-500/10' => $activeTab === 'private', 'text-slate-500 hover:bg-white/40' => $activeTab !== 'private'])>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Privé</span>
                </button>
                <button wire:click="selectTab('projects')" @class(['flex-1 py-3 md:py-4 rounded-xl md:rounded-2xl font-black text-[10px] md:text-xs uppercase tracking-widest transition-all flex items-center justify-center gap-3', 'bg-white text-indigo-600 shadow-xl shadow-indigo-500/10' => $activeTab === 'projects', 'text-slate-500 hover:bg-white/40' => $activeTab !== 'projects'])>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Projets</span>
                </button>
                <button wire:click="selectTab('circles')" @class(['flex-1 py-3 md:py-4 rounded-xl md:rounded-2xl font-black text-[10px] md:text-xs uppercase tracking-widest transition-all flex items-center justify-center gap-3', 'bg-white text-purple-600 shadow-xl shadow-purple-500/10' => $activeTab === 'circles', 'text-slate-500 hover:bg-white/40' => $activeTab !== 'circles'])>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    <span>Cercles</span>
                </button>
            </div>

            {{-- Content Area --}}
            <div class="flex-grow bg-white/40 backdrop-blur-2xl rounded-[2.5rem] md:rounded-[3.5rem] border border-white/60 shadow-inner overflow-hidden flex flex-col">
                <div class="flex-grow overflow-y-auto custom-scrollbar-messaging p-4 md:p-10">
                    
                    @if($activeTab === 'private')
                        @if($selectedParticipantId)
                            {{-- Detail View --}}
                            <div class="h-full flex flex-col min-h-[400px]">
                                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-white/40">
                                    <button wire:click="$set('selectedParticipantId', null)" class="p-2 hover:bg-white/40 rounded-xl transition-all text-slate-400 hover:text-slate-900">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                                    </button>
                                    <div class="w-12 h-12 rounded-2xl border-2 border-white overflow-hidden shadow-lg">
                                        <img src="{{ $selectedParticipant->avatar }}" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <h4 class="font-black text-slate-900 uppercase text-xs">{{ $selectedParticipant->name }}</h4>
                                        <p class="text-[8px] font-black text-blue-600 uppercase tracking-widest">Discussion Privée</p>
                                    </div>
                                </div>
                                
                                <div class="flex-grow space-y-4 mb-6 overflow-y-auto pr-2 custom-scrollbar-messaging">
                                    @forelse($activeMessages as $msg)
                                        <div @class(['flex flex-col', 'items-end' => $msg->sender_id === auth()->id(), 'items-start' => $msg->sender_id !== auth()->id()])>
                                            <div @class(['max-w-[85%] p-4 rounded-2xl text-[11px] font-medium leading-relaxed shadow-sm', 'bg-blue-600 text-white rounded-br-none' => $msg->sender_id === auth()->id(), 'bg-white text-slate-900 rounded-bl-none border border-slate-100/50' => $msg->sender_id !== auth()->id()])>
                                                {{ $msg->content }}
                                                @if($msg->metadata && isset($msg->metadata['type']) && $msg->metadata['type'] === 'quote_request')
                                                    <div class="mt-2 p-2 bg-black/10 rounded-lg border border-white/10">
                                                        <span class="text-[7px] uppercase font-black tracking-widest block opacity-70">Référence Offre :</span>
                                                        <span class="text-[9px] font-black">{{ $msg->metadata['offer_title'] ?? 'Offre sans titre' }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <span class="text-[7px] font-black text-slate-400 uppercase mt-1 px-1">{{ $msg->created_at->diffForHumans(null, true) }}</span>
                                        </div>
                                    @empty
                                        <div class="h-full flex flex-col items-center justify-center text-center p-12 opacity-40">
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Aucun message échangé</p>
                                        </div>
                                    @endforelse
                                </div>

                                <form wire:submit.prevent="sendMessage" class="relative mt-auto">
                                    <textarea wire:model="messageText" rows="2" placeholder="Écrire un message..." class="w-full bg-white/60 border border-white focus:border-blue-500 focus:ring-0 rounded-2xl p-4 pr-16 text-xs font-medium text-slate-800 placeholder:text-slate-400 resize-none transition-all outline-none italic shadow-inner"></textarea>
                                    <button type="submit" class="absolute right-3 bottom-3 w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20 hover:scale-105 active:scale-95 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    </button>
                                </form>
                            </div>
                        @else
                            {{-- List View --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                                @forelse($conversations as $participantId => $msgs)
                                    @php $other = $msgs->first()->sender_id == auth()->id() ? $msgs->first()->receiver : $msgs->first()->sender; @endphp
                                    <div wire:click="selectConversation({{ $participantId }})" class="bg-white/60 p-5 md:p-6 rounded-[2rem] border border-white hover:bg-white transition-all group cursor-pointer shadow-sm hover:shadow-2xl hover:shadow-blue-500/10 active:scale-[0.98]">
                                        <div class="flex items-center gap-4">
                                            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl md:rounded-3xl border-2 border-white overflow-hidden shadow-lg shrink-0">
                                                <img src="{{ $other->avatar }}" class="w-full h-full object-cover">
                                            </div>
                                            <div class="flex-grow min-w-0">
                                                <div class="flex items-center justify-between mb-1">
                                                    <h4 class="font-black text-slate-900 uppercase text-[10px] md:text-xs truncate">{{ $other->name }}</h4>
                                                    <span class="text-[8px] font-black text-slate-400 uppercase">{{ $msgs->first()->created_at->diffForHumans(null, true) }}</span>
                                                </div>
                                                <p class="text-[9px] md:text-[10px] text-slate-500 font-medium line-clamp-2 italic leading-relaxed">
                                                    {{ $msgs->first()->sender_id == auth()->id() ? 'Vous : ' : '' }}{{ $msgs->first()->content }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-full h-full flex flex-col items-center justify-center text-center p-12">
                                        <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center text-slate-300 mb-6 border border-slate-50 shadow-inner">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2-0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"/></svg>
                                        </div>
                                        <h5 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2">Aucune conversation</h5>
                                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em]">Commencez par envoyer une demande de devis sur un projet !</p>
                                    </div>
                                @endforelse
                            </div>
                        @endif
                    @endif

                    @if($activeTab === 'projects')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8">
                            @forelse($projects as $proj)
                                <a href="{{ route('projects.show', $proj) }}" class="bg-indigo-600/5 p-6 md:p-8 rounded-[2.5rem] md:rounded-[3rem] border border-white hover:bg-white transition-all group cursor-pointer shadow-sm hover:shadow-2xl hover:shadow-indigo-500/10 active:scale-[0.98] flex flex-col md:flex-row gap-6">
                                    <div class="w-full md:w-32 h-32 bg-indigo-600 rounded-[2rem] flex items-center justify-center text-white shrink-0 shadow-2xl shadow-indigo-500/30">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    </div>
                                    <div class="flex-grow">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-[8px] md:text-[9px] font-black text-indigo-400 uppercase tracking-widest">Forum Projet</span>
                                            @if($proj->messages->first())
                                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter">{{ $proj->messages->first()->created_at->diffForHumans() }}</span>
                                            @endif
                                        </div>
                                        <h4 class="font-black text-slate-900 uppercase text-xs md:text-sm lg:text-lg tracking-tight mb-2 group-hover:text-indigo-600 transition-colors">{{ $proj->title }}</h4>
                                        @if($proj->messages->first())
                                            <p class="text-[10px] text-slate-500 font-medium line-clamp-2 leading-relaxed bg-white/40 p-3 rounded-xl border border-white italic">
                                                <span class="font-black text-indigo-400 not-italic mr-1">{{ $proj->messages->first()->sender->name }} :</span> 
                                                "{{ $proj->messages->first()->content }}"
                                            </p>
                                        @else
                                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest italic leading-relaxed">Aucun message pour le moment</p>
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <div class="col-span-full h-full flex flex-col items-center justify-center text-center p-12">
                                    <div class="w-20 h-20 bg-indigo-50 rounded-3xl flex items-center justify-center text-indigo-200 mb-6 border border-indigo-100 shadow-inner">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    </div>
                                    <h5 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2">Aucun projet joint</h5>
                                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em]">Rejoignez un projet pour accéder à son forum !</p>
                                </div>
                            @endforelse
                        </div>
                    @endif

                    @if($activeTab === 'circles')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8">
                            @forelse($circles as $circle)
                                <a href="{{ route('circles.show', $circle) }}" class="bg-purple-600/5 p-6 md:p-8 rounded-[2.5rem] md:rounded-[3rem] border border-white hover:bg-white transition-all group cursor-pointer shadow-sm hover:shadow-2xl hover:shadow-purple-500/10 active:scale-[0.98] flex flex-col md:flex-row gap-6">
                                    <div class="w-full md:w-32 h-32 bg-purple-600 rounded-[2rem] flex items-center justify-center text-white shrink-0 shadow-2xl shadow-purple-500/30">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    </div>
                                    <div class="flex-grow">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-[8px] md:text-[9px] font-black text-purple-400 uppercase tracking-widest">Forum Cercle</span>
                                            @if($circle->messages->first())
                                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter">{{ $circle->messages->first()->created_at->diffForHumans() }}</span>
                                            @endif
                                        </div>
                                        <h4 class="font-black text-slate-900 uppercase text-xs md:text-sm lg:text-lg tracking-tight mb-2 group-hover:text-purple-600 transition-colors">{{ $circle->name }}</h4>
                                        @if($circle->messages->first())
                                            <p class="text-[10px] text-slate-500 font-medium line-clamp-2 leading-relaxed bg-white/40 p-3 rounded-xl border border-white italic">
                                                <span class="font-black text-purple-400 not-italic mr-1">{{ $circle->messages->first()->sender->name }} :</span> 
                                                "{{ $circle->messages->first()->content }}"
                                            </p>
                                        @else
                                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest italic leading-relaxed">Aucun message pour le moment</p>
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <div class="col-span-full h-full flex flex-col items-center justify-center text-center p-12">
                                    <div class="w-20 h-20 bg-purple-50 rounded-3xl flex items-center justify-center text-purple-200 mb-6 border border-purple-100 shadow-inner">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    </div>
                                    <h5 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2">Aucun cercle rejoint</h5>
                                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em]">Rejoignez un cercle pour accéder à ses discussions !</p>
                                </div>
                            @endforelse
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar-messaging::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar-messaging::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar-messaging::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.05); border-radius: 10px; }
        .custom-scrollbar-messaging::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.1); }
    </style>
</div>
