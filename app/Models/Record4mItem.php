<?php

namespace App\Models;

use Bpjs\Framework\Helpers\BaseModel;

class Record4mItem extends BaseModel
{
    protected string $table = 'record_4m_items';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
    protected bool $timestamps = true;
    protected bool $softDelete = false;
}
