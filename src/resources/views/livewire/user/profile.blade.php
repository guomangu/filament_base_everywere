<div class="space-y-12">
    <!-- User Header Portfolio -->
    <div class="bg-white/40 backdrop-blur-xl border border-white/40 rounded-[3rem] p-8 md:p-16 relative overflow-hidden">
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <div class="relative group">
                    <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.$user->name }}" class="w-40 h-40 md:w-56 md:h-56 rounded-[3rem] object-cover shadow-2xl shadow-blue-500/10 group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute -bottom-4 -right-4 w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-lg border border-slate-50">
                        <span class="text-2xl font-black text-blue-600">{{ $user->trust_score }}</span>
                    </div>
                </div>

                <div class="flex-grow text-center md:text-left">
                    <h1 class="text-5xl md:text-6xl font-black text-slate-900 leading-none mb-4">{{ $user->name }}</h1>
                    <p class="text-slate-500 font-medium text-xl max-w-2xl">{{ $user->bio ?? 'No bio yet. This user is building their trust network.' }}</p>
                    
                    <div class="flex flex-wrap justify-center md:justify-start gap-4 mt-8">
                        @auth
                            @if(auth()->id() !== $user->id)
                                <button class="btn-premium">
                                    Vouch for {{$user->name}}
                                </button>
                                <button class="px-8 py-3 bg-white border border-slate-200 rounded-2xl font-bold text-slate-700 hover:border-blue-600 hover:text-blue-600 transition-all shadow-sm">
                                    Send Message
                                </button>
                            @else
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('profile.edit') }}" class="px-8 py-3 bg-white border border-slate-200 rounded-2xl font-bold text-slate-700 hover:border-blue-600 hover:text-blue-600 transition-all shadow-sm">
                                        Edit Profile
                                    </a>
                                    <a href="{{ route('circles.create') }}" class="px-8 py-3 bg-blue-600 text-white rounded-2xl font-bold shadow-lg shadow-blue-500/20 hover:bg-blue-700 transition-all">
                                        Start a Circle
                                    </a>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-blue-400/5 to-transparent"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Sidebar: Trust & Circles -->
        <div class="lg:col-span-1 space-y-12">
            <!-- Stats -->
            <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden">
                <h3 class="text-xs font-black text-blue-400 uppercase tracking-[0.3em] mb-8">Trust Metrics</h3>
                <div class="space-y-6 relative z-10">
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-sm font-bold text-slate-400">Trust Score</span>
                            <span class="text-2xl font-black">{{ $user->trust_score }}/100</span>
                        </div>
                        <div class="w-full h-2 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $user->trust_score }}%"></div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center py-4 border-t border-white/5">
                        <span class="text-sm font-bold text-slate-400">Total Vouchs</span>
                        <span class="text-xl font-black">{{ $totalVouchs }}</span>
                    </div>
                </div>
                <div class="absolute bottom-0 right-0 w-32 h-32 bg-blue-600/20 blur-3xl"></div>
            </div>
 
            <!-- Active Circles -->
            <div>
                <h3 class="text-2xl font-black text-slate-900 mb-6">Active Circles</h3>
                <div class="space-y-4">
                    @forelse($user->joinedCircles as $member)
                        <a href="{{ route('circles.show', $member->circle) }}" class="flex items-center space-x-4 p-4 hover:bg-white rounded-2xl border border-transparent hover:border-slate-100 transition-all group">
                            <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div class="min-w-0 flex-grow">
                                <h4 class="font-bold text-slate-900 truncate group-hover:text-blue-600 transition-colors">{{ $member->circle->name }}</h4>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $member->circle->type }}</span>
                            </div>
                        </a>
                    @empty
                        <p class="text-slate-400 text-sm font-bold uppercase tracking-widest text-center py-8">No circles joined.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Portfolio: Achievements -->
        <div class="lg:col-span-2 space-y-12">
            <div>
                <div class="flex items-center justify-between mb-8 px-4">
                    <h2 class="text-3xl font-black text-slate-900">Portfolio of Proofs</h2>
                    @auth
                        @if(auth()->id() === $user->id)
                            <a href="{{ route('achievements.create') }}" class="flex items-center space-x-2 px-6 py-2 bg-white border border-slate-200 rounded-xl font-bold text-slate-600 hover:text-blue-600 hover:border-blue-600 transition-all shadow-sm text-sm">
                                <span>Share a New Success</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </a>
                        @endif
                    @endauth
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 px-4">
                    @forelse($user->achievements as $achievement)
                        <div class="card-glass p-8 group overflow-hidden">
                            <div class="flex items-start justify-between mb-6">
                                <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest rounded-lg">#{{ $achievement->skill->name }}</span>
                                @if($achievement->is_verified)
                                    <div class="flex items-center space-x-1 text-green-500 font-black text-[10px] uppercase tracking-widest">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        <span>Verified</span>
                                    </div>
                                @endif
                            </div>
                            
                            <h4 class="text-2xl font-black text-slate-900 mb-4 group-hover:text-blue-600 transition-colors">{{ $achievement->title }}</h4>
                            <p class="text-slate-500 font-medium mb-8 leading-relaxed">{{ $achievement->description }}</p>

                            <div class="pt-6 border-t border-slate-50 flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <span class="text-xs font-bold text-slate-500 truncate max-w-[120px]">{{ $achievement->circle?->name ?? 'External' }}</span>
                                </div>
                                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">{{ $achievement->created_at->format('M Y') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-20 text-center card-glass border-dashed">
                            <p class="text-slate-400 font-bold uppercase tracking-widest text-sm">Proof your value. Add your first achievement.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
