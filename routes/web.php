<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

// Guest routes (only accessible when NOT logged in)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes (require login AND active account)
Route::middleware(['auth.custom', 'active'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // CREATE ROUTE - MUST COME FIRST (before the show route)
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');

    // EDIT ROUTE
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');

    // STORE, UPDATE, DESTROY
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Comment routes
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Drafts route
    Route::get('/posts/drafts', [PostController::class, 'drafts'])->name('posts.drafts');

    Route::patch('/posts/{post}/publish', [PostController::class, 'publish'])->name('posts.publish');
});

// Admin routes
Route::middleware(['auth.custom', 'active'])->prefix('admin')->group(function () {
    Route::get('/roles', [App\Http\Controllers\RoleController::class, 'index'])->name('admin.roles');
    Route::post('/roles/{role}/permissions', [App\Http\Controllers\RoleController::class, 'assignPermission'])->name('admin.roles.permissions.assign');
});

// PUBLIC ROUTES - THESE COME LAST
Route::get('/', [PostController::class, 'index'])->name('home');

Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

// Suspended account page
Route::get('/suspended', function () {
    return view('suspended');
})->name('suspended');

// Task 1: Lifecycle test route
Route::get('/lifecycle-test', function () {
    return response()->json([
        'php_version' => PHP_VERSION,
        'timestamp' => now()->toIso8601String(),
    ]);
});
