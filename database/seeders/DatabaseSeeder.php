<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Role;
use App\Models\Status;
use App\Models\Permission;
use App\Enums\RoleName;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Roles
        $adminRole = Role::create(['name' => RoleName::ADMIN]);
        $userRole = Role::create(['name' => RoleName::USER]);

        // 2. Create Permissions (based on route names from PDF)
        $permissions = [
            ['name' => 'View Posts', 'route_name' => 'posts.index'],
            ['name' => 'Create Post', 'route_name' => 'posts.create'],
            ['name' => 'Store Post', 'route_name' => 'posts.store'],
            ['name' => 'Edit Post', 'route_name' => 'posts.edit'],
            ['name' => 'Update Post', 'route_name' => 'posts.update'],
            ['name' => 'Delete Post', 'route_name' => 'posts.destroy'],
            ['name' => 'Manage Users', 'route_name' => 'users.index'],
        ];

        foreach ($permissions as $perm) {
            Permission::create($perm);
        }

        // 3. Assign all permissions to admin role
        $adminRole->permissions()->attach(Permission::all());

        // 4. Assign basic permissions to user role
        $userRole->permissions()->attach(
            Permission::whereIn('route_name', ['posts.index', 'posts.create', 'posts.store'])->get()
        );

        // 5. Create Admin User
        $adminUsers = [
            ['name' => 'Admin User 1', 'email' => 'admin1@blog.com'],
            ['name' => 'Admin User 2', 'email' => 'admin2@blog.com'],
            ['name' => 'Admin User 3', 'email' => 'admin3@blog.com'],
        ];

        foreach ($adminUsers as $adminData) {
            $admin = User::create([
                'name' => $adminData['name'],
                'email' => $adminData['email'],
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
            $admin->roles()->attach($adminRole);
        }

        // 6. Create Regular Users (10 users)
        $users = User::factory(10)->create();
        foreach ($users as $user) {
            $user->roles()->attach($userRole);
        }

        // 7. Create Categories
        $categories = ['Technology', 'Lifestyle', 'Travel', 'Food', 'Business'];
        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat,
                'slug' => \Str::slug($cat),
            ]);
        }

        // 8. Create Statuses (for polymorphic relationship)
        $allCategories = Category::all();

        foreach (User::all() as $user) {
            $numPosts = fake()->numberBetween(2, 5);

            for ($i = 0; $i < $numPosts; $i++) {
                $post = Post::factory()->create([
                    'user_id' => $user->id,
                ]);

                // Attach 1-3 random categories
                $post->categories()->attach(
                    $allCategories->random(fake()->numberBetween(1, 3))->pluck('id')->toArray()
                );

                // Assign status (70% published, 30% draft)
                $statusValue = fake()->boolean(70) ? 'published' : 'draft';

                // Create polymorphic status relationship
                Status::create([
                    'status' => $statusValue,
                    'statusable_type' => Post::class,
                    'statusable_id' => $post->id,
                ]);
            }
        }

        // 9. Create Posts with statuses
        $allCategories = Category::all();
        $publishedStatus = Status::where('status', 'published')->first();
        $draftStatus = Status::where('status', 'draft')->first();

        // Create posts for each user (2-5 posts per user)
        foreach (User::all() as $user) {
            $numPosts = fake()->numberBetween(2, 5);

            for ($i = 0; $i < $numPosts; $i++) {
                $post = Post::factory()->create([
                    'user_id' => $user->id,
                ]);

                // Attach 1-3 random categories
                $post->categories()->attach(
                    $allCategories->random(fake()->numberBetween(1, 3))->pluck('id')->toArray()
                );

                // Assign status (70% published, 30% draft)
                $status = fake()->boolean(70) ? $publishedStatus : $draftStatus;

                // Create polymorphic status relationship
                Status::create([
                    'status' => $status->status,
                    'statusable_type' => Post::class,
                    'statusable_id' => $post->id,
                ]);
            }
        }

        // 10. Create comments (0-5 comments per post)
        $posts = Post::all();
        foreach ($posts as $post) {
            $numComments = fake()->numberBetween(0, 5);

            for ($i = 0; $i < $numComments; $i++) {
                Comment::factory()->create([
                    'post_id' => $post->id,
                    'user_id' => User::inRandomOrder()->first()->id,
                ]);
            }
        }
    }
}
