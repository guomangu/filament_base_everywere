<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'God Stack' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Tailwind -->
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            body { font-family: 'Outfit', sans-serif; }
            .bg-gradient-mesh {
                background-color: #f8fafc;
                background-image: 
                    radial-gradient(at 0% 0%, hsla(217,100%,94%,1) 0, transparent 50%), 
                    radial-gradient(at 50% 0%, hsla(225,100%,94%,1) 0, transparent 50%), 
                    radial-gradient(at 100% 0%, hsla(217,100%,94%,1) 0, transparent 50%);
            }
            /* Over-aggressive Visibility Patch */
            .fi-input-wrp, .fi-input, textarea, input, select {
                background-color: white !important;
                color: #0f172a !important;
                border-color: #cbd5e1 !important;
            }
            .fi-input-wrp:focus-within {
                ring-color: #3b82f6 !important;
            }
            .fi-fo-field-wrp-label, .fi-fo-field-wrp-label * {
                color: #1e293b !important;
                font-weight: 800 !important;
                opacity: 1 !important;
            }
            .fi-btn {
                background-color: #0f172a !important;
            }
        </style>
        
        @livewireStyles
        @filamentStyles
    </head>
    <body class="bg-gradient-mesh text-slate-900 antialiased min-h-screen">
        <nav x-data="{ open: false }" class="bg-white/40 backdrop-blur-2xl sticky top-0 z-50 border-b border-white/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-24">
                    <!-- Left: Logo -->
                    <div class="flex items-center">
                        <a href="/" class="flex items-center space-x-3 group">
                            <div class="w-12 h-12 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-2xl shadow-blue-500/20 group-hover:rotate-12 transition-all duration-500">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <span class="text-2xl font-black tracking-tighter text-slate-900 group-hover:text-blue-600 transition-colors">
                                Trust<span class="text-blue-600 group-hover:text-slate-900 transition-colors">Circle</span>
                            </span>
                        </a>
                    </div>

                    <!-- Center: Primary Actions (Desktop) -->
                    <div class="hidden md:flex items-center space-x-1 bg-slate-900/5 p-1.5 rounded-[2rem] border border-white/20 my-auto">
                        <a href="/" class="px-6 py-2 rounded-full text-xs font-black uppercase tracking-widest text-slate-600 hover:text-blue-600 transition-all hover:bg-white/60">
                            Exploration
                        </a>
                        @auth
                            <a href="{{ route('circles.create') }}" class="px-6 py-2 bg-white rounded-full text-xs font-black uppercase tracking-widest text-slate-900 shadow-sm hover:bg-blue-600 hover:text-white transition-all">
                                Nouveau Cercle
                            </a>
                        @endauth
                    </div>

                    <!-- Right: Auth & Profile -->
                    <div class="hidden md:flex items-center space-x-6">
                        @auth
                            <div class="flex items-center gap-6">
                                <a href="{{ route('users.show', auth()->user()) }}" class="flex items-center gap-3 group">
                                    <div class="text-right hidden lg:block">
                                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ auth()->user()->trust_score }}% Trust</div>
                                        <div class="text-xs font-black text-slate-900 uppercase group-hover:text-blue-600 transition-colors">{{ auth()->user()->name }}</div>
                                    </div>
                                    <div class="w-12 h-12 rounded-2xl border-2 border-white/60 overflow-hidden group-hover:border-blue-500 transition-all shadow-xl">
                                        <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.auth()->user()->name }}" class="w-full h-full object-cover">
                                    </div>
                                </a>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-red-50 text-slate-300 hover:text-red-500 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    </button>
                                </form>
                            </div>
                        @else
                            <a href="/admin/login" class="text-xs font-black uppercase tracking-widest text-slate-600 hover:text-blue-600 transition-colors">Connexion</a>
                            <a href="/admin/register" class="bg-slate-900 text-white px-8 py-3 rounded-2xl text-xs font-black uppercase tracking-widest shadow-xl shadow-slate-900/20 hover:bg-blue-600 hover:-translate-y-1 transition-all">
                                Rejoindre
                            </a>
                        @endauth
                    </div>

                    <!-- Hamburger Button -->
                    <div class="flex items-center md:hidden">
                        <button @click="open = !open" class="text-slate-900 p-2">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                                <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 @click.away="open = false" 
                 class="md:hidden bg-white/95 backdrop-blur-xl border-t border-slate-100 p-6 space-y-6">
                <a href="/" class="block text-xl font-black uppercase tracking-tight text-slate-900">Exploration</a>
                @auth
                    <a href="{{ route('circles.create') }}" class="block text-xl font-black uppercase tracking-tight text-blue-600">Nouveau Cercle</a>
                    <div class="pt-6 border-t border-slate-100">
                        <div class="flex items-center gap-4 mb-6 text-slate-900">
                             <img src="{{ auth()->user()->avatar_url }}" class="w-12 h-12 rounded-2xl object-cover">
                             <span class="font-black uppercase tracking-tight">{{ auth()->user()->name }}</span>
                        </div>
                        <a href="{{ route('users.show', auth()->user()) }}" class="block font-black uppercase tracking-widest text-xs text-slate-400 mb-4">Mon Profil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-red-500 font-extrabold uppercase tracking-widest text-xs">Déconnexion</button>
                        </form>
                    </div>
                @else
                    <a href="/admin/login" class="block text-slate-900 font-black uppercase">Connexion</a>
                    <a href="/admin/register" class="block bg-blue-600 text-white text-center py-4 rounded-2xl font-black uppercase tracking-widest">Rejoindre</a>
                @endauth
            </div>
        </nav>

        <main class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

        @livewireScripts
        @filamentScripts
    </body>
</html>
