<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditService
{
    public static function log(
        string $tableName,
        int $recordId,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null,
        array $options = []
    ): void {
        try {
            $changedFields = self::computeChanged($action, $oldValues, $newValues);

            $user = $options['user'] ?? (function_exists('auth') ? auth()->user() : null);

            AuditLog::create([
                'table_name'      => $tableName,
                'record_id'       => $recordId,
                'action'          => $action,
                'old_values'      => $oldValues !== null ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : null,
                'new_values'      => $newValues !== null ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null,
                'changed_fields'  => $changedFields ? json_encode($changedFields) : null,
                'changed_by_id'   => $user->id   ?? null,
                'changed_by_name' => $user->name ?? null,
                'changed_by_role' => isset($user->role) ? strtolower($user->role) : null,
                'ip_address'      => $_SERVER['REMOTE_ADDR']     ?? null,
                'user_agent'      => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
                'notes'           => $options['notes'] ?? null,
            ]);
        } catch (\Throwable $e) {
            error_log('[AuditService::log] ' . $e->getMessage());
        }
    }

    private static function computeChanged(string $action, ?array $old, ?array $new): ?array
    {
        if ($action !== 'update' || $old === null || $new === null) return null;

        $changed = [];
        foreach ($new as $k => $v) {
            $ov = $old[$k] ?? null;
            $ovNorm = ($ov === '' || $ov === null) ? null : (string) $ov;
            $nvNorm = ($v  === '' || $v  === null) ? null : (string) $v;
            if ($ovNorm !== $nvNorm) $changed[] = $k;
        }
        return $changed;
    }

    public static function snapshot(string $modelClass, int $recordId): ?array
    {
        try {
            $rows = $modelClass::query()
                ->withTrashed()
                ->where('id', '=', $recordId)
                ->limit(1)
                ->get(\PDO::FETCH_ASSOC);

            return $rows[0] ?? null;
        } catch (\Throwable $e) {
            error_log('[AuditService::snapshot] ' . $e->getMessage());
            return null;
        }
    }
}