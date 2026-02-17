<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 font-['Inter'] selection:bg-blue-100 antialiased print:contents">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap');
        
        @media print {
            /* Hide everything except CV */
            .no-print { display: none !important; }
            
            /* Reset body and html */
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            /* Page setup - A4 with small internal margins */
            @page {
                size: A4 portrait;
                margin: 8mm;
            }
            
            /* Container adjustments */
            .cv-wrapper {
                background: white !important;
                min-height: 0 !important;
                padding: 0 !important;
            }
            
            .a4-container {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                min-height: 0 !important;
                max-height: none !important;
                overflow: visible !important;
            }
            
            /* Condensed spacing for print */
            .print-compact-header {
                padding: 8mm 10mm !important;
            }
            
            .print-compact-main {
                padding: 6mm 10mm !important;
            }
            
            .print-compact-sidebar {
                padding: 6mm 8mm !important;
            }
            
            .print-compact-section {
                margin-bottom: 4mm !important;
            }
            
            .print-compact-spacing {
                gap: 3mm !important;
            }
            
            /* Reduce font sizes slightly for print */
            .print-text-sm {
                font-size: 8px !important;
                line-height: 1.3 !important;
            }
            
            .print-text-xs {
                font-size: 7px !important;
                line-height: 1.2 !important;
            }
            
            /* Hide decorative elements */
            .bg-mesh {
                display: none !important;
            }
            
            /* Ensure single page */
            .a4-container {
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
            }
            
            .a4-container > * {
                page-break-inside: avoid !important;
            }
        }

        .a4-container {
            width: 210mm;
            min-height: 297mm;
            margin: 2rem auto;
            background: white;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            border: 1px solid #f1f5f9;
        }

        .bg-mesh {
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(at 100% 0%, rgba(37, 99, 235, 0.05) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(30, 41, 59, 0.05) 0px, transparent 50%);
            z-index: 0;
            pointer-events: none;
        }
    </style>

    <!-- Controls -->
    <div class="no-print fixed top-6 right-6 flex items-center gap-4 z-50">
        <button onclick="window.print()" class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-2xl shadow-slate-900/20 group flex items-center gap-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4"/></svg>
            Imprimer / PDF
        </button>
        <a href="{{ $this->type === 'user' ? route('users.show', $user) : route('circles.show', $circle) }}" class="p-4 bg-white border border-slate-100 text-slate-400 rounded-2xl hover:text-slate-900 transition-all shadow-xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
        </a>
    </div>

    <!-- CV Container -->
    <div class="a4-container flex flex-col relative">
        <div class="bg-mesh"></div>

        <!-- Header -->
        <header class="relative z-10 px-16 py-12 print-compact-header flex items-center justify-between border-b border-slate-50">
            <div class="flex items-center gap-8">
                <div class="relative">
                    <img src="{{ $type === 'user' ? $user->avatar : ($circle->owner->avatar ?? '') }}" class="w-32 h-32 rounded-3xl object-cover border-4 border-white shadow-xl">
                    <div class="absolute -bottom-2 -right-2 w-12 h-12 bg-slate-900 rounded-xl flex flex-col items-center justify-center text-white border-2 border-white shadow-lg">
                        <span class="text-[6px] font-black uppercase opacity-60">Score</span>
                        <span class="text-sm font-black">{{ $type === 'user' ? $user->trust_score : $circle->getAverageTrustScore() }}</span>
                    </div>
                </div>
                <div>
                    <h1 class="text-5xl font-black text-slate-900 tracking-tighter uppercase leading-tight mb-2">
                        {{ $type === 'user' ? $user->name : $circle->name }}
                    </h1>
                    <div class="flex items-center gap-6">
                        @if($type === 'user')
                            <span class="text-xs font-black text-blue-600 uppercase tracking-widest py-1.5 px-3 bg-blue-50 rounded-lg">Bâtisseur Certifié</span>
                        @else
                            <span class="text-xs font-black text-blue-600 uppercase tracking-widest py-1.5 px-3 bg-blue-50 rounded-lg">Cercle d'Experts</span>
                        @endif
                        <span class="text-xs font-bold text-slate-400 flex items-center gap-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $type === 'user' ? ($user->location ?? 'Global') : ($circle->address ?? 'Global') }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="text-right flex flex-col items-end gap-2">
                <div class="text-xs font-black text-slate-900 uppercase tracking-[0.2em] leading-none mb-4">Généré via TrustCircle</div>
            </div>

        </header>

        <!-- Content Grid -->
        <main class="relative z-10 flex-grow grid grid-cols-12">
            <!-- Sidebar -->
            <div class="col-span-4 bg-slate-50/50 border-r border-slate-100 p-12 print-compact-sidebar space-y-12 print-compact-spacing">
                <!-- Bio -->
                <section>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4">Résumé</h3>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed italic">
                        "{{ $type === 'user' ? ($user->bio ?? 'Aucune biographie disponible.') : ($circle->description ?? 'Ce cercle n\'a pas encore de description.') }}"
                    </p>
                </section>

                <!-- Expertise Scores -->
                <section>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4">Expertise Certifiée</h3>
                    <div class="space-y-6">
                        @php 
                            $skills = $type === 'user' 
                                ? $user->achievements->groupBy('skill_id') 
                                : $circle->getAllMemberAchievements()->groupBy('skill_id');
                        @endphp
                        @foreach($skills->take(4) as $skillId => $proofs)
                            <div>
                                <div class="flex justify-between items-end mb-2">
                                    <span class="text-[9px] font-black text-slate-900 uppercase tracking-widest">{{ $proofs->first()->skill->name }}</span>
                                    <span class="text-[10px] font-black text-blue-600">{{ $proofs->count() }} Preuves</span>
                                </div>
                                <div class="w-full h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-600 rounded-full" style="width: {{ min(100, $proofs->count() * 20) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <!-- Status & Metrics -->
                <section>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4">Confiance Digitale</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-4 bg-white border border-slate-100 rounded-xl">
                            <span class="text-[7px] font-black text-slate-400 uppercase block mb-1">Validations</span>
                            <span class="text-lg font-black text-green-600">+{{ $type === 'user' ? $user->validationsReceived->where('type', 'validate')->count() : $circle->getAllMemberValidations()->where('type', 'validate')->count() }}</span>
                        </div>
                        <div class="p-4 bg-white border border-slate-100 rounded-xl">
                            <span class="text-[7px] font-black text-slate-400 uppercase block mb-1">Réfutations</span>
                            <span class="text-lg font-black text-red-600">-{{ $type === 'user' ? $user->validationsReceived->where('type', 'reject')->count() : $circle->getAllMemberValidations()->where('type', 'reject')->count() }}</span>
                        </div>
                    </div>
                </section>

                <!-- Contact Information -->
                <section>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4">Contact & Liens</h3>
                    <div class="space-y-2">
                        @foreach(($type === 'user' ? $user->informations : $circle->getAllMemberInformation()) as $info)
                            <div class="flex items-center gap-2 text-[9px]">
                                <svg class="w-3 h-3 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                <div class="flex-grow">
                                    <div class="font-black text-slate-900 uppercase tracking-wider">{{ $info->title }}</div>
                                    <div class="text-slate-500 font-medium break-all">{{ parse_url($info->label, PHP_URL_HOST) ?? $info->label }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <!-- QR Code -->
                <section>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4">Vérification</h3>
                    @php
                        $profileUrl = $type === 'user' 
                            ? route('users.show', $user) 
                            : route('circles.show', $circle);
                        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode(url($profileUrl));
                    @endphp
                    <div class="bg-white p-3 rounded-2xl border-2 border-slate-200 inline-block">
                        <img src="{{ $qrCodeUrl }}" alt="QR Code" class="w-24 h-24">
                    </div>
                    <p class="text-[7px] font-black text-slate-400 uppercase mt-2">Scanner pour vérifier</p>
                </section>
            </div>

            <!-- Main Content -->
            <div class="col-span-8 p-12 print-compact-main space-y-12 print-compact-spacing">
                <!-- Achievements / Experience -->
                <section>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-6 flex items-center gap-4">
                        Timeline des Réalisations
                        <div class="h-px bg-slate-100 flex-grow"></div>
                    </h3>

                    <div class="space-y-8 print-compact-spacing">
                        @php 
                            $list = $type === 'user' 
                                ? $user->achievements->sortByDesc('realized_at')
                                : $circle->getAllMemberAchievements()->sortByDesc('realized_at');
                        @endphp

                        @foreach($list->take(5) as $ach)
                            <div class="relative pl-8 before:absolute before:left-0 before:top-1.5 before:w-2 before:h-2 before:bg-blue-600 before:rounded-full before:z-10 after:absolute after:left-[3px] after:top-4 after:w-[2px] after:h-[calc(100%+1.5rem)] after:bg-slate-100 last:after:hidden">
                                <div class="flex justify-between items-start mb-1">
                                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $ach->title }}</h4>
                                    <span class="text-[9px] font-black text-slate-400 uppercase">{{ $ach->realized_at ? \Carbon\Carbon::parse($ach->realized_at)->translatedFormat('M Y') : 'Date inconnue' }}</span>
                                </div>
                                <div class="text-[9px] font-bold text-blue-500 uppercase tracking-widest mb-2">{{ $ach->skill->name }}</div>
                                <p class="text-[10px] text-slate-500 font-medium leading-relaxed">{{ $ach->description }}</p>
                                
                                @php
                                    $positiveValidations = $ach->validations
                                        ->where('type', 'validate')
                                        ->filter(fn($v) => !empty($v->comment))
                                        ->take(3);
                                @endphp

                                @if($positiveValidations->count() > 0)
                                    <div class="mt-4 space-y-2">
                                        @foreach($positiveValidations as $val)
                                            <div class="bg-green-50/50 border border-green-100 rounded-xl p-3">
                                                <div class="flex items-center gap-2 mb-1.5">
                                                    <img src="{{ $val->user->avatar }}" class="w-4 h-4 rounded-full border border-green-200">
                                                    <span class="text-[8px] font-black text-green-700 uppercase">{{ $val->user->name }}</span>
                                                    <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                </div>
                                                <p class="text-[9px] text-slate-600 font-medium leading-relaxed italic">"{{ $val->comment }}"</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>

                @if($type === 'circle')
                    <!-- Members section for Circle CV -->
                    <section>
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-6 flex items-center gap-4">
                            L'Équipe d'Experts
                            <div class="h-px bg-slate-100 flex-grow"></div>
                        </h3>
                        <div class="grid grid-cols-2 gap-6">
                            @foreach($circle->activeMembers->take(4) as $member)
                                <div class="flex items-center gap-4 p-4 bg-slate-50 border border-slate-100 rounded-2xl">
                                    <img src="{{ $member->user->avatar }}" class="w-10 h-10 rounded-xl object-cover grayscale opacity-80">
                                    <div>
                                        <div class="text-[10px] font-black text-slate-900 uppercase leading-none">{{ $member->user->name }}</div>
                                        <div class="text-[8px] font-black text-blue-500 uppercase tracking-widest mt-1">{{ $member->user->trust_score }}% Trust</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 p-12 border-t border-slate-50 flex items-center justify-between text-[8px] font-black text-slate-300 uppercase tracking-[0.5em]">
            <span>Généré numériquement par TrustCircle</span>
            <span>Certifié Factuel par Preuve Sociale</span>
            <span>{{ now()->format('d/m/Y') }}</span>
        </footer>
    </div>
</div>
