<?php

namespace App\Controllers;

use App\Models\Task;
use App\Models\TaskHistory;
use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\Date;
use Bpjs\Framework\Helpers\View;

class TaskController extends BaseController
{
    // Controller logic here
    // GET /api/tasks - List tasks with filters
    public function index(Request $request)
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        
        $query = Task::query();

        // Filters
        if ($request->stage && $request->stage !== 'all') {
            $query->where('stage','=',$request->stage);
        }

        if ($request->status && $request->status !== 'all') {
            $query->where('status','=',$request->status);
        }

        if ($request->section_id) {
            $query->where('section','=',$request->section);
        }

        $search = $request->search;

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('operator_name', 'LIKE', "%{$search}%")
                ->orWhere('problem', 'LIKE', "%{$search}%")
                ->orWhere('temporary_action', 'LIKE', "%{$search}%")
                ->orWhere('permanent_action', 'LIKE', "%{$search}%")
                ->orWhere('pic', 'LIKE', "%{$search}%")
                ->orWhere('task_code', 'LIKE', "%{$search}%")
                ->orWhere('category', 'LIKE', "%{$search}%")
                ->orWhere('pic_section', 'LIKE', "%{$search}%");
            });
        }

        if ($request->date_from) {
            $query->where('task_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('task_date', '<=', $request->date_to);
        }

        $perPage = $request->per_page ?? 20;
        $tasks = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->json($tasks,200);
    }

    // POST /api/tasks - Create task (Operator)
    public function store(Request $request)
    {
        try {
            $taskCode = $this->generateTaskCode();

            $user     = auth()->user();
            $userRole = strtolower($user->role ?? 'operator');
            $userName = $user->name ?? $request->operator_name ?? 'Operator';

            $canEdit = $this->canEditTask($user);

            $task = Task::create([
                'task_code'       => $taskCode,
                'operator_name'   => $request->operator_name,
                'task_date'       => $request->task_date,
                'section'         => $request->section,
                'problem'         => $request->problem,
                'category'        => $canEdit ? ($request->category    ?: null) : null,
                'pic_section'     => $canEdit ? ($request->pic_section ?: null) : null,
                'temporary_action' => $canEdit ? ($request->temporary_action ?: null) : null,
                'permanent_action' => $canEdit ? ($request->permanent_action ?: null) : null,
                'deadline'         => $canEdit ? ($request->deadline ?: null) : null,
                'pic'              => $canEdit ? ($request->pic ?: null) : null,

                'stage'           => 'plan',
                'status'          => 'open',
                'created_by_name' => $userName,
                'created_by_role' => $userRole,
                'created_by'      => $userName,
            ]);

            TaskHistory::create([
                'task_id'         => $task->id,
                'action'          => 'create',
                'new_stage'       => 'plan',
                'new_status'      => 'open',
                'changed_by_name' => $userName,
                'changed_by_role' => $userRole,
                'notes'           => 'Task created by ' . $userName . ' (' . $userRole . ')',
            ]);

            return $this->json($task, 201);

        } catch (\Throwable $e) {
            error_log('[TaskController::store] ' . $e->getMessage()
                . ' @ ' . $e->getFile() . ':' . $e->getLine());

            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'class'   => get_class($e),
                'trace'   => array_slice(explode("\n", $e->getTraceAsString()), 0, 8),
            ], 500);
        }
    }

    // GET /api/tasks/{id} - Get task detail
    public function show($id)
    {
        $task = Task::query()->with(['section', 'creator', 'approver', 'histories'])
                    ->findOrFail($id);

        return $this->json($task, 200);
    }

    // PUT /api/tasks/{id} - Update task (Leader)
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $user = auth()->user();

        if (!$this->canEditTask($user)) {
            return $this->json(['message' => 'Only leader can update this task'], 403);
        }

        if (in_array(strtolower($task->status), ['done', 'cancelled'], true)) {
            return $this->json([
                'message' => 'Task yang sudah done/cancelled tidak bisa diubah'
            ], 422);
        }

        $trackedFields = [
            'operator_name', 'task_date', 'section', 'problem',
            'category', 'pic_section',
            'temporary_action', 'permanent_action', 'deadline', 'pic',
            'doc_4m_status', 'doc_logbook_status', 'doc_nursecall_status',
        ];

        $oldValues = [];
        foreach ($trackedFields as $f) {
            $oldValues[$f] = $task->$f ?? null;
        }

        $payload = [
            'category'         => $request->category    ?: $task->category,
            'pic_section'      => $request->pic_section ?: $task->pic_section,
            'temporary_action' => $request->temporary_action,
            'permanent_action' => $request->permanent_action,
            'deadline'         => $request->deadline ?: null,
            'pic'              => $request->pic,
            'updated_by'       => $user->id ?? null,
            'doc_4m_status'        => $request->doc_4m_status        ?: 'belum',
            'doc_logbook_status'   => $request->doc_logbook_status   ?: 'belum',
            'doc_nursecall_status' => $request->doc_nursecall_status ?: 'belum',
        ];

        if ($request->operator_name) $payload['operator_name'] = $request->operator_name;
        if ($request->task_date)     $payload['task_date']     = $request->task_date;
        if ($request->section)       $payload['section']       = $request->section;
        if ($request->problem)       $payload['problem']       = $request->problem;

        $task->update($payload);
        $task->refresh();

        $changedOld = [];
        $changedNew = [];

        foreach ($trackedFields as $f) {
            $ov = $oldValues[$f];
            $nv = $task->$f ?? null;

            $ovNorm = ($ov === '' || $ov === null) ? null : (string) $ov;
            $nvNorm = ($nv === '' || $nv === null) ? null : (string) $nv;

            if ($ovNorm !== $nvNorm) {
                $changedOld[$f] = $ovNorm;
                $changedNew[$f] = $nvNorm;
            }
        }

        if (!empty($changedOld) || !empty($changedNew)) {
            TaskHistory::create([
                'task_id'         => $task->id,
                'action'          => 'update',
                'old_values'      => json_encode($changedOld, JSON_UNESCAPED_UNICODE),
                'new_values'      => json_encode($changedNew, JSON_UNESCAPED_UNICODE),
                'changed_by_name' => $user->name,
                'changed_by_role' => strtolower($user->role),
                'notes'           => 'Task updated by ' . $user->name,
            ]);
        }

        return $this->json($task, 200);
    }

    // PATCH /api/tasks/{id}/stage - Update stage (Leader only)
    public function updateStage(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);

            if (!$this->isLeader(auth()->user())) {
                return $this->json(['message' => 'Only leader/admin can change stage'], 403);
            }

            // Validasi transisi stage
            $validTransitions = [
                'PLAN'  => ['DO'],
                'DO'    => ['CHECK', 'PLAN'],
                'CHECK' => ['ACT', 'DO'],
                'ACT'   => [],
            ];

            $oldStage = strtoupper($task->stage);
            $newStage = strtoupper($request->stage);

            if (!in_array($newStage, $validTransitions[$oldStage] ?? [], true)) {
                return $this->json([
                    'success' => false,
                    'message' => "Tidak bisa pindah dari {$oldStage} ke {$newStage}"
                ], 422);
            }

            // Validasi DO → CHECK butuh tindakan
            if ($oldStage === 'DO' && $newStage === 'CHECK') {
                if (empty($task->category) && empty($task->pic_section)) {
                    return $this->json([
                        'success' => false,
                        'message' => 'Isi category dan pic section sebelum pindah ke phase 3'
                    ], 422);
                }
            }

            if ($oldStage === 'CHECK' && $newStage === 'ACT') {

                $missing = [];

                if (($task->doc_4m_status        ?? 'belum') !== 'sudah') $missing[] = 'Dokumen 4M';
                if (($task->doc_logbook_status   ?? 'belum') !== 'sudah') $missing[] = 'Dokumen Logbook';
                if (($task->doc_nursecall_status ?? 'belum') !== 'sudah') $missing[] = 'Dokumen Nursecall';

                $required = [
                    'category'         => 'Category',
                    'pic_section'      => 'PIC Section',
                    'temporary_action' => 'Tindakan Temporary',
                    'permanent_action' => 'Tindakan Permanent',
                    'deadline'         => 'Deadline',
                    'pic'              => 'PIC',
                ];

                foreach ($required as $field => $label) {
                    if (empty($task->$field)) {
                        $missing[] = $label;
                    }
                }

                if (!empty($missing)) {
                    return $this->json([
                        'success' => false,
                        'message' => 'Form belum lengkap, isi dulu: ' . implode(', ', $missing),
                    ], 422);
                }
            }

            // AUTO STATUS berdasarkan stage baru
            $statusMap = [
                'PLAN'  => 'open',
                'DO'    => 'in_progress',
                'CHECK' => 'in_progress',
                'ACT'   => 'in_progress',
            ];
            $newStatus = $statusMap[$newStage] ?? $task->status;
            $oldStatus = $task->status;

            $user     = auth()->user();
            $userRole = strtolower($user->role);

            $task->update([
                'stage'             => strtolower($request->stage),
                'stage_updated_at'  => Date::Now(),
                'stage_updated_by'  => $user->id ?? null,
                'status'            => $newStatus,
                'status_updated_at' => Date::Now(),
                'status_updated_by' => $user->id ?? null,
            ]);

            TaskHistory::create([
                'task_id'         => $task->id,
                'action'          => 'move_stage',
                'old_stage'       => $oldStage,
                'new_stage'       => $newStage,
                'old_status'      => $oldStatus,
                'new_status'      => $newStatus,
                'changed_by_name' => $user->name ?? $userRole,
                'changed_by_role' => $userRole,
                'notes'           => "Stage: {$oldStage} → {$newStage}, Status: {$oldStatus} → {$newStatus}",
            ]);

            return $this->json($task, 200);

        } catch (\Throwable $e) {
            error_log('[TaskController::updateStage] ' . $e->getMessage()
                . ' @ ' . $e->getFile() . ':' . $e->getLine());

            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ], 500);
        }
    }

    // PATCH /api/tasks/{id}/approve - Approve task (Leader)
    public function approve(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);

            if (!$this->isLeader(auth()->user())) {
                return $this->json(['message' => 'Only leader/admin can approve'], 403);
            }

            // Validasi: harus di ACT dulu
            if (strtoupper($task->stage) !== 'ACT') {
                return $this->json([
                    'success' => false,
                    'message' => 'Task harus di stage ACT dulu sebelum di-approve'
                ], 422);
            }

            // Validasi: cegah double approve
            if ($task->approved_at) {
                return $this->json([
                    'success' => false,
                    'message' => 'Task sudah pernah di-approve'
                ], 422);
            }

            $user     = auth()->user();
            $userRole = strtolower($user->role);
            $oldStatus = $task->status;

            $task->update([
                'status'            => 'done',
                'leader_signature' => $user->name ?? 'Leader',
                'approved_at'       => Date::Now(),
                'approved_by'       => $user->id ?? null,
                'status_updated_at' => Date::Now(),
                'status_updated_by' => $user->id ?? null,
            ]);

            TaskHistory::create([
                'task_id'         => $task->id,
                'action'          => 'approve',
                'old_status'      => $oldStatus,
                'new_status'      => 'done',
                'changed_by_name' => $user->name ?? $userRole,
                'changed_by_role' => $userRole,
                'notes'           => 'Task approved by ' . ($user->name ?? 'Leader'),
            ]);

            return $this->json($task, 200);

        } catch (\Throwable $e) {
            error_log('[TaskController::approve] ' . $e->getMessage()
                . ' @ ' . $e->getFile() . ':' . $e->getLine());

            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ], 500);
        }
    }

    // DELETE /api/tasks/{id} - Delete task (Leader)
    public function destroy(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        // Hanya leader yang bisa delete
        if (!$this->isLeader(auth()->user())) {
            return $this->json([
                'message' => 'Only leader can delete task'
            ], 403);
        }

        $task->delete();

        return $this->json([
            'message' => 'Task deleted successfully'
        ],200);
    }

    public function cancel(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);

            if (!$this->isLeader(auth()->user())) {
                return $this->json(['message' => 'Only leader/admin can cancel'], 403);
            }

            if (strtolower($task->status) === 'done') {
                return $this->json([
                    'success' => false,
                    'message' => 'Task yang sudah done tidak bisa di-cancel'
                ], 422);
            }

            $user     = auth()->user();
            $userRole = strtolower($user->role);
            $oldStatus = $task->status;

            $task->update([
                'status'            => 'cancelled',
                'status_updated_at' => Date::Now(),
                'status_updated_by' => $user->id ?? null,
            ]);

            TaskHistory::create([
                'task_id'         => $task->id,
                'action'          => 'cancel',
                'old_status'      => $oldStatus,
                'new_status'      => 'cancelled',
                'changed_by_name' => $user->name ?? $userRole,
                'changed_by_role' => $userRole,
                'notes'           => 'Task cancelled by ' . ($user->name ?? 'Leader'),
            ]);

            return $this->json($task, 200);

        } catch (\Throwable $e) {
            error_log('[TaskController::cancel] ' . $e->getMessage()
                . ' @ ' . $e->getFile() . ':' . $e->getLine());

            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ], 500);
        }
    }

    public function history($id)
    {
        try {
            $task = Task::query()->withTrashed()->where('id', '=', $id)->first();

            if (!$task) {
                return $this->json(['success' => false, 'message' => 'Task tidak ditemukan'], 404);
            }

            $histories = TaskHistory::query()
                ->where('task_id', '=', $id)
                ->orderBy('created_at', 'asc')   // ← ubah ke ASC: dari lama ke baru
                ->get(\PDO::FETCH_ASSOC);

            // Normalisasi untuk frontend
            $data = array_map(function ($h) {
                // Parse JSON snapshot kalau ada
                $oldValues = null;
                $newValues = null;

                if (!empty($h['old_values'])) {
                    $oldValues = is_array($h['old_values'])
                        ? $h['old_values']
                        : json_decode($h['old_values'], true);
                }
                if (!empty($h['new_values'])) {
                    $newValues = is_array($h['new_values'])
                        ? $h['new_values']
                        : json_decode($h['new_values'], true);
                }

                return [
                    'id'            => (int) $h['id'],
                    'action'        => $h['action'] ?? '',
                    'oldStage'      => $h['old_stage']       ?? null,
                    'newStage'      => $h['new_stage']       ?? null,
                    'oldStatus'     => $h['old_status']      ?? null,
                    'newStatus'     => $h['new_status']      ?? null,
                    'oldValues'     => $oldValues,   // ← TAMBAH
                    'newValues'     => $newValues,   // ← TAMBAH
                    'changedByName' => $h['changed_by_name'] ?? null,
                    'changedByRole' => isset($h['changed_by_role']) ? strtolower($h['changed_by_role']) : null,
                    'notes'         => $h['notes']           ?? null,
                    'createdAt'     => $h['created_at']      ?? null,
                ];
            }, $histories);

            return $this->json($data, 200);

        } catch (\Throwable $e) {
            error_log('[TaskController::history] ' . $e->getMessage()
                . ' @ ' . $e->getFile() . ':' . $e->getLine());

            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ], 500);
        }
    }

    // GET /tasks/report - Aggregated report data
    public function report(Request $request)
    {
        try {
            $query = Task::query();

            if ($request->date_from) {
                $query->where('task_date', '>=', $request->date_from);
            }
            if ($request->date_to) {
                $query->where('task_date', '<=', $request->date_to);
            }
            if ($request->section && $request->section !== 'all') {
                $query->where('section', '=', $request->section);
            }
            if ($request->stage && $request->stage !== 'all') {
                $query->where('stage', '=', $request->stage);
            }
            if ($request->status && $request->status !== 'all') {
                $query->where('status', '=', $request->status);
            }

            $tasks = $query->orderBy('task_date', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->get(\PDO::FETCH_ASSOC);

            $total = count($tasks);
            $byStage = ['plan' => 0, 'do' => 0, 'check' => 0, 'act' => 0];
            $byStatus = ['open' => 0, 'in_progress' => 0, 'done' => 0, 'cancelled' => 0];
            $bySection = [];

            foreach ($tasks as $t) {
                $stage  = strtolower($t['stage']  ?? 'plan');
                $status = strtolower($t['status'] ?? 'open');
                $section = $t['section'] ?? '-';

                $byStage[$stage]   = ($byStage[$stage] ?? 0) + 1;
                $byStatus[$status] = ($byStatus[$status] ?? 0) + 1;
                $bySection[$section] = ($bySection[$section] ?? 0) + 1;
            }

            $normalized = array_map(function ($t) {
                return [
                    'id'              => (int) $t['id'],
                    'taskCode'        => $t['task_code']       ?? '',
                    'operatorName'    => $t['operator_name']   ?? '',
                    'date'            => $t['task_date']       ?? '',
                    'section'         => $t['section']         ?? '',
                    'category'        => $t['category']        ?? '',
                    'picSection'      => $t['pic_section']     ?? '',
                    'problem'         => $t['problem']         ?? '',
                    'tempAction'      => $t['temporary_action']?? '',
                    'permAction'      => $t['permanent_action']?? '',
                    'deadline'        => $t['deadline']        ?? '',
                    'pic'             => $t['pic']             ?? '',
                    'stage'           => strtolower($t['stage']  ?? 'plan'),
                    'status'          => strtolower($t['status'] ?? 'open'),
                    'approvedAt'      => $t['approved_at']     ?? null,
                    'leaderSignature' => $t['ttd_leader']      ?? '',
                ];
            }, $tasks);

            return $this->json([
                'summary' => [
                    'total'      => $total,
                    'by_stage'   => $byStage,
                    'by_status'  => $byStatus,
                    'by_section' => $bySection,
                ],
                'tasks'  => $normalized,
                'period' => [
                    'from' => $request->date_from ?: null,
                    'to'   => $request->date_to   ?: null,
                ],
            ], 200);

        } catch (\Throwable $e) {
            error_log('[TaskController::report] ' . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // GET /api/tasks/stats - Get statistics
    public function stats()
    {
        $stats = [
            'total' => Task::query()->count(),
            'by_stage' => [
                'plan' => Task::query()->where('stage', '=','plan')->count(),
                'do' => Task::query()->where('stage', '=', 'do')->count(),
                'check' => Task::query()->where('stage', '=', 'check')->count(),
                'act' => Task::query()->where('stage', '=', 'act')->count(),
            ],
            'by_status' => [
                'open' => Task::query()->where('status', '=', 'open')->count(),
                'in_progress' => Task::query()->where('status', '=', 'in_progress')->count(),
                'done' => Task::query()->where('status', '=', 'done')->count(),
                'cancelled' => Task::query()->where('status', '=', 'cancelled')->count(),
            ],
        ];

        return $this->json($stats,200);
    }

    // Helper: Generate task code
    private function generateTaskCode()
    {
        $date   = date('Ymd');
        $prefix = "PDCA-{$date}-";

        $lastTask = Task::query()
            ->where('task_code', 'LIKE', "{$prefix}%")
            ->orderBy('task_code', 'desc')
            ->first();

        $lastNumber = 0;
        if ($lastTask) {
            $lastCode = is_object($lastTask)
                ? ($lastTask->task_code ?? '')
                : (is_array($lastTask) ? ($lastTask['task_code'] ?? '') : '');
            $lastNumber = (int) substr($lastCode, -3);
        }

        return $prefix . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    // Helper: Check if user is leader
    private function isLeader($user)
    {
        if (!$user || !isset($user->role)) return false;
        return in_array(strtolower($user->role), ['team_leader','group_leader', 'manager', 'admin'], true);
    }

    private function canEditTask($user)
    {
        if (!$user || !isset($user->role)) return false;
        return in_array(strtolower($user->role), [
            'team_leader', 'group_leader', 'manager', 'admin'
        ], true);
    }
}
