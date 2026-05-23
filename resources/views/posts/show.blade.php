@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-8">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>

                <div class="flex items-center justify-between mb-6 pb-4 border-b">
                    <div class="text-sm text-gray-600">
                        By <span class="font-semibold">{{ $post->user->name }}</span> |
                        Published: {{ $post->created_at->format('F j, Y') }} |
                        Views: {{ $post->view_count }}
                        @if($post->status)
                            <span class="ml-2 px-2 py-1 text-xs rounded
                            {{ $post->status->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($post->status->status) }}
                        </span>
                        @endif
                    </div>

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
                                            onclick="return confirm('Are you sure you want to delete this post?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>

                <div class="mb-6">
                    @foreach($post->categories as $category)
                        <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm text-gray-700 mr-2">
                        {{ $category->name }}
                    </span>
                    @endforeach
                </div>

                <div class="prose max-w-none mb-8">
                    {!! nl2br(e($post->body)) !!}
                </div>

                @if(isset($relatedPosts) && $relatedPosts->count() > 0)
                    <div class="mt-8 pt-6 border-t">
                        <h3 class="text-xl font-bold mb-4">Related Posts</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($relatedPosts as $related)
                                <div class="bg-gray-50 rounded p-4">
                                    <h4 class="font-semibold mb-2">
                                        <a href="{{ route('posts.show', $related->slug) }}" class="text-blue-500 hover:underline">
                                            {{ $related->title }}
                                        </a>
                                    </h4>
                                    <p class="text-sm text-gray-600">{{ $related->created_at->format('M d, Y') }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
