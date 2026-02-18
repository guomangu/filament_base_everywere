@props(['project'])

@if($project)
<a href="{{ route('projects.show', $project) }}" 
    class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-br from-blue-600 to-indigo-600 text-white rounded-xl shadow-lg shadow-blue-500/20 hover:scale-105 transition-all group/trans"
    title="Projet Actif: {{ $project->title }}">
    <svg class="w-3.5 h-3.5 group-hover/trans:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
    </svg>
    <span class="text-[9px] font-black uppercase tracking-widest truncate max-w-[120px]">{{ $project->title }}</span>
</a>
@endif
