<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::apiResource('tasks', TaskController::class);
Route::post('tasks/{id}/completed', [TaskController::class, 'completed']);
Route::post('tasks/{id}/at-work', [TaskController::class, 'atWork']);
