<div>
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center space-x-4 mb-4">
            <div class="flex-shrink-0">
                <img class="h-10 w-10 rounded-full" src="{{ $post->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.$post->user->name }}" alt="{{ $post->user->name }}">
            </div>
            <div>
                <div class="text-sm font-medium text-gray-900">{{ $post->user->name }}</div>
                <div class="text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</div>
            </div>
        </div>
        
        <p class="text-gray-800 mb-4">{{ $post->content }}</p>
        
        <div class="flex items-center space-x-4 text-gray-500 text-sm">
            <span>{{ $post->reactions->count() }} Reactions</span>
            <span>{{ $post->comments->count() }} Comments</span>
            
            <livewire:reaction-picker :post="$post" :wire:key="'reaction-picker-'.$post->id" />
        </div>
    </div>
</div>
