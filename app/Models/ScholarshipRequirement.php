<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarshipRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'scholarship_id',
        'name',
        'description',
        'is_mandatory',
    ];

    // Relationship back to scholarship
    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }
}
