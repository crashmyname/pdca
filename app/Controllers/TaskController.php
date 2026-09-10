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
                ->orWhere('task_code', 'LIKE', "%{$search}%");
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

            $task = Task::create([
                'task_code'       => $taskCode,
                'operator_name'   => $request->operator_name,
                'task_date'       => $request->task_date,
                'section'         => $request->section,
                'problem'         => $request->problem,
                'stage'           => 'plan',
                'status'          => 'open',
                'created_by_name' => $request->operator_name,
                'created_by_role' => 'operator',
                'created_by'      => $request->operator_name,
            ]);

            TaskHistory::create([
                'task_id'         => $task->id,
                'action'          => 'create',
                'new_stage'       => 'plan',
                'new_status'      => 'open',
                'changed_by_name' => $request->operator_name,
                'changed_by_role' => 'operator',
                'notes'           => 'Task created by ' . $request->operator_name,
            ]);

            return $this->json($task, 201);

        } catch (\Throwable $e) {
            // ⬅ pakai \Throwable, bukan \Exception, supaya Error juga ketangkep
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

        // Hanya leader yang bisa update leader fields
        if (!$this->isLeader(auth()->user())) {
            return $this->json([
                'message' => 'Only leader can update this task'
            ], 403);
        }
        
        $task->update([
            'temporary_action' => $request->temporary_action,
            'permanent_action' => $request->permanent_action,
            'deadline' => $request->deadline,
            'pic' => $request->pic,
            'updated_by' => auth()->user()->id ?? null,
        ]);

        // Log history
        TaskHistory::create([
            'task_id' => $task->id,
            'action' => 'update',
            'changed_by_name' => auth()->user()->name,
            'changed_by_role' => 'leader',
            'notes' => 'Leader fields updated',
        ]);

        return $this->json($task,200);
    }

    // PATCH /api/tasks/{id}/stage - Update stage (Leader only)
    public function updateStage(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);

            // ⬇ pakai isLeader() yang sudah di-fix
            if (!$this->isLeader(auth()->user())) {
                return $this->json(['message' => 'Only leader/admin can change stage'], 403);
            }

            $user     = auth()->user();
            $userRole = strtolower($user->role);   // ⬅ lowercase untuk konsistensi

            $oldStage = $task->stage;

            $task->update([
                'stage'            => $request->stage,
                'stage_updated_at' => date('Y-m-d H:i:s'),
                'stage_updated_by' => $user->id ?? null,
            ]);

            TaskHistory::create([
                'task_id'         => $task->id,
                'action'          => 'move_stage',
                'old_stage'       => $oldStage,
                'new_stage'       => $request->stage,
                'changed_by_name' => $user->name ?? $userRole,
                'changed_by_role' => $userRole,              // ⬅ pakai role asli, bukan hardcode
                'notes'           => "Stage changed from {$oldStage} to {$request->stage}",
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
        $task = Task::findOrFail($id);

        // Hanya leader yang bisa approve
        if (!$this->isLeader(auth()->user())) {
            return $this->json([
                'message' => 'Only leader can approve task'
            ], 403);
        }

        $task->update([
            'status' => 'done',
            'ttd_leader' => auth()->user()->full_name,
            'approved_at' => Date::Now(),
            'approved_by' => auth()->user()->id,
            'status_updated_at' => Date::Now(),
            'status_updated_by' => auth()->user()->id,
        ]);

        // Log history
        TaskHistory::create([
            'task_id' => $task->id,
            'action' => 'approve',
            'old_status' => $task->getOriginal('status'),
            'new_status' => 'done',
            'changed_by' => auth()->user()->id,
            'changed_by_name' => auth()->user()->name,
            'changed_by_role' => 'leader',
            'notes' => 'Task approved by ' . auth()->user()->name,
        ]);

        return response()->json($task);
    }

    // DELETE /api/tasks/{id} - Delete task (Leader)
    public function destroy(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        // Hanya leader yang bisa delete
        if (!$this->isLeader(auth()->user())) {
            return response()->json([
                'message' => 'Only leader can delete task'
            ], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
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
        return in_array(strtolower($user->role), ['leader', 'admin'], true);
    }
}
