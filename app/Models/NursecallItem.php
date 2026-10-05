<?php

namespace App\Models;

use Bpjs\Framework\Helpers\BaseModel;

class NursecallItem extends BaseModel
{
    protected string $table = 'nursecall_items';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
    protected bool $timestamps = true;
    protected bool $softDelete = false;
}
