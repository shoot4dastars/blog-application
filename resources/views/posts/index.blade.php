@extends('layouts.app')

@section('title', 'All Posts')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Blog Posts</h1>
            @auth
                <a href="{{ route('posts.create') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                    Create New Post
                </a>
            @endauth
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($posts as $post)
                <!-- Using the post-card component -->
                <x-post-card :post="$post" />
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
