@extends('layouts.app')

@section('title', 'Create New Post')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Create New Post</h1>

                <!-- Display all validation errors -->
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

                <form action="{{ route('posts.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="title" class="block text-gray-700 font-bold mb-2">Title *</label>
                        <input type="text" name="title" id="title"
                               value="{{ old('title') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 @error('title') border-red-500 @enderror"
                               required>
                        @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="slug" class="block text-gray-700 font-bold mb-2">Slug (optional)</label>
                        <input type="text" name="slug" id="slug"
                               value="{{ old('slug') }}"
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
                                  required>{{ old('body') }}</textarea>
                        @error('body')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="category_ids" class="block text-gray-700 font-bold mb-2">Categories</label>
                        <select name="category_ids[]" id="category_ids" multiple
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 @error('category_ids') border-red-500 @enderror">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ in_array($category->id, old('category_ids', [])) ? 'selected' : '' }}>
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
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                            </select>
                        @else
                            <input type="hidden" name="status" value="draft">
                            <p class="text-gray-600 text-sm bg-gray-100 p-3 rounded">
                                Your post will be saved as a draft and will be published after admin review.
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
                            Create Post
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
