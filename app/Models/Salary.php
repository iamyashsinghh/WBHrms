<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salary extends Model
{
    use SoftDeletes;
    protected $table = 'salary';
    protected $guarded = [];

    public function salaryType()
    {
        return $this->belongsTo(SalaryType::class, 'salary_type', 'id');
    }

}
