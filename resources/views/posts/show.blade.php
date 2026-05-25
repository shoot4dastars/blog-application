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
                        @if(auth()->id() === $post->user_id || (auth()->user() && auth()->user()->isAdmin()))
                            <div class="space-x-2">
                                <a href="{{ route('posts.edit', $post) }}"
                                   class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-sm transition">
                                    Edit
                                </a>
                                <form action="{{ route('posts.destroy', $post) }}"
                                      method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm transition"
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

                <!-- Comments Section -->
                    <!-- Comments Section -->
                    <div class="mt-8 pt-6 border-t">
                        <h3 class="text-xl font-bold mb-4">Comments ({{ $post->comments->count() }})</h3>

                        <!-- Add Comment Form (only for logged in users) -->
                        @auth
                            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                <h4 class="font-bold mb-3">Add a Comment</h4>

                                @if(session('comment_error'))
                                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                                        {{ session('comment_error') }}
                                    </div>
                                @endif

                                <form action="{{ route('comments.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="post_id" value="{{ $post->id }}">

                                    <div class="mb-3">
                    <textarea name="body" rows="3"
                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 @error('body') border-red-500 @enderror"
                              placeholder="Write your comment here..." required>{{ old('body') }}</textarea>
                                        @error('body')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <button type="submit"
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                                        Post Comment
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-lg p-4 mb-6 text-center">
                                <p class="text-gray-600">
                                    <a href="{{ route('login') }}" class="text-blue-500 hover:underline">Login</a>
                                    to leave a comment.
                                </p>
                            </div>
                        @endauth

                        <!-- Display Comments -->
                        <div id="comments-list">
                            @forelse($post->comments as $comment)
                                <div class="bg-gray-50 rounded-lg p-4 mb-3 comment-item" data-comment-id="{{ $comment->id }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-grow">
                                            <div class="flex items-center justify-between mb-2">
                                                <div>
                                                    <strong class="text-gray-800">{{ $comment->user->name }}</strong>
                                                    <span class="text-gray-400 text-sm ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                                                </div>

                                                @auth
                                                    @if(auth()->id() === $comment->user_id || auth()->user()->isAdmin())
                                                        <div class="space-x-2">
                                                            <button onclick="editComment({{ $comment->id }}, '{{ addslashes($comment->body) }}')"
                                                                    class="text-yellow-500 hover:text-yellow-700 text-sm">
                                                                Edit
                                                            </button>
                                                            <form action="{{ route('comments.destroy', $comment) }}"
                                                                  method="POST" class="inline delete-comment-form"
                                                                  data-comment-id="{{ $comment->id }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                        class="text-red-500 hover:text-red-700 text-sm"
                                                                        onclick="return confirm('Are you sure you want to delete this comment?')">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                @endauth
                                            </div>
                                            <div class="comment-body" data-original-body="{{ $comment->body }}">
                                                <p class="text-gray-600">{{ $comment->body }}</p>
                                            </div>

                                            <!-- Edit Comment Form (hidden by default) -->
                                            <div class="edit-comment-form mt-3 hidden">
                                                <form action="{{ route('comments.update', $comment) }}" method="POST" class="inline-edit-form">
                                                    @csrf
                                                    @method('PUT')
                                                    <textarea name="body" rows="2"
                                                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 mb-2"
                                                              required>{{ $comment->body }}</textarea>
                                                    <div class="space-x-2">
                                                        <button type="submit"
                                                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm transition">
                                                            Save
                                                        </button>
                                                        <button type="button"
                                                                onclick="cancelEdit({{ $comment->id }})"
                                                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-3 rounded text-sm transition">
                                                            Cancel
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="bg-gray-50 rounded-lg p-4 text-center text-gray-500">
                                    No comments yet. Be the first to comment!
                                </div>
                            @endforelse
                        </div>
                    </div>

                    @push('scripts')
                        <script>
                            function editComment(commentId, commentBody) {
                                // Hide the comment body and show the edit form
                                const commentItem = document.querySelector(`.comment-item[data-comment-id="${commentId}"]`);
                                const commentBodyDiv = commentItem.querySelector('.comment-body');
                                const editForm = commentItem.querySelector('.edit-comment-form');

                                commentBodyDiv.classList.add('hidden');
                                editForm.classList.remove('hidden');
                            }

                            function cancelEdit(commentId) {
                                const commentItem = document.querySelector(`.comment-item[data-comment-id="${commentId}"]`);
                                const commentBodyDiv = commentItem.querySelector('.comment-body');
                                const editForm = commentItem.querySelector('.edit-comment-form');

                                commentBodyDiv.classList.remove('hidden');
                                editForm.classList.add('hidden');
                            }

                            // Handle AJAX form submissions for a smoother experience
                            document.querySelectorAll('.inline-edit-form').forEach(form => {
                                form.addEventListener('submit', async (e) => {
                                    e.preventDefault();
                                    const formData = new FormData(form);
                                    const response = await fetch(form.action, {
                                        method: 'POST',
                                        body: formData,
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest'
                                        }
                                    });

                                    if (response.ok) {
                                        const result = await response.json();
                                        if (result.success) {
                                            location.reload(); // Simple reload to show updated comment
                                        }
                                    }
                                });
                            });
                        </script>
                    @endpush

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
