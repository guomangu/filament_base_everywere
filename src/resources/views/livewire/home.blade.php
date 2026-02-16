<div>
    <!-- Hero Section -->
    <div class="relative py-20 px-6 overflow-hidden">
        <div class="max-w-7xl mx-auto relative z-10 text-center">
            <h1 class="text-6xl md:text-7xl font-black text-slate-900 leading-tight mb-6">
                Connect via <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Trust.</span>
            </h1>
            <p class="text-xl text-slate-500 max-w-2xl mx-auto mb-10 leading-relaxed font-medium">
                The world's first cooptation-based professional network. Find skills verified by the circles you trust.
            </p>
            <div class="max-w-4xl mx-auto bg-white/60 backdrop-blur-2xl p-4 rounded-[2.5rem] shadow-2xl shadow-blue-500/10 border border-white/60 flex flex-col md:flex-row gap-4">
                <div class="flex-grow relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Que cherchez-vous ? (ex: Sushi, Plombier)" class="w-full bg-transparent border-none focus:ring-0 text-lg font-bold placeholder:text-slate-400 py-4 pl-12">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div class="w-full md:w-64 relative border-t md:border-t-0 md:border-l border-slate-200">
                    <input type="text" wire:model.live.debounce.300ms="location" placeholder="Où ?" class="w-full bg-transparent border-none focus:ring-0 text-lg font-bold placeholder:text-slate-400 py-4 pl-12">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
        </div>
        
        <!-- Background Elements -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full -z-0">
            <div class="absolute top-20 left-10 w-72 h-72 bg-blue-400/10 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-indigo-400/10 rounded-full blur-3xl animate-float" style="animation-delay: -3s"></div>
        </div>
    </div>

    @auth
        <!-- Trust Network Stats (Mocked for now) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-20">
            <div class="bg-white/40 backdrop-blur-xl border border-white/40 rounded-3xl p-8 shadow-sm">
                <div class="flex items-center space-x-4 mb-2">
                    <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <span class="text-3xl font-black text-slate-900">0</span>
                </div>
                <p class="text-sm font-bold text-slate-500 uppercase tracking-widest leading-none">Mutual Connections</p>
            </div>
            <div class="bg-white/40 backdrop-blur-xl border border-white/40 rounded-3xl p-8 shadow-sm">
                <div class="flex items-center space-x-4 mb-2">
                    <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <span class="text-3xl font-black text-slate-900">{{ auth()->user()->trust_score }}</span>
                </div>
                <p class="text-sm font-bold text-slate-500 uppercase tracking-widest leading-none">Your Trust Score</p>
            </div>
            <div class="bg-white/40 backdrop-blur-xl border border-white/40 rounded-3xl p-8 shadow-sm">
                <div class="flex items-center space-x-4 mb-2">
                    <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z"/></svg>
                    </div>
                    <span class="text-3xl font-black text-slate-900">{{ auth()->user()->achievements->count() }}</span>
                </div>
                <p class="text-sm font-bold text-slate-500 uppercase tracking-widest leading-none">Verified Proofs</p>
            </div>
        </div>
    @endauth

    <!-- Circles Grid -->
    <div class="mb-24">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
            <div>
                <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-2">Active Circles</h2>
                <p class="text-slate-500 font-medium italic">Communities building and verifying real talent.</p>
            </div>
            <a href="/admin/circles" class="inline-flex items-center px-6 py-3 bg-white border border-slate-200 rounded-2xl font-bold text-slate-700 hover:border-blue-600 hover:text-blue-600 transition-all shadow-sm">
                Explore All
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($circles as $circle)
                <a href="{{ route('circles.show', $circle) }}" class="group bg-white/60 backdrop-blur-lg border border-white/40 rounded-[2.5rem] p-8 hover:bg-white hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8">
                        <span class="text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-300 group-hover:text-blue-200 transition-colors">
                            {{ $circle->id }} / CIRCLE
                        </span>
                    </div>

                    <div class="w-14 h-14 bg-slate-100 rounded-2xl mb-6 flex items-center justify-center text-slate-600 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 rotate-3 group-hover:rotate-12 shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($circle->type === 'business')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            @endif
                        </svg>
                    </div>

                    <h3 class="text-2xl font-black text-slate-900 mb-3 group-hover:text-blue-600 transition-colors leading-tight">
                        {{ $circle->name }}
                    </h3>
                    <p class="text-slate-500 font-medium text-sm leading-relaxed mb-8 line-clamp-2">
                        {{ $circle->description }}
                    </p>

                    <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                        <div class="flex -space-x-3">
                            <a href="{{ route('users.show', $circle->owner) }}" class="relative inline-block">
                                <img src="{{ $circle->owner->avatar_url ?? 'https://ui-avatars.com/api/?name='.$circle->owner->name }}" class="w-10 h-10 rounded-full border-4 border-white shadow-sm ring-1 ring-slate-100 hover:ring-blue-500 transition-all">
                            </a>
                            <div class="w-10 h-10 rounded-full border-4 border-white bg-slate-100 flex items-center justify-center text-[10px] font-black text-slate-400 ring-1 ring-slate-100">
                                +{{ $circle->members->count() }}
                            </div>
                        </div>
                        <span class="text-xs font-black text-slate-300 uppercase tracking-widest">{{ $circle->type }}</span>
                    </div>
                </a>
            @empty
                <!-- No results -->
            @endforelse
        </div>
    </div>

    <!-- Recent Proofs -->
    <div class="relative">
        <div class="bg-slate-900 rounded-[3rem] p-8 md:p-16 overflow-hidden">
            <div class="relative z-10">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end mb-12 gap-6">
                    <div>
                        <span class="text-blue-400 font-black uppercase tracking-[0.3em] text-xs mb-4 block">Proven Talents</span>
                        <h2 class="text-4xl font-extrabold text-white tracking-tight">Recent Verified Proofs</h2>
                    </div>
                    <a href="/admin/achievements" class="px-8 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-md rounded-2xl font-bold text-white transition-all text-sm border border-white/10">
                        Browse All Achievements
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse ($achievements as $achievement)
                        <div class="bg-white/5 border border-white/10 rounded-3xl p-8 hover:bg-white/10 transition-all duration-500 group">
                            <div class="flex items-start space-x-6">
                                <a href="{{ route('users.show', $achievement->user) }}">
                                    <img src="{{ $achievement->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.$achievement->user->name }}" class="w-16 h-16 rounded-2xl object-cover ring-4 ring-white/5 group-hover:scale-110 transition-transform">
                                </a>
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <a href="{{ route('users.show', $achievement->user) }}" class="font-bold text-white truncate text-lg hover:text-blue-400 transition-colors">{{ $achievement->user->name }}</a>
                                        <div class="flex items-center space-x-1 text-green-400">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.64.304 1.24.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            <span class="text-xs font-black uppercase tracking-widest">{{ $achievement->skill->name }}</span>
                                        </div>
                                    </div>
                                    <h4 class="text-xl font-black text-white mb-2 leading-tight">{{ $achievement->title }}</h4>
                                    <p class="text-slate-400 font-medium text-sm line-clamp-2">{{ $achievement->description }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-500 font-bold uppercase tracking-widest text-sm py-12 text-center w-full col-span-2">Waiting for proofs of excellence...</p>
                    @endforelse
                </div>
            </div>

            <!-- Dark bg decorations -->
            <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-blue-600/10 to-transparent"></div>
        </div>
    </div>
</div>
