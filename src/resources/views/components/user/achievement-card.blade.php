@props([
    'type',
    'model',
    'user',
    'canEdit' => false,
    'availableSkills' => collect(),
])

@php
    $status = $type === 'project' ? $model->status : ($model->metadata['status'] ?? 'terminée');
    $statusColors = [
        'actuelle' => 'bg-blue-50 text-blue-600 border-blue-100',
        'verrouillée' => 'bg-slate-100 text-slate-500 border-slate-200',
        'terminée' => 'bg-green-50 text-green-600 border-green-100',
        'annulée' => 'bg-red-50 text-red-600 border-red-100',
    ];
    
    $canManage = ($type === 'project' && $model->canManage(auth()->user())) || 
                 ($type === 'achievement' && ($canEdit || $model->user_id === auth()->id()));
@endphp

<div class="group relative" wire:key="achievement-card-{{ $type }}-{{ $model->id }}">
    <div @class([
        'relative bg-white/60 backdrop-blur-2xl border border-white/60 p-8 rounded-[2.5rem] hover:bg-white transition-all duration-500 group-hover:shadow-[0_40px_80px_-15px_rgba(59,130,246,0.08)] group/card overflow-hidden',
    ])>
        {{-- Big Link Overlay --}}
        @if($type === 'project')
            <a href="{{ route('projects.show', $model) }}" class="absolute inset-0 z-10" title="Voir la réalisation"></a>
        @else
            <a href="{{ route('achievements.show', $model) }}" class="absolute inset-0 z-10" title="Voir les détails"></a>
        @endif

        <div class="absolute inset-0 bg-gradient-to-br {{ $type === 'project' ? 'from-purple-600/5' : 'from-blue-600/5' }} to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity"></div>
        
        {{-- Dropdown Menu for owner/admin --}}
        @auth
            @if($canManage)
                <div x-data="{ open: false }" class="absolute top-8 right-8 z-40">
                    <button @click="open = !open" 
                            class="w-8 h-8 rounded-xl bg-white/80 shadow-xl flex items-center justify-center text-slate-400 hover:text-blue-600 transition-all opacity-0 group-hover/card:opacity-100 pointer-events-auto">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" 
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        class="absolute right-0 mt-2 w-48 bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-slate-100 py-2 z-50 pointer-events-auto">
                        <button wire:click="editItem('{{ $type }}', {{ $model->id }})" @click="open = false" class="w-full text-left px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors">
                            Modifier
                        </button>
                        <div class="border-t border-slate-50 my-1"></div>
                        <button wire:click="{{ $type === 'project' ? 'deleteProject' : 'deleteAchievement' }}({{ $model->id }})" 
                                wire:confirm="Êtes-vous sûr de vouloir supprimer cette {{ $type === 'project' ? 'réalisation' : 'expertise' }} ?"
                                @click="open = false" class="w-full text-left px-4 py-2 text-[10px] font-black uppercase tracking-widest text-red-600 hover:bg-red-50 transition-colors">
                            Supprimer
                        </button>
                    </div>
                </div>
            @endif
        @endauth
        
        <div class="relative z-20 pointer-events-none">
            <div class="flex flex-col gap-1">
                <span class="text-[9px] font-black uppercase text-slate-300 tracking-widest">
                    {{ $model->realized_at ? (\Illuminate\Support\Carbon::parse($model->realized_at)->format('M Y')) : $model->created_at->format('M Y') }}
                </span>
                @if($type === 'achievement' && $model->proche_id)
                    <span class="text-[8px] font-black text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md uppercase tracking-tighter">Proche : {{ $model->proche->name }}</span>
                @elseif($type === 'project')
                    <span class="text-[8px] font-black text-purple-600 bg-purple-50 px-2 py-0.5 rounded-md uppercase tracking-tighter">Réalisation Mission</span>
                @endif
            </div>
            
            <div class="flex items-center gap-2" @click.stop="">
                @if($type === 'achievement')
                    @auth
                        @if(auth()->id() !== $user->id)
                            @php 
                                $myValidation = $model->validations->where('user_id', auth()->id())->first();
                            @endphp
                            <div class="flex bg-slate-100 rounded-xl p-1 gap-1 pointer-events-auto relative z-30">
                                <button wire:click="initiateValidation({{ $model->id }}, 'validate')" @class([
                                    'p-1.5 rounded-lg transition-all',
                                    'bg-white text-green-600 shadow-sm' => $myValidation && $myValidation->type === 'validate',
                                    'text-slate-400 hover:text-green-600' => !$myValidation || $myValidation->type !== 'validate'
                                ]) title="Valider">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                <button wire:click="initiateValidation({{ $model->id }}, 'reject')" @class([
                                    'p-1.5 rounded-lg transition-all',
                                    'bg-white text-red-600 shadow-sm' => $myValidation && $myValidation->type === 'reject',
                                    'text-slate-400 hover:text-red-600' => !$myValidation || $myValidation->type !== 'reject'
                                ]) title="Rejeter">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endif
                    @endauth
                    
                    <x-user.validation-stats :achievement="$model" />
                @else
                    {{-- Project Participants --}}
                    <div class="flex items-center gap-3 pointer-events-auto relative z-30">
                        <div class="flex -space-x-2">
                            <div class="relative z-10 pointer-events-auto">
                                <img src="{{ $model->owner->avatar }}" class="w-8 h-8 rounded-xl border-2 border-white shadow-sm" title="Responsable: {{ $model->owner->name }}">
                            </div>
                            @foreach($model->activeMembers->take(4) as $member)
                                @php $mUser = $member->memberable; @endphp
                                @if($mUser && $mUser->id !== $model->owner_id)
                                    <div class="relative pointer-events-auto">
                                        <img src="{{ $mUser->avatar }}" class="w-8 h-8 rounded-xl border-2 border-white shadow-sm" title="{{ $mUser->name }}">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        @if($model->activeMembers->count() > 5)
                            <span class="text-[9px] font-black text-slate-400">+{{ $model->activeMembers->count() - 5 }}</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        
        <div class="flex items-center justify-between gap-3 mb-4 mt-4">
            <div class="flex items-center gap-3 flex-grow min-w-0">
                <h4 class="text-xl font-black text-slate-900 tracking-tight leading-tight italic truncate">"{{ $model->title }}"</h4>
                <div class="pointer-events-auto relative z-30">
                    <livewire:information.manager :model="$model" :key="'info-'.$type.'-'.$model->id" />
                </div>
            </div>
            
            <span @class([
                'px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border shrink-0',
                $statusColors[$status] ?? 'bg-slate-50 text-slate-400 border-slate-100'
            ])>
                {{ $status }}
            </span>
        </div>
        <p class="text-slate-500 text-sm font-medium mb-4 leading-relaxed line-clamp-2 italic">{{ $model->description }}</p>

        {{-- Secondary Skills Tags & Actions --}}
        <div class="flex items-center justify-between w-full mt-4 pointer-events-auto relative z-30 gap-4">
            <div class="flex flex-wrap items-center gap-1.5 min-w-0">
                @if($model->skills && $model->skills->count() > 0)
                    @foreach($model->skills as $s)
                        <a href="{{ route('mission.show', $s) }}" class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[8px] font-black uppercase rounded-lg border border-slate-200/50 hover:bg-blue-600 hover:text-white transition-colors truncate">
                            {{ $s->name }}
                        </a>
                    @endforeach
                @endif

                @if($canEdit || ($type === 'project' && $model->canManage(auth()->user())))
                    <div x-data="{ open: false, search: '' }" class="relative shrink-0">
                        <button @click="open = !open; if(open) $nextTick(() => $refs.skillSearch.focus())" class="w-6 h-6 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg border border-blue-100 hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Ajouter une compétence">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        </button>

                        <div x-show="open" x-cloak @click.away="open = false" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            class="absolute z-[100] left-0 bottom-full mb-2 w-48 bg-white border border-slate-200 rounded-2xl shadow-2xl p-2 animate-in fade-in zoom-in-95 duration-200">
                            <input x-ref="skillSearch" x-model="search" type="text" placeholder="Taguer une compétence..." 
                                class="w-full bg-slate-50 border-none rounded-xl p-2 text-[10px] font-bold placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500 outline-none"
                                @keydown.enter="$wire.addSkillToRealisation(search, {{ $model->id }}, '{{ $type }}'); open = false; search = ''">
                            
                            <div class="mt-2 max-h-32 overflow-y-auto custom-scrollbar pointer-events-auto">
                                @foreach($availableSkills->take(15) as $skill)
                                    <button x-show="!search || '{{ strtolower($skill->name) }}'.includes(search.toLowerCase())"
                                        @click="$wire.addSkillToRealisation('{{ $skill->name }}', {{ $model->id }}, '{{ $type }}'); open = false; search = ''"
                                        class="w-full text-left px-2 py-1.5 hover:bg-blue-50 rounded-lg text-[9px] font-black uppercase tracking-widest text-slate-600 hover:text-blue-600 transition-colors">
                                        {{ $skill->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @if($type === 'project')
                <a href="{{ route('projects.show', $model) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white hover:bg-blue-600 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-md shrink-0">
                    Voir la page
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            @endif
        </div>
    </div>
</div>
