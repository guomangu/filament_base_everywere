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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($results as $item)
                @php 
                    $url = route('circles.show', $item);
                    $avatar = $item->owner->avatar ?? '';
                    $name = $item->name;
                    $score = $item->average_trust_score ?? $item->trust_score ?? 0;
                    $activeMembers = $item->members ? $item->members->take(6) : collect();
                @endphp
                <div class="bg-white/80 backdrop-blur-3xl border border-slate-100 rounded-[3rem] p-8 hover:shadow-2xl hover:shadow-blue-500/10 transition-all group overflow-hidden relative flex flex-col h-full ring-1 ring-white/50">
                    <div class="flex items-center gap-6 mb-8">
                        <a href="{{ $url }}" class="relative shrink-0 group/av hover:scale-105 transition-transform duration-300">
                            <div class="w-20 h-20 bg-gradient-to-tr from-blue-600 to-indigo-700 rounded-3xl flex items-center justify-center text-white shadow-xl group-hover/av:scale-110 transition-transform duration-500 ring-4 ring-white">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div class="absolute -bottom-2 -right-2 px-2.5 py-1 bg-slate-900 rounded-xl border-4 border-white shadow-lg">
                                <span class="text-[10px] font-black text-white leading-none">{{ $score }}%</span>
                            </div>
                        </a>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-3 mb-1.5">
                                <span @class([
                                    'text-[9px] font-black uppercase px-3 py-1 rounded-full tracking-widest shadow-sm',
                                    'bg-blue-600 text-white shadow-blue-500/20' => ($item->proximity_level ?? 5) <= 2,
                                    'bg-slate-100 text-slate-500' => ($item->proximity_level ?? 5) > 2
                                ])>
                                    {{ match($item->proximity_type ?? 'global') {
                                        'direct' => 'Direct',
                                        'city' => 'Ville',
                                        'region' => 'Région',
                                        'global' => 'Pays',
                                        'earth' => 'Monde',
                                        default => 'Réseau'
                                    } }}
                                </span>
                                <span class="text-[9px] font-black text-slate-300 uppercase tracking-[0.3em]">Cercle de Confiance</span>
                            </div>
                            <a href="{{ $url }}" class="text-2xl font-black text-slate-900 uppercase truncate block hover:text-blue-600 transition-colors tracking-tighter">
                                {{ $name }}
                            </a>
                            <div class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">{{ $item->city ?? 'Remote' }}</div>
                        </div>
                    </div>

                    <!-- Membres du cercle avec leurs expertises -->
                    <div class="flex flex-col gap-4 mb-8 flex-1">
                        @foreach($activeMembers as $member)
                            <div class="flex flex-col gap-3 p-4 bg-slate-50/50 border border-slate-100 rounded-[2rem] group/m hover:bg-white hover:border-blue-500 transition-all shadow-sm hover:shadow-md">
                                <a href="{{ route('users.show', $member->user) }}" class="flex items-center gap-4">
                                    <div class="relative">
                                        <img src="{{ $member->user->avatar }}" class="w-10 h-10 rounded-2xl object-cover group-hover/m:scale-110 transition-transform shadow-sm ring-2 ring-white">
                                        @if($member->user->trust_score > 80)
                                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-white shadow-sm"></div>
                                        @endif
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="text-[11px] font-black text-slate-800 uppercase tracking-tight group-hover/m:text-blue-600 truncate">{{ $member->user->name }}</span>
                                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none">Expertise Certifiée</span>
                                    </div>
                                </a>
                                
                                {{-- Skills for this specific user --}}
                                <div class="flex flex-wrap gap-1.5 pl-1">
                                    @foreach($member->user->achievements->take(4) as $achievement)
                                        <a href="{{ route('mission.show', $achievement->skill) }}" class="px-2.5 py-1 bg-white border border-slate-200 text-slate-500 rounded-lg text-[8px] font-black uppercase tracking-tight shadow-sm hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all">
                                            {{ $achievement->skill->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        
                        @if($item->members && $item->members->count() > 6)
                            <div class="text-center py-2 bg-slate-50/30 rounded-2xl border border-dashed border-slate-100">
                                <span class="text-[9px] font-black text-slate-300 uppercase tracking-[0.2em]">+{{ $item->members->count() - 6 }} autres membres du réseau</span>
                            </div>
                        @endif
                    </div>

                    @if(!empty($item->trustPath))
                        <div class="pt-6 border-t border-slate-100 relative z-10">
                            <div class="mb-3 text-[8px] font-black text-slate-400 uppercase tracking-[0.3em]">Lien de confiance détecté :</div>
                            <x-user-trust-chain :path="$item->trustPath" class="scale-100 origin-left" />
                        </div>
                    @endif

                    <!-- Description overlay/hint on hover -->
                    <div class="mt-6">
                        <p class="text-[11px] text-slate-400 font-medium leading-relaxed line-clamp-2 italic">
                            "{{ $item->description }}"
                        </p>
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
    @include('livewire.offers.modals')
</div>
