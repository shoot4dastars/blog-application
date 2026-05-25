@extends('layouts.app')

@section('title', 'Account Suspended')

@section('content')
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-8 text-center">
            <div class="text-red-500 mb-4">
                <svg class="mx-auto h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-2">Account Suspended</h1>

            <p class="text-gray-600 mb-6">
                Your account has been suspended. Please contact the administrator for assistance.
            </p>

            <a href="{{ route('login') }}"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                Back to Login
            </a>
        </div>
    </div>
@endsection
