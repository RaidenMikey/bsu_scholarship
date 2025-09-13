<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScholarshipCondition extends Model
{
    protected $table = 'scholarship_conditions';

    protected $fillable = [
        'scholarship_id',
        'field_name',
        'value',
    ];

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }
}
