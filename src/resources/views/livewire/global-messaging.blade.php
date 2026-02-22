<div id="global-messaging-root" 
     x-data="{ 
        open: @entangle('isOpen'),
        showIndicator: false,
        playNotify() {
            let audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2358/2358-preview.mp3');
            audio.volume = 0.2;
            audio.play().catch(e => console.log('Audio play failed:', e));
        }
     }" 
     x-on:global-messaging-updated.window="showIndicator = true; playNotify(); setTimeout(() => showIndicator = false, 3000)"
     wire:poll.5s="refresh">

    <!-- Top-right Loading Indicator (Global Messaging Content Detect) -->
    <div x-show="showIndicator" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-[-20px]"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-[-20px]"
        class="fixed top-24 right-6 z-[1100]"
    >
        <div class="flex items-center gap-3 bg-white border border-blue-100 px-5 py-3 rounded-2xl shadow-2xl transition-all border-b-4 border-b-blue-500">
            <div class="relative">
                <div class="w-2 h-2 bg-blue-600 rounded-full animate-ping absolute -top-1 -right-1"></div>
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <span class="text-[11px] font-black text-slate-900 uppercase tracking-widest leading-none">Nouveau message !</span>
        </div>
    </div>

    @if($isOpen)
        <div class="fixed inset-0 z-[1000] bg-slate-900/60 backdrop-blur-3xl overflow-hidden animate-in fade-in duration-500 flex flex-col pt-4 md:pt-10">
            
            <!-- Header / Navigation -->
            <div class="w-full max-w-7xl mx-auto px-6 mb-4 md:mb-8 flex items-center justify-between shrink-0">
                <div>
                    <h2 class="text-3xl md:text-5xl font-black text-white tracking-tighter uppercase leading-none">Messagerie Globale</h2>
                    <div class="flex items-center gap-4 mt-2">
                        <button wire:click="selectTab('private')" wire:key="tab-nav-private"
                            @class([
                                'text-[10px] font-black uppercase tracking-widest transition-all px-4 py-2 rounded-full',
                                'bg-white text-slate-900 shadow-xl' => $activeTab === 'private',
                                'text-white/40 hover:text-white' => $activeTab !== 'private'
                            ])>
                            Privé
                        </button>
                        <button wire:click="selectTab('forums')" wire:key="tab-nav-forums"
                            @class([
                                'text-[10px] font-black uppercase tracking-widest transition-all px-4 py-2 rounded-full',
                                'bg-white text-slate-900 shadow-xl' => $activeTab === 'forums',
                                'text-white/40 hover:text-white' => $activeTab !== 'forums'
                            ])>
                            Forums
                        </button>
                    </div>
                </div>
                <button wire:click="toggle" class="w-12 h-12 md:w-16 md:h-16 rounded-full bg-white/10 hover:bg-white text-white hover:text-slate-900 flex items-center justify-center transition-all border border-white/20 shadow-2xl">
                    <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Main Content Area -->
            <div class="flex-grow max-w-7xl mx-auto w-full px-4 md:px-6 pb-6 overflow-hidden">
                <div class="bg-white/10 backdrop-blur-2xl border border-white/20 rounded-[3rem] w-full h-full flex flex-col md:flex-row overflow-hidden shadow-3xl">
                    
                    <!-- Sidebar -->
                    <div @class([
                        'w-full md:w-1/3 border-b md:border-b-0 md:border-r border-white/10 overflow-y-auto no-scrollbar shrink-0 h-full transition-all',
                        'hidden md:block' => $selectedParticipantId && $activeTab === 'private',
                        'block' => !$selectedParticipantId || $activeTab !== 'private'
                    ])>
                        <div class="p-4 md:p-8 space-y-3">
                            @if($activeTab === 'private')
                                @forelse($conversations as $c)
                                    <button wire:click="selectConversation({{ $c['id'] }})" 
                                            wire:key="conv-item-btn-{{ $c['id'] }}-{{ $c['latest_timestamp'] }}"
                                            @class([
                                                'w-full text-left p-4 md:p-6 rounded-[2rem] transition-all group relative',
                                                'bg-white shadow-2xl scale-[1.02]' => (int)$selectedParticipantId === $c['id'],
                                                'bg-white/5 hover:bg-white/10' => (int)$selectedParticipantId !== $c['id']
                                            ])>
                                        <div class="flex items-center gap-4">
                                            <div class="relative shrink-0">
                                                <img src="{{ $c['avatar'] }}" class="w-12 h-12 md:w-14 md:h-14 rounded-2xl object-cover border-2 border-white/40 group-hover:border-white transition-all">
                                                @if($c['unread_count'] > 0)
                                                    <div class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full border-2 border-white flex items-center justify-center text-[10px] font-black text-white">
                                                        {{ $c['unread_count'] }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow min-w-0">
                                                <div class="flex items-center justify-between mb-1">
                                                    <h4 @class([
                                                        'text-xs font-black uppercase tracking-tight truncate',
                                                        'text-slate-900' => (int)$selectedParticipantId === $c['id'],
                                                        'text-white' => (int)$selectedParticipantId !== $c['id'],
                                                    ])>{{ $c['name'] }}</h4>
                                                    <span class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter">{{ $c['latest_time'] }}</span>
                                                </div>
                                                <p class="text-[9px] font-medium text-slate-400 line-clamp-1 italic">
                                                    {{ $c['is_mine'] ? 'Vous: ' : '' }}{{ $c['latest_msg'] }}
                                                </p>
                                            </div>
                                        </div>
                                    </button>
                                @empty
                                    <div class="py-20 text-center">
                                        <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 border border-white/10">
                                            <svg class="w-8 h-8 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        </div>
                                        <p class="text-white/40 text-[10px] font-black uppercase tracking-widest">Aucune conversation privée</p>
                                    </div>
                                @endforelse
                            @else
                                @forelse($forums as $forum)
                                    @php 
                                        $isProject = $forum->forum_type === 'project';
                                        $latestForumMsg = $forum->messages->first();
                                    @endphp
                                    <a href="{{ $isProject ? route('projects.show', $forum) : route('circles.show', $forum) }}" 
                                       wire:key="forum-link-item-{{ $forum->forum_type }}-{{ $forum->id }}-{{ $forum->latest_activity_dt->timestamp }}"
                                       class="block w-full text-left p-4 md:p-6 rounded-[2.5rem] bg-white/5 hover:bg-white/10 transition-all group border border-white/5">
                                        <div class="flex items-center gap-4">
                                            <div class="shrink-0 w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-white/10 border-2 border-white/20 flex items-center justify-center overflow-hidden">
                                                @if($isProject)
                                                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                @else
                                                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 0012 20a10.003 10.003 0 006.235-2.197m-2.322-9.047a7.334 7.334 0 011.129 3.125m-1.282-3.125a10 10 0 11-14.703 0m14.703 0c-1.347-1.625-3.323-2.651-5.547-2.651-2.224 0-4.2 1.026-5.547 2.651"/></svg>
                                                @endif
                                            </div>
                                            <div class="flex-grow min-w-0">
                                                <div class="flex items-center justify-between mb-1">
                                                    <h4 class="text-[10px] md:text-sm font-black text-white uppercase tracking-widest truncate">{{ $forum->forum_name }}</h4>
                                                    <span class="text-[8px] font-bold text-slate-400 uppercase bg-white/5 px-2 py-0.5 rounded-md">
                                                        {{ $forum->latest_activity_dt->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span @class([
                                                        'text-[7px] font-black px-1.5 py-0.5 rounded uppercase tracking-tighter',
                                                        'bg-blue-600/20 text-blue-400' => $isProject,
                                                        'bg-orange-600/20 text-orange-400' => !$isProject,
                                                    ])>
                                                        {{ $isProject ? 'Projet' : 'Cercle' }}
                                                    </span>
                                                    <p class="text-[10px] md:text-xs font-semibold text-slate-300 italic truncate flex-grow">
                                                        {{ $latestForumMsg ? '"' . $latestForumMsg->content . '"' : 'Aucun message' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="py-20 text-center">
                                        <p class="text-white/40 text-[10px] font-black uppercase tracking-widest">Aucune activité</p>
                                    </div>
                                @endforelse
                            @endif
                        </div>
                    </div>

                    <!-- Chat Area -->
                    <div @class([
                        'flex-grow flex flex-col h-full bg-slate-900/40 relative transition-all',
                        'hidden md:flex' => !$selectedParticipantId,
                        'flex' => $selectedParticipantId
                    ])>
                        @if($selectedParticipantId && $selectedParticipant)
                            <div wire:key="chat-box-container-{{ $selectedParticipantId }}" class="flex flex-col h-full overflow-hidden">
                                <div class="p-6 md:p-10 border-b border-white/5 flex items-center justify-between shrink-0">
                                    <div class="flex items-center gap-4">
                                        <button wire:click="selectConversation(null)" class="md:hidden text-white/40 hover:text-white transition-colors p-2 -ml-2">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                                        </button>
                                        <img src="{{ $selectedParticipant->avatar }}" class="w-12 h-12 md:w-16 md:h-16 rounded-2xl border-2 border-blue-500 shadow-2xl">
                                        <div>
                                            <h3 class="text-xl md:text-3xl font-black text-white italic tracking-tight">{{ $selectedParticipant->name }}</h3>
                                            <div class="flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">En ligne</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-grow overflow-y-auto p-6 md:p-10 space-y-6 flex flex-col no-scrollbar" 
                                     id="chat-scroller"
                                     x-init="$nextTick(() => { $el.scrollTop = $el.scrollHeight }); $watch('open', value => { if(value) setTimeout(() => $el.scrollTop = $el.scrollHeight, 100) }); Livewire.on('messagingOpened', () => { setTimeout(() => $el.scrollTop = $el.scrollHeight, 100) });"
                                     x-on:messagingOpened.window="setTimeout(() => $el.scrollTop = $el.scrollHeight, 100)">
                                    @foreach($activeMessages as $m)
                                        @php $isMe = $m->sender_id === auth()->id(); @endphp
                                        <div wire:key="chat-msg-row-{{ $m->id }}" @class([
                                            'flex gap-4 items-end max-w-[90%] md:max-w-[80%]',
                                            'self-end flex-row-reverse' => $isMe,
                                            'self-start' => !$isMe
                                        ])>
                                            @if(!$isMe)
                                                <a href="{{ route('users.show', $m->sender) }}" class="shrink-0 mb-1 group/avatar">
                                                    <img src="{{ $m->sender->avatar ?? 'https://ui-avatars.com/api/?name='.$m->sender->name }}" class="w-8 h-8 rounded-full border border-white/20 group-hover/avatar:border-blue-500 transition-all shadow-lg">
                                                </a>
                                            @endif
                                            
                                            <div @class([
                                                'rounded-2xl p-4 md:p-8 italic relative group shadow-xl',
                                                'bg-blue-600 text-white rounded-br-none' => $isMe,
                                                'bg-white/10 text-white rounded-bl-none border border-white/10' => !$isMe
                                            ])>
                                                @if(!$isMe)
                                                    <div class="text-[7px] font-black uppercase text-blue-400 mb-1 tracking-widest">{{ $m->sender->name }}</div>
                                                @endif
                                                
                                                {{-- Attachment Render --}}
                                                @if($m->metadata && isset($m->metadata['attachment']))
                                                    @php $att = $m->metadata['attachment']; @endphp
                                                    <div class="mb-4">
                                                        <a href="{{ 
                                                            $att['type'] === 'user' ? route('users.show', $att['id']) : 
                                                            ($att['type'] === 'circle' ? route('circles.show', $att['id']) : 
                                                            ($att['type'] === 'project' ? route('projects.show', $att['id']) : 
                                                            ($att['type'] === 'skill' ? route('mission.show', $att['id']) : '#'))) 
                                                        }}" class="block p-3 rounded-xl bg-white/5 border border-white/10 hover:border-blue-500/50 hover:bg-white/10 transition-all group/att">
                                                            <div class="flex items-center gap-3">
                                                                <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0 group-hover/att:scale-110 transition-transform">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.823a4 4 0 015.656 0l4 4a4 4 0 01-5.656 5.656l-1.102-1.101"/></svg>
                                                                </div>
                                                                <div class="min-w-0">
                                                                    <div class="text-[7px] font-black uppercase opacity-50 tracking-widest">{{ $att['type'] }}</div>
                                                                    <div class="text-[10px] font-bold truncate">{{ $att['name'] }}</div>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                @endif

                                                {{-- File Attachment Render --}}
                                                @if($m->metadata && isset($m->metadata['file']))
                                                    @php $file = $m->metadata['file']; @endphp
                                                    <div class="mt-4 mb-4">
                                                        @if(Str::startsWith($file['type'] ?? '', 'image/'))
                                                            <div class="relative group/file overflow-hidden rounded-2xl border border-white/10 shadow-lg">
                                                                <img src="{{ Storage::url($file['path']) }}" alt="{{ $file['name'] }}" class="w-full max-h-[300px] object-cover hover:scale-105 transition-transform duration-500 cursor-pointer" onclick="window.open('{{ Storage::url($file['path']) }}', '_blank')">
                                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/file:opacity-100 transition-opacity flex items-center justify-center gap-4">
                                                                    <a href="{{ Storage::url($file['path']) }}" download="{{ $file['name'] }}" class="p-3 bg-white/20 backdrop-blur-md rounded-full text-white hover:bg-white/40 transition-all">
                                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <a href="{{ Storage::url($file['path']) }}" download="{{ $file['name'] }}" class="flex items-center gap-4 p-4 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 transition-all group/file">
                                                                <div class="w-10 h-10 bg-red-500/10 rounded-xl flex items-center justify-center text-red-500">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                                </div>
                                                                <div class="min-w-0 flex-1">
                                                                    <div class="text-[7px] font-black text-slate-500 uppercase tracking-widest mb-1">{{ strtoupper(explode('/', $file['type'])[1] ?? 'FILE') }}</div>
                                                                    <div class="text-[10px] font-black text-white truncate">{{ $file['name'] }}</div>
                                                                </div>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif

                                                <p class="text-xs md:text-base font-semibold leading-relaxed">{{ $m->content }}</p>
                                                <span class="text-[7px] absolute -bottom-5 right-2 uppercase font-black text-white/40">{{ $m->created_at->format('H:i') }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="p-6 md:p-10 shrink-0 bg-white/5 border-t border-white/5">
                                    <div class="relative" 
                                        x-data="{ 
                                            showMentions: false, 
                                            mentionSearch: '',
                                            items: @js($contextItems),
                                            get filteredItems() {
                                                if (!this.mentionSearch) return this.items;
                                                return this.items.filter(i => i.name.toLowerCase().includes(this.mentionSearch.toLowerCase()));
                                            },
                                            handleInput(e) {
                                                const val = e.target.value;
                                                const cursor = e.target.selectionStart;
                                                const lastAt = val.lastIndexOf('@', cursor - 1);
                                                if (lastAt !== -1 && (lastAt === 0 || val[lastAt - 1] === ' ')) {
                                                    this.showMentions = true;
                                                    this.mentionSearch = val.substring(lastAt + 1, cursor);
                                                } else {
                                                    this.showMentions = false;
                                                }
                                            },
                                            selectItem(item) {
                                                @this.selectAttachment(item.type, item.id, item.name);
                                                const val = $refs.globalMsgInput.value;
                                                const cursor = $refs.globalMsgInput.selectionStart;
                                                const lastAt = val.lastIndexOf('@', cursor - 1);
                                                const newVal = val.substring(0, lastAt) + val.substring(cursor);
                                                @this.set('messageText', newVal);
                                                this.showMentions = false;
                                                $refs.globalMsgInput.focus();
                                            }
                                        }"
                                    >
                                        <!-- Attachment Badge -->
                                        @if($attachment)
                                            <div class="absolute left-10 -top-12 flex items-center gap-2 px-3 py-1.5 bg-blue-600 rounded-xl shadow-lg border border-white/20 animate-in slide-in-from-bottom-2 duration-300 z-10">
                                                <span class="text-[8px] font-black text-white uppercase tracking-widest">PJ : {{ $attachment['name'] }}</span>
                                                <button type="button" wire:click="removeAttachment" class="p-0.5 hover:bg-white/20 rounded-md transition-colors text-white">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                        @endif

                                        <!-- Mentions Dropdown -->
                                        <div x-show="showMentions && filteredItems.length > 0" 
                                            x-transition
                                            class="absolute bottom-full left-10 mb-4 w-64 bg-slate-800 border border-white/10 rounded-2xl shadow-3xl overflow-hidden z-[100]"
                                            style="display: none;">
                                            <div class="p-2 border-b border-white/5 bg-slate-900/50">
                                                <span class="text-[8px] font-black text-slate-500 uppercase tracking-widest px-2">Autour de vous</span>
                                            </div>
                                            <div class="max-h-48 overflow-y-auto no-scrollbar">
                                                <template x-for="item in filteredItems" :key="item.type + item.id">
                                                    <button @click="selectItem(item)" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-blue-600 transition-colors text-left group">
                                                        <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 bg-white/10 text-white/40 group-hover:bg-white/20 group-hover:text-white">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.823a4 4 0 015.656 0l4 4a4 4 0 01-5.656 5.656l-1.102-1.101"/></svg>
                                                        </div>
                                                        <div class="min-w-0">
                                                            <div class="text-[7px] font-black uppercase tracking-widest opacity-50 text-white" x-text="item.type"></div>
                                                            <div class="text-[10px] font-bold text-white truncate" x-text="item.name"></div>
                                                        </div>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>

                                        <form wire:submit.prevent="sendMessage" class="flex items-center gap-4 bg-white/10 border-2 border-white/10 focus-within:border-blue-500 rounded-full px-6 py-2 transition-all">
                                            <!-- Mobile Plus Button for Mentions -->
                                        <button type="button" 
                                            @click="showMentions = !showMentions"
                                            class="shrink-0 w-10 h-10 md:w-12 md:h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white/40 hover:text-blue-400 hover:border-blue-500/50 hover:bg-blue-500/10 transition-all active:scale-95"
                                            title="Attacher un objet">
                                            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                        </button>

                                        <!-- File Upload Button -->
                                        <div class="relative flex items-center">
                                            <input type="file" wire:model="upload" class="hidden" x-ref="globalFileInput">
                                            <button type="button" 
                                                @click="$refs.globalFileInput.click()"
                                                class="shrink-0 w-10 h-10 md:w-12 md:h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white/40 hover:text-blue-400 hover:border-blue-500/50 hover:bg-blue-500/10 transition-all active:scale-95"
                                                title="Envoyer une image ou un PDF">
                                                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                            </button>
                                            
                                            <div wire:loading wire:target="upload" class="absolute -top-1 -right-1">
                                                <div class="w-3 h-3 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                                            </div>
                                        </div>

                                        <div class="flex-grow relative">
                                            <!-- Attachment Badges Container -->
                                            <div class="absolute left-0 -top-12 flex items-center gap-3 z-20">
                                                <!-- Mentions Attachment Badge -->
                                                @if($attachment)
                                                    <div class="flex items-center gap-2 px-3 py-2 bg-blue-600 rounded-xl shadow-lg border border-white/20 animate-in slide-in-from-bottom-2 duration-300">
                                                        <span class="text-[8px] font-black text-white uppercase tracking-widest">OBJET : {{ $attachment['name'] }}</span>
                                                        <button type="button" wire:click="removeAttachment" class="p-0.5 hover:bg-white/20 rounded-md transition-colors text-white">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </div>
                                                @endif

                                                <!-- File Preview Badge -->
                                                @if($upload)
                                                    <div class="flex items-center gap-2 px-3 py-2 bg-blue-600 rounded-xl shadow-lg border border-white/20 animate-in slide-in-from-bottom-2 duration-300">
                                                        <span class="text-[8px] font-black text-white uppercase tracking-widest truncate max-w-[150px]">FICHIER : {{ $upload->getClientOriginalName() }}</span>
                                                        <button type="button" wire:click="$set('upload', null)" class="p-0.5 hover:bg-white/20 rounded-md transition-colors text-white">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>

                                            <input wire:model="messageText" 
                                                x-ref="globalMsgInput"
                                                @input="handleInput"
                                                @keydown.escape="showMentions = false"
                                                type="text" 
                                                placeholder="Répondre..." 
                                                class="w-full bg-transparent border-none focus:ring-0 text-sm md:text-lg font-bold italic text-white placeholder:text-white/20 py-4 md:py-6">
                                        </div>
                                            
                                            <button type="submit" class="shrink-0 px-8 py-3 md:py-4 bg-blue-600 hover:bg-white hover:text-blue-600 text-white rounded-full font-black text-[10px] uppercase transition-all shadow-xl active:scale-95">
                                                Envoyer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div wire:key="chat-placeholder-view" class="flex-grow flex flex-col items-center justify-center p-10 text-center">
                                <div class="w-32 h-32 md:w-48 md:h-48 bg-white/5 rounded-full flex items-center justify-center mb-10 border border-white/10 shadow-3xl opacity-20">
                                    <svg class="w-16 h-16 md:w-24 md:h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                </div>
                                <h3 class="text-3xl md:text-6xl font-black text-white/10 italic uppercase leading-none tracking-tighter">Nexus de<br>Confiance</h3>
                                <p class="mt-6 text-white/20 text-[10px] font-black uppercase tracking-[0.3em]">Sélectionnez une discussion</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
