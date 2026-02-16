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

            <div class="space-y-2">
                <label class="text-sm font-black text-slate-400 uppercase tracking-widest px-1">Avatar URL</label>
                <input wire:model="avatar_url" type="text" class="w-full bg-white/60 border-slate-100 rounded-2xl px-6 py-4 font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none" placeholder="https://...">
                @error('avatar_url') <span class="text-red-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
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
