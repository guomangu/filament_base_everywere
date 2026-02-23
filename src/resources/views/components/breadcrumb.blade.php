@props([
    'circle' => null,
    'user' => null,
    'skill' => null,
    'project' => null,
    'achievement' => null,
])

<nav class="flex mb-8" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        {{-- Accueil --}}
        <li class="inline-flex items-center">
            <a href="{{ route('home') }}" class="inline-flex items-center text-xs font-black uppercase tracking-widest text-slate-500 hover:text-blue-600 transition-colors">
                <svg class="w-3 h-3 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                Accueil
            </a>
        </li>

        {{-- Circle --}}
        @if($circle)
            <li>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('circles.show', $circle) }}" class="ml-1 text-xs font-black uppercase tracking-widest text-slate-500 hover:text-blue-600 transition-colors md:ml-2">
                        {{ $circle->name }}
                    </a>
                </div>
            </li>
        @endif

        {{-- User --}}
        @if($user)
            <li>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('users.show', $user) }}" class="ml-1 text-xs font-black uppercase tracking-widest text-slate-500 hover:text-blue-600 transition-colors md:ml-2">
                        {{ $user->name }}
                    </a>
                </div>
            </li>
        @endif

        {{-- Mission (Skill) --}}
        @if($skill)
            <li>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('mission.show', $skill) }}" class="ml-1 text-xs font-black uppercase tracking-widest text-slate-500 hover:text-blue-600 transition-colors md:ml-2">
                        {{ $skill->name }}
                    </a>
                </div>
            </li>
        @endif

        {{-- Project or Achievement --}}
        @if($project || $achievement)
            @php
                $item = $project ?: $achievement;
                $isProject = (bool)$project;
            @endphp
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-xs font-black uppercase tracking-widest text-blue-600 md:ml-2 flex items-center gap-2">
                        {{ Str::limit($item->title, 25) }}
                        @if($isProject)
                             <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded text-[7px] leading-none">Projet</span>
                        @else
                             <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded text-[7px] leading-none">Preuve</span>
                        @endif
                    </span>
                </div>
            </li>
        @endif
    </ol>
</nav>
