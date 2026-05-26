<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\User;
use App\Policies\PostPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register PostPolicy
        Gate::policy(Post::class, PostPolicy::class);

        // Define Gates for dashboard access
        Gate::define('view-dashboard', function (User $user) {
            return $user->is_active;
        });

        // Define Gates for admin access
        Gate::define('admin-access', function (User $user) {
            return $user->isAdmin();
        });
    }
}
