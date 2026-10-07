<?php

namespace App\Models;

use Bpjs\Framework\Helpers\BaseModel;

class AuditLog extends BaseModel
{
    protected string $table = 'audit_logs';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
    protected bool $timestamps = true;
    protected bool $softDelete = false;
}
