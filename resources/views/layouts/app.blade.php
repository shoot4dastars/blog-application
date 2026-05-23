<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel Blog')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
            <div class="flex items-center space-x-8">
                <!-- Logo / Home link -->
                <a href="/" class="text-xl font-bold text-gray-800">Laravel Blog</a>

                <!-- BLOG LINK - ADD THIS LINE -->
                <a href="{{ route('posts.index') }}" class="text-gray-600 hover:text-gray-900">Blog</a>
            </div>

            <div class="flex items-center space-x-4">
                @auth
                    <span class="text-gray-600">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800">Logout</button>
                    </form>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">Login</a>
                    <a href="{{ route('register') }}" class="text-green-600 hover:text-green-800">Register</a>
                @endguest
            </div>
        </div>
    </div>
</nav>

<main class="py-8">
    @yield('content')
</main>
</body>
</html>
