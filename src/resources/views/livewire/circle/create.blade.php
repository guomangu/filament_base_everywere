<div class="max-w-4xl mx-auto py-12 px-4">
    <div class="bg-white/40 backdrop-blur-xl border border-white/40 rounded-[3rem] p-8 md:p-16 shadow-2xl shadow-blue-500/5">
        <div class="mb-12">
            <h1 class="text-4xl font-black text-slate-900 mb-2">Initialize a New Circle</h1>
            <p class="text-slate-500 font-medium">Create a space for community, work, or local collaboration.</p>
        </div>

        <form wire:submit.prevent="store">
            {{ $this->form }}

            <div class="pt-12">
                <button type="submit" class="w-full py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-[2rem] font-black text-xl shadow-2xl shadow-blue-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                    Establish Circle
                </button>
            </div>
        </form>

        <x-filament-actions::modals />
    </div>
</div>
