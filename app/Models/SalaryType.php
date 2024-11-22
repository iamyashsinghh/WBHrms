<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryType extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function get_user_salary($emp_code)
    {
        return Salary::where('emp_code', $emp_code)
            ->where('salary_type', $this->id)
            ->first();
    }
}
