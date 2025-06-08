{{-- resources/views/roles.blade.php --}}
@extends('components.layout')

@section('title', 'Role Management')

@section('styles')
    <style>
        .modal-bg {
            background: rgba(0, 0, 0, 0.5);
        }
    </style>
@endsection

@section('content')
    @include('components.navs')
    <main class="flex-1 overflow-auto p-4 sm:p-6">
        <div class="bg-white rounded-lg shadow p-6">

            {{-- Page heading --}}
            <h2 class="text-2xl font-bold mb-6">Role Management</h2>

            {{-- Add New Role --}}
            <div class="mb-8 p-6 border rounded-lg bg-gray-50">
                <h3 class="text-lg font-semibold mb-4">Add New Role</h3>
                <form class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label for="name" class="block text-sm font-medium">Full Name</label>
                            <input type="text" name="name" id="name" required
                                class="mt-1 block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus:ring-1 focus:ring-ring focus:outline-none">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium">Email Address</label>
                            <input type="email" name="email" id="email" required
                                class="mt-1 block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus:ring-1 focus:ring-ring focus:outline-none">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium">Password</label>
                            <input type="password" name="password" id="password" required
                                class="mt-1 block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus:ring-1 focus:ring-ring focus:outline-none">
                        </div>

                        <div>
                            <label for="confirmPassword" class="block text-sm font-medium">Confirm Password</label>
                            <input type="password" name="confirmPassword" id="confirmPassword" required
                                class="mt-1 block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus:ring-1 focus:ring-ring focus:outline-none">
                        </div>

                        <div>
                            <label for="role" class="block text-sm font-medium">Role</label>
                            <select name="role" id="role"
                                class="mt-1 block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus:ring-1 focus:ring-ring focus:outline-none">
                                <option>Admin</option>
                                <option>Editor</option>
                                <option>Viewer</option>
                            </select>
                        </div>

                    </div>

                    {{-- blue “Add Role” button with hover --}}
                    <button type="submit"
                        class="mt-4 inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow transition-colors duration-200 hover:bg-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-300">
                        Add Role
                    </button>
                </form>
            </div>

            {{-- Current Roles --}}
            <div>
                <h3 class="text-lg font-semibold mb-4">Current Roles</h3>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border p-2 text-left text-sm">Name</th>
                                <th class="border p-2 text-left text-sm">Email</th>
                                <th class="border p-2 text-left text-sm">Role</th>
                                <th class="border p-2 text-left text-sm">Date Added</th>
                                <th class="border p-2 text-left text-sm">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Static example rows --}}
                            @foreach ([['John Doe', 'john.doe@example.com', 'Admin', '2023-05-15'], ['Jane Smith', 'jane.smith@example.com', 'Editor', '2023-06-20'], ['Mike Johnson', 'mike.johnson@example.com', 'Viewer', '2023-07-10']] as [$name, $email, $role, $date])
                                <tr>
                                    <td class="border p-2 text-sm">{{ $name }}</td>
                                    <td class="border p-2 text-sm">{{ $email }}</td>
                                    <td class="border p-2 text-sm">{{ $role }}</td>
                                    <td class="border p-2 text-sm">{{ $date }}</td>
                                    <td class="border p-2">
                                        {{-- red “Delete” button with hover --}}
                                        <button
                                            class="inline-flex items-center rounded-md bg-red-600 px-3 py-1 text-xs font-medium text-white shadow-sm transition-colors duration-200 hover:bg-red-500 focus:outline-none focus:ring-1 focus:ring-red-300">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
@endsection
