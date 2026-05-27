<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Status;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostCollection;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of posts.
     *
     * GET /api/posts
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // If authenticated user is admin, show all posts
        if ($user && $user->isAdmin()) {
            $posts = Post::with('user', 'categories', 'status', 'comments')
                ->latest()
                ->paginate(15);
        }
        // If authenticated user is regular user, show their posts + published posts
        else if ($user) {
            $posts = Post::with('user', 'categories', 'status', 'comments')
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhereHas('status', function ($q) {
                            $q->where('status', 'published');
                        });
                })
                ->latest()
                ->paginate(15);
        }
        // For guests, only show published posts
        else {
            $posts = Post::with('user', 'categories', 'status', 'comments')
                ->whereHas('status', function ($query) {
                    $query->where('status', 'published');
                })
                ->latest()
                ->paginate(15);
        }

        return new PostCollection($posts);
    }

    /**
     * Store a newly created post.
     *
     * POST /api/posts
     */
    public function store(StorePostRequest $request)
    {
        // Only authenticated users can create posts
        if (!$request->user()) {
            return response()->json([
                'message' => 'Unauthenticated. Please login to create a post.'
            ], 401);
        }

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
            'user_id' => $request->user()->id,
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

        // Load relationships
        $post->load('user', 'categories', 'status');

        return (new PostResource($post))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified post.
     *
     * GET /api/posts/{id}
     */
    public function show(Request $request, Post $post)
    {
        $user = $request->user();

        // Check if user can view this post
        $canView = false;

        // Published posts are viewable by everyone
        if ($post->status && $post->status->status === 'published') {
            $canView = true;
        }
        // Draft posts only for owner or admin
        else if ($user && ($user->id === $post->user_id || $user->isAdmin())) {
            $canView = true;
        }

        if (!$canView) {
            return response()->json([
                'message' => 'You do not have permission to view this post.'
            ], 403);
        }

        // Increment view count
        $post->increment('view_count');

        // Load relationships
        $post->load('user', 'categories', 'status', 'comments.user');

        return new PostResource($post);
    }

    /**
     * Update the specified post.
     *
     * PUT/PATCH /api/posts/{id}
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $user = $request->user();

        // Check authorization
        if (!$user || ($user->id !== $post->user_id && !$user->isAdmin())) {
            return response()->json([
                'message' => 'You do not have permission to update this post.'
            ], 403);
        }

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

        // Load relationships
        $post->load('user', 'categories', 'status');

        return new PostResource($post);
    }

    /**
     * Remove the specified post.
     *
     * DELETE /api/posts/{id}
     */
    public function destroy(Request $request, Post $post)
    {
        $user = $request->user();

        // Check authorization
        if (!$user || ($user->id !== $post->user_id && !$user->isAdmin())) {
            return response()->json([
                'message' => 'You do not have permission to delete this post.'
            ], 403);
        }

        // Delete relationships first
        $post->categories()->detach();
        $post->comments()->delete();
        if ($post->status) {
            $post->status->delete();
        }

        // Delete the post
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully.'
        ], 204);
    }
}
