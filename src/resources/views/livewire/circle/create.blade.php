<div class="max-w-4xl mx-auto py-12 px-4">
    <div class="bg-white/40 backdrop-blur-xl border border-white/40 rounded-[3rem] p-8 md:p-16 shadow-2xl shadow-blue-500/5">
        <div class="mb-12">
            <h1 class="text-4xl font-black text-slate-900 mb-2">Initialiser un Nouveau Cercle</h1>
            <p class="text-slate-500 font-medium italic">Le nom du cercle sera automatiquement défini par la ville sélectionnée.</p>
        </div>

        <form wire:submit.prevent="store" class="space-y-6">
            {{ $this->form }}

            <div class="pt-8">
                <button type="submit" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-bold text-lg hover:bg-blue-600 transition-all shadow-xl shadow-blue-500/10">
                    Establish Circle
                </button>
            </div>
        </form>

        <x-filament-actions::modals />
    </div>
</div>
