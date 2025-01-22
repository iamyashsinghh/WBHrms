<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resignation extends Model
{
    protected $guarded = [];
    
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_code', 'emp_code');
    }

}
