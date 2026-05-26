@props(['post'])

<div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
    <div class="p-6">
        <h2 class="text-xl font-bold mb-2">
            <a href="{{ route('posts.show', $post->slug) }}" class="text-gray-900 hover:text-blue-600">
                {{ $post->title }}
            </a>
        </h2>

        <div class="text-sm text-gray-600 mb-3">
            By <span class="font-semibold">{{ $post->user->name }}</span> |
            {{ $post->created_at->format('M d, Y') }}
            @if($post->status)
                <span class="ml-2 px-2 py-1 text-xs rounded
                    {{ $post->status->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ ucfirst($post->status->status) }}
                </span>
            @endif
        </div>

        <div class="mb-4">
            @foreach($post->categories as $category)
                <span class="inline-block bg-gray-200 rounded-full px-2 py-1 text-xs text-gray-700 mr-1">
                    {{ $category->name }}
                </span>
            @endforeach
        </div>

        <p class="text-gray-700 mb-4">
            {{ Str::limit($post->body, 150) }}
        </p>

        <div class="flex justify-between items-center">
            <a href="{{ route('posts.show', $post->slug) }}"
               class="text-blue-500 hover:text-blue-700 font-semibold">
                Read More →
            </a>

            @auth
                <div class="space-x-2">
                    @can('update', $post)
                        <a href="{{ route('posts.edit', $post) }}"
                           class="text-yellow-500 hover:text-yellow-700 text-sm">Edit</a>
                    @endcan

                    @can('delete', $post)
                        <form action="{{ route('posts.destroy', $post) }}"
                              method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-red-500 hover:text-red-700 text-sm"
                                    onclick="return confirm('Are you sure you want to delete this post?')">
                                Delete
                            </button>
                        </form>
                    @endcan
                </div>
            @endauth
        </div>
    </div>
</div>
