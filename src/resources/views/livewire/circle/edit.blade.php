<div class="max-w-3xl mx-auto py-12 px-4">
    <div class="bg-white/40 backdrop-blur-xl border border-white/40 rounded-[3rem] p-8 md:p-16 shadow-2xl shadow-blue-500/5">
        <div class="mb-12">
            <h1 class="text-4xl font-black text-slate-900 mb-2">Refine Your Circle</h1>
            <p class="text-slate-500 font-medium">Update the details of {{ $circle->name }} to better reflect your mission.</p>
        </div>

        <form wire:submit.prevent="update" class="space-y-8">
            <div class="space-y-2">
                <label class="text-sm font-black text-slate-400 uppercase tracking-widest px-1">Circle Name</label>
                <input wire:model="name" type="text" class="w-full bg-white/60 border-slate-100 rounded-2xl px-6 py-4 font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none" placeholder="Circle Name">
                @error('name') <span class="text-red-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-black text-slate-400 uppercase tracking-widest px-1">Type</label>
                    <div class="relative">
                        <select wire:model="type" class="w-full bg-white/60 border-slate-100 rounded-2xl px-6 py-4 font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none appearance-none cursor-pointer">
                            <option value="business">Business / Coworking</option>
                            <option value="place">Place / Location</option>
                            <option value="event">Event / Meetup</option>
                            <option value="project">Project / Squad</option>
                        </select>
                        <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-black text-slate-400 uppercase tracking-widest px-1">Visibility</label>
                    <div class="relative">
                        <select wire:model="is_public" class="w-full bg-white/60 border-slate-100 rounded-2xl px-6 py-4 font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none appearance-none cursor-pointer">
                            <option value="1">Public (Visible to all)</option>
                            <option value="0">Private (Invite only)</option>
                        </select>
                        <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-black text-slate-400 uppercase tracking-widest px-1">Location / Address</label>
                <input wire:model="address" type="text" class="w-full bg-white/60 border-slate-100 rounded-2xl px-6 py-4 font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none" placeholder="Address">
                @error('address') <span class="text-red-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-sm font-black text-slate-400 uppercase tracking-widest px-1">Description & Mission</label>
                <textarea wire:model="description" rows="5" class="w-full bg-white/60 border-slate-100 rounded-2xl px-6 py-4 font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none resize-none" placeholder="Description"></textarea>
                @error('description') <span class="text-red-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
            </div>

            <div class="pt-8">
                <button type="submit" class="w-full py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-[2rem] font-black text-xl shadow-2xl shadow-blue-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
