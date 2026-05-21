@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-4">Welcome, {{ auth()->user()->name }}!</h2>
        <p class="text-gray-600">You are logged into your dashboard.</p>

        <div class="mt-6 p-4 bg-gray-100 rounded">
            <h3 class="font-bold mb-2">Your Information:</h3>
            <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
            <p><strong>Member since:</strong> {{ auth()->user()->created_at->format('F j, Y') }}</p>
            <p><strong>Status:</strong>
                @if(auth()->user()->is_active)
                    <span class="text-green-600">Active</span>
                @else
                    <span class="text-red-600">Inactive</span>
                @endif
            </p>
        </div>
    </div>
@endsection
