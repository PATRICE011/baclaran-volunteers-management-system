<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Get only non-archived users and map them
        $nonArchivedUsers = User::where('is_archived', false)
                           ->where('id', '!=', $user->id)
                           ->get();

        $users = $nonArchivedUsers->map(function ($user) {
            return [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'role' => $user->role,
                'dateAdded' => $user->created_at->format('Y-m-d'),
                'profile_picture' => $user->profile_picture,
            ];
        });

        return view('admin_roleManagement', compact('user', 'nonArchivedUsers', 'users'));
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).+$/'
            ],
            'role' => 'required|in:admin,staff',
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one number, and one special character'
        ]);
        try {
            // Apply proper capitalization to names
            $firstName = ucwords(strtolower($request->first_name));
            $lastName = ucwords(strtolower($request->last_name));

            // Create the new user
            $user = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at->format('Y-m-d'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating user: ' . $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,staff',
        ], [
            'email.unique' => 'This email is already in use by another account',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = User::findOrFail($id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User information updated successfully',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'role' => $user->role,
                'dateAdded' => $user->created_at->format('Y-m-d'),
            ]
        ]);
    }

    public function archive(User $user)
    {
        $user->archive(request('reason'));

        return response()->json([
            'success' => true,
            'message' => 'User archived successfully'
        ]);
    }

    public function restore(User $user)
    {
        try {
            $user->restore(); // This will set is_archived to false
            return response()->json([
                'success' => true,
                'message' => 'User restored successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error restoring user: ' . $e->getMessage()
            ]);
        }
    }

    public function forceDelete(User $user)
    {
        try {
            $user->forceDelete(); // Permanent deletion
            return response()->json([
                'success' => true,
                'message' => 'User permanently deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user: ' . $e->getMessage()
            ]);
        }
    }

    // RoleController.php
    public function bulkForceDelete(Request $request)
    {
        $ids = $request->input('ids');
        $count = User::where('is_archived', true)
            ->whereIn('id', $ids)
            ->forceDelete();

        return response()->json([
            'success' => true,
            'deleted_count' => $count,
            'message' => "$count user(s) permanently deleted"
        ]);
    }

    public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids');
        $count = User::where('is_archived', true)
            ->whereIn('id', $ids)
            ->update(['is_archived' => false]);

        return response()->json([
            'success' => true,
            'restored_count' => $count,
            'message' => "$count user(s) restored successfully"
        ]);
    }
}
