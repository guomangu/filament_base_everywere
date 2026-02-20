<div x-data="{ 
    async updateLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(async position => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=10&addressdetails=1`);
                    const data = await response.json();
                    const city = data.address.city || data.address.town || data.address.village || data.address.municipality || 'Localisation inconnue';
                    const country = data.address.country;
                    const locationName = `${city}, ${country}`;
                    
                    $wire.call('setLocation', lat, lng, locationName);
                } catch (error) {
                    console.error('Reverse geocoding error:', error);
                    $wire.call('setLocation', lat, lng, 'Localisation détectée');
                }
            }, (error) => {
                console.error('Geolocation error:', error);
            }, { enableHighAccuracy: true });
        }
    }
}" 
x-init="if (!$wire.lat) updateLocation()"
class="min-h-screen bg-slate-50/50">
    <!-- Hero Section / Search -->
    <div class="relative pt-20 pb-12 px-6 overflow-hidden">
        <!-- Floating Circles Background -->
        <div class="absolute inset-0 -z-10 overflow-hidden">
            <div class="absolute top-[-10%] left-[-5%] w-[40%] aspect-square bg-blue-400/10 rounded-full blur-[120px] animate-pulse"></div>
            <div class="absolute bottom-[-10%] right-[-5%] w-[40%] aspect-square bg-indigo-400/10 rounded-full blur-[120px] animate-pulse" style="animation-delay: 2s"></div>
        </div>

        <div class="max-w-5xl mx-auto text-center relative z-10">
            <h1 class="text-4xl sm:text-6xl md:text-8xl font-black text-slate-900 tracking-tighter mb-8 leading-[0.9]">
                RECHERCHE<br/>
                <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-800 bg-clip-text text-transparent">DE CONFIANCE.</span>
            </h1>
            
            <div class="max-w-3xl mx-auto mt-8 group">
                <div class="relative p-1.5 bg-white/40 backdrop-blur-3xl rounded-[3rem] border border-white/60 shadow-2xl shadow-blue-500/10 transition-all duration-500 hover:shadow-blue-500/20 hover:border-blue-200">
                    <div class="relative flex items-center">
                        <div class="absolute left-6 text-blue-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" 
                            wire:model.live.debounce.300ms="search" 
                            placeholder="Sushi, Plombier, Laravel..." 
                            class="w-full bg-transparent border-none focus:ring-0 text-xl md:text-2xl font-black placeholder:text-slate-300 py-4 md:py-6 pl-18 pr-8 text-slate-900">
                        
                        <div wire:loading wire:target="search" class="absolute right-8">
                            <div class="w-6 h-6 border-4 border-blue-600/30 border-t-blue-600 rounded-full animate-spin"></div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex justify-center gap-4 text-sm font-bold text-slate-400 uppercase tracking-widest">
                    <span>Proximité active</span>
                    <span class="text-blue-600 animate-pulse">•</span>
                    <span>Confiance vérifiée</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div class="max-w-7xl mx-auto px-6 pb-20">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">
                    {{ $search ? 'Résultats de recherche' : 'Découvertes de Proximité' }}
                </h2>
                <p class="text-slate-500 font-bold uppercase tracking-widest text-[10px] mt-2">
                    {{ $lat ? 'Cercles et Offres triés par distance' : 'Explorez les initiatives locales' }}
                </p>
            </div>
            
            @if($locationName)
                <div class="flex items-center gap-3">
                    <div class="flex flex-col items-end hidden sm:flex">
                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter">Position actuelle</span>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-[10px] text-blue-600 font-black uppercase tracking-widest">{{ $locationName }}</span>
                        </div>
                    </div>
                    <button @click="updateLocation()" class="group bg-blue-50 text-blue-600 p-2 md:px-4 md:py-2 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-2 hover:bg-blue-600 hover:text-white transition-all">
                        <span class="w-2 h-2 bg-blue-600 rounded-full animate-ping group-hover:bg-white"></span>
                        <span class="hidden md:inline">Actualiser</span>
                    </button>
                    <button wire:click="resetLocation" class="p-2 text-slate-300 hover:text-red-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-8">
            @forelse ($results as $item)
                @if($item->is_circle)
                    <div class="relative group h-full">
                        <div class="flex flex-col relative h-full bg-white border border-slate-100 rounded-[2rem] md:rounded-[2.5rem] overflow-hidden hover:shadow-[0_20px_40px_-10px_rgba(0,0,0,0.08)] transition-all duration-500 group/card">
                            <a href="{{ route('circles.show', $item) }}" class="absolute inset-0 z-10"></a>

                            <div class="p-5 md:p-6 flex flex-col h-full relative z-0">
                                <!-- Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 group-hover/card:bg-blue-600 group-hover/card:text-white transition-all duration-500 shadow-sm">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <div class="text-right">
                                        @if(isset($item->smart_distance))
                                            <div @class([
                                                'inline-block px-2 py-0.5 rounded-full text-[8px] font-black shadow-sm uppercase',
                                                'bg-blue-50 text-blue-600 shadow-blue-500/5' => !str_contains($item->smart_distance, 'Remote'),
                                                'bg-slate-900 text-white shadow-slate-900/10' => str_contains($item->smart_distance, 'Remote'),
                                            ])>{{ $item->smart_distance }}</div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Title -->
                                <div class="mb-3">
                                    <h3 class="text-lg md:text-xl font-black text-slate-900 mb-1 leading-tight group-hover/card:text-blue-600 transition-colors uppercase line-clamp-2">
                                        {{ $item->name }}
                                    </h3>
                                    <div class="flex items-center gap-1">
                                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">{{ $item->city }}</span>
                                    </div>
                                </div>

                                @if($item->matching_context)
                                    <div class="mb-3 text-[8px] font-black text-blue-600 uppercase tracking-widest bg-blue-50 px-2 py-1 rounded-lg inline-block w-fit">
                                        {{ $item->matching_context }}
                                    </div>
                                @endif

                                <p class="text-slate-500 font-medium text-[11px] leading-relaxed line-clamp-3 mb-6">
                                    {{ $item->description }}
                                </p>

                                <!-- Footer -->
                                <div class="mt-auto pt-4 border-t border-slate-50 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $item->owner->avatar }}" class="w-6 h-6 rounded-lg shadow-sm">
                                        <div class="text-[8px] font-black text-slate-900 uppercase tracking-tight">{{ explode(' ', $item->owner->name)[0] }}</div>
                                    </div>
                                    <div class="flex -space-x-2">
                                        @foreach($item->members->take(3) as $member)
                                            <img src="{{ $member->user->avatar }}" class="w-4 h-4 rounded-full border border-white ring-1 ring-slate-100">
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <x-offer-card 
                        :offer="$item" 
                        :showProjectLink="true"
                        :quoteAction="true"
                        :reviewAction="true"
                    />
                @endif
            @empty
                <div class="col-span-full py-24 text-center">
                    <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-2">Aucun résultat</h3>
                    <p class="text-slate-500 font-bold uppercase tracking-widest text-xs">Ajustez vos critères pour trouver la confiance.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Stats Section -->
    <div class="bg-slate-900 py-32 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-1/3 h-full bg-blue-600/10 blur-[100px]"></div>
        <div class="max-w-7xl mx-auto px-6 relative z-10 text-center">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-12">
                <div>
                    <div class="text-6xl font-black text-white mb-2 leading-none">100%</div>
                    <div class="text-blue-400 font-bold uppercase tracking-[0.2em] text-xs">Vérifié</div>
                </div>
                <div>
                    <div class="text-6xl font-black text-white mb-2 leading-none">{{ \App\Models\Circle::count() }}</div>
                    <div class="text-blue-400 font-bold uppercase tracking-[0.2em] text-xs">Cercles actifs</div>
                </div>
                <div>
                    <div class="text-6xl font-black text-white mb-2 leading-none">{{ \App\Models\User::count() }}</div>
                    <div class="text-blue-400 font-bold uppercase tracking-[0.2em] text-xs">Talents</div>
                </div>
                <div>
                    <div class="text-6xl font-black text-white mb-2 leading-none">{{ \App\Models\Achievement::where('is_verified', true)->count() }}</div>
                    <div class="text-blue-400 font-bold uppercase tracking-[0.2em] text-xs">Preuves</div>
                </div>
            </div>
        </div>
    @include('livewire.offers.modals')
</div>
