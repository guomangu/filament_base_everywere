<div class="inline-block" x-data="{ 
    open: @entangle('showModal'),
    viewOpen: @entangle('showViewModal') 
}">
    <!-- Display Existing Information -->
    <div class="flex flex-wrap gap-2 mb-4">
        @foreach(($model->informations ?? []) as $info)
            <div class="group relative bg-white/60 backdrop-blur-md border border-slate-100 rounded-2xl px-4 py-2 flex items-center gap-3 shadow-sm hover:shadow-md transition-all cursor-pointer"
                wire:click="viewInfo({{ $info->id }})">
                @if($info->images && count($info->images) > 0)
                    <img src="{{ $info->images[0] }}" class="w-6 h-6 rounded-full object-cover">
                @else
                    <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                @endif
                
                <div class="flex flex-col">
                    <span class="text-[10px] font-black text-slate-900 uppercase tracking-tight">{{ $info->title }}</span>
                    @if($info->label)
                        <span class="text-[8px] font-bold text-slate-400 truncate max-w-[100px]">{{ $info->label }}</span>
                    @endif
                </div>

                @if(auth()->id() === $info->author_id)
                    <button wire:click.stop="delete({{ $info->id }})" class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-lg">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                @endif
            </div>
        @endforeach

        <!-- Add Button (Plus Icon) -->
        @if(auth()->id() === ($model->owner_id ?? ($model->user_id ?? $model->id)))
            <button @click="open = true" class="w-10 h-10 rounded-full border-2 border-dashed border-slate-200 flex items-center justify-center text-slate-400 hover:border-blue-500 hover:text-blue-500 hover:bg-blue-50 transition-all group">
                <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </button>
        @endif
    </div>

    <template x-teleport="body">
        <!-- Create Modal -->
        <div x-show="open" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-[9999] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-sm"
            @keydown.escape.window="open = false"
            style="display: none;">
        
        <div class="bg-white w-full max-w-lg rounded-[2.5rem] p-10 shadow-2xl overflow-hidden relative" @click.away="open = false">
            <h3 class="text-3xl font-black text-slate-900 mb-8 tracking-tighter uppercase">Enrichir l'objet</h3>
            
            <div class="space-y-6">
                <!-- (Existing Create Form Inputs) -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Titre de l'info</label>
                    <input wire:model="title" type="text" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none" placeholder="Ex: Matériel, Staff, Spécialité...">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Détails (Label)</label>
                    <input wire:model="label" type="text" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none" placeholder="Ex: Haute qualité, 5 personnes...">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Images (Fichiers)</label>
                    
                    <div class="relative group" 
                        x-data="{ isUploading: false, progress: 0 }"
                        x-on:livewire-upload-start="isUploading = true"
                        x-on:livewire-upload-finish="isUploading = false"
                        x-on:livewire-upload-error="isUploading = false"
                        x-on:livewire-upload-progress="progress = $event.detail.progress">
                        
                        <label class="flex flex-col items-center justify-center w-full h-32 bg-slate-50 border-2 border-dashed border-slate-200 rounded-[2rem] cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition-all">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-3 text-slate-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Cliquez pour téléverser</p>
                            </div>
                            <input wire:model="newImage" type="file" class="hidden" accept="image/*" />
                        </label>

                        <!-- Upload Progress Bar -->
                        <div x-show="isUploading" class="absolute inset-0 bg-white/80 backdrop-blur-sm rounded-[2rem] flex flex-col items-center justify-center p-6 transition-all">
                            <div class="w-full bg-slate-100 rounded-full h-2 mb-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" :style="`width: ${progress}%` shadow-sm"></div>
                            </div>
                            <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest animate-pulse" x-text="`Chargement... ${progress}%` "></span>
                        </div>
                    </div>
                    
                    @if(count($images) > 0)
                        <div class="flex flex-wrap gap-2 mt-4">
                            @foreach($images as $index => $img)
                                <div class="relative group">
                                    <img src="{{ $img }}" class="w-12 h-12 rounded-xl object-cover border-2 border-slate-100">
                                    <button @click="$wire.removeImage({{ $index }})" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-12 flex gap-4">
                <button @click="open = false" class="flex-grow py-4 bg-slate-100 text-slate-500 rounded-2xl font-bold text-sm hover:bg-slate-200 transition-all">Annuler</button>
                <button wire:click="save" class="flex-grow py-4 bg-slate-900 text-white rounded-2xl font-bold text-sm hover:bg-blue-600 transition-all shadow-xl shadow-blue-500/10">Valider l'info</button>
            </div>
        </div>
    </template>

    <template x-teleport="body">
        <!-- View Detail Modal -->
        <div x-show="viewOpen" 
            x-transition:enter="transition ease-out duration-400"
            x-transition:enter-start="opacity-0 translate-y-8"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="fixed inset-0 z-[10000] flex items-center justify-center p-6 bg-slate-900/80 backdrop-blur-md"
            @keydown.escape.window="viewOpen = false"
            style="display: none;">
        
        @if($selectedInfo)
            <div class="bg-white w-full max-w-2xl rounded-[3rem] shadow-2xl relative overflow-hidden" @click.away="viewOpen = false">
                <!-- Header with Title -->
                <div class="p-12 pb-6">
                    <div class="flex items-center justify-between mb-8">
                        <span class="px-4 py-1.5 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-[0.2em] border border-blue-100">Détail Information</span>
                        <button @click="viewOpen = false" class="text-slate-300 hover:text-slate-900 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <h2 class="text-5xl font-black text-slate-900 tracking-tighter uppercase leading-none mb-6">{{ $selectedInfo->title }}</h2>
                    <p class="text-xl text-slate-500 font-medium leading-relaxed italic">"{{ $selectedInfo->label ?? 'Aucune précision supplémentaire.' }}"</p>
                </div>

                <!-- Gallery -->
                @if($selectedInfo->images && count($selectedInfo->images) > 0)
                    <div class="px-12 pb-12">
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($selectedInfo->images as $img)
                                <div class="relative aspect-square rounded-[2rem] overflow-hidden border-4 border-slate-50 shadow-inner group">
                                    <img src="{{ $img }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="bg-slate-50 p-8 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Partagé par {{ $selectedInfo->author->name }}</span>
                    </div>
                    <button @click="viewOpen = false" class="px-8 py-3 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-blue-500/10">Fermer</button>
                </div>
            </div>
        @endif
    </div>
    </template>
</div>
