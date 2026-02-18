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
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">
                    {{ $search ? 'Résultats de recherche' : 'Découvertes de Proximité' }}
                </h2>
                <p class="text-slate-500 font-bold uppercase tracking-widest text-xs mt-2">
                    {{ $lat ? 'Cercles et Projets triés par distance' : 'Explorez les initiatives locales' }}
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

        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-8">
            @forelse ($results as $item)
                @if($item->is_circle)
                    <div class="relative group">
                        <!-- Circle Entity Aesthetic -->
                        <div class="absolute -top-6 -left-6 w-24 h-24 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-full opacity-10 group-hover:scale-150 transition-transform duration-700 blur-2xl"></div>
                        
                        <div class="flex flex-col relative h-full bg-white border border-slate-100 rounded-[2.5rem] md:rounded-[3rem] overflow-hidden hover:shadow-[0_30px_60px_-15px_rgba(0,0,0,0.1)] transition-all duration-500 group/card">
                            <!-- Full Card Link Overlay -->
                            <a href="{{ route('circles.show', $item) }}" class="absolute inset-0 z-0"></a>

                            <div class="flex-grow p-6 md:p-8 relative z-10 pointer-events-none">
                                <!-- Header -->
                                <div class="flex items-start justify-between mb-6">
                                    <div class="w-16 h-16 md:w-20 md:h-20 bg-slate-50 rounded-[1.8rem] md:rounded-[2.2rem] flex items-center justify-center text-slate-400 group-hover/card:bg-blue-600 group-hover/card:text-white transition-all duration-500 group-hover/card:rotate-6 shadow-sm">
                                        <svg class="w-8 h-8 md:w-10 md:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-[0.6rem] font-black uppercase tracking-[0.2em] text-slate-300 mb-1">Cercle #{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</div>
                                        @if(isset($item->smart_distance))
                                            <div @class([
                                                'inline-block px-3 py-1 rounded-full text-[10px] font-black shadow-sm',
                                                'bg-blue-50 text-blue-600 shadow-blue-500/5' => !str_contains($item->smart_distance, 'Remote'),
                                                'bg-slate-900 text-white shadow-slate-900/10' => str_contains($item->smart_distance, 'Remote'),
                                            ])>{{ $item->smart_distance }}</div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Title & Location -->
                                <div class="mb-4">
                                    <h3 class="text-2xl md:text-3xl font-black text-slate-900 mb-1 leading-tight group-hover/card:text-blue-600 transition-colors uppercase">
                                        {{ $item->name }}
                                    </h3>
                                    <div class="flex flex-wrap items-center gap-2 text-slate-400 relative z-10 pointer-events-auto">
                                        @if($item->city)
                                            <a href="{{ url('/?search=' . urlencode($item->city)) }}" class="text-[8px] md:text-[9px] font-black text-slate-900 bg-slate-100 px-1.5 py-0.5 rounded hover:bg-slate-900 hover:text-white transition-all uppercase tracking-tight">
                                                {{ $item->city }}
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                @if($item->matching_context)
                                    <div class="relative z-10 p-4 bg-slate-50 border border-slate-100 rounded-3xl mb-4 text-[10px] font-black text-blue-600 uppercase tracking-widest">
                                        {{ $item->matching_context }}
                                    </div>
                                @endif

                                <p class="text-slate-500 font-medium text-sm md:text-base leading-relaxed line-clamp-3 mb-6">
                                    {{ $item->description }}
                                </p>

                                <!-- Skills -->
                                <div class="flex flex-wrap gap-2 text-slate-900">
                                    @foreach($item->achievements->pluck('skill.name')->unique()->take(3) as $skillName)
                                        <a href="{{ url('/?search=' . urlencode($skillName)) }}" class="bg-white border border-slate-100 px-3 py-1 rounded-lg text-[0.65rem] font-black uppercase tracking-wider hover:bg-blue-600 hover:text-white transition-all pointer-events-auto">
                                            {{ $skillName }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="px-8 md:px-10 py-6 border-t border-slate-50 flex items-center justify-between mt-auto relative z-10">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $item->owner->avatar }}" class="w-10 h-10 md:w-12 md:h-12 rounded-full border-4 border-white shadow-md">
                                    <div>
                                        <div class="text-[10px] font-black text-slate-900 uppercase tracking-tight mb-1">{{ $item->owner->name }}</div>
                                        <div class="flex gap-1">
                                            @foreach($item->members->take(5) as $member)
                                                <div class="w-4 h-4 rounded-full border border-white bg-slate-100 overflow-hidden">
                                                    <img src="{{ $member->user->avatar }}" class="w-full h-full object-cover">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">Détails</div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="relative group">
                        <!-- Project Card Logic -->
                        <div class="absolute -top-6 -right-6 w-24 h-24 bg-gradient-to-br from-indigo-600 to-blue-600 rounded-full opacity-10 group-hover:scale-150 transition-transform duration-700 blur-2xl"></div>
                        
                        <div class="flex flex-col relative h-full bg-white border border-blue-50 rounded-[2.5rem] md:rounded-[3rem] overflow-hidden hover:shadow-[0_40px_80px_-20px_rgba(37,99,235,0.15)] transition-all duration-700 group/card border-l-4 border-l-blue-600">
                            <!-- Full Card Link Overlay -->
                            <a href="{{ route('projects.show', $item) }}" class="absolute inset-0 z-0"></a>

                            <div class="flex-grow p-6 md:p-8 relative z-10 pointer-events-none">
                                <!-- Header -->
                                <div class="flex items-start justify-between mb-6">
                                    <div class="w-16 h-16 md:w-20 md:h-20 bg-blue-50 rounded-[1.8rem] md:rounded-[2.2rem] flex items-center justify-center text-blue-600 group-hover/card:bg-blue-600 group-hover/card:text-white transition-all duration-500 group-hover/card:rotate-[-6deg] shadow-sm">
                                        <svg class="w-8 h-8 md:w-10 md:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 0 00-2-2h-4a2 0 00-2 2v2m4 6h.01M5 20h14a2 0 002-2V8a2 0 00-2-2H5a2 0 00-2 2v10a2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-[0.6rem] font-black uppercase tracking-[0.2em] text-blue-400 mb-1">PROJET</div>
                                        @if(isset($item->smart_distance))
                                            <div class="inline-block px-3 py-1 bg-blue-600 text-white rounded-full text-[10px] font-black shadow-lg shadow-blue-500/20">{{ $item->smart_distance }}</div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Title & Location -->
                                <div class="mb-4">
                                    <h3 class="text-2xl md:text-3xl font-black text-slate-900 mb-1 leading-tight group-hover/card:text-blue-600 transition-colors uppercase">
                                        {{ $item->title }}
                                    </h3>
                                    <div class="flex flex-wrap items-center gap-2 relative z-10 pointer-events-auto">
                                        @if($item->city)
                                            <a href="{{ url('/?search=' . urlencode($item->city)) }}" class="text-[8px] md:text-[9px] font-black text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded hover:bg-blue-600 hover:text-white transition-all uppercase tracking-tight">
                                                {{ $item->city }}
                                            </a>
                                        @endif
                                        <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest pl-2">#{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                </div>

                                @if($item->matching_context)
                                    <div class="relative z-10 p-4 bg-blue-50/50 border border-blue-100 rounded-3xl mb-4 text-[10px] font-black text-blue-600 uppercase tracking-widest">
                                        {{ $item->matching_context }}
                                    </div>
                                @endif

                                <p class="text-slate-500 font-medium text-sm md:text-base leading-relaxed line-clamp-3 mb-6">
                                    {{ $item->description }}
                                </p>

                                <!-- Skills/Offers -->
                                <div class="flex flex-wrap gap-2 pointer-events-auto">
                                    @foreach($item->skills->take(3) as $skill)
                                        <a href="{{ url('/?search=' . urlencode($skill->name)) }}" class="bg-blue-50 text-blue-700 px-3 py-1 rounded-lg text-[0.65rem] font-black uppercase tracking-wider hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                            {{ $skill->name }}
                                        </a>
                                    @endforeach
                                    @if($item->offers->count() > 0)
                                        <span class="bg-green-50 text-green-700 px-3 py-1 rounded-lg text-[0.65rem] font-black uppercase tracking-wider border border-green-100 shadow-sm shadow-green-500/5">
                                            {{ $item->offers->count() }} Offres
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="px-8 md:px-10 py-6 border-t border-blue-50 bg-blue-50/20 flex items-center justify-between mt-auto relative z-10">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $item->owner->avatar }}" class="w-10 h-10 md:w-12 md:h-12 rounded-full border-4 border-white shadow-md">
                                    <div class="text-[10px] font-black text-slate-900 uppercase tracking-tight">
                                        Par {{ $item->owner->name }}
                                    </div>
                                </div>
                                <div class="text-[10px] font-black text-blue-500 uppercase tracking-[0.2em] animate-pulse">Découvrir</div>
                            </div>
                        </div>
                    </div>
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
    </div>
</div>
