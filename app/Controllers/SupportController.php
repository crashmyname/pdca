<?php

namespace App\Controllers;

use App\Models\NursecallRecord;
use App\Models\NursecallItem;
use App\Models\Record4m;
use App\Models\Record4mItem;
use App\Services\AuditService;
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

            $records = NursecallRecord::query()
                ->where('no_lane',    '=', $noLane)
                ->where('type',       '=', $type)
                ->where('month_year', '=', $monthYear)
                ->get(\PDO::FETCH_ASSOC);

            if (empty($records)) {
                return $this->json(null, 200);
            }

            $record   = $records[0];
            $recordId = (int) $record['id'];

            $items = NursecallItem::query()
                ->where('record_id', '=', $recordId)
                ->orderBy('row_no', 'asc')
                ->get(\PDO::FETCH_ASSOC);

            return $this->json([
                'record' => $record,
                'items'  => $items ?: [],
            ], 200);

        } catch (\Throwable $e) {
            error_log('[findNursecall] ' . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ═══════════════════════════════════════════════════════
    // NURSECALL — BY LANE
    // ═══════════════════════════════════════════════════════
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

            if ($monthYear) {
                $exact = NursecallRecord::query()
                    ->where('no_lane',    '=', $noLane)
                    ->where('month_year', '=', $monthYear)
                    ->limit(1)
                    ->get(\PDO::FETCH_ASSOC);

                if (!empty($exact)) $record = $exact[0];
            }

            if (!$record) {
                $latest = NursecallRecord::query()
                    ->where('no_lane', '=', $noLane)
                    ->orderBy('month_year', 'desc')
                    ->orderBy('date_created', 'desc')
                    ->limit(1)
                    ->get(\PDO::FETCH_ASSOC);

                if (!empty($latest)) $record = $latest[0];
            }

            if (!$record) {
                return $this->json(null, 200);
            }

            $recordId = (int) $record['id'];

            $items = NursecallItem::query()
                ->where('record_id', '=', $recordId)
                ->orderBy('row_no', 'asc')
                ->get(\PDO::FETCH_ASSOC);

            return $this->json([
                'record'        => $record,
                'items'         => $items ?: [],
                'is_same_month' => $monthYear && $record['month_year'] === $monthYear,
            ], 200);

        } catch (\Throwable $e) {
            error_log('[byLaneNursecall] ' . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ═══════════════════════════════════════════════════════
    // NURSECALL — LIST LANES
    // ═══════════════════════════════════════════════════════
    public function listNursecallLanes(Request $request)
    {
        $this->closeSessionEarly();

        try {
            $rows = NursecallRecord::query()->get(\PDO::FETCH_ASSOC);

            $lanes = [];
            foreach ($rows as $r) {
                $ln = trim((string) ($r['no_lane'] ?? ''));
                if ($ln !== '' && !in_array($ln, $lanes, true)) {
                    $lanes[] = $ln;
                }
            }
            sort($lanes);

            return $this->json($lanes, 200);

        } catch (\Throwable $e) {
            return $this->json([], 200);
        }
    }

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

            $recordIdFromFront = (int) ($body['record_id'] ?? 0);
            $noLane      = trim((string) ($body['no_lane']         ?? ''));
            $type        = trim((string) ($body['type']            ?? ''));
            $monthYear   = trim((string) ($body['month_year']      ?? ''));
            $dateCreated = trim((string) ($body['date_created']    ?? ''));
            $createdName = trim((string) ($body['created_by_name'] ?? ''));
            $approvedName= trim((string) ($body['approved_by_name']?? ''));
            $items       = is_array($body['items']  ?? null) ? $body['items']  : [];
            $footer      = is_array($body['footer'] ?? null) ? $body['footer'] : [];

            if (!$noLane || !$type || !$monthYear || !$dateCreated) {
                return $this->json(['success' => false, 'message' => 'Header belum lengkap'], 422);
            }
            if (empty($items)) {
                return $this->json(['success' => false, 'message' => 'Content minimal 1 baris'], 422);
            }

            $existingId = null;

            if ($recordIdFromFront) {
                $recCheck = NursecallRecord::query()
                    ->where('id', '=', $recordIdFromFront)
                    ->limit(1)
                    ->get(\PDO::FETCH_ASSOC);
                if (!empty($recCheck)) {
                    $existingId = (int) $recCheck[0]['id'];
                }
            }

            if (!$existingId) {
                $existingRows = NursecallRecord::query()
                    ->where('no_lane',    '=', $noLane)
                    ->where('type',       '=', $type)
                    ->where('month_year', '=', $monthYear)
                    ->get(\PDO::FETCH_ASSOC);
                $existingId = !empty($existingRows) ? (int) $existingRows[0]['id'] : null;
            }

            $headerData = [
                'no_lane'              => $noLane,
                'type'                 => $type,
                'month_year'           => $monthYear,
                'date_created'         => $dateCreated,
                'created_by_name'      => $createdName ?: ($user->name ?? ''),
                'approved_by_name'     => $approvedName ?: null,
                'footer_comment'       => trim((string) ($footer['comment']           ?? '')) ?: null,
                'footer_date'          => $footer['date'] ?? null ?: null,
                'footer_created_name'  => trim((string) ($footer['created_by_name']   ?? '')) ?: null,
                'footer_approved_name' => trim((string) ($footer['approved_by_name']  ?? '')) ?: null,
            ];

            $recordId = null;
            $isNew    = false;

            if ($existingId) {
                $record = NursecallRecord::findOrFail($existingId);

                // ===== AUDIT LOG: UPDATE =====
                $oldSnapShot = AuditService::snapshot(NursecallRecord::class, $existingId);

                $record->update($headerData);

                $newSnapShot = AuditService::snapshot(NursecallRecord::class, $existingId);

                AuditService::log(
                    'nursecall_records',
                    (int) $existingId,
                    'update',
                    $oldSnapShot,
                    $newSnapShot,
                    ['notes' => 'Nursecall Record Update: ' . ($newSnapShot['id'] ?? $existingId)]
                );
                // =============================

                $recordId = $existingId;

                NursecallItem::deleteWhere(['record_id' => $existingId]);

            } else {
                $headerData['created_by'] = $user->id ?? null;

                $record   = NursecallRecord::create($headerData);
                $recordId = ($record && isset($record->id)) ? (int) $record->id : 0;

                if (!$recordId) {
                    $newRows = NursecallRecord::query()
                        ->where('no_lane',    '=', $noLane)
                        ->where('type',       '=', $type)
                        ->where('month_year', '=', $monthYear)
                        ->get(\PDO::FETCH_ASSOC);
                    $recordId = !empty($newRows) ? (int) $newRows[0]['id'] : 0;
                }

                // ===== AUDIT LOG: CREATE =====
                if ($recordId) {
                    $newSnapShot = AuditService::snapshot(NursecallRecord::class, $recordId);

                    AuditService::log(
                        'nursecall_records',
                        (int) $recordId,
                        'create',
                        null,
                        $newSnapShot,
                        ['notes' => 'Nursecall Record Create: ' . ($newSnapShot['id'] ?? $recordId)]
                    );
                }
                // =============================

                $isNew = true;
            }

            if (!$recordId) {
                return $this->json(['success' => false, 'message' => 'Gagal mendapatkan record ID'], 500);
            }

            $now = date('Y-m-d H:i:s');
            $itemsData = [];
            $rowNo = 1;

            foreach ($items as $item) {
                if (!is_array($item)) continue;

                $problem = trim((string) ($item['problem'] ?? ''));
                $action  = trim((string) ($item['action']  ?? ''));
                $nurse   = trim((string) ($item['nurse_leader'] ?? ''));

                if ($problem === '' && $action === '' && $nurse === '') continue;

                $itemsData[] = [
                    'record_id'    => $recordId,
                    'task_id'      => !empty($item['task_id']) ? (int) $item['task_id'] : null,
                    'row_no'       => $rowNo,
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
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
                $rowNo++;
            }

            if (empty($itemsData)) {
                return $this->json(['success' => false, 'message' => 'Semua baris content kosong'], 422);
            }

            $result = NursecallItem::insertBatch($itemsData);

            if ($result === false) {
                return $this->json([
                    'success' => false,
                    'message' => 'Gagal insert items ke database',
                ], 500);
            }

            return $this->json([
                'success'     => true,
                'message'     => $isNew ? 'Nursecall dibuat' : 'Nursecall diperbarui',
                'is_new'      => $isNew,
                'record_id'   => $recordId,
                'items_count' => count($itemsData),
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

            $records = Record4m::query()
                ->where('no_lane',    '=', $noLane)
                ->where('type',       '=', $type)
                ->where('month_year', '=', $monthYear)
                ->get(\PDO::FETCH_ASSOC);

            if (empty($records)) {
                return $this->json(null, 200);
            }

            $record   = $records[0];
            $recordId = (int) $record['id'];

            $items = Record4mItem::query()
                ->where('record_id', '=', $recordId)
                ->orderBy('row_no', 'asc')
                ->get(\PDO::FETCH_ASSOC);

            return $this->json([
                'record' => $record,
                'items'  => $items ?: [],
            ], 200);

        } catch (\Throwable $e) {
            error_log('[findRecord4m] ' . $e->getMessage());
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

                if (!empty($exact)) $record = $exact[0];
            }

            if (!$record) {
                $latest = Record4m::query()
                    ->where('no_lane', '=', $noLane)
                    ->orderBy('month_year', 'desc')
                    ->orderBy('date', 'desc')
                    ->limit(1)
                    ->get(\PDO::FETCH_ASSOC);

                if (!empty($latest)) $record = $latest[0];
            }

            if (!$record) {
                return $this->json(null, 200);
            }

            $recordId = (int) $record['id'];

            $items = Record4mItem::query()
                ->where('record_id', '=', $recordId)
                ->orderBy('row_no', 'asc')
                ->get(\PDO::FETCH_ASSOC);

            return $this->json([
                'record'        => $record,
                'items'         => $items ?: [],
                'is_same_month' => $monthYear && $record['month_year'] === $monthYear,
            ], 200);

        } catch (\Throwable $e) {
            error_log('[byLaneRecord4m] ' . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ═══════════════════════════════════════════════════════
    // 4M — LIST LANES
    // ═══════════════════════════════════════════════════════
    public function listRecord4mLanes(Request $request)
    {
        $this->closeSessionEarly();

        try {
            $rows = Record4m::query()->get(\PDO::FETCH_ASSOC);

            $lanes = [];
            foreach ($rows as $r) {
                $ln = trim((string) ($r['no_lane'] ?? ''));
                if ($ln !== '' && !in_array($ln, $lanes, true)) {
                    $lanes[] = $ln;
                }
            }
            sort($lanes);

            return $this->json($lanes, 200);

        } catch (\Throwable $e) {
            return $this->json([], 200);
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

            $recordIdFromFront = (int) ($body['record_id'] ?? 0);
            $noLane      = trim((string) ($body['no_lane']         ?? ''));
            $type        = trim((string) ($body['type']            ?? ''));
            $monthYear   = trim((string) ($body['month_year']      ?? ''));
            $date        = trim((string) ($body['date']            ?? ''));
            $createdName = trim((string) ($body['created_by_name'] ?? ''));
            $approvedName= trim((string) ($body['approved_by_name']?? ''));
            $items       = is_array($body['items'] ?? null) ? $body['items'] : [];

            if (!$noLane || !$type || !$monthYear || !$date) {
                return $this->json(['success' => false, 'message' => 'Header belum lengkap'], 422);
            }
            if (empty($items)) {
                return $this->json(['success' => false, 'message' => 'Content minimal 1 baris'], 422);
            }

            $existingId = null;

            if ($recordIdFromFront) {
                $recCheck = Record4m::query()
                    ->where('id', '=', $recordIdFromFront)
                    ->limit(1)
                    ->get(\PDO::FETCH_ASSOC);
                if (!empty($recCheck)) {
                    $existingId = (int) $recCheck[0]['id'];
                }
            }

            if (!$existingId) {
                $existingRows = Record4m::query()
                    ->where('no_lane',    '=', $noLane)
                    ->where('type',       '=', $type)
                    ->where('month_year', '=', $monthYear)
                    ->get(\PDO::FETCH_ASSOC);
                $existingId = !empty($existingRows) ? (int) $existingRows[0]['id'] : null;
            }

            $headerData = [
                'no_lane'          => $noLane,
                'type'             => $type,
                'month_year'       => $monthYear,
                'date'             => $date,
                'created_by_name'  => $createdName ?: ($user->name ?? ''),
                'approved_by_name' => $approvedName ?: null,
            ];

            $recordId = null;
            $isNew    = false;

            if ($existingId) {
                $record = Record4m::findOrFail($existingId);

                // ===== AUDIT LOG: UPDATE =====
                $oldSnapShot = AuditService::snapshot(Record4m::class, $existingId);

                $record->update($headerData);

                $newSnapShot = AuditService::snapshot(Record4m::class, $existingId);

                AuditService::log(
                    'record4m',
                    (int) $existingId,
                    'update',
                    $oldSnapShot,
                    $newSnapShot,
                    ['notes' => 'Record 4M Update: ' . ($newSnapShot['id'] ?? $existingId)]
                );
                // =============================

                $recordId = $existingId;

                Record4mItem::deleteWhere(['record_id' => $existingId]);

            } else {
                $headerData['created_by'] = $user->id ?? null;

                $record   = Record4m::create($headerData);
                $recordId = ($record && isset($record->id)) ? (int) $record->id : 0;

                if (!$recordId) {
                    $newRows = Record4m::query()
                        ->where('no_lane',    '=', $noLane)
                        ->where('type',       '=', $type)
                        ->where('month_year', '=', $monthYear)
                        ->get(\PDO::FETCH_ASSOC);
                    $recordId = !empty($newRows) ? (int) $newRows[0]['id'] : 0;
                }

                // ===== AUDIT LOG: CREATE =====
                if ($recordId) {
                    $newSnapShot = AuditService::snapshot(Record4m::class, $recordId);

                    AuditService::log(
                        'record4m',
                        (int) $recordId,
                        'create',
                        null,
                        $newSnapShot,
                        ['notes' => 'Record 4M Create: ' . ($newSnapShot['id'] ?? $recordId)]
                    );
                }
                // =============================

                $isNew = true;
            }

            if (!$recordId) {
                return $this->json(['success' => false, 'message' => 'Gagal mendapatkan record ID'], 500);
            }

            $now = date('Y-m-d H:i:s');
            $itemsData = [];
            $rowNo = 1;

            foreach ($items as $item) {
                if (!is_array($item)) continue;

                $problem  = trim((string) ($item['problem']  ?? ''));
                $category = $this->enumOrNull($item['category'] ?? null,
                                ['man','machine','material','methode']);

                if ($problem === '' && !$category) continue;

                $itemsData[] = [
                    'record_id'    => $recordId,
                    'task_id'      => !empty($item['task_id']) ? (int) $item['task_id'] : null,
                    'row_no'       => $rowNo,
                    'date'         => !empty($item['date']) ? $item['date'] : null,
                    'shift'        => trim((string) ($item['shift'] ?? '')) ?: null,
                    'problem'      => $problem ?: null,
                    'category'     => $category,
                    'team_leader'  => trim((string) ($item['team_leader'] ?? '')) ?: null,
                    'group_leader' => trim((string) ($item['group_leader'] ?? '')) ?: null,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
                $rowNo++;
            }

            if (empty($itemsData)) {
                return $this->json(['success' => false, 'message' => 'Semua baris content kosong'], 422);
            }

            $result = Record4mItem::insertBatch($itemsData);

            if ($result === false) {
                return $this->json([
                    'success' => false,
                    'message' => 'Gagal insert items ke database',
                ], 500);
            }

            return $this->json([
                'success'     => true,
                'message'     => $isNew ? 'Record 4M dibuat' : 'Record 4M diperbarui',
                'is_new'      => $isNew,
                'record_id'   => $recordId,
                'items_count' => count($itemsData),
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

    public function showNursecall($id)
    {
        try {
            $record = NursecallRecord::findOrFail($id);

            $items = NursecallItem::query()
                ->where('record_id', '=', $id)
                ->orderBy('row_no', 'asc')
                ->get(\PDO::FETCH_ASSOC);

            return $this->json([
                'record' => $record->toCleanArray(),
                'items'  => $items ?: [],
            ], 200);

        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    // ============================================================
    // GET /support/4m/{id}
    // ============================================================
    public function showRecord4m($id)
    {
        try {
            $record = Record4m::findOrFail($id);

            $items = Record4mItem::query()
                ->where('record_id', '=', $id)
                ->orderBy('row_no', 'asc')
                ->get(\PDO::FETCH_ASSOC);

            return $this->json([
                'record' => $record->toCleanArray(),
                'items'  => $items ?: [],
            ], 200);

        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function assignedTaskIds(Request $request)
    {
        $this->closeSessionEarly();

        try {
            // Nursecall
            $ncRows = NursecallItem::query()
                ->whereNotNull('task_id')
                ->get(\PDO::FETCH_ASSOC);

            $ncIds = [];
            foreach ($ncRows as $r) {
                if (!empty($r['task_id'])) $ncIds[] = (int) $r['task_id'];
            }

            // 4M
            $m4Rows = Record4mItem::query()
                ->whereNotNull('task_id')
                ->get(\PDO::FETCH_ASSOC);

            $m4Ids = [];
            foreach ($m4Rows as $r) {
                if (!empty($r['task_id'])) $m4Ids[] = (int) $r['task_id'];
            }

            return $this->json([
                'nursecall' => array_values(array_unique($ncIds)),
                '4m'        => array_values(array_unique($m4Ids)),
            ], 200);

        } catch (\Throwable $e) {
            error_log('[assignedTaskIds] ' . $e->getMessage());
            return $this->json(['nursecall' => [], '4m' => []], 200);
        }
    }

    // ═══════════════════════════════════════════════════════
    // NURSECALL — REPORT (list records + items)
    // ═══════════════════════════════════════════════════════
    public function reportNursecall(Request $request)
    {
        $this->closeSessionEarly();

        try {
            $monthYear = trim((string) ($request->month_year ?? ''));
            $noLane    = trim((string) ($request->no_lane    ?? ''));

            $query = NursecallRecord::query();
            if ($monthYear) $query->where('month_year', '=', $monthYear);
            if ($noLane)    $query->where('no_lane',    '=', $noLane);

            $records = $query->orderBy('month_year', 'desc')
                            ->orderBy('no_lane',    'asc')
                            ->orderBy('date_created', 'desc')
                            ->get(\PDO::FETCH_ASSOC);

            if (empty($records)) {
                return $this->json([], 200);
            }

            $result = [];
            foreach ($records as $rec) {
                $items = NursecallItem::query()
                    ->where('record_id', '=', (int) $rec['id'])
                    ->orderBy('row_no', 'asc')
                    ->get(\PDO::FETCH_ASSOC);

                $result[] = [
                    'record'      => $rec,
                    'items'       => $items ?: [],
                    'items_count' => count($items ?: []),
                ];
            }

            return $this->json($result, 200);

        } catch (\Throwable $e) {
            error_log('[reportNursecall] ' . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ═══════════════════════════════════════════════════════
    // 4M — REPORT
    // ═══════════════════════════════════════════════════════
    public function reportRecord4m(Request $request)
    {
        $this->closeSessionEarly();

        try {
            $monthYear = trim((string) ($request->month_year ?? ''));
            $noLane    = trim((string) ($request->no_lane    ?? ''));

            $query = Record4m::query();
            if ($monthYear) $query->where('month_year', '=', $monthYear);
            if ($noLane)    $query->where('no_lane',    '=', $noLane);

            $records = $query->orderBy('month_year', 'desc')
                            ->orderBy('no_lane',    'asc')
                            ->orderBy('date',       'desc')
                            ->get(\PDO::FETCH_ASSOC);

            if (empty($records)) {
                return $this->json([], 200);
            }

            $result = [];
            foreach ($records as $rec) {
                $items = Record4mItem::query()
                    ->where('record_id', '=', (int) $rec['id'])
                    ->orderBy('row_no', 'asc')
                    ->get(\PDO::FETCH_ASSOC);

                $result[] = [
                    'record'      => $rec,
                    'items'       => $items ?: [],
                    'items_count' => count($items ?: []),
                ];
            }

            return $this->json($result, 200);

        } catch (\Throwable $e) {
            error_log('[reportRecord4m] ' . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function checkTaskDocs(Request $request)
    {
        $this->closeSessionEarly();

        try {
            $taskId    = (int) ($request->task_id ?? 0);
            $noLane    = trim((string) ($request->no_lane    ?? ''));
            $monthYear = trim((string) ($request->month_year ?? ''));

            $result = [
                'nursecall'       => ['exists' => false],
                'four_m'          => ['exists' => false],
                'lembar_point_4m' => ['uploaded' => false],
            ];

            // ═══════════════════════════════════════════════════
            // NURSECALL
            // ═══════════════════════════════════════════════════
            $ncRecord      = null;
            $ncTaskItemIds = [];

            if ($taskId) {
                $ncTaskItems = NursecallItem::query()
                    ->where('task_id', '=', $taskId)
                    ->get(\PDO::FETCH_ASSOC);

                if (!empty($ncTaskItems)) {
                    $recordId = (int) $ncTaskItems[0]['record_id'];
                    $recs = NursecallRecord::query()
                        ->where('id', '=', $recordId)
                        ->limit(1)
                        ->get(\PDO::FETCH_ASSOC);
                    if (!empty($recs)) {
                        $ncRecord      = $recs[0];
                        $ncTaskItemIds = array_column($ncTaskItems, 'id');
                    }
                }
            }

            if (!$ncRecord && $noLane && $monthYear) {
                $recs = NursecallRecord::query()
                    ->where('no_lane',    '=', $noLane)
                    ->where('month_year', '=', $monthYear)
                    ->limit(1)
                    ->get(\PDO::FETCH_ASSOC);
                if (!empty($recs)) $ncRecord = $recs[0];
            }

            if ($ncRecord) {
                $recId = (int) $ncRecord['id'];

                $allItems = NursecallItem::query()
                    ->where('record_id', '=', $recId)
                    ->orderBy('row_no', 'asc')
                    ->get(\PDO::FETCH_ASSOC);

                $taskItems = $allItems ?: [];
                if (!empty($ncTaskItemIds)) {
                    $taskItems = array_values(array_filter($allItems, function ($it) use ($ncTaskItemIds) {
                        return in_array((int) $it['id'], $ncTaskItemIds, true);
                    }));
                }

                $preview = array_slice($taskItems, 0, 3);

                $result['nursecall'] = [
                    'exists'        => true,
                    'record_id'     => $recId,
                    'no_lane'       => $ncRecord['no_lane']      ?? '',
                    'type'          => $ncRecord['type']         ?? '',
                    'month_year'    => $ncRecord['month_year']   ?? '',
                    'date_created'  => $ncRecord['date_created'] ?? '',
                    'items_count'   => count($taskItems),
                    'items_total'   => count($allItems ?: []),
                    'items_preview' => $preview,
                ];
            }

            // ═══════════════════════════════════════════════════
            // 4M
            // ═══════════════════════════════════════════════════
            $m4Record      = null;
            $m4TaskItemIds = [];

            if ($taskId) {
                $m4TaskItems = Record4mItem::query()
                    ->where('task_id', '=', $taskId)
                    ->get(\PDO::FETCH_ASSOC);

                if (!empty($m4TaskItems)) {
                    $recordId = (int) $m4TaskItems[0]['record_id'];
                    $recs = Record4m::query()
                        ->where('id', '=', $recordId)
                        ->limit(1)
                        ->get(\PDO::FETCH_ASSOC);
                    if (!empty($recs)) {
                        $m4Record      = $recs[0];
                        $m4TaskItemIds = array_column($m4TaskItems, 'id');
                    }
                }
            }

            if (!$m4Record && $noLane && $monthYear) {
                $recs = Record4m::query()
                    ->where('no_lane',    '=', $noLane)
                    ->where('month_year', '=', $monthYear)
                    ->limit(1)
                    ->get(\PDO::FETCH_ASSOC);
                if (!empty($recs)) $m4Record = $recs[0];
            }

            if ($m4Record) {
                $recId = (int) $m4Record['id'];

                $allItems = Record4mItem::query()
                    ->where('record_id', '=', $recId)
                    ->orderBy('row_no', 'asc')
                    ->get(\PDO::FETCH_ASSOC);

                $taskItems = $allItems ?: [];
                if (!empty($m4TaskItemIds)) {
                    $taskItems = array_values(array_filter($allItems, function ($it) use ($m4TaskItemIds) {
                        return in_array((int) $it['id'], $m4TaskItemIds, true);
                    }));
                }

                $preview = array_slice($taskItems, 0, 3);

                $result['four_m'] = [
                    'exists'        => true,
                    'record_id'     => $recId,
                    'no_lane'       => $m4Record['no_lane']    ?? '',
                    'type'          => $m4Record['type']       ?? '',
                    'month_year'    => $m4Record['month_year'] ?? '',
                    'date'          => $m4Record['date']       ?? '',
                    'items_count'   => count($taskItems),
                    'items_total'   => count($allItems ?: []),
                    'items_preview' => $preview,
                ];
            }

            // ═══════════════════════════════════════════════════
            // LEMBAR POINT 4M
            // ═══════════════════════════════════════════════════
            if ($taskId) {
                $docRows = \App\Models\TaskDocument::query()
                    ->where('task_id',  '=', $taskId)
                    ->where('doc_type', '=', 'lembar_point_4m')
                    ->orderBy('id', 'desc')
                    ->limit(1)
                    ->get(\PDO::FETCH_ASSOC);

                if (!empty($docRows)) {
                    $result['lembar_point_4m'] = [
                        'uploaded'    => true,
                        'file_name'   => $docRows[0]['original_name'] ?? '',
                        'file_url'    => '/' . env('APP_NAME') . '/' . ltrim($docRows[0]['file_path'], '/'),
                        'file_size'   => (int) ($docRows[0]['file_size'] ?? 0),
                        'uploaded_at' => $docRows[0]['created_at'] ?? '',
                        'uploaded_by' => $docRows[0]['uploaded_by_name'] ?? '',
                    ];
                }
            }

            return $this->json($result, 200);

        } catch (\Throwable $e) {
            error_log('[checkTaskDocs] ' . $e->getMessage());
            return $this->json([
                'nursecall'       => ['exists' => false],
                'four_m'          => ['exists' => false],
                'lembar_point_4m' => ['uploaded' => false],
            ], 200);
        }
    }

    // ═══════════════════════════════════════════════════════
    // UPLOAD LEMBAR POINT 4M
    // ═══════════════════════════════════════════════════════
    public function uploadTaskDoc(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$this->isLeader($user)) {
                return $this->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }

            $taskId  = (int) ($request->task_id ?? 0);
            $docType = trim((string) ($request->doc_type ?? ''));

            if (!$taskId || !$docType) {
                return $this->json(['success' => false, 'message' => 'task_id & doc_type wajib'], 422);
            }

            // Cek file
            if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                return $this->json(['success' => false, 'message' => 'File tidak ditemukan atau gagal upload'], 422);
            }

            $file = $_FILES['file'];

            // Validasi ukuran (max 5 MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                return $this->json(['success' => false, 'message' => 'Ukuran file maksimal 5 MB'], 422);
            }

            // Validasi tipe
            $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, $allowedMimes, true)) {
                return $this->json(['success' => false, 'message' => 'Hanya PDF / JPG / PNG'], 422);
            }

            // Simpan file
            $uploadDir = __DIR__ . '/../../storage/uploads/task_docs/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext       = pathinfo($file['name'], PATHINFO_EXTENSION);
            $storedName = 'task_' . $taskId . '_' . $docType . '_' . time() . '.' . $ext;
            $destPath  = $uploadDir . $storedName;

            if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                return $this->json(['success' => false, 'message' => 'Gagal menyimpan file'], 500);
            }

            // Hapus dokumen lama (kalau ada) untuk task + doc_type ini
            $old = \App\Models\TaskDocument::query()
                ->where('task_id',  '=', $taskId)
                ->where('doc_type', '=', $docType)
                ->get(\PDO::FETCH_ASSOC);

            if (!empty($old)) {
                foreach ($old as $o) {
                    @unlink($uploadDir . basename($o['file_path']));
                    \App\Models\TaskDocument::deleteWhere(['id' => (int) $o['id']]);
                }
            }

            // Insert record baru
            \App\Models\TaskDocument::create([
                'task_id'          => $taskId,
                'doc_type'         => $docType,
                'file_path'        => 'storage/uploads/task_docs/' . $storedName,
                'original_name'    => $file['name'],
                'file_size'        => $file['size'],
                'mime_type'        => $mime,
                'uploaded_by'      => $user->id ?? null,
                'uploaded_by_name' => $user->name ?? '',
            ]);

            return $this->json([
                'success'   => true,
                'message'   => 'File berhasil diupload',
                'file_name' => $file['name'],
                'file_url'  => '/' . 'storage/uploads/task_docs/' . $storedName,
            ], 200);

        } catch (\Throwable $e) {
            error_log('[uploadTaskDoc] ' . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ═══════════════════════════════════════════════════════
    // GET TASK DOC (info + link)
    // ═══════════════════════════════════════════════════════
    public function getTaskDoc(Request $request)
    {
        $this->closeSessionEarly();

        try {
            $taskId  = (int) ($request->task_id ?? 0);
            $docType = trim((string) ($request->doc_type ?? ''));

            if (!$taskId || !$docType) {
                return $this->json(null, 200);
            }

            $rows = \App\Models\TaskDocument::query()
                ->where('task_id',  '=', $taskId)
                ->where('doc_type', '=', $docType)
                ->orderBy('id', 'desc')
                ->limit(1)
                ->get(\PDO::FETCH_ASSOC);

            if (empty($rows)) {
                return $this->json(null, 200);
            }

            $doc = $rows[0];
            $doc['file_url'] = '/' . ltrim($doc['file_path'], '/');

            return $this->json($doc, 200);

        } catch (\Throwable $e) {
            return $this->json(null, 200);
        }
    }

    // ═══════════════════════════════════════════════════════
    // DELETE TASK DOC
    // ═══════════════════════════════════════════════════════
    public function deleteTaskDoc(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$this->isLeader($user)) {
                return $this->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }

            $taskId  = (int) ($request->task_id ?? 0);
            $docType = trim((string) ($request->doc_type ?? ''));

            if (!$taskId || !$docType) {
                return $this->json(['success' => false, 'message' => 'Parameter kurang'], 422);
            }

            $rows = \App\Models\TaskDocument::query()
                ->where('task_id',  '=', $taskId)
                ->where('doc_type', '=', $docType)
                ->get(\PDO::FETCH_ASSOC);

            $uploadDir = __DIR__ . '/../../storage/uploads/task_docs/';

            foreach ($rows as $r) {
                @unlink($uploadDir . basename($r['file_path']));
                \App\Models\TaskDocument::deleteWhere(['id' => (int) $r['id']]);
            }

            return $this->json(['success' => true, 'message' => 'File dihapus'], 200);

        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ═══════════════════════════════════════════════════════
    // GET /support/reference-tasks
    // ═══════════════════════════════════════════════════════
    public function referenceTasks(Request $request)
    {
        $this->closeSessionEarly();

        try {
            $docType   = trim((string) ($request->doc_type   ?? 'nursecall'));
            $noLane    = trim((string) ($request->no_lane    ?? ''));
            $monthYear = trim((string) ($request->month_year ?? ''));

            $isNc = ($docType === 'nursecall');

            // ═══════════════════════════════════════════════════
            // 1. Ambil record ID untuk lane + bulan ini
            // ═══════════════════════════════════════════════════
            $recordId = null;
            if ($noLane && $monthYear) {
                if ($isNc) {
                    $rows = NursecallRecord::query()
                        ->where('no_lane',    '=', $noLane)
                        ->where('month_year', '=', $monthYear)
                        ->limit(1)
                        ->get(\PDO::FETCH_ASSOC);
                } else {
                    $rows = Record4m::query()
                        ->where('no_lane',    '=', $noLane)
                        ->where('month_year', '=', $monthYear)
                        ->limit(1)
                        ->get(\PDO::FETCH_ASSOC);
                }
                if (!empty($rows)) $recordId = (int) $rows[0]['id'];
            }

            // ═══════════════════════════════════════════════════
            // 2. Task yang sudah ada di RECORD INI (untuk refresh)
            // ═══════════════════════════════════════════════════
            $taskIdsInThisRecord = [];
            if ($recordId) {
                if ($isNc) {
                    $items = NursecallItem::query()
                        ->where('record_id', '=', $recordId)
                        ->get(\PDO::FETCH_ASSOC);
                } else {
                    $items = Record4mItem::query()
                        ->where('record_id', '=', $recordId)
                        ->get(\PDO::FETCH_ASSOC);
                }
                foreach ($items as $it) {
                    if (!empty($it['task_id'])) {
                        $taskIdsInThisRecord[] = (int) $it['task_id'];
                    }
                }
                $taskIdsInThisRecord = array_values(array_unique($taskIdsInThisRecord));
            }

            // ═══════════════════════════════════════════════════
            // 3. Task yang sudah dipakai di RECORD LAIN (doc_type sama)
            // ═══════════════════════════════════════════════════
            if ($isNc) {
                $allAssigned = NursecallItem::query()
                    ->whereNotNull('task_id')
                    ->get(\PDO::FETCH_ASSOC);
            } else {
                $allAssigned = Record4mItem::query()
                    ->whereNotNull('task_id')
                    ->get(\PDO::FETCH_ASSOC);
            }

            $taskIdsInOtherRecords = [];
            foreach ($allAssigned as $it) {
                $tid = (int) ($it['task_id']  ?? 0);
                $rid = (int) ($it['record_id'] ?? 0);
                if ($tid && $rid !== $recordId) {
                    $taskIdsInOtherRecords[] = $tid;
                }
            }
            $taskIdsInOtherRecords = array_values(array_unique($taskIdsInOtherRecords));

            // ═══════════════════════════════════════════════════
            // 4. Ambil data task ON-PROGRESS (belum done/cancelled)
            //    ⭐ FILTER UTAMA: exclude done + cancelled
            // ═══════════════════════════════════════════════════
            $onProgress = \App\Models\Task::query()
                ->where(function ($q) {
                    $q->where('status', '!=', 'done')
                    ->where('status', '!=', 'cancelled');
                })
                ->orderBy('task_date', 'desc')
                ->limit(500)
                ->get(\PDO::FETCH_ASSOC);

            // ═══════════════════════════════════════════════════
            // 5. Ambil data task yang ADA DI RECORD INI
            //    (walau sudah done — untuk keperluan refresh data)
            // ═══════════════════════════════════════════════════
            $tasksInRecord = [];
            if (!empty($taskIdsInThisRecord)) {
                $tasksInRecord = \App\Models\Task::query()
                    ->whereIn('id', $taskIdsInThisRecord)
                    ->get(\PDO::FETCH_ASSOC);
            }

            // ═══════════════════════════════════════════════════
            // 6. Gabungkan — no duplicate
            // ═══════════════════════════════════════════════════
            $result = [];
            $seen   = [];

            // Prioritas 1: task yang sudah ada di record ini (kiri tanda 🔵)
            foreach ($tasksInRecord as $t) {
                $id = (int) $t['id'];
                if (isset($seen[$id])) continue;
                $seen[$id] = true;
                $t['_in_record'] = true;
                $result[] = $this->normalizeTaskRef($t);
            }

            // Prioritas 2: task on-progress yang belum di-assign ke record lain
            foreach ($onProgress as $t) {
                $id = (int) $t['id'];
                if (isset($seen[$id])) continue;

                // Skip kalau sudah dipakai di record LAIN
                if (in_array($id, $taskIdsInOtherRecords, true)) continue;

                $seen[$id] = true;
                $t['_in_record'] = false;
                $result[] = $this->normalizeTaskRef($t);
            }

            return $this->json($result, 200);

        } catch (\Throwable $e) {
            error_log('[referenceTasks] ' . $e->getMessage());
            return $this->json([], 200);
        }
    }

    // Helper normalisasi task
    private function normalizeTaskRef(array $t): array
    {
        return [
            'id'              => (int) $t['id'],
            'taskCode'        => $t['task_code']         ?? '',
            'operatorName'    => $t['operator_name']     ?? '',
            'date'            => $t['task_date']         ?? '',
            'section'         => $t['section']           ?? '',
            'category'        => strtolower($t['category']    ?? ''),
            'picSection'      => strtolower($t['pic_section'] ?? ''),
            'problem'         => $t['problem']           ?? '',
            'tempAction'      => $t['temporary_action']  ?? '',
            'permAction'      => $t['permanent_action']  ?? '',
            'deadline'        => $t['deadline']          ?? '',
            'pic'             => $t['pic']               ?? '',
            'stage'           => strtolower($t['stage']  ?? 'plan'),
            'status'          => strtolower($t['status'] ?? 'open'),
            'leaderSignature' => $t['leader_signature']  ?? ($t['ttd_leader'] ?? ''),
            'approvedAt'      => $t['approved_at']       ?? null,
            'createdAt'       => $t['created_at']        ?? null,
            'createdByName'   => $t['created_by_name']   ?? '',
            'inRecord'        => (bool) ($t['_in_record'] ?? false),
        ];
    }

    public function pageNursecall(Request $request)
    {
        $user = auth()->user();
        return view('support/page', [
            'type'       => 'nursecall',
            'title'      => 'Catatan Nurse Call',
            'user'       => $user,
            'userName'   => $user->name  ?? '',
            'userRole'   => $user->role  ?? '',
            'isLoggedIn' => (bool) $user,
            'embedded'   => (bool) ($request->embedded ?? false),
        ]);
    }

    public function pageRecord4m(Request $request)
    {
        $user = auth()->user();
        return view('support/page', [
            'type'       => '4m',
            'title'      => 'Record Perubahan 4M',
            'user'       => $user,
            'userName'   => $user->name  ?? '',
            'userRole'   => $user->role  ?? '',
            'isLoggedIn' => (bool) $user,
            'embedded'   => (bool) ($request->embedded ?? false),
        ]);
    }

    // ═══════════════════════════════════════════════════════
    // REPORT PAGES — standalone view (bisa di-embed)
    // ═══════════════════════════════════════════════════════
    public function pageReportNursecall(Request $request)
    {
        $user = auth()->user();
        return view('support/report', [
            'type'       => 'nursecall',
            'title'      => 'Laporan Catatan Nurse Call',
            'user'       => $user,
            'userName'   => $user->name  ?? '',
            'userRole'   => $user->role  ?? '',
            'isLoggedIn' => (bool) $user,
            'embedded'   => (bool) ($request->embedded ?? false),
        ]);
    }

    public function pageReport4m(Request $request)
    {
        $user = auth()->user();
        return view('support/report', [
            'type'       => '4m',
            'title'      => 'Laporan Record Perubahan 4M',
            'user'       => $user,
            'userName'   => $user->name  ?? '',
            'userRole'   => $user->role  ?? '',
            'isLoggedIn' => (bool) $user,
            'embedded'   => (bool) ($request->embedded ?? false),
        ]);
    }

    // ═══════════════════════════════════════════════════════
    // GET /api/support/stats
    // Untuk konsumsi server-to-server (monitoring)
    // ═══════════════════════════════════════════════════════
    public function apiSupportStats(Request $request)
    {
        $this->closeSessionEarly();

        try {
            // ── Auth via API Key ──
            $apiKey   = $_SERVER['HTTP_X_API_KEY'] ?? '';
            $expected = env('PDCA_API_KEY', 'change-me');

            if (!$apiKey || $apiKey !== $expected) {
                return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $monthYear = trim((string) ($request->month_year ?? date('Y-m')));
            $noLane    = trim((string) ($request->no_lane    ?? ''));

            // ── NURSECALL ──
            $ncQ = NursecallRecord::query()->where('month_year', '=', $monthYear);
            if ($noLane) $ncQ->where('no_lane', '=', $noLane);
            $ncRecords = $ncQ->get(\PDO::FETCH_ASSOC) ?: [];
            $ncIds     = array_column($ncRecords, 'id');

            $ncItems = [];
            if (!empty($ncIds)) {
                $ncItems = NursecallItem::query()
                    ->whereIn('record_id', $ncIds)
                    ->orderBy('row_no', 'asc')
                    ->get(\PDO::FETCH_ASSOC) ?: [];
            }

            // ── 4M ──
            $m4Q = Record4m::query()->where('month_year', '=', $monthYear);
            if ($noLane) $m4Q->where('no_lane', '=', $noLane);
            $m4Records = $m4Q->get(\PDO::FETCH_ASSOC) ?: [];
            $m4Ids     = array_column($m4Records, 'id');

            $m4Items = [];
            if (!empty($m4Ids)) {
                $m4Items = Record4mItem::query()
                    ->whereIn('record_id', $m4Ids)
                    ->orderBy('row_no', 'asc')
                    ->get(\PDO::FETCH_ASSOC) ?: [];
            }

            return $this->json([
                'success'    => true,
                'month_year' => $monthYear,
                'no_lane'    => $noLane,
                'nursecall'  => [
                    'total_records' => count($ncRecords),
                    'total_items'   => count($ncItems),
                    'items_preview' => array_slice($ncItems, 0, 5),
                ],
                '4m' => [
                    'total_records' => count($m4Records),
                    'total_items'   => count($m4Items),
                    'items_preview' => array_slice($m4Items, 0, 5),
                ],
            ], 200);

        } catch (\Throwable $e) {
            error_log('[apiSupportStats] ' . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
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