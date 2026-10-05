<?php

use App\Controllers\AuthController;
use App\Controllers\SsoController;
use App\Controllers\SupportController;
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
Route::get('/tasks/onprogress',[TaskController::class,'getTaskOnProgress']);

// Nursecall
Route::post('/support/nursecall',[SupportController::class,'storeNurseCall']);
Route::get('/support/nursecall/find', [SupportController::class, 'findNursecall']);
Route::get('/support/nursecall', [SupportController::class,'listNursecall']);
Route::get('/support/nursecall/by-lane',  [SupportController::class, 'byLaneNursecall']);
Route::get('/support/nursecall/{id}', [SupportController::class,'showNursecall']);
Route::get('/support/nursecall/latest', [SupportController::class, 'latestNursecall']);

// 4M
Route::post('/support/4m', [SupportController::class,'store4m']);
Route::get('/support/4m', [SupportController::class,'listRecord4m']);
Route::get('/support/4m/{id}', [SupportController::class,'showRecord4m']);
Route::get('/support/4m/by-lane',         [SupportController::class, 'byLaneRecord4m']);
Route::get('/support/4m/find',        [SupportController::class, 'findRecord4m']);
Route::get('/support/4m/latest',        [SupportController::class, 'latestRecord4m']);

Route::get('/debug/test-nursecall', function () {
    try {
        $record = \App\Models\NursecallRecord::create([
            'no_lane'      => 'TEST-DEBUG',
            'type'         => 'Test',
            'month_year'   => '2026-10',
            'date_created' => '2026-10-05',
        ]);

        return json_encode([
            'success' => true,
            'record_id' => $record->id ?? 'NO_ID',   // ← tes akses ->id
            'all' => method_exists($record, 'toArray') ? $record->toArray() : 'no_toArray',
        ]);
    } catch (\Throwable $e) {
        return json_encode([
            'error' => $e->getMessage(),
            'file'  => $e->getFile(),
            'line'  => $e->getLine(),
        ]);
    }
});

Route::post('/tasks', [TaskController::class, 'store']);
Route::get('/endpoint/employee',[UtilsController::class, 'getEmployee']);
Route::get('/endpoint/dept',[UtilsController::class, 'getAllDept']);
Route::get('/sso/callback',[SsoController::class,'callback'])->name('callback');
Route::get('/auth/me',[AuthController::class,'me'])->name('auth.me');
Route::get('/csrf-token', function () {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    header('Content-Type: application/json');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    
    echo json_encode([
        'token' => csrf(),
        'header' => csrfHeader(),
    ]);
    exit;
});

// ============================================================
// PROTECTED ROUTES AUTH
// ============================================================
Route::group([AuthMiddleware::class], function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::put('/auth/profile',[AuthController::class, 'update']);

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
});