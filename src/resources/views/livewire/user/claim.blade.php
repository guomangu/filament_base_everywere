<div class="min-h-screen bg-slate-900 flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-white rounded-[4rem] shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-500">
        <div class="bg-blue-600 p-12 text-white text-center">
            <h1 class="text-3xl font-black uppercase tracking-tight mb-2">Récupérer votre compte</h1>
            <p class="text-blue-100 text-[10px] font-black uppercase tracking-widest">Bienvenue dans votre nouvel espace de confiance</p>
        </div>

        <div class="p-12">
            @if(!$verified)
                <div class="space-y-8">
                    <p class="text-slate-500 text-sm font-medium text-center leading-relaxed">
                        Veuillez saisir le code de sécurité à 6 chiffres transmis par votre parrain pour activer votre compte.
                    </p>
                    
                    <div>
                        <input wire:model="code" type="text" placeholder="CODE-123" maxlength="6"
                            class="w-full bg-slate-50 border-white focus:ring-blue-500 rounded-3xl p-6 text-3xl font-black tracking-[0.5em] text-center uppercase placeholder:tracking-normal placeholder:text-slate-200">
                        @error('code') <span class="text-red-500 text-[10px] font-black uppercase mt-2 block text-center">{{ $message }}</span> @enderror
                    </div>

                    <button wire:click="verifyCode" class="w-full py-6 bg-slate-900 text-white rounded-[2rem] font-black text-sm tracking-[0.2em] uppercase hover:bg-blue-600 transition-all shadow-xl">
                        Vérifier le code
                    </button>
                </div>
            @else
                <form wire:submit="claimAccount" class="space-y-6">
                    <p class="text-slate-500 text-sm font-medium text-center leading-relaxed mb-6">
                        Dernière étape ! Définissez vos accès personnels pour devenir autonome.
                    </p>

                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Votre Email</label>
                        <input wire:model="email" type="email" class="w-full bg-slate-50 border-white focus:ring-blue-500 rounded-2xl p-4 text-sm font-bold">
                        @error('email') <span class="text-red-500 text-[10px] font-black uppercase mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Nouveau Mot de passe</label>
                        <input wire:model="password" type="password" class="w-full bg-slate-50 border-white focus:ring-blue-500 rounded-2xl p-4 text-sm font-bold">
                        @error('password') <span class="text-red-500 text-[10px] font-black uppercase mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Confirmer</label>
                        <input wire:model="password_confirmation" type="password" class="w-full bg-slate-50 border-white focus:ring-blue-500 rounded-2xl p-4 text-sm font-bold">
                    </div>

                    <button type="submit" class="w-full py-6 bg-blue-600 text-white rounded-[2rem] font-black text-sm tracking-[0.2em] uppercase hover:bg-blue-700 transition-all shadow-xl shadow-blue-500/20">
                        Activer mon compte
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
