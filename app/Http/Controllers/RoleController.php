<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Ministry;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get only non-archived users
        $nonArchivedUsers = User::with('ministry')
            ->where('is_archived', false)
            ->where('id', '!=', $user->id)
            ->paginate(10); // Paginate with 10 users per page

        // Get all ministries with hierarchical structure
        $ministries = Ministry::with(['children.children'])
            ->whereNull('parent_id')
            ->get();

        // Map users for JavaScript (only current page users)
        $users = $nonArchivedUsers->map(function ($user) {
            return [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'ministry_id' => $user->ministry_id,
                'ministry_name' => $user->ministry ? $user->ministry->ministry_name : null,
                'created_at' => $user->created_at,
                'dateAdded' => $user->created_at->format('Y-m-d'),
                'profile_picture' => $user->profile_picture,
            ];
        });

        return view('admin_roleManagement', compact('user', 'nonArchivedUsers', 'users', 'ministries'));
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).+$/'
            ],
            'role' => 'required|in:admin,staff',
            'ministry_id' => 'required|exists:ministries,id',
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one number, and one special character',
            'ministry_id.required' => 'Please select a ministry',
            'ministry_id.exists' => 'Selected ministry is invalid'
        ]);

        try {
            // Create the new user - DON'T manually hash password, let the 'hashed' cast handle it
            $user = User::create([
                'email' => $request->email,
                'password' => $request->password, // ← Changed: Remove Hash::make()
                'role' => $request->role,
                'ministry_id' => $request->ministry_id,
            ]);

            // Load the ministry relationship
            $user->load('ministry');

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                    'ministry_id' => $user->ministry_id,
                    'ministry_name' => $user->ministry ? $user->ministry->ministry_name : null,
                    'dateAdded' => $user->created_at->format('Y-m-d'),
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
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,staff',
            'ministry_id' => 'required|exists:ministries,id',
        ], [
            'email.unique' => 'This email is already in use by another account',
            'ministry_id.required' => 'Please select a ministry',
            'ministry_id.exists' => 'Selected ministry is invalid'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = User::findOrFail($id);
        $user->email = $request->email;
        $user->role = $request->role;
        $user->ministry_id = $request->ministry_id;
        $user->save();

        // Load the ministry relationship
        $user->load('ministry');

        return response()->json([
            'success' => true,
            'message' => 'User information updated successfully',
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'ministry_id' => $user->ministry_id,
                'ministry_name' => $user->ministry ? $user->ministry->ministry_name : null,
                'dateAdded' => $user->created_at->format('Y-m-d'),
            ]
        ]);
    }

    public function archive(User $user)
    {
        try {
            // Set is_archived to true and save the reason
            $user->is_archived = true;
            $user->archive_reason = request('reason');

            // Add Archived Date and Archived By
            $user->archived_at = now();
            $user->archived_by = Auth::id();

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User archived successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error archiving user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restore(User $user)
    {
        try {
            $user->is_archived = false;
            $user->archive_reason = null;
            $user->save();

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

    public function bulkForceDelete(Request $request)
    {
        $ids = $request->input('ids');
        $count = User::where('is_archived', true)
            ->whereIn('id', $ids)
            ->delete(); // Use delete() instead of forceDelete() for soft deletes

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
            ->update(['is_archived' => false, 'archive_reason' => null]);

        return response()->json([
            'success' => true,
            'restored_count' => $count,
            'message' => "$count user(s) restored successfully"
        ]);
    }
}