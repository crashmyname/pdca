<?php

namespace App\Controllers;

use App\Models\NursecallRecord;
use App\Models\Record4m;
use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;

class SupportController extends BaseController
{
    // ═══════════════════════════════════════════════════════
    // NURSECALL — FIND
    // ═══════════════════════════════════════════════════════
    public function findNursecall(Request $request)
    {
        $this->closeSessionEarly();

        try {
            $noLane    = trim((string) ($request->no_lane    ?? ''));
            $type      = trim((string) ($request->type       ?? ''));
            $monthYear = trim((string) ($request->month_year ?? ''));

            if (!$noLane || !$type || !$monthYear) {
                return $this->json(null, 200);
            }

            $rows = NursecallRecord::query()
                ->where('no_lane',    '=', $noLane)
                ->where('type',       '=', $type)
                ->where('month_year', '=', $monthYear)
                ->get(\PDO::FETCH_ASSOC);

            if (empty($rows)) {
                return $this->json(null, 200);
            }

            $record = $rows[0];
            $items  = [];
            if (!empty($record['items_json'])) {
                $decoded = json_decode($record['items_json'], true);
                if (is_array($decoded)) $items = $decoded;
            }
            unset($record['items_json']); // optional

            return $this->json([
                'record' => $record,
                'items'  => $items,
            ], 200);

        } catch (\Throwable $e) {
            error_log('[findNursecall] ' . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ═══════════════════════════════════════════════════════
    // NURSECALL — STORE
    // ═══════════════════════════════════════════════════════
    public function storeNursecall(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$this->isLeader($user)) {
                return $this->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }

            $body = $request->all() ?: [];
            if (empty($body) && method_exists($request, 'json')) {
                $body = $request->json();
            }

            $noLane      = trim((string) ($body['no_lane']        ?? ''));
            $type        = trim((string) ($body['type']           ?? ''));
            $monthYear   = trim((string) ($body['month_year']     ?? ''));
            $dateCreated = trim((string) ($body['date_created']   ?? ''));
            $createdName = trim((string) ($body['created_by_name'] ?? ''));
            $approvedName= trim((string) ($body['approved_by_name']?? ''));
            $items       = is_array($body['items'] ?? null) ? $body['items'] : [];
            $footer      = is_array($body['footer'] ?? null) ? $body['footer'] : [];

            if (!$noLane || !$type || !$monthYear || !$dateCreated) {
                return $this->json(['success' => false, 'message' => 'Header belum lengkap'], 422);
            }
            if (empty($items)) {
                return $this->json(['success' => false, 'message' => 'Content minimal 1 baris'], 422);
            }

            // Sanitize items — buang baris kosong
            $cleanItems = [];
            foreach ($items as $item) {
                if (!is_array($item)) continue;
                $problem = trim((string) ($item['problem'] ?? ''));
                $action  = trim((string) ($item['action']  ?? ''));
                $nurse   = trim((string) ($item['nurse_leader'] ?? ''));
                if ($problem === '' && $action === '' && $nurse === '') continue;

                $cleanItems[] = [
                    'task_id'      => !empty($item['task_id']) ? (int) $item['task_id'] : null,
                    'date'         => !empty($item['date']) ? $item['date'] : null,
                    'time'         => !empty($item['time']) ? $item['time'] : null,
                    'problem'      => $problem ?: null,
                    'source'       => $this->enumOrNull($item['source'] ?? null,
                                        ['part','machine','man_power','tool','material','dll']),
                    'action'       => $action ?: null,
                    'nurse_leader' => $nurse ?: null,
                    'status'       => $this->enumOrNull($item['status'] ?? null, ['O', 'X']),
                    'judgement'    => $this->enumOrNull($item['judgement'] ?? null, ['O', 'X']),
                    'group_leader' => trim((string) ($item['group_leader'] ?? '')) ?: null,
                ];
            }

            if (empty($cleanItems)) {
                return $this->json(['success' => false, 'message' => 'Semua baris content kosong'], 422);
            }

            // Cek existing
            $existingRows = NursecallRecord::query()
                ->where('no_lane',    '=', $noLane)
                ->where('type',       '=', $type)
                ->where('month_year', '=', $monthYear)
                ->get(\PDO::FETCH_ASSOC);

            $existingId = !empty($existingRows) ? (int) $existingRows[0]['id'] : null;

            $data = [
                'date_created'         => $dateCreated,
                'created_by_name'      => $createdName ?: ($user->name ?? ''),
                'approved_by_name'     => $approvedName ?: null,
                'footer_comment'       => trim((string) ($footer['comment']           ?? '')) ?: null,
                'footer_date'          => $footer['date'] ?? null ?: null,
                'footer_created_name'  => trim((string) ($footer['created_by_name']   ?? '')) ?: null,
                'footer_approved_name' => trim((string) ($footer['approved_by_name']  ?? '')) ?: null,
                'items_json'           => json_encode($cleanItems, JSON_UNESCAPED_UNICODE),
            ];

            $recordId = null;
            $isNew    = false;

            if ($existingId) {
                // UPDATE
                $record = NursecallRecord::findOrFail($existingId);
                $record->update($data);
                $recordId = $existingId;
            } else {
                // CREATE
                $data['no_lane']    = $noLane;
                $data['type']       = $type;
                $data['month_year'] = $monthYear;
                $data['created_by'] = $user->id ?? null;

                $record   = NursecallRecord::create($data);
                $recordId = isset($record->id) ? (int) $record->id : 0;

                if (!$recordId) {
                    $newRows = NursecallRecord::query()
                        ->where('no_lane',    '=', $noLane)
                        ->where('type',       '=', $type)
                        ->where('month_year', '=', $monthYear)
                        ->get(\PDO::FETCH_ASSOC);
                    $recordId = !empty($newRows) ? (int) $newRows[0]['id'] : 0;
                }
                $isNew = true;
            }

            if (!$recordId) {
                return $this->json(['success' => false, 'message' => 'Gagal mendapatkan record ID'], 500);
            }

            return $this->json([
                'success'   => true,
                'message'   => $isNew ? 'Nursecall dibuat' : 'Nursecall diperbarui',
                'is_new'    => $isNew,
                'record_id' => $recordId,
                'items_count' => count($cleanItems),
            ], $isNew ? 201 : 200);

        } catch (\Throwable $e) {
            error_log('[storeNursecall] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ], 500);
        }
    }

    // ═══════════════════════════════════════════════════════
    // 4M — FIND
    // ═══════════════════════════════════════════════════════
    public function findRecord4m(Request $request)
    {
        $this->closeSessionEarly();

        try {
            $noLane    = trim((string) ($request->no_lane    ?? ''));
            $type      = trim((string) ($request->type       ?? ''));
            $monthYear = trim((string) ($request->month_year ?? ''));

            if (!$noLane || !$type || !$monthYear) {
                return $this->json(null, 200);
            }

            $rows = Record4m::query()
                ->where('no_lane',    '=', $noLane)
                ->where('type',       '=', $type)
                ->where('month_year', '=', $monthYear)
                ->get(\PDO::FETCH_ASSOC);

            if (empty($rows)) {
                return $this->json(null, 200);
            }

            $record = $rows[0];
            $items  = [];
            if (!empty($record['items_json'])) {
                $decoded = json_decode($record['items_json'], true);
                if (is_array($decoded)) $items = $decoded;
            }
            unset($record['items_json']);

            return $this->json([
                'record' => $record,
                'items'  => $items,
            ], 200);

        } catch (\Throwable $e) {
            error_log('[findRecord4m] ' . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ═══════════════════════════════════════════════════════
    // 4M — STORE
    // ═══════════════════════════════════════════════════════
    public function storeRecord4m(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$this->isLeader($user)) {
                return $this->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }

            $body = $request->all() ?: [];
            if (empty($body) && method_exists($request, 'json')) {
                $body = $request->json();
            }

            $noLane      = trim((string) ($body['no_lane']        ?? ''));
            $type        = trim((string) ($body['type']           ?? ''));
            $monthYear   = trim((string) ($body['month_year']     ?? ''));
            $date        = trim((string) ($body['date']           ?? ''));
            $createdName = trim((string) ($body['created_by_name'] ?? ''));
            $approvedName= trim((string) ($body['approved_by_name']?? ''));
            $items       = is_array($body['items'] ?? null) ? $body['items'] : [];

            if (!$noLane || !$type || !$monthYear || !$date) {
                return $this->json(['success' => false, 'message' => 'Header belum lengkap'], 422);
            }
            if (empty($items)) {
                return $this->json(['success' => false, 'message' => 'Content minimal 1 baris'], 422);
            }

            $cleanItems = [];
            foreach ($items as $item) {
                if (!is_array($item)) continue;
                $problem  = trim((string) ($item['problem']  ?? ''));
                $category = $this->enumOrNull($item['category'] ?? null,
                                ['man','machine','material','methode']);
                if ($problem === '' && !$category) continue;

                $cleanItems[] = [
                    'task_id'      => !empty($item['task_id']) ? (int) $item['task_id'] : null,
                    'date'         => !empty($item['date']) ? $item['date'] : null,
                    'shift'        => trim((string) ($item['shift'] ?? '')) ?: null,
                    'problem'      => $problem ?: null,
                    'category'     => $category,
                    'team_leader'  => trim((string) ($item['team_leader'] ?? '')) ?: null,
                    'group_leader' => trim((string) ($item['group_leader'] ?? '')) ?: null,
                ];
            }

            if (empty($cleanItems)) {
                return $this->json(['success' => false, 'message' => 'Semua baris content kosong'], 422);
            }

            $existingRows = Record4m::query()
                ->where('no_lane',    '=', $noLane)
                ->where('type',       '=', $type)
                ->where('month_year', '=', $monthYear)
                ->get(\PDO::FETCH_ASSOC);

            $existingId = !empty($existingRows) ? (int) $existingRows[0]['id'] : null;

            $data = [
                'date'             => $date,
                'created_by_name'  => $createdName ?: ($user->name ?? ''),
                'approved_by_name' => $approvedName ?: null,
                'items_json'       => json_encode($cleanItems, JSON_UNESCAPED_UNICODE),
            ];

            $recordId = null;
            $isNew    = false;

            if ($existingId) {
                $record = Record4m::findOrFail($existingId);
                $record->update($data);
                $recordId = $existingId;
            } else {
                $data['no_lane']    = $noLane;
                $data['type']       = $type;
                $data['month_year'] = $monthYear;
                $data['created_by'] = $user->id ?? null;

                $record   = Record4m::create($data);
                $recordId = isset($record->id) ? (int) $record->id : 0;

                if (!$recordId) {
                    $newRows = Record4m::query()
                        ->where('no_lane',    '=', $noLane)
                        ->where('type',       '=', $type)
                        ->where('month_year', '=', $monthYear)
                        ->get(\PDO::FETCH_ASSOC);
                    $recordId = !empty($newRows) ? (int) $newRows[0]['id'] : 0;
                }
                $isNew = true;
            }

            if (!$recordId) {
                return $this->json(['success' => false, 'message' => 'Gagal mendapatkan record ID'], 500);
            }

            return $this->json([
                'success'   => true,
                'message'   => $isNew ? 'Record 4M dibuat' : 'Record 4M diperbarui',
                'is_new'    => $isNew,
                'record_id' => $recordId,
                'items_count' => count($cleanItems),
            ], $isNew ? 201 : 200);

        } catch (\Throwable $e) {
            error_log('[storeRecord4m] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ], 500);
        }
    }

    public function latestNursecall(Request $request)
    {
        $this->closeSessionEarly();

        try {
            $rows = NursecallRecord::query()
                ->orderBy('month_year', 'desc')
                ->orderBy('date_created', 'desc')
                ->limit(1)
                ->get(\PDO::FETCH_ASSOC);

            if (empty($rows)) {
                return $this->json(null, 200);
            }

            $record = $rows[0];
            $items  = [];
            if (!empty($record['items_json'])) {
                $decoded = json_decode($record['items_json'], true);
                if (is_array($decoded)) $items = $decoded;
            }
            unset($record['items_json']);

            return $this->json([
                'record' => $record,
                'items'  => $items,
            ], 200);

        } catch (\Throwable $e) {
            error_log('[latestNursecall] ' . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ═══════════════════════════════════════════════════════
    // 4M — LATEST
    // ═══════════════════════════════════════════════════════
    public function latestRecord4m(Request $request)
    {
        $this->closeSessionEarly();

        try {
            $rows = Record4m::query()
                ->orderBy('month_year', 'desc')
                ->orderBy('date', 'desc')
                ->limit(1)
                ->get(\PDO::FETCH_ASSOC);

            if (empty($rows)) {
                return $this->json(null, 200);
            }

            $record = $rows[0];
            $items  = [];
            if (!empty($record['items_json'])) {
                $decoded = json_decode($record['items_json'], true);
                if (is_array($decoded)) $items = $decoded;
            }
            unset($record['items_json']);

            return $this->json([
                'record' => $record,
                'items'  => $items,
            ], 200);

        } catch (\Throwable $e) {
            error_log('[latestRecord4m] ' . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function byLaneNursecall(Request $request)
{
    $this->closeSessionEarly();

    try {
        $noLane    = trim((string) ($request->no_lane    ?? ''));
        $monthYear = trim((string) ($request->month_year ?? ''));

        if (!$noLane) {
            return $this->json(null, 200);
        }

        $record = null;

        // 1. Coba exact match (bulan yang sama)
        if ($monthYear) {
            $exact = NursecallRecord::query()
                ->where('no_lane',    '=', $noLane)
                ->where('month_year', '=', $monthYear)
                ->limit(1)
                ->get(\PDO::FETCH_ASSOC);

            if (!empty($exact)) {
                $record = $exact[0];
            }
        }

        // 2. Fallback: latest record untuk lane ini (bulan apapun)
        if (!$record) {
            $latest = NursecallRecord::query()
                ->where('no_lane', '=', $noLane)
                ->orderBy('month_year', 'desc')
                ->orderBy('date_created', 'desc')
                ->limit(1)
                ->get(\PDO::FETCH_ASSOC);

            if (!empty($latest)) {
                $record = $latest[0];
            }
        }

        if (!$record) {
            return $this->json(null, 200);
        }

        $items = [];
        if (!empty($record['items_json'])) {
            $decoded = json_decode($record['items_json'], true);
            if (is_array($decoded)) $items = $decoded;
        }
        unset($record['items_json']);

        return $this->json([
            'record' => $record,
            'items'  => $items,
            'is_same_month' => $monthYear && $record['month_year'] === $monthYear,
        ], 200);

    } catch (\Throwable $e) {
        error_log('[byLaneNursecall] ' . $e->getMessage());
        return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

// ═══════════════════════════════════════════════════════
// 4M — BY LANE
// ═══════════════════════════════════════════════════════
public function byLaneRecord4m(Request $request)
{
    $this->closeSessionEarly();

    try {
        $noLane    = trim((string) ($request->no_lane    ?? ''));
        $monthYear = trim((string) ($request->month_year ?? ''));

        if (!$noLane) {
            return $this->json(null, 200);
        }

        $record = null;

        if ($monthYear) {
            $exact = Record4m::query()
                ->where('no_lane',    '=', $noLane)
                ->where('month_year', '=', $monthYear)
                ->limit(1)
                ->get(\PDO::FETCH_ASSOC);

            if (!empty($exact)) {
                $record = $exact[0];
            }
        }

        if (!$record) {
            $latest = Record4m::query()
                ->where('no_lane', '=', $noLane)
                ->orderBy('month_year', 'desc')
                ->orderBy('date', 'desc')
                ->limit(1)
                ->get(\PDO::FETCH_ASSOC);

            if (!empty($latest)) {
                $record = $latest[0];
            }
        }

        if (!$record) {
            return $this->json(null, 200);
        }

        $items = [];
        if (!empty($record['items_json'])) {
            $decoded = json_decode($record['items_json'], true);
            if (is_array($decoded)) $items = $decoded;
        }
        unset($record['items_json']);

        return $this->json([
            'record' => $record,
            'items'  => $items,
            'is_same_month' => $monthYear && $record['month_year'] === $monthYear,
        ], 200);

    } catch (\Throwable $e) {
        error_log('[byLaneRecord4m] ' . $e->getMessage());
        return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

// ═══════════════════════════════════════════════════════
// NURSECALL — LIST LANES (untuk datalist suggestion)
// ═══════════════════════════════════════════════════════
public function listNursecallLanes(Request $request)
{
    $this->closeSessionEarly();

    try {
        $rows = NursecallRecord::query()
            ->orderBy('no_lane', 'asc')
            ->get(\PDO::FETCH_ASSOC);

        $lanes = [];
        foreach ($rows as $r) {
            if (!empty($r['no_lane']) && !in_array($r['no_lane'], $lanes, true)) {
                $lanes[] = $r['no_lane'];
            }
        }

        return $this->json($lanes, 200);

    } catch (\Throwable $e) {
        return $this->json([], 200);
    }
}

// ═══════════════════════════════════════════════════════
// 4M — LIST LANES
// ═══════════════════════════════════════════════════════
public function listRecord4mLanes(Request $request)
{
    $this->closeSessionEarly();

    try {
        $rows = Record4m::query()
            ->orderBy('no_lane', 'asc')
            ->get(\PDO::FETCH_ASSOC);

        $lanes = [];
        foreach ($rows as $r) {
            if (!empty($r['no_lane']) && !in_array($r['no_lane'], $lanes, true)) {
                $lanes[] = $r['no_lane'];
            }
        }

        return $this->json($lanes, 200);

    } catch (\Throwable $e) {
        return $this->json([], 200);
    }
}

    // ═══════════════════════════════════════════════════════
    // HELPERS
    // ═══════════════════════════════════════════════════════
    private function isLeader($user): bool
    {
        if (!$user || !isset($user->role)) return false;
        return in_array(strtolower($user->role),
            ['team_leader', 'group_leader', 'manager', 'admin'], true);
    }

    private function enumOrNull($value, array $allowed)
    {
        $v = trim((string) ($value ?? ''));
        if ($v === '') return null;
        return in_array($v, $allowed, true) ? $v : null;
    }

    protected function closeSessionEarly()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
    }
}