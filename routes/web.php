<?php

use App\Controllers\AuthController;
use App\Controllers\TaskController;
use Bpjs\Framework\Helpers\AuthMiddleware;
use Bpjs\Framework\Helpers\Route;
use Bpjs\Framework\Helpers\View;

Route::get('/', fn() => view('new-home'));

// ============================================================
// PUBLIC ROUTES (tanpa login)
// ============================================================
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

// Board bisa dilihat siapa saja
Route::get('/tasks',        [TaskController::class, 'index']);   // ⬅ PINDAH KELUAR
Route::get('/tasks/stats',  [TaskController::class, 'stats']);   // ⬅ PINDAH KELUAR
Route::get('/tasks/{id}',   [TaskController::class, 'show']);    // ⬅ opsional, pindah juga

// Operator bisa create tanpa login
Route::post('/tasks', [TaskController::class, 'store']);

// ============================================================
// PROTECTED ROUTES (perlu login)
// ============================================================
Route::group([AuthMiddleware::class], function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Hanya leader/admin yang bisa ubah data
    Route::put('/tasks/{id}',            [TaskController::class, 'update']);
    Route::patch('/tasks/{id}/stage',    [TaskController::class, 'updateStage']);
    Route::patch('/tasks/{id}/approve',  [TaskController::class, 'approve']);
    Route::delete('/tasks/{id}',         [TaskController::class, 'destroy']);

    Route::patch('/tasks/{id}/cancel', [TaskController::class, 'cancel']);
    
});