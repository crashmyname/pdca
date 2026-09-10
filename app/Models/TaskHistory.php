<?php

namespace App\Models;

use Bpjs\Framework\Helpers\BaseModel;

class TaskHistory extends BaseModel
{
    protected string $table = 'task_histories';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
}
