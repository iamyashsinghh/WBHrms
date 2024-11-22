<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    use SoftDeletes;
    protected $guarded = [];
    public function get_role() {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
}
