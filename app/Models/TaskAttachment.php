<?php

namespace App\Models;

use Bpjs\Framework\Helpers\BaseModel;

class TaskAttachment extends BaseModel
{
    protected string $table = 'attachments';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
}
