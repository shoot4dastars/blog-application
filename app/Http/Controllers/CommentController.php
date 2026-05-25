<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;

class CommentController extends Controller
{
    /**
     * Store a newly created comment.
     */
    public function store(StoreCommentRequest $request)
    {
        $validated = $request->validated();

        $comment = Comment::create([
            'body' => $validated['body'],
            'user_id' => auth()->id(),
            'post_id' => $validated['post_id'],
        ]);

        $comment->load('user');

        // For AJAX requests (if you want to add via AJAX later)
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'comment' => $comment,
                'message' => 'Comment added successfully!'
            ]);
        }

        return redirect()->back()
            ->with('success', 'Comment added successfully!');
    }

    /**
     * Update the specified comment.
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $validated = $request->validated();

        $comment->update([
            'body' => $validated['body'],
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'comment' => $comment,
                'message' => 'Comment updated successfully!'
            ]);
        }

        return redirect()->back()
            ->with('success', 'Comment updated successfully!');
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Comment $comment)
    {
        // Authorize: only comment owner or admin can delete
        if (auth()->id() !== $comment->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully!'
            ]);
        }

        return redirect()->back()
            ->with('success', 'Comment deleted successfully!');
    }
}
