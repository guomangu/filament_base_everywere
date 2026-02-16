<div x-data="{ 
    init() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                $wire.set('lat', position.coords.latitude);
                $wire.set('lng', position.coords.longitude);
            });
        }
    }
}" class="min-h-screen bg-slate-50/50">
    <!-- Hero Section / Search -->
    <div class="relative pt-32 pb-20 px-6 overflow-hidden">
        <!-- Floating Circles Background -->
        <div class="absolute inset-0 -z-10 overflow-hidden">
            <div class="absolute top-[-10%] left-[-5%] w-[40%] aspect-square bg-blue-400/10 rounded-full blur-[120px] animate-pulse"></div>
            <div class="absolute bottom-[-10%] right-[-5%] w-[40%] aspect-square bg-indigo-400/10 rounded-full blur-[120px] animate-pulse" style="animation-delay: 2s"></div>
        </div>

        <div class="max-w-5xl mx-auto text-center relative z-10">
            <h1 class="text-7xl md:text-8xl font-black text-slate-900 tracking-tighter mb-8 leading-[0.9]">
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
                            placeholder="Que cherchez-vous ? (Sushi, Plombier, Expert Laravel...)" 
                            class="w-full bg-transparent border-none focus:ring-0 text-2xl font-black placeholder:text-slate-300 py-8 pl-18 pr-8 text-slate-900">
                        
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
            
            @if($lat)
                <div class="bg-blue-50 text-blue-600 px-4 py-2 rounded-full text-xs font-black uppercase tracking-widest flex items-center gap-2">
                    <span class="w-2 h-2 bg-blue-600 rounded-full animate-ping"></span>
                    Localisation active
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
            @forelse ($circles as $circle)
                <div class="relative group">
                    <!-- Circle Entity Aesthetic -->
                    <div class="absolute -top-6 -left-6 w-24 h-24 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-full opacity-10 group-hover:scale-150 transition-transform duration-700 blur-2xl"></div>
                    
                    <a href="{{ route('circles.show', $circle) }}" class="block relative h-full bg-white/60 backdrop-blur-xl border border-white/60 rounded-[3.5rem] p-10 hover:bg-white hover:shadow-[0_40px_80px_-15px_rgba(59,130,246,0.15)] transition-all duration-500">
                        <!-- Circle Header -->
                        <div class="flex items-start justify-between mb-8">
                            <div class="w-20 h-20 bg-slate-100 rounded-[2.5rem] flex items-center justify-center text-slate-600 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 group-hover:rotate-12 group-hover:scale-110 shadow-inner">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($circle->type === 'business')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    @endif
                                </svg>
                            </div>
                            <div class="text-right">
                                <span class="text-[0.6rem] font-black uppercase tracking-[0.2em] text-slate-300 group-hover:text-blue-200 block mb-1">
                                    #{{ str_pad($circle->id, 3, '0', STR_PAD_LEFT) }}
                                </span>
                                @if(isset($circle->distance))
                                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full whitespace-nowrap">
                                        {{ round($circle->distance, 1) }} km
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Content -->
                        <h3 class="text-3xl font-black text-slate-900 mb-2 leading-[1.1] group-hover:text-blue-600 transition-colors">
                            {{ $circle->name }}
                        </h3>
                        
                        @if($circle->matching_context)
                            <div class="inline-flex items-center gap-2 bg-green-50 text-green-700 px-4 py-1.5 rounded-2xl text-xs font-black uppercase tracking-widest mb-4 border border-green-100">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                {{ $circle->matching_context }}
                            </div>
                        @endif

                        <p class="text-slate-500 font-medium text-base leading-relaxed mb-6 line-clamp-3">
                            {{ $circle->description }}
                        </p>

                        <!-- Stack / Skills -->
                        <div class="flex flex-wrap gap-2 mb-8">
                            @foreach($circle->achievements->pluck('skill.name')->unique()->take(3) as $skillName)
                                <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-[0.65rem] font-black uppercase tracking-wider">
                                    {{ $skillName }}
                                </span>
                            @endforeach
                            @if($circle->achievements->pluck('skill.name')->unique()->count() > 3)
                                <span class="text-slate-300 text-[0.65rem] font-black uppercase self-center ml-1">
                                    +{{ $circle->achievements->pluck('skill.name')->unique()->count() - 3 }}
                                </span>
                            @endif
                        </div>

                        <!-- Footer -->
                        <div class="flex items-center justify-between">
                            <div class="flex -space-x-3">
                                <a href="{{ route('users.show', $circle->owner) }}" class="block hover:scale-110 transition-transform">
                                    <img src="{{ $circle->owner->avatar }}" class="w-12 h-12 rounded-full border-4 border-white shadow-lg ring-1 ring-slate-100 group-hover/card:ring-blue-500 transition-all">
                                </a>
                                <div class="w-12 h-12 rounded-full border-4 border-white bg-slate-100 flex items-center justify-center text-xs font-black text-slate-400 ring-1 ring-slate-100">
                                    +{{ $circle->members->count() }}
                                </div>
                            </div>
                            
                            <div class="flex flex-col items-end">
                                <span class="text-[0.65rem] font-black text-slate-300 uppercase tracking-widest leading-none mb-1">Localisation</span>
                                <span class="text-xs font-bold text-slate-600">{{ $circle->address }}</span>
                            </div>
                        </div>
                    </a>
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
