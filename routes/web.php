<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

// Guest routes (only accessible when NOT logged in)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes (require login)
Route::middleware(['auth.custom'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Post routes - protected routes (create, store, edit, update, destroy)
    Route::resource('posts', PostController::class)->except(['index', 'show']);
});

// Public post routes (anyone can view)
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Task 1: Lifecycle test route
Route::get('/lifecycle-test', function () {
    return response()->json([
        'php_version' => PHP_VERSION,
        'timestamp' => now()->toIso8601String(),
        'unix_timestamp' => now()->timestamp,
        'date' => now()->toDateTimeString(),
    ]);
});
