<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;

class PostController extends Controller
{
    /**
     * Display a listing of published posts.
     */
    public function index()
    {
        // Fetch published posts with user relationship, paginate 8 per page
        $posts = Post::published()
            ->with('user', 'categories', 'status')
            ->latest()
            ->paginate(8);

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        $categories = Category::all();
        return view('posts.create', compact('categories'));
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(StorePostRequest $request)
    {
        // Get validated data
        $validated = $request->validated();

        // Generate slug from title if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Create the post
        $post = Post::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'body' => $validated['body'],
            'user_id' => auth()->id(),
            'view_count' => 0,
        ]);

        // Attach categories if selected
        if (!empty($validated['category_ids'])) {
            $post->categories()->attach($validated['category_ids']);
        }

        // Create polymorphic status
        Status::create([
            'status' => $validated['status'],
            'statusable_type' => Post::class,
            'statusable_id' => $post->id,
        ]);

        return redirect()->route('posts.show', $post->slug)
            ->with('success', 'Post created successfully!');
    }
    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        // Increment view count
        $post->increment('view_count');

        // Load relationships
        $post->load('user', 'categories', 'comments.user', 'status');

        // Get related posts (same categories, excluding current)
        $relatedPosts = Post::published()
            ->whereHas('categories', function ($query) use ($post) {
                $query->whereIn('categories.id', $post->categories->pluck('id'));
            })
            ->where('id', '!=', $post->id)
            ->limit(3)
            ->get();

        return view('posts.show', compact('post', 'relatedPosts'));
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post)
    {
        // Authorize user (only owner or admin can edit)
        if (auth()->id() !== $post->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $categories = Category::all();
        $post->load('categories', 'status');

        return view('posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified post in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        // Get validated data
        $validated = $request->validated();

        // Generate slug from title if not provided
        if (!empty($validated['title']) && empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Update only the fields that were provided
        $updateData = [];

        if (isset($validated['title'])) {
            $updateData['title'] = $validated['title'];
        }

        if (isset($validated['slug'])) {
            $updateData['slug'] = $validated['slug'];
        }

        if (isset($validated['body'])) {
            $updateData['body'] = $validated['body'];
        }

        // Update the post
        if (!empty($updateData)) {
            $post->update($updateData);
        }

        // Sync categories if provided
        if (isset($validated['category_ids'])) {
            if (!empty($validated['category_ids'])) {
                $post->categories()->sync($validated['category_ids']);
            } else {
                $post->categories()->detach();
            }
        }

        // Update status if provided
        if (isset($validated['status'])) {
            if ($post->status) {
                $post->status->update(['status' => $validated['status']]);
            } else {
                Status::create([
                    'status' => $validated['status'],
                    'statusable_type' => Post::class,
                    'statusable_id' => $post->id,
                ]);
            }
        }

        return redirect()->route('posts.show', $post->slug)
            ->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post)
    {
        // Authorize user
        if (auth()->id() !== $post->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete relationships first
        $post->categories()->detach();  // Remove pivot table entries
        $post->comments()->delete();     // Delete comments
        if ($post->status) {
            $post->status->delete();      // Delete status
        }

        // Now delete the post
        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully!');
    }
}
