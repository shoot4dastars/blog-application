@extends('layouts.app')

@section('title', 'Edit Post')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Post</h1>

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <h4 class="font-bold mb-2">Please fix the following errors:</h4>
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('posts.update', $post) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="title" class="block text-gray-700 font-bold mb-2">Title *</label>
                        <input type="text" name="title" id="title"
                               value="{{ old('title', $post->title) }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 @error('title') border-red-500 @enderror"
                               required>
                        @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="slug" class="block text-gray-700 font-bold mb-2">Slug (optional)</label>
                        <input type="text" name="slug" id="slug"
                               value="{{ old('slug', $post->slug) }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 @error('slug') border-red-500 @enderror">
                        <p class="text-sm text-gray-500 mt-1">Leave empty to auto-generate from title</p>
                        @error('slug')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="body" class="block text-gray-700 font-bold mb-2">Content *</label>
                        <textarea name="body" id="body" rows="10"
                                  class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 @error('body') border-red-500 @enderror"
                                  required>{{ old('body', $post->body) }}</textarea>
                        @error('body')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="category_ids" class="block text-gray-700 font-bold mb-2">Categories</label>
                        <select name="category_ids[]" id="category_ids" multiple
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 @error('category_ids') border-red-500 @enderror">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ in_array($category->id, old('category_ids', $post->categories->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
                        @error('category_ids')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="status" class="block text-gray-700 font-bold mb-2">Status</label>
                        @if(auth()->user()->isAdmin())
                            <select name="status" id="status"
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 @error('status') border-red-500 @enderror"
                                    required>
                                <option value="draft" {{ old('status', $post->status->status ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $post->status->status ?? '') == 'published' ? 'selected' : '' }}>Published</option>
                            </select>
                        @else
                            <input type="hidden" name="status" value="{{ $post->status->status ?? 'draft' }}">
                            <p class="text-gray-600 text-sm bg-gray-100 p-3 rounded">
                                @if(($post->status->status ?? 'draft') === 'published')
                                    ✓ This post is published. Contact admin to change status.
                                @else
                                    ⏳ This post is pending admin review.
                                @endif
                            </p>
                        @endif
                        @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('posts.index') }}"
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition">
                            Cancel
                        </a>
                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                            Update Post
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
