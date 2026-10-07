<?php

namespace App\Controllers;

use App\Models\AuditLog;
use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\View;

class AuditLogController extends BaseController
{
    // Controller logic here
    public function index(Request $request)
    {
        $this->closeSessionEarly();

        $query = AuditLog::query();

        // Filter
        if ($request->table_name) {
            $query->where('table_name', '=', $request->table_name);
        }
        if ($request->action && $request->action !== 'all') {
            $query->where('action', '=', $request->action);
        }
        if ($request->date_from) {
            $query->where('created_at', '>=', $request->date_from . ' 00:00:00');
        }
        if ($request->date_to) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $logs = $query->orderBy('created_at', 'desc')
                      ->paginate((int) ($request->per_page ?? 50));

        return $this->json($logs, 200);
    }

    // GET /audit/{id}
    public function show($id)
    {
        $log = AuditLog::findOrFail($id);
        $logArr = $log->toCleanArray();

        // Decode JSON untuk frontend
        $logArr['old_values']     = !empty($logArr['old_values'])     ? json_decode($logArr['old_values'], true)     : null;
        $logArr['new_values']     = !empty($logArr['new_values'])     ? json_decode($logArr['new_values'], true)     : null;
        $logArr['changed_fields'] = !empty($logArr['changed_fields']) ? json_decode($logArr['changed_fields'], true) : null;

        return $this->json($logArr, 200);
    }

    // GET /audit/by-record/{table}/{id}
    public function byRecord($table, $id)
    {
        $logs = AuditLog::query()
            ->where('table_name', '=', $table)
            ->where('record_id', '=', $id)
            ->orderBy('created_at', 'desc')
            ->get(\PDO::FETCH_ASSOC);

        return $this->json($logs ?: [], 200);
    }

