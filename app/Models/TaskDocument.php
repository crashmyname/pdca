<?php

namespace App\Models;

use Bpjs\Framework\Helpers\BaseModel;

class TaskDocument extends BaseModel
{
    protected string $table = 'task_documents';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
    protected bool $timestamps = true;
    protected bool $softDelete = false;
}
