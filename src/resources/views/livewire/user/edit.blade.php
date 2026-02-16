<div class="max-w-3xl mx-auto py-12 px-4">
    <div class="bg-white/40 backdrop-blur-xl border border-white/40 rounded-[3rem] p-8 md:p-16 shadow-2xl shadow-blue-500/5">
        <div class="mb-12">
            <h1 class="text-4xl font-black text-slate-900 mb-2">Edit Your Identity</h1>
            <p class="text-slate-500 font-medium">Update your profile to reflect your current skills and status.</p>
        </div>

        <form wire:submit.prevent="update" class="space-y-8">
            <div class="space-y-2">
                <label class="text-sm font-black text-slate-400 uppercase tracking-widest px-1">Display Name</label>
                <input wire:model="name" type="text" class="w-full bg-white/60 border-slate-100 rounded-2xl px-6 py-4 font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none" placeholder="Your Name">
                @error('name') <span class="text-red-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-4">
                <label class="text-sm font-black text-slate-400 uppercase tracking-widest px-1">Photo de Profil</label>
                <div class="flex items-center gap-8">
                    <div class="relative group">
                        @if ($avatar)
                            <img src="{{ $avatar->temporaryUrl() }}" class="w-32 h-32 rounded-[2.5rem] object-cover ring-4 ring-blue-500/20 shadow-2xl transition-transform group-hover:scale-105 duration-500">
                        @else
                            <img src="{{ auth()->user()->avatar }}" class="w-32 h-32 rounded-[2.5rem] object-cover ring-4 ring-slate-100 shadow-xl transition-transform group-hover:scale-105 duration-500">
                        @endif
                        <div class="absolute inset-0 bg-blue-600/20 rounded-[2.5rem] opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                    </div>
                    <div class="flex-grow">
                        <label class="block">
                            <span class="sr-only">Choisir une photo</span>
                            <input type="file" wire:model="avatar" class="block w-full text-sm text-slate-500
                                file:mr-4 file:py-3 file:px-6
                                file:rounded-full file:border-0
                                file:text-xs file:font-black file:uppercase file:tracking-widest
                                file:bg-blue-600 file:text-white
                                hover:file:bg-blue-700
                                transition-all">
                        </label>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-3">PNG, JPG jusqu'à 1MB</p>
                    </div>
                </div>
                @error('avatar') <span class="text-red-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-sm font-black text-slate-400 uppercase tracking-widest px-1">Bio / Personal Mission</label>
                <textarea wire:model="bio" rows="5" class="w-full bg-white/60 border-slate-100 rounded-2xl px-6 py-4 font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none resize-none" placeholder="Tell the network about yourself..."></textarea>
                @error('bio') <span class="text-red-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
            </div>

            <div class="pt-8">
                <button type="submit" class="w-full py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-[2rem] font-black text-xl shadow-2xl shadow-blue-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                    Update Profile
                </button>
            </div>
        </form>
    </div>
</div>
