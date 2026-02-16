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

        <!-- Tailwind & Alpine -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>
            body { font-family: 'Outfit', sans-serif; }
            .bg-gradient-mesh {
                background-color: #f8fafc;
                background-image: 
                    radial-gradient(at 0% 0%, hsla(217,100%,94%,1) 0, transparent 50%), 
                    radial-gradient(at 50% 0%, hsla(225,100%,94%,1) 0, transparent 50%), 
                    radial-gradient(at 100% 0%, hsla(217,100%,94%,1) 0, transparent 50%);
            }
        </style>
        
        @livewireStyles
        @filamentStyles
    </head>
    <body class="bg-gradient-mesh text-slate-900 antialiased min-h-screen">
        <nav x-data="{ open: false }" class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center space-x-8">
                        <a href="/" class="flex items-center space-x-2 group">
                            <div class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20 group-hover:rotate-12 transition-transform">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <span class="text-2xl font-extrabold tracking-tight text-slate-900">
                                Trust<span class="text-blue-600">Circle</span>
                            </span>
                        </a>
 
                        <div class="hidden md:flex items-center space-x-6">
                            <a href="/" class="text-slate-600 hover:text-blue-600 font-medium transition-colors">Home</a>
                            <a href="/" class="text-slate-600 hover:text-blue-600 font-medium transition-colors">Discovery</a>
                            @auth
                                <a href="{{ route('circles.create') }}" class="text-slate-600 hover:text-blue-600 font-medium transition-colors">Start a Circle</a>
                                <a href="{{ route('achievements.create') }}" class="text-slate-600 hover:text-blue-600 font-medium transition-colors">Post Proof</a>
                            @endauth
                        </div>
                    </div>
 
                    <div class="hidden md:flex items-center space-x-4">
                        @auth
                            <div class="flex items-center space-x-4 pr-4 border-r border-slate-200">
                                <a href="{{ route('users.show', auth()->user()) }}" class="text-sm font-semibold text-slate-700 hover:text-blue-600 transition-colors">{{ auth()->user()->name }}</a>
                                <a href="{{ route('users.show', auth()->user()) }}" class="w-10 h-10 rounded-full border-2 border-slate-100 overflow-hidden hover:border-blue-500 transition-colors">
                                    <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.auth()->user()->name }}" class="w-full h-full object-cover">
                                </a>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm font-bold text-slate-400 hover:text-red-500 transition-colors">
                                    Logout
                                </button>
                            </form>
                        @else
                            <a href="/admin/login" class="text-slate-600 hover:text-blue-600 font-bold px-4 transition-colors">Login</a>
                            <a href="/admin/register" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-500/20 hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0 transition-all">
                                Join Network
                            </a>
                        @endauth
                    </div>

                    <!-- Hamburger Button -->
                    <div class="flex items-center md:hidden">
                        <button @click="open = !open" class="text-slate-600 hover:text-blue-600 p-2">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="open" @click.away="open = false" x-transition.opacity class="md:hidden bg-white border-t border-slate-100 p-4 space-y-4">
                <a href="/" class="block text-slate-600 hover:text-blue-600 font-medium">Home</a>
                <a href="/" class="block text-slate-600 hover:text-blue-600 font-medium">Discovery</a>
                @auth
                    <a href="{{ route('circles.create') }}" class="block text-slate-600 hover:text-blue-600 font-medium">Start a Circle</a>
                    <a href="{{ route('achievements.create') }}" class="block text-slate-600 hover:text-blue-600 font-medium">Post Proof</a>
                    <div class="pt-4 border-t border-slate-100">
                        <a href="{{ route('users.show', auth()->user()) }}" class="block font-bold text-slate-900 mb-2">{{ auth()->user()->name }}</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-red-500 font-bold">Logout</button>
                        </form>
                    </div>
                @else
                    <a href="/admin/login" class="block text-slate-600 font-bold">Login</a>
                    <a href="/admin/register" class="block bg-blue-600 text-white text-center py-3 rounded-xl font-bold">Join Network</a>
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
