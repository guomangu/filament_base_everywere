<div x-data="{ 
    query: @entangle($getStatePath()),
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
        @js('$wire.set($getStatePath(), this.query)')
    }
}" class="w-full">
    <div class="relative">
        <input 
            x-model="query" 
            @input.debounce.300ms="fetchSuggestions()"
            @click.away="isOpen = false"
            type="text" 
            class="fi-input block w-full border-none bg-white/60 py-3 px-4 text-base text-gray-900 rounded-2xl ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 transition-all outline-none" 
            placeholder="Start typing an address or city...">
        
        <div x-show="loading" class="absolute right-4 top-1/2 -translate-y-1/2">
            <div class="w-4 h-4 border-2 border-blue-600/30 border-t-blue-600 rounded-full animate-spin"></div>
        </div>

        <!-- Suggestions Dropdown -->
        <div x-show="isOpen" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="absolute z-[100] w-full mt-2 bg-white border border-gray-200 rounded-2xl shadow-2xl overflow-hidden shadow-blue-500/10">
            <template x-for="suggestion in suggestions" :key="suggestion.place_id">
                <button @click="select(suggestion)" type="button" class="w-full text-left px-5 py-3 hover:bg-blue-50 transition-colors border-b border-gray-50 last:border-none group">
                    <div class="text-xs font-bold text-gray-900 group-hover:text-blue-600 transition-colors" x-text="suggestion.display_name"></div>
                    <div class="text-[8px] font-black text-gray-400 uppercase tracking-tighter mt-1" x-text="suggestion.type"></div>
                </button>
            </template>
        </div>
    </div>
    <p class="text-[10px] font-bold text-gray-400 px-1 mt-2 italic">Laissez vide ou imprécis pour passer en mode <span class="text-blue-500 uppercase">"Remote"</span>.</p>
</div>
