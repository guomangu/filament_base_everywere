<div class="max-w-3xl mx-auto py-12 px-4">
    <div class="bg-white/40 backdrop-blur-xl border border-white/40 rounded-[3rem] p-8 md:p-16 shadow-2xl shadow-blue-500/5">
        <div class="mb-12">
            <h1 class="text-4xl font-black text-slate-900 mb-2">Refine Your Circle</h1>
            <p class="text-slate-500 font-medium">Update the details of {{ $circle->name }} to better reflect your mission.</p>
        </div>

        <form wire:submit.prevent="update" class="space-y-8">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-black text-slate-400 uppercase tracking-widest px-1">Type</label>
                    <div class="relative">
                        <select wire:model="type" class="w-full bg-white/60 border-slate-100 rounded-2xl px-6 py-4 font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none appearance-none cursor-pointer">
                            <option value="business">Business / Coworking</option>
                            <option value="place">Place / Location</option>
                            <option value="event">Event / Meetup</option>
                            <option value="project">Project / Squad</option>
                        </select>
                        <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-black text-slate-400 uppercase tracking-widest px-1">Visibility</label>
                    <div class="relative">
                        <select wire:model="is_public" class="w-full bg-white/60 border-slate-100 rounded-2xl px-6 py-4 font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none appearance-none cursor-pointer">
                            <option value="1">Public (Visible to all)</option>
                            <option value="0">Private (Invite only)</option>
                        </select>
                        <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-2" x-data="{ 
                query: @entangle('address'),
                suggestions: [],
                loading: false,
                isOpen: false,
                async fetchSuggestions() {
                    if (this.query.length < 3) {
                        this.suggestions = [];
                        return;
                    }
                    this.loading = true;
                    try {
                        const response = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(this.query)}&format=json&limit=5`);
                        this.suggestions = await response.json();
                        this.isOpen = this.suggestions.length > 0;
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.loading = false;
                    }
                },
                select(suggestion) {
                    this.query = suggestion.display_name;
                    this.isOpen = false;
                    $wire.set('address', this.query);
                }
            }">
                <label class="text-sm font-black text-slate-400 uppercase tracking-widest px-1">Location / Address</label>
                <div class="relative">
                    <input 
                        x-model="query" 
                        @input.debounce.300ms="fetchSuggestions()"
                        @click.away="isOpen = false"
                        type="text" 
                        autocomplete="off"
                        class="w-full !bg-white border-slate-100 rounded-2xl px-6 py-4 font-bold !text-slate-950 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none" 
                        placeholder="Start typing an address or city...">
                    
                    <div x-show="loading" class="absolute right-6 top-1/2 -translate-y-1/2">
                        <div class="w-4 h-4 border-2 border-blue-600/30 border-t-blue-600 rounded-full animate-spin"></div>
                    </div>

                    <!-- Suggestions Dropdown -->
                    <div x-show="isOpen" x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute z-60 w-full mt-2 !bg-white border border-slate-100 rounded-2xl shadow-xl overflow-hidden divide-y divide-slate-50">
                        <template x-for="suggestion in suggestions" :key="suggestion.place_id">
                            <button @click="select(suggestion)" type="button" class="w-full text-left px-6 py-4 hover:bg-blue-50 transition-colors group">
                                <div class="text-xs font-black !text-slate-950 group-hover:text-blue-600 transition-colors" x-text="suggestion.display_name"></div>
                                <div class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter mt-1" x-text="suggestion.type"></div>
                            </button>
                        </template>
                    </div>
                </div>
                <p class="text-[10px] font-bold text-slate-400 px-1 mt-1 italic">Laissez vide ou imprécis pour passer en mode <span class="text-blue-500">"Remote"</span>.</p>
                @error('address') <span class="text-red-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
            </div>


            <div class="pt-8">
                <button type="submit" class="w-full py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-[2rem] font-black text-xl shadow-2xl shadow-blue-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
