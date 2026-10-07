<?php

use App\Controllers\AuditLogController;
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

Route::get('/support/page/nursecall', [SupportController::class, 'pageNursecall']);
Route::get('/support/page/4m',        [SupportController::class, 'pageRecord4m']);

// NURSECALL
Route::post('/support/nursecall',        [SupportController::class, 'storeNursecall']);
Route::get('/support/nursecall',         [SupportController::class, 'listNursecall']);
Route::get('/support/nursecall/find',    [SupportController::class, 'findNursecall']);
Route::get('/support/nursecall/lanes',   [SupportController::class, 'listNursecallLanes']);
Route::get('/support/nursecall/by-lane', [SupportController::class, 'byLaneNursecall']);

Route::get('/support/nursecall/{id}',    [SupportController::class, 'showNursecall']);

// 4M
Route::post('/support/4m',               [SupportController::class, 'storeRecord4m']);
Route::get('/support/4m',                [SupportController::class, 'listRecord4m']);
Route::get('/support/4m/find',           [SupportController::class, 'findRecord4m']);
Route::get('/support/4m/lanes',          [SupportController::class, 'listRecord4mLanes']); 
Route::get('/support/4m/by-lane',        [SupportController::class, 'byLaneRecord4m']);

Route::get('/support/4m/{id}',           [SupportController::class, 'showRecord4m']);

Route::get('/support/nursecall/report', [SupportController::class, 'reportNursecall']);
Route::get('/support/4m/report',        [SupportController::class, 'reportRecord4m']);
Route::get('/support/check-task-docs', [SupportController::class, 'checkTaskDocs']);

Route::post('/support/upload-task-doc',   [SupportController::class, 'uploadTaskDoc']);
Route::get('/support/get-task-doc',       [SupportController::class, 'getTaskDoc']);
Route::post('/support/delete-task-doc',   [SupportController::class, 'deleteTaskDoc']);

Route::get('/support/assigned-task-ids', [SupportController::class, 'assignedTaskIds']);
Route::get('/support/reference-tasks', [SupportController::class, 'referenceTasks']);

Route::get('/support/report/nursecall', [SupportController::class, 'pageReportNursecall']);
Route::get('/support/report/4m',        [SupportController::class, 'pageReport4m']);

// Route::get('/debug/test-nursecall', function () {
//     try {
//         $record = \App\Models\NursecallRecord::create([
//             'no_lane'      => 'TEST-DEBUG',
//             'type'         => 'Test',
//             'month_year'   => '2026-10',
//             'date_created' => '2026-10-05',
//         ]);

//         return json([
//             'success' => true,
//             'record_id' => $record->id ?? 'NO_ID',
//             'all' => method_exists($record, 'toArray') ? $record->toArray() : 'no_toArray',
//         ]);
//     } catch (\Throwable $e) {
//         return json([
//             'error' => $e->getMessage(),
//             'file'  => $e->getFile(),
//             'line'  => $e->getLine(),
//         ]);
//     }
// });

Route::get('/debug/tz', function () {
    return json([
        'env_TIMEZONE'              => env('TIMEZONE', '(not set)'),
        'config_app_timezone'       => function_exists('config') ? config('app.timezone') : '(no config())',
        'php_date_default_timezone' => date_default_timezone_get(),
        'php_date_now'              => date('Y-m-d H:i:s'),
        'php_datetime_now'          => (new \DateTime('now'))->format('Y-m-d H:i:s'),
        'mysql_now'                 => \Bpjs\Framework\Helpers\Database::connection()
                                          ->query('SELECT NOW()')->fetchColumn(),
        'mysql_tz'                  => \Bpjs\Framework\Helpers\Database::connection()
                                          ->query('SELECT @@session.time_zone')->fetchColumn(),
    ]);
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
    Route::get('/audit/view', function () {
        return view('audit/index');
    });
    Route::get('/audit',                          [AuditLogController::class, 'index']);
    Route::get('/audit/{id}',                     [AuditLogController::class, 'show']);
    Route::get('/audit/by-record/{table}/{id}',   [AuditLogController::class, 'byRecord']);
    Route::post('/audit/{id}/restore',            [AuditLogController::class, 'restore']);
    Route::post('/audit/{id}/rollback', [AuditLogController::class, 'rollback']);
});

