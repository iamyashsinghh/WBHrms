<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentType extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function get_user_doc($emp_code)
    {
        return Document::where('emp_code', $emp_code)
            ->where('doc_type', $this->id)
            ->first();
    }
}
