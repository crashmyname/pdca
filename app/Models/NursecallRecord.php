<?php

namespace App\Models;

use Bpjs\Framework\Helpers\BaseModel;

class NursecallRecord extends BaseModel
{
    protected string $table = 'nursecall_records';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
    protected bool $timestamps = true;
    protected bool $softDelete = false;
}
