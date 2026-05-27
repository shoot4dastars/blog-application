<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laravel Blog')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

<!-- Navigation Bar -->
<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="text-xl font-bold text-gray-800 hover:text-gray-600">Laravel Blog</a>
            </div>

            <div class="flex items-center space-x-4">
                @auth
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">Welcome, {{ auth()->user()->name }}</span>
                        <a href="{{ route('posts.drafts') }}" class="text-gray-600 hover:text-gray-900">
                            Drafts
                            @if(auth()->user()->draftsCount() > 0)
                                <span class="bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full">
                                    {{ auth()->user()->draftsCount() }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>

                        <!-- Admin Link - Only visible to admins with pending count -->
                        @can('admin-access')
                            @php
                                $pendingCount = App\Models\Post::whereHas('status', function($q) {
                                    $q->where('status', 'draft');
                                })->count();
                            @endphp
                            <a href="{{ route('admin.roles') }}" class="text-purple-600 hover:text-purple-800 relative">
                                Admin
                            </a>
                        @endcan

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800">Logout</button>
                        </form>
                    </div>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">Login</a>
                    <a href="{{ route('register') }}" class="text-green-600 hover:text-green-800">Register</a>
                @endguest
            </div>
        </div>
    </div>
</nav>

<!-- Main Content -->
<main class="flex-grow py-8">
    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-white shadow-lg mt-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="text-center text-gray-500 text-sm">
            <p>&copy; {{ date('Y') }} Laravel Blog. All rights reserved.</p>
            <p class="mt-1">Built with Laravel and ❤️</p>
        </div>
    </div>
</footer>
</body>
</html>
