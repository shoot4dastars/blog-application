<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Anyone can view published posts
     */
    public function view(?User $user, Post $post): bool
    {
        // Published posts are visible to everyone
        if ($post->status && $post->status->status === 'published') {
            return true;
        }

        // Draft posts only visible to owner or admin
        return $user && ($user->id === $post->user_id || $user->isAdmin());
    }

    /**
     * Only authenticated users can create posts
     */
    public function create(User $user): bool
    {
        return $user->is_active;
    }

    /**
     * Only post owner or admin can update
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->isAdmin();
    }

    /**
     * Only post owner or admin can delete
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->isAdmin();
    }

    /**
     * Anyone can view the post list
     */
    public function viewAny(User $user): bool
    {
        return true;
    }
}
