@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6 text-center">
        <h2 class="text-3xl font-bold mb-4">Welcome to Laravel Blog</h2>
        <p class="text-gray-600 mb-6">A simple blog management system built with Laravel.</p>

        @guest
            <div class="space-x-4">
                <a href="{{ route('login') }}" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">Login</a>
                <a href="{{ route('register') }}" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">Register</a>
            </div>
        @endguest

        @auth
            <a href="{{ route('dashboard') }}" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">Go to Dashboard</a>
        @endauth
    </div>
@endsection
