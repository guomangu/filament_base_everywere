<div class="space-y-6">
    @foreach ($posts as $post)
        <livewire:post-item :post="$post" :key="$post->id" />
    @endforeach

    {{ $posts->links() }}
</div>
