@extends('layouts.app')

@section('title', 'Drafts')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    @can('admin-access')
                        All Drafts
                    @else
                        My Drafts
                    @endcan
                </h1>
                <p class="text-gray-600 mt-1">Posts awaiting review</p>
            </div>
            <a href="{{ route('posts.create') }}"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                New Post
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($posts as $post)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-2">
                                <h2 class="text-xl font-bold">
                                    <a href="{{ route('posts.edit', $post) }}" class="text-gray-900 hover:text-blue-600">
                                        {{ $post->title }}
                                    </a>
                                </h2>
                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Pending Review</span>
                            </div>

                            <div class="text-sm text-gray-600 mb-3">
                                By <span class="font-semibold">{{ $post->user->name }}</span> |
                                Last edited: {{ $post->updated_at->diffForHumans() }}
                            </div>

                            <p class="text-gray-700 mb-4">
                                {{ Str::limit($post->body, 100) }}
                            </p>

                            <div class="flex justify-between items-center">
                                <div class="space-x-2">
                                    <a href="{{ route('posts.edit', $post) }}"
                                       class="text-blue-500 hover:text-blue-700 text-sm">
                                        Edit Draft
                                    </a>

                                    @can('admin-access')
                                        <form action="{{ route('posts.publish', $post) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="bg-green-500 hover:bg-green-700 text-white text-xs px-3 py-1 rounded"
                                                    onclick="return confirm('Approve and publish this post?')">
                                                Approve & Publish
                                            </button>
                                        </form>
                                    @endcan
                                </div>

                                @can('delete', $post)
                                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm"
                                                onclick="return confirm('Delete this draft?')">
                                            Delete
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $posts->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <p class="text-gray-500 text-lg mb-4">No pending drafts.</p>
                <a href="{{ route('posts.create') }}" class="text-blue-500 hover:underline">
                    Create your first post →
                </a>
            </div>
        @endif
    </div>
@endsection
