<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class PostController extends Controller
{
    /**
     * Display a listing of published posts.
     */

    use AuthorizesRequests;
    public function index()
    {
        // Show ONLY published posts to everyone
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
        $this->authorize('create', Post::class);
        $categories = Category::all();
        return view('posts.create', compact('categories'));
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(StorePostRequest $request)
    {
        $this->authorize('create', Post::class);
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $post = Post::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'body' => $validated['body'],
            'user_id' => auth()->id(),
            'view_count' => 0,
        ]);

        if (!empty($validated['category_ids'])) {
            $post->categories()->attach($validated['category_ids']);
        }

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
        // Check if post is a draft
        if ($post->status && $post->status->status === 'draft') {
            // Only allow owner or admin to view drafts
            if (auth()->guest() || (auth()->id() !== $post->user_id && !auth()->user()->isAdmin())) {
                abort(404, 'Post not found.');
            }
        }

        // Increment view count
        $post->increment('view_count');

        // Load relationships
        $post->load('user', 'categories', 'comments.user', 'status');

        // Get related posts
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
        // Check if user is authorized to edit this post
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
        $this->authorize('update', $post);
        $validated = $request->validated();

        if (!empty($validated['title']) && empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

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

        if (!empty($updateData)) {
            $post->update($updateData);
        }

        if (isset($validated['category_ids'])) {
            if (!empty($validated['category_ids'])) {
                $post->categories()->sync($validated['category_ids']);
            } else {
                $post->categories()->detach();
            }
        }

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
        $this->authorize('delete', $post);
        $post->categories()->detach();
        $post->comments()->delete();
        if ($post->status) {
            $post->status->delete();
        }

        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully!');
    }

    public function drafts()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            // Admin sees all drafts
            $posts = Post::whereHas('status', function($q) {
                $q->where('status', 'draft');
            })->with('user', 'categories', 'status')
                ->latest()
                ->paginate(8);
        } else {
            // Regular users see only their own drafts
            $posts = Post::whereHas('status', function($q) {
                $q->where('status', 'draft');
            })->where('user_id', $user->id)
                ->with('user', 'categories', 'status')
                ->latest()
                ->paginate(8);
        }

        return view('posts.drafts', compact('posts'));
    }
    public function publish(Post $post)
    {
        // Only admin can publish
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Update or create status
        if ($post->status) {
            $post->status->update(['status' => 'published']);
        } else {
            Status::create([
                'status' => 'published',
                'statusable_type' => Post::class,
                'statusable_id' => $post->id,
            ]);
        }

        return redirect()->back()->with('success', 'Post published successfully!');
    }
}
