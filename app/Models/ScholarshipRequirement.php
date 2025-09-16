<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarshipRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'scholarship_id',
        'type',
        'name',
        'description',
        'value',
        'is_mandatory',
    ];

    // Relationship back to scholarship
    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }

    // Scopes for filtering by type
    public function scopeConditions($query)
    {
        return $query->where('type', 'condition');
    }

    public function scopeDocuments($query)
    {
        return $query->where('type', 'document');
    }
}
