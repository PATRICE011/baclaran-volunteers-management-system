<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\Ministry;
use Carbon\Carbon;
class TasksController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Task::where('is_archived', false);

        $ministries = Ministry::mainMinistries()->with('children.children')->get();

        // Search functionality (both title and description)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->input('status'));
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(12);

        $totalTasks = Task::where('is_archived', false)->count();
        $todoTasks = Task::where('status', 'To Do')->where('is_archived', false)->count();
        $inProgressTasks = Task::where('status', 'In Progress')->where('is_archived', false)->count();
        $completedTasks = Task::where('status', 'Completed')->where('is_archived', false)->count();

        return view('admin_tasks', compact(
            'tasks',
            'totalTasks',
            'todoTasks',
            'inProgressTasks',
            'completedTasks',
            'user',
            'ministries'
        ));
    }

    public function edit(Task $task)
    {
        return response()->json($task);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:To Do,In Progress,Completed',
            'ministry_id' => 'nullable|exists:ministries,id', // Add this validation
        ]);

        Task::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully!'
        ]);
    }


    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date_format:Y-m-d',
            'status' => 'required|in:To Do,In Progress,Completed',
            'ministry_id' => 'nullable|exists:ministries,id', // Add this validation
        ]);

        // Format the date before saving
        if ($request->has('due_date') && $request->due_date) {
            $validated['due_date'] = Carbon::createFromFormat('Y-m-d', $request->due_date);
        }

        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully!'
        ]);
    }

    public function destroy(Task $task)
    {
        // Change from delete to archive
        $task->archive(request('reason'));

        return response()->json([
            'success' => true,
            'message' => 'Task archived successfully'
        ]);
    }

    public function archive(Task $task)
    {
        $task->archive(request('reason'));

        return response()->json([
            'success' => true,
            'message' => 'Task archived successfully'
        ]);
    }

    public function restore(Task $task)
    {
        $task->restore();

        return response()->json([
            'success' => true,
            'message' => 'Task restored successfully'
        ]);
    }

    public function forceDelete(Task $task)
    {
        $task->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Task permanently deleted'
        ]);
    }

    public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids');
        $count = Task::where('is_archived', true)
            ->whereIn('id', $ids)
            ->update(['is_archived' => false]);

        return response()->json([
            'success' => true,
            'restored_count' => $count,
            'message' => "$count task(s) restored successfully"
        ]);
    }

    public function bulkForceDelete(Request $request)
    {
        $ids = $request->input('ids');
        $count = Task::where('is_archived', true)
            ->whereIn('id', $ids)
            ->forceDelete();

        return response()->json([
            'success' => true,
            'deleted_count' => $count,
            'message' => "$count task(s) permanently deleted"
        ]);
    }
}
