<div class="space-y-12">
    <!-- Circle Header -->
    <div class="bg-white/40 backdrop-blur-xl border border-white/40 rounded-[3rem] p-8 md:p-16 relative overflow-hidden">
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row items-center md:items-end justify-between gap-8">
                <div class="flex flex-col md:flex-row items-center gap-8 text-center md:text-left">
                    <div class="w-24 h-24 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-3xl flex items-center justify-center text-white shadow-xl shadow-blue-500/20">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($circle->type === 'business')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            @endif
                        </svg>
                    </div>
                    <div>
                        <div class="inline-flex items-center space-x-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-[10px] font-black uppercase tracking-widest mb-4">
                            <span>{{ $circle->type }}</span>
                            <span class="w-1 h-1 bg-blue-300 rounded-full"></span>
                            <span>{{ $circle->is_public ? 'Public' : 'Private' }}</span>
                        </div>
                        <h1 class="text-4xl md:text-5xl font-black text-slate-900 leading-none mb-2">{{ $circle->name }}</h1>
                        <p class="text-slate-500 font-medium text-lg">{{ $circle->address }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    @auth
                        @php 
                            $isMember = $circle->members->contains('user_id', auth()->id()); 
                            $isOwner = $circle->owner_id === auth()->id();
                        @endphp

                        @if($isOwner)
                            <a href="{{ route('circles.edit', $circle) }}" class="px-8 py-3 bg-white border border-slate-200 rounded-2xl font-bold text-slate-700 hover:border-blue-600 hover:text-blue-600 transition-all shadow-sm">
                                Edit Circle
                            </a>
                            <button class="px-8 py-3 bg-blue-600 text-white rounded-2xl font-bold shadow-lg shadow-blue-500/20 hover:bg-blue-700 transition-all">
                                Invite Member
                            </button>
                        @elseif($isMember)
                            <button class="px-8 py-3 bg-slate-100 text-slate-500 rounded-2xl font-bold cursor-default">
                                Already Member
                            </button>
                        @else
                            <button class="px-8 py-3 bg-blue-600 text-white rounded-2xl font-bold shadow-lg shadow-blue-500/20 hover:bg-blue-700 transition-all">
                                Request Access
                            </button>
                        @endif
                    @else
                        <a href="/admin/login" class="px-8 py-3 bg-blue-600 text-white rounded-2xl font-bold shadow-lg shadow-blue-500/20 hover:bg-blue-700 transition-all">
                            Login to Join
                        </a>
                    @endauth
                </div>
            </div>

            <div class="mt-12 max-w-3xl">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Description</h3>
                <p class="text-slate-600 leading-relaxed font-medium capitalize">{{ $circle->description }}</p>
            </div>
        </div>

        <div class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-blue-400/5 to-transparent"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Message Board -->
        <div class="lg:col-span-1 space-y-8">
            <h2 class="text-2xl font-black text-slate-900 mb-6 flex items-center">
                Circle Board
                <span class="ml-3 px-2 py-0.5 bg-blue-100 text-blue-400 text-xs rounded-lg font-black">{{ $circle->messages->count() }}</span>
            </h2>

            <!-- Message Feed -->
            <div class="space-y-4 max-h-[500px] overflow-y-auto px-2">
                @forelse($circle->messages as $msg)
                    <div class="bg-white/60 p-4 rounded-2xl border border-white/40 shadow-sm relative group">
                        <div class="flex items-center space-x-3 mb-2">
                            <img src="{{ $msg->sender->avatar_url ?? 'https://ui-avatars.com/api/?name='.$msg->sender->name }}" class="w-6 h-6 rounded-full">
                            <span class="font-bold text-slate-900 text-sm">{{ $msg->sender->name }}</span>
                            <span class="text-[10px] text-slate-400 uppercase font-black">{{ $msg->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-slate-600 text-sm leading-relaxed">{{ $msg->content }}</p>
                    </div>
                @empty
                    <p class="text-slate-400 text-sm font-bold uppercase tracking-widest text-center py-8">No messages yet.</p>
                @endforelse
            </div>

            @auth
                @if($circle->members->contains('user_id', auth()->id()) || $circle->owner_id === auth()->id())
                    <div class="mt-6">
                        <textarea wire:model="message" placeholder="Post a message to the circle..." class="w-full bg-white/40 border-white/60 rounded-2xl text-sm font-medium focus:ring-blue-500 focus:border-blue-500 placeholder:text-slate-400" rows="3"></textarea>
                        <button wire:click="sendMessage" class="mt-2 w-full py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-500/20 hover:bg-blue-700 transition-all text-sm">
                            Send Message
                        </button>
                    </div>
                @endif
            @endauth
        </div>

        <!-- Members & Achievements -->
        <div class="lg:col-span-2 space-y-8">
            <div>
                <h2 class="text-2xl font-black text-slate-900 mb-6">Proven Talents in this Circle</h2>
                <div class="grid grid-cols-1 gap-6">
                    @forelse($circle->members as $member)
                        <div class="bg-white/60 backdrop-blur-lg border border-white/40 p-6 rounded-[2rem] hover:shadow-xl transition-all group">
                            <div class="flex items-center space-x-4 mb-6">
                                <a href="{{ route('users.show', $member->user) }}">
                                    <img src="{{ $member->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.$member->user->name }}" class="w-12 h-12 rounded-xl object-cover ring-2 ring-transparent group-hover:ring-blue-500/20 transition-all">
                                </a>
                                <div class="min-w-0 flex-grow">
                                    <a href="{{ route('users.show', $member->user) }}" class="font-bold text-slate-900 truncate hover:text-blue-600 transition-colors">{{ $member->user->name }}</a>
                                    <div class="flex items-center text-[10px] font-black space-x-2 uppercase tracking-widest text-slate-400">
                                        <span class="text-blue-500">{{ $member->role }}</span>
                                        <span>•</span>
                                        <span>Vouched by {{ $member->voucher?->name ?? 'Founder' }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pl-4 border-l-2 border-slate-100">
                                @foreach($member->user->achievements as $achievement)
                                    <div class="bg-white/40 p-4 rounded-2xl border border-white/40">
                                        <span class="text-[9px] font-black uppercase text-blue-500 mb-1 block">{{ $achievement->skill->name }}</span>
                                        <h4 class="font-bold text-slate-900 text-sm mb-1">{{ $achievement->title }}</h4>
                                        <p class="text-slate-500 text-xs line-clamp-2">{{ $achievement->description }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 text-sm font-bold uppercase tracking-widest text-center py-8">Lonely circle...</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
