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
    public function get_documents() {
        return $this->hasMany(Document::class, 'emp_code', 'emp_code');
    }
    public function get_reporting_manager() {
        return $this->hasOne(Employee::class, 'emp_code', 'reporting_manager');
    }
}
