<div class="flex space-x-2">
    @foreach ($types as $type)
        <button wire:click="toggle('{{ $type->slug }}')" 
                class="flex items-center space-x-1 px-2 py-1 rounded hover:bg-gray-100 {{ $post->reactions->where('user_id', auth()->id())->where('type', $type->slug)->count() ? 'bg-blue-50 text-blue-600' : 'text-gray-500' }}">
            <span>{{ $type->emoji }}</span>
            <span class="text-xs">{{ $post->reactions->where('type', $type->slug)->count() }}</span>
        </button>
    @endforeach
</div>
