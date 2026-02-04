<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TasksController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:5,1')->group(function () {
    Route::post('register', [AuthController::class,'register']);
    Route::post('login', [AuthController::class,'login']);
});

Route::middleware(['auth:api','throttle:5,1'])->group(function () {
    Route::post('logout', [AuthController::class,'logout']);
    
    //Tasks CRUD
    Route::get('/tasks', [TasksController::class,'index']);
    Route::get('/tasks/{id}', [TasksController::class,'show']);
    Route::post('/tasks', [TasksController::class,'store']);
    Route::put('/tasks/{id}', [TasksController::class,'update']);
    Route::delete('/tasks/{id}', [TasksController::class,'destroy']);


});