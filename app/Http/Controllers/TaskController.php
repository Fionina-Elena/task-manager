<?php

namespace App\Http\Controllers;

use App\Models\TaskModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{
    private function getResponse(): JsonResponse
    {
        $cacheKey = 'tasks_list';

        $tasks = Cache::remember($cacheKey, now()->addMinutes(30), function () {
            return TaskModel::orderBy('date', 'desc')->get();
        });

        return response()->json(data: [
            'status' => true,
            'message' => 'Успешно',
            'tasks' => $tasks,
        ]);
    }

    public function index(): JsonResponse
    {
        return $this->getResponse();
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        TaskModel::create($validated);
        Cache::forget('tasks_list');

        return $this->getResponse();
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i:s',
        ]);

        $task = TaskModel::findOrFail($id);
        $task->update($validated);
        Cache::forget('tasks_list');

        return $this->getResponse();
    }

    public function destroy(int $id): JsonResponse
    {
        $task = TaskModel::findOrFail($id);
        $task->delete();
        Cache::forget('tasks_list');

        return $this->getResponse();
    }

    public function completed(int $id): JsonResponse
    {
        $task = TaskModel::findOrFail($id);
        $task->update(['task_status' => 1]);
        Cache::forget('tasks_list');

        return $this->getResponse();
    }

    public function atWork(int $id): JsonResponse
    {
        $task = TaskModel::findOrFail($id);
        $task->update(['task_status' => 0]);
        Cache::forget('tasks_list');

        return $this->getResponse();
    }
}
