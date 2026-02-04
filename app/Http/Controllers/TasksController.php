<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class TasksController extends Controller
{
    public function index(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $perPage = $request->get('per_page', 10);
        $tasks = Task::where('user_id', $user->id)
                       ->orderBy('created_at', 'desc')
                       ->paginate($perPage);
        return response()->json($tasks, 200);
    }

    public function show($id)
    {
       $user = JWTAuth::parseToken()->authenticate();

        $task = Task::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

        if (! $task) {
        $exists = Task::where('id', $id)->exists();
        if ($exists) {
             return response()->json(['message' => 'Forbidden'], 403);
        } else {
             return response()->json(['message' => 'Task not found'], 404);
        }
        }

        return response()->json($task, 200);
    }

     public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,completed'
        ]);

        $task = Task::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ?? 'pending'
        ]);

        return response()->json($task, 201);
    }

    public function update(Request $request, $id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $task = Task::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

        if (! $task) {
        $exists = Task::where('id', $id)->exists();
        if ($exists) {
             return response()->json(['message' => 'Forbidden'], 403);
        } else {
             return response()->json(['message' => 'Task not found'], 404);
        }
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,completed'
        ]);

        $task->update($request->only(['title', 'description', 'status']));

        return response()->json($task, 200);
    }

    public function destroy($id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $task = Task::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

        if (! $task) {
        $exists = Task::where('id', $id)->exists();
        if ($exists) {
             return response()->json(['message' => 'Forbidden'], 403);
        } else {
             return response()->json(['message' => 'Task not found'], 404);
        }
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully'], 200);
    }
}