    // POST /audit/{id}/restore
    public function restore($id)
    {
        try {
            $log = AuditLog::findOrFail($id);
            $logArr = $log->toCleanArray();

            if ($logArr['action'] !== 'delete') {
                return $this->json([
                    'success' => false,
                    'message' => 'Hanya log action=delete yang bisa di-restore',
                ], 422);
            }

            if (empty($logArr['old_values'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Snapshot tidak tersedia, tidak bisa restore',
                ], 422);
            }

            $tableName = $logArr['table_name'];
            $recordId  = (int) $logArr['record_id'];
            $oldValues = json_decode($logArr['old_values'], true);

            if (!is_array($oldValues)) {
                return $this->json([
                    'success' => false,
                    'message' => 'Format snapshot tidak valid',
                ], 422);
            }

            $modelMap = [
                'tasks'              => \App\Models\Task::class,
                'task_histories'     => \App\Models\TaskHistory::class,
                'task_documents'     => \App\Models\TaskDocument::class,
                'nursecall_records'  => \App\Models\NursecallRecord::class,
                'nursecall_items'    => \App\Models\NursecallItem::class,
                'record_4m'          => \App\Models\Record4m::class,
                'record_4m_items'    => \App\Models\Record4mItem::class,
            ];

            if (!isset($modelMap[$tableName])) {
                return $this->json([
                    'success' => false,
                    'message' => "Tabel {$tableName} tidak didukung untuk restore",
                ], 422);
            }

            $modelClass = $modelMap[$tableName];

            // ⭐ Ambil record termasuk soft-deleted
            $item = $modelClass::query()
                ->withTrashed()
                ->where('id', '=', $recordId)
                ->first();

            if ($item) {
                // Cek apakah soft-deleted
                if ($item->trashed()) {
                    // ⭐ RESTORE
                    $item->restore();

                    \App\Services\AuditService::log(
                        $tableName,
                        $recordId,
                        'restore',
                        null,
                        $oldValues,
                        ['notes' => "Restore dari audit log #{$id}"]
                    );

                    return $this->json([
                        'success' => true,
                        'message' => 'Data berhasil di-restore (soft delete)',
                    ], 200);
                }

                return $this->json([
                    'success' => false,
                    'message' => 'Record masih ada di DB (tidak dalam status deleted)',
                ], 422);
            }

            // ⭐ Sudah hard delete → insert ulang dari snapshot
            $insertData = $oldValues;
            $insertData['id'] = $recordId;
            unset($insertData['created_at']);
            unset($insertData['deleted_at']);

            $created = $modelClass::create($insertData);

            \App\Services\AuditService::log(
                $tableName,
                $recordId,
                'restore',
                null,
                $oldValues,
                ['notes' => "Restore (re-insert) dari audit log #{$id}"]
            );

            return $this->json([
                'success' => true,
                'message' => 'Data berhasil di-restore (insert ulang)',
                'data'    => $created,
            ], 200);

        } catch (\Throwable $e) {
            error_log('[AuditLogController::restore] ' . $e->getMessage()
                . ' @ ' . $e->getFile() . ':' . $e->getLine());

            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ], 500);
        }
    }

    // POST /audit/{id}/rollback
    public function rollback($id)
    {
        try {
            $log = AuditLog::findOrFail($id);
            $logArr = $log->toCleanArray();

            $tableName = $logArr['table_name'];
            $recordId  = (int) $logArr['record_id'];
            $action    = $logArr['action'];

            $oldValues = !empty($logArr['old_values']) ? json_decode($logArr['old_values'], true) : null;
            $newValues = !empty($logArr['new_values']) ? json_decode($logArr['new_values'], true) : null;

            // ── Mapping table → model class ──
            $modelMap = [
                'tasks'              => \App\Models\Task::class,
                'task_documents'     => \App\Models\TaskDocument::class,
                'nursecall_records'  => \App\Models\NursecallRecord::class,
                'nursecall_items'    => \App\Models\NursecallItem::class,
                'record_4m'          => \App\Models\Record4m::class,
                'record_4m_items'    => \App\Models\Record4mItem::class,
            ];

            if (!isset($modelMap[$tableName])) {
                return $this->json([
                    'success' => false,
                    'message' => "Tabel {$tableName} tidak didukung rollback",
                ], 422);
            }

            $modelClass = $modelMap[$tableName];

            // ⭐ Helper lokal: ambil record termasuk soft-deleted
            $getItem = function () use ($modelClass, $recordId) {
                return $modelClass::query()
                    ->withTrashed()
                    ->where('id', '=', $recordId)
                    ->first();
            };

            $result = null;

            // ═══════════════════════════════════════════════════
            // CASE 1: CREATE → rollback = HAPUS
            // ═══════════════════════════════════════════════════
            if ($action === 'create') {
                $item = $getItem();

                if (!$item) {
                    return $this->json([
                        'success' => false,
                        'message' => "Record #{$recordId} tidak ada di DB",
                    ], 422);
                }

                // Cek apakah sudah soft-deleted
                if ($item->trashed()) {
                    return $this->json([
                        'success' => false,
                        'message' => 'Record sudah dalam status terhapus',
                    ], 422);
                }

                $item->delete();

                $result = "Record #{$recordId} dihapus (rollback dari create)";

                \App\Services\AuditService::log(
                    $tableName, $recordId, 'delete', $newValues, null,
                    ['notes' => "Rollback dari log #{$id} (create)"]
                );
            }

            // ═══════════════════════════════════════════════════
            // CASE 2: UPDATE → rollback = REVERT ke old_values
            // ═══════════════════════════════════════════════════
            elseif ($action === 'update') {
                if (empty($oldValues)) {
                    return $this->json([
                        'success' => false,
                        'message' => 'Snapshot old_values kosong',
                    ], 422);
                }

                $item = $getItem();
                if (!$item) {
                    return $this->json([
                        'success' => false,
                        'message' => "Record #{$recordId} tidak ada di DB",
                    ], 422);
                }

                // ⭐ Kalau soft-deleted → restore dulu
                if ($item->trashed()) {
                    $item->restore();
                }

                // Snapshot saat ini untuk log
                $currentSnapshot = \App\Services\AuditService::snapshot($modelClass, $recordId);

                // Bersihkan field yang tidak boleh di-revert
                unset($oldValues['id']);
                unset($oldValues['created_at']);
                unset($oldValues['deleted_at']);
                unset($oldValues['updated_at']);

                // Re-fetch setelah restore
                $item = $getItem();
                $item->update($oldValues);

                $result = "Record #{$recordId} dikembalikan ke state sebelum update";

                \App\Services\AuditService::log(
                    $tableName, $recordId, 'update', $currentSnapshot, $oldValues,
                    ['notes' => "Rollback dari log #{$id} (update)"]
                );
            }

            // ═══════════════════════════════════════════════════
            // CASE 3: DELETE → rollback = RESTORE
            // ═══════════════════════════════════════════════════
            elseif ($action === 'delete') {
                if (empty($oldValues)) {
                    return $this->json([
                        'success' => false,
                        'message' => 'Snapshot old_values kosong',
                    ], 422);
                }

                $item = $getItem();

                if ($item) {
                    // Masih ada di DB
                    if ($item->trashed()) {
                        // Soft deleted → restore
                        $item->restore();
                        $result = "Record #{$recordId} di-restore (soft delete)";
                    } else {
                        return $this->json([
                            'success' => false,
                            'message' => 'Record masih ada di DB (tidak dalam status deleted)',
                        ], 422);
                    }
                } else {
                    // ⭐ Sudah hard delete → insert ulang dari snapshot
                    $insertData = $oldValues;
                    $insertData['id'] = $recordId;
                    unset($insertData['created_at']);
                    unset($insertData['deleted_at']);

                    $modelClass::create($insertData);
                    $result = "Record #{$recordId} di-insert ulang dari snapshot";
                }

                \App\Services\AuditService::log(
                    $tableName, $recordId, 'restore', null, $oldValues,
                    ['notes' => "Rollback dari log #{$id} (delete)"]
                );
            }

            // ═══════════════════════════════════════════════════
            // CASE 4: RESTORE → rollback = SOFT DELETE LAGI
            // ═══════════════════════════════════════════════════
            elseif ($action === 'restore') {
                $item = $getItem();

                if (!$item) {
                    return $this->json([
                        'success' => false,
                        'message' => "Record #{$recordId} tidak ada di DB",
                    ], 422);
                }

                if ($item->trashed()) {
                    return $this->json([
                        'success' => false,
                        'message' => 'Record sudah dalam status deleted',
                    ], 422);
                }

                $item->delete();

                $result = "Record #{$recordId} dihapus ulang (rollback dari restore)";

                \App\Services\AuditService::log(
                    $tableName, $recordId, 'delete', $newValues, null,
                    ['notes' => "Rollback dari log #{$id} (restore)"]
                );
            }

            else {
                return $this->json([
                    'success' => false,
                    'message' => "Action '{$action}' tidak bisa di-rollback",
                ], 422);
            }

            return $this->json([
                'success' => true,
                'message' => $result,
            ], 200);

        } catch (\Throwable $e) {
            error_log('[AuditLogController::rollback] ' . $e->getMessage()
                . ' @ ' . $e->getFile() . ':' . $e->getLine());

            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ], 500);
        }
    }

    protected function closeSessionEarly()
    {
        if(session_status() === PHP_SESSION_ACTIVE)
        {
            session_write_close();
        }
    }
}
