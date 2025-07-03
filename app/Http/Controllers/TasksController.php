<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;

class TasksController extends Controller
{
    //
    // In your controller method
    public function index()
    {
        $user = Auth::user();
        $tasks = Task::with('volunteer')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $totalTasks = Task::count();
        $todoTasks = Task::where('status', 'To Do')->count();
        $inProgressTasks = Task::where('status', 'In Progress')->count();
        $completedTasks = Task::where('status', 'Completed')->count();

        return view('admin_tasks', compact(
            'tasks',
            'totalTasks',
            'todoTasks',
            'inProgressTasks',
            'completedTasks',
            'user'
        ));
    }
}
