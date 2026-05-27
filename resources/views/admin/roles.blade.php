@extends('layouts.app')

@section('title', 'Manage Roles & Permissions')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h1 class="text-2xl font-bold mb-6">Role & Permission Management</h1>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    @foreach($roles as $role)
                        @php
                            $isAdminRole = ($role->name->value ?? $role->name) === 'admin';
                        @endphp

                        <div class="mb-8">
                            <h2 class="text-xl font-bold mb-4">{{ ucfirst($role->name->value ?? $role->name) }}</h2>

                            <form action="{{ route('admin.roles.permissions.assign', $role) }}" method="POST">
                                @csrf
                                <table class="min-w-full bg-white border">
                                    <thead>
                                    <tr class="bg-gray-100">
                                        <th class="py-2 px-4 text-left">Permission</th>
                                        <th class="py-2 px-4 text-center">Assign</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($permissions as $permission)
                                        <tr class="border-b">
                                            <td class="py-2 px-4">{{ $permission->name }}</td>
                                            <td class="py-2 px-4 text-center">
                                                @if($isAdminRole)
                                                    <!-- Admin role: always checked and disabled -->
                                                    <input type="checkbox"
                                                           name="permissions[]"
                                                           value="{{ $permission->id }}"
                                                           class="w-4 h-4"
                                                           checked
                                                           disabled>
                                                    <input type="hidden" name="permissions[]" value="{{ $permission->id }}">
                                                @else
                                                    <!-- Other roles: can be toggled -->
                                                    <input type="checkbox"
                                                           name="permissions[]"
                                                           value="{{ $permission->id }}"
                                                           class="w-4 h-4"
                                                        {{ $role->permissions->contains($permission) ? 'checked' : '' }}>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                @if(!$isAdminRole)
                                    <div class="mt-4">
                                        <button type="submit"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                                            Save Permissions for {{ ucfirst($role->name->value ?? $role->name) }}
                                        </button>
                                    </div>
                                @else
                                    <div class="mt-4 text-sm text-gray-500 italic">
                                        Admin role has all permissions by default and cannot be modified.
                                    </div>
                                @endif
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
