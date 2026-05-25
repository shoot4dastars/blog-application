<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Middleware\LogRequestDetails;
use Illuminate\Support\Facades\Route;

// Guest routes (only accessible when NOT logged in)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Apply LogRequestDetails to all posts routes (public and protected)
Route::middleware([LogRequestDetails::class])->prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('posts.index');
    Route::get('/{post:slug}', [PostController::class, 'show'])->name('posts.show');
});

// Authenticated routes (require login AND active account)
Route::middleware(['auth.custom', 'active'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Protected post routes (create, edit, update, delete)
    Route::resource('posts', PostController::class)
        ->except(['index', 'show'])
        ->middleware([LogRequestDetails::class]);

    // Comment routes
    Route::resource('comments', CommentController::class)->only(['store', 'update', 'destroy']);
});

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

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
