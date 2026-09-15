<?php

namespace App\Controllers;

use App\Models\Task;
use App\Models\TaskAttachment;
use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\Char;
use Bpjs\Framework\Helpers\View;

class TaskAttachmentController extends BaseController
{
    // Controller logic here
    private const ALLOWED_TYPES = ['4m', 'logbook', 'nursecall'];
    private const MAX_SIZE_KB   = 4096; // 4 MB
    private const MIMES = 'pdf,jpg,jpeg,png';

    public function index($taskId)
    {
        $task = TaskAttachment::query();
        return $this->json($task->where('task_id','=', $taskId)->get(),200);
    }

    public function store(Request $req, $taskId)
    {
        $req->validate([
            'file_type' => 'required|in:4m,logbook,nursecall',
            'file'      => 'required|file|mimes:' . self::MIMES . '|max:' . self::MAX_SIZE_KB,
        ]);

        $type = strtolower($req->input('file_type'));
        $file = $req->file('file');

        if (!$file || !isset($file['tmp_name'])) {
            return $this->json(['message' => 'File tidak valid'], 422);
        }

        $task = Task::query()->where('id', '=', $taskId)->first();
        if (!$task) {
            return $this->json(['message' => 'Task tidak ditemukan'], 404);
        }

        $status = strtolower($task->status ?? 'open');
        if (in_array($status, ['done', 'cancelled'], true)) {
            return $this->json([
                'message' => 'Task sudah di-approve, dokumen tidak bisa dihapus.'
            ], 403);
        }

        $existing = TaskAttachment::query()
            ->where('task_id', '=', $task->id)
            ->where('file_type','=',$type)
            ->get();

        foreach ($existing as $ex) {
            if (!empty($ex->file_path)) {
                $oldFile = storage_path($ex->file_path);
                if (is_file($oldFile)) {
                    @unlink($oldFile);
                }
            }
            $ex->delete();
        }

        $ext       = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'bin';
        $filename  = "{$type}.{$ext}";
        $subDir    = "attachments/{$task->id}";
        $targetDir = storage_path($subDir);

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $ok = store(
            $file['tmp_name'],
            $targetDir,
            $filename
        );

        if (!$ok) {
            return $this->json(['message' => 'Gagal menyimpan file'], 500);
        }

        $relativePath = "{$subDir}/{$filename}";

        $att = TaskAttachment::create([
            'task_id'          => $task->id,
            'file_type'        => $type,
            'original_name'    => $file['name'],
            'stored_name'      => $filename,
            'file_path'        => $relativePath,
            'mime_type'        => $file['type'] ?? 'application/octet-stream',
            'file_size'        => $file['size'] ?? 0,
            'uploaded_by'      => auth()->user()->id,
            'uploaded_by_name' => auth()->user()->name ?? 'System',
        ]);

        return $this->json($att, 201);
    }

    public function destroy($taskId, string $type)
    {
        $type = strtolower($type);

        $task = Task::query()->where('id', '=', (int) $taskId)->first();
        if (!$task) {
            return $this->json(['message' => 'Task tidak ditemukan'], 404);
        }

        $status = strtolower($task->status ?? 'open');
        if (in_array($status, ['done', 'cancelled'], true)) {
            return $this->json([
                'message' => 'Task sudah di-approve, dokumen tidak bisa dihapus.'
            ], 403);
        }

        $att = TaskAttachment::query()
            ->where('task_id', '=', (int) $taskId)
            ->where('file_type','=',$type)
            ->first();

        if (!$att) {
            return $this->json(['message' => 'Attachment tidak ditemukan'], 404);
        }

        if (!empty($att->file_path)) {
            $fullPath = storage_path('public/' . $att->file_path);
            if (is_file($fullPath)) {
                @unlink($fullPath);
            }
        }

        $att->delete();

        return $this->json(['ok' => true], 200);
    }
}
