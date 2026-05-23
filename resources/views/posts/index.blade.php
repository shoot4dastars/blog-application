@extends('layouts.app')

@section('title', 'All Posts')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Blog Posts</h1>
            @auth
                <a href="{{ route('posts.create') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create New Post
                </a>
            @endauth
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($posts as $post)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <div class="p-6">
                        <h2 class="text-xl font-bold mb-2">
                            <a href="{{ route('posts.show', $post->slug) }}" class="text-gray-900 hover:text-blue-600">
                                {{ $post->title }}
                            </a>
                        </h2>

                        <div class="text-sm text-gray-600 mb-3">
                            By {{ $post->user->name }} | {{ $post->created_at->format('M d, Y') }}
                            @if($post->status)
                                <span class="ml-2 px-2 py-1 text-xs rounded
                                {{ $post->status->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($post->status->status) }}
                            </span>
                            @endif
                        </div>

                        <p class="text-gray-700 mb-4">
                            {{ Str::limit($post->body, 150) }}
                        </p>

                        <div class="flex justify-between items-center">
                            <a href="{{ route('posts.show', $post->slug) }}"
                               class="text-blue-500 hover:text-blue-700">
                                Read More →
                            </a>

                            @auth
                                @if(auth()->id() === $post->user_id || auth()->user()->isAdmin())
                                    <div class="space-x-2">
                                        <a href="{{ route('posts.edit', $post) }}"
                                           class="text-yellow-500 hover:text-yellow-700">Edit</a>
                                        <form action="{{ route('posts.destroy', $post) }}"
                                              method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-500 hover:text-red-700"
                                                    onclick="return confirm('Are you sure?')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">No posts found.</p>
                    @auth
                        <a href="{{ route('posts.create') }}" class="text-blue-500 hover:underline">
                            Create the first post!
                        </a>
                    @endauth
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $posts->links() }}
        </div>
    </div>
@endsection
