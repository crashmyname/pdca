<?php

use App\Controllers\AuthController;
use App\Controllers\TaskAttachmentController;
use App\Controllers\TaskController;
use App\Controllers\UtilsController;
use Bpjs\Framework\Helpers\AuthMiddleware;
use Bpjs\Framework\Helpers\Route;
use Bpjs\Framework\Helpers\View;


// ============================================================
// PUBLIC ROUTES 
// ============================================================
Route::get('/', fn() => view('new-home'));
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::get('/tasks',        [TaskController::class, 'index']);
Route::get('/tasks/stats',  [TaskController::class, 'stats']);
Route::get('/tasks/{id}',   [TaskController::class, 'show']);

Route::post('/tasks', [TaskController::class, 'store']);
Route::get('/endpoint/employee',[UtilsController::class, 'getEmployee']);
Route::get('/endpoint/dept',[UtilsController::class, 'getAllDept']);

// ============================================================
// PROTECTED ROUTES AUTH
// ============================================================
Route::group([AuthMiddleware::class], function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Hanya leader/admin yang bisa ubah data
    Route::put('/tasks/{id}',            [TaskController::class, 'update']);
    Route::patch('/tasks/{id}/stage',    [TaskController::class, 'updateStage']);
    Route::patch('/tasks/{id}/approve',  [TaskController::class, 'approve']);
    Route::delete('/tasks/{id}',         [TaskController::class, 'destroy']);
    Route::get('/tasks/{id}/history', [TaskController::class, 'history']);

    Route::patch('/tasks/{id}/cancel', [TaskController::class, 'cancel']);
    Route::get('/tasks/report', [TaskController::class, 'report']);
    
    Route::get('/tasks/{task}/attachments', [TaskAttachmentController::class, 'index']);
    Route::post('/tasks/{task}/attachments', [TaskAttachmentController::class, 'store']);
    Route::delete('/tasks/{task}/attachments/{attachment}', [TaskAttachmentController::class, 'destroy']);
    // Route::prefix('/tasks/{task}', function(){
    // });
});