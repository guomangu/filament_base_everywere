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
    <body class="bg-gradient-mesh text-slate-900 antialiased min-h-screen print:bg-white print:min-h-0">
        <nav x-data="{ open: false }" class="bg-white/40 backdrop-blur-2xl sticky top-0 z-50 border-b border-white/20 print:hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-24">
                    <!-- Left: Logo -->
                    <div class="flex items-center flex-shrink-0">
                        <a href="/" class="flex items-center space-x-3 group">
                            <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-xl md:rounded-2xl flex items-center justify-center shadow-xl shadow-blue-500/20 group-hover:rotate-12 transition-all duration-500">
                                <svg class="w-6 h-6 md:w-7 md:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <span class="text-xl md:text-2xl font-black tracking-tighter text-slate-900 group-hover:text-blue-600 transition-colors">
                                Trust<span class="text-blue-600 group-hover:text-slate-900 transition-colors">Circle</span>
                            </span>
                        </a>
                    </div>

                    <!-- Right: Auth & Profile (Universal) -->
                    <div class="flex items-center space-x-3 md:space-x-6">
                        @auth
                            <div class="flex items-center gap-3 md:gap-6">
                                <a href="{{ route('users.show', auth()->user()) }}" class="flex items-center gap-2 md:gap-3 group">
                                    <div class="text-right flex flex-col justify-center">
                                        <div class="text-[8px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">{{ auth()->user()->trust_score }}% <span class="hidden sm:inline">Trust</span></div>
                                        <div class="text-[10px] md:text-xs font-black text-slate-900 uppercase group-hover:text-blue-600 transition-colors truncate max-w-[80px] md:max-w-none">{{ auth()->user()->name }}</div>
                                    </div>
                                    <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl md:rounded-2xl border-2 border-white/60 overflow-hidden group-hover:border-blue-500 transition-all shadow-lg">
                                        <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.auth()->user()->name }}" class="w-full h-full object-cover">
                                    </div>
                                </a>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 md:w-10 md:h-10 flex items-center justify-center rounded-lg md:rounded-xl hover:bg-red-50 text-slate-300 hover:text-red-500 transition-all">
                                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    </button>
                                </form>
                            </div>
                        @else
                            <a href="/admin/login" class="text-[10px] md:text-xs font-black uppercase tracking-widest text-slate-600 hover:text-blue-600 transition-colors">Login</a>
                            <a href="/admin/register" class="bg-slate-900 text-white px-4 md:px-8 py-2 md:py-3 rounded-xl md:rounded-2xl text-[10px] md:text-xs font-black uppercase tracking-widest shadow-xl shadow-slate-900/20 hover:bg-blue-600 hover:-translate-y-1 transition-all">
                                Rejoindre
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <main class="py-12 print:p-0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 print:max-w-none print:p-0">
                {{ $slot }}
            </div>
        </main>

        @livewireScripts
        @filamentScripts
    </body>
</html>
