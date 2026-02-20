<div>
    {{-- ===== REVIEW MODAL (Avis) ===== --}}
    @if($showReviewModal && $activeOfferIdForReview)
        @php 
            $offerForReview = \App\Models\ProjectOffer::with('project')->find($activeOfferIdForReview); 
            $projectForReview = $offerForReview?->project;
        @endphp
        @if($offerForReview)
            <div class="fixed inset-0 z-[200] flex items-center justify-center px-4 md:px-6 py-6 md:py-12" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                {{-- Backdrop --}}
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xl" wire:click="setReviewOffer(null, false)"></div>

                {{-- Modal Card --}}
                <div class="bg-white rounded-[2rem] md:rounded-[3rem] shadow-3xl w-full max-w-2xl relative overflow-hidden animate-in zoom-in-95 duration-300 flex flex-col md:flex-row max-h-[95vh] md:max-h-[90vh]">
                    {{-- Left side: Product Info --}}
                    <div class="w-full md:w-1/3 bg-slate-50 p-6 md:p-8 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-slate-100 shrink-0">
                        @if($offerForReview->images && count($offerForReview->images) > 0)
                            <img src="{{ Storage::url($offerForReview->images[0]) }}" class="w-24 h-24 md:w-40 md:h-40 rounded-2xl md:rounded-3xl object-cover shadow-2xl mb-4 md:mb-6">
                        @else
                            <div class="w-24 h-24 md:w-40 md:h-40 bg-white rounded-2xl md:rounded-3xl flex items-center justify-center text-slate-200 mb-4 md:mb-6 shadow-xl">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                        @endif
                        <h4 class="text-[10px] md:text-xs font-black text-slate-900 uppercase tracking-tight text-center line-clamp-2 px-2">{{ $offerForReview->title }}</h4>
                    </div>

                    {{-- Right side: Form --}}
                    <div class="flex-grow p-6 md:p-10 relative overflow-y-auto custom-scrollbar-modal">
                        <button wire:click="setReviewOffer(null, false)" class="absolute top-6 right-6 text-slate-300 hover:text-slate-900 transition-colors z-10">
                            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        <div class="mb-6 md:mb-8">
                            <span class="text-[8px] md:text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] mb-1 md:mb-2 block">{{ $replyTo ? 'Répondre à l\'avis' : 'Donner mon avis' }}</span>
                            <h2 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight leading-tight">Partagez votre expérience</h2>
                        </div>

                        @auth
                            @php 
                                $alreadyReviewed = \App\Models\ProjectReview::where('project_offer_id', $offerForReview->id)->where('user_id', auth()->id())->whereNull('parent_id')->exists();
                            @endphp
                            
                            @if(!$alreadyReviewed || $replyTo)
                                <div class="space-y-4 md:space-y-6">
                                    {{-- Rating --}}
                                    @if(!$replyTo)
                                        <div class="space-y-1.5 md:space-y-2">
                                            <div class="flex items-center justify-between px-1">
                                                <label class="text-[8px] md:text-[9px] font-black text-slate-400 uppercase tracking-widest">Note globale</label>
                                                <span class="text-[10px] md:text-xs font-black text-blue-600">{{ $reviewRating }}/5</span>
                                            </div>
                                            <div class="flex items-center gap-2 md:gap-3 bg-slate-50 p-2 md:p-3 rounded-xl md:rounded-2xl border border-slate-100">
                                                @foreach(range(1, 5) as $star)
                                                    <button wire:click="$set('reviewRating', {{ $star }})" class="p-1.5 md:p-2 transition-all hover:scale-110 active:scale-90">
                                                        <svg @class(['w-5 h-5 md:w-7 md:h-7 transition-colors', 'text-amber-400 fill-amber-400' => $reviewRating >= $star, 'text-slate-200' => $reviewRating < $star]) fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                                        </svg>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Type Toggle --}}
                                    <div class="flex gap-2 p-1 bg-slate-100 rounded-xl md:rounded-2xl">
                                        <button wire:click="$set('reviewType', 'validate')" @class(['flex-1 py-2 md:py-3 rounded-lg md:rounded-xl font-black text-[9px] md:text-[10px] uppercase tracking-widest transition-all flex items-center justify-center gap-2', 'bg-white text-blue-600 shadow-xl shadow-blue-500/10' => $reviewType === 'validate', 'text-slate-500 hover:bg-white/50' => $reviewType !== 'validate'])>
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
        @php $offerForQuote = \App\Models\ProjectOffer::with('project')->find($quoteOfferId); @endphp
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
</div>
