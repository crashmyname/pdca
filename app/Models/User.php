<?php

namespace App\Models;
use Bpjs\Framework\Helpers\BaseModel;

class User extends BaseModel {
    
    // Protected table Users
    protected string $table = 'users';
    protected string $primaryKey = 'id';
}