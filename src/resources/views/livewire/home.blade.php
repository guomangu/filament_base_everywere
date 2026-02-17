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
    <div class="relative pt-32 pb-20 px-6 overflow-hidden">
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
            
            <div class="max-w-3xl mx-auto mt-12 group">
                <div class="relative p-2 bg-white/40 backdrop-blur-3xl rounded-[3rem] border border-white/60 shadow-2xl shadow-blue-500/10 transition-all duration-500 hover:shadow-blue-500/20 hover:border-blue-200">
                    <div class="relative flex items-center">
                        <div class="absolute left-6 text-blue-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" 
                            wire:model.live.debounce.300ms="search" 
                            placeholder="Sushi, Plombier, Laravel..." 
                            class="w-full bg-transparent border-none focus:ring-0 text-xl md:text-2xl font-black placeholder:text-slate-300 py-6 md:py-8 pl-18 pr-8 text-slate-900">
                        
                        <div wire:loading wire:target="search" class="absolute right-8">
                            <div class="w-6 h-6 border-4 border-blue-600/30 border-t-blue-600 rounded-full animate-spin"></div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-center gap-4 text-sm font-bold text-slate-400 uppercase tracking-widest">
                    <span>Proximité active</span>
                    <span class="text-blue-600 animate-pulse">•</span>
                    <span>Confiance vérifiée</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div class="max-w-7xl mx-auto px-6 pb-32">
        <div class="flex items-center justify-between mb-12">
            <div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">
                    {{ $search ? 'Résultats de recherche' : 'Mur de Cercles Proches' }}
                </h2>
                <p class="text-slate-500 font-bold uppercase tracking-widest text-xs mt-2">
                    {{ $lat ? 'Trié par localisation réelle' : 'Découvrez les talents locaux' }}
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
                    <button @click="updateLocation()" class="group bg-blue-50 text-blue-600 p-2 md:px-4 md:py-2 rounded-full text-xs font-black uppercase tracking-widest flex items-center gap-2 hover:bg-blue-600 hover:text-white transition-all">
                        <span class="w-2 h-2 bg-blue-600 rounded-full animate-ping group-hover:bg-white"></span>
                        <span class="hidden md:inline">Actualiser</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </button>
                    <button wire:click="resetLocation" class="p-2 text-slate-300 hover:text-red-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @else
                <button @click="updateLocation()" class="bg-blue-600 text-white px-6 py-2 rounded-full text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Autour de moi
                </button>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
            @forelse ($circles as $circle)
                <div class="relative group">
                    <!-- Circle Entity Aesthetic -->
                    <div class="absolute -top-6 -left-6 w-24 h-24 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-full opacity-10 group-hover:scale-150 transition-transform duration-700 blur-2xl"></div>
                    
                    <div class="flex flex-col relative h-full bg-white border border-slate-100 rounded-[2.5rem] md:rounded-[3rem] overflow-hidden hover:shadow-[0_30px_60px_-15px_rgba(0,0,0,0.1)] transition-all duration-500 group/card">
                        <!-- Full Card Link Overlay -->
                        <a href="{{ route('circles.show', $circle) }}" class="absolute inset-0 z-0"></a>

                        <div class="flex-grow p-8 md:p-10 relative z-10 pointer-events-none">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-8">
                                <div class="w-16 h-16 md:w-20 md:h-20 bg-slate-50 rounded-[1.8rem] md:rounded-[2.2rem] flex items-center justify-center text-slate-400 group-hover/card:bg-blue-600 group-hover/card:text-white transition-all duration-500 group-hover/card:rotate-6 shadow-sm">
                                    <svg class="w-8 h-8 md:w-10 md:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($circle->type === 'business')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        @endif
                                    </svg>
                                </div>
                                <div class="text-right">
                                    <div class="text-[0.6rem] font-black uppercase tracking-[0.2em] text-slate-300 mb-1">Cercle #{{ str_pad($circle->id, 3, '0', STR_PAD_LEFT) }}</div>
                                    @if(isset($circle->smart_distance))
                                        <div @class([
                                            'inline-block px-3 py-1 rounded-full text-[10px] font-black shadow-sm',
                                            'bg-blue-50 text-blue-600 shadow-blue-500/5' => !str_contains($circle->smart_distance, 'Remote'),
                                            'bg-slate-900 text-white shadow-slate-900/10' => str_contains($circle->smart_distance, 'Remote'),
                                        ])>{{ $circle->smart_distance }}</div>
                                    @endif
                                </div>
                            </div>

                            <!-- Title & Location -->
                            <div class="mb-6">
                                <h3 class="text-2xl md:text-3xl font-black text-slate-900 mb-1 leading-tight group-hover/card:text-blue-600 transition-colors">
                                    {{ $circle->name }}
                                </h3>
                                <div class="flex items-center gap-1.5 text-slate-400 overflow-hidden">
                                    <svg class="w-1.5 h-1.5 text-blue-500 animate-pulse flex-shrink-0" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                    <span class="text-[10px] md:text-xs font-bold truncate italic">{{ $circle->address }}</span>
                                </div>
                            </div>

                            @if($circle->matching_context)
                                <div class="relative z-10 p-4 bg-slate-50 border border-slate-100 rounded-3xl mb-6 group/highlight hover:border-blue-200 transition-colors">
                                    <div class="flex items-center gap-3">
                                        @if($circle->matched_object && isset($circle->matched_object['image']))
                                            <img src="{{ $circle->matched_object['image'] }}" class="w-8 h-8 rounded-full border-2 border-white shadow-sm">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            </div>
                                        @endif
                                        
                                        <div class="flex-grow min-w-0">
                                            <div class="text-[8px] font-black text-slate-400 uppercase tracking-tighter leading-none mb-1">{{ $circle->matching_context }}</div>
                                            <div class="text-xs font-black text-slate-900 truncate tracking-tight">
                                                @if($circle->matched_object)
                                                    {{ $circle->matched_object['name'] }}
                                                    @if(isset($circle->matched_object['detail']))
                                                        <span class="text-blue-600 font-bold ml-1">• {{ $circle->matched_object['detail'] }}</span>
                                                    @endif
                                                @else
                                                    Trouvé par correspondance
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <p class="text-slate-500 font-medium text-sm md:text-base leading-relaxed line-clamp-3 mb-8">
                                {{ $circle->description }}
                            </p>

                            <!-- Skills -->
                            <div class="flex flex-wrap gap-2">
                                @foreach($circle->achievements->pluck('skill.name')->unique()->take(3) as $skillName)
                                    <span class="bg-slate-100 text-slate-500 px-3 py-1 rounded-full text-[0.65rem] font-black uppercase tracking-wider">
                                        {{ $skillName }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- User Info Bar -->
                        <div class="px-8 md:px-10 py-6 border-t border-slate-50 flex items-center justify-between mt-auto relative z-10">
                            <div class="flex -space-x-3">
                                <a href="{{ route('users.show', $circle->owner) }}" class="relative z-10 block hover:scale-110 transition-transform">
                                    <img src="{{ $circle->owner->avatar }}" class="w-10 h-10 md:w-12 md:h-12 rounded-full border-4 border-white shadow-md">
                                </a>
                                <div class="w-10 h-10 md:w-12 md:h-12 rounded-full border-4 border-white bg-slate-50 flex items-center justify-center text-[10px] font-black text-slate-300">
                                    +{{ $circle->members->count() }}
                                </div>
                            </div>
                            <div class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em] group-hover/card:text-blue-600 transition-colors pointer-events-none">
                                Détails
                            </div>
                        </div>
                    </div>
                </div>
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
    </div>
</div>
