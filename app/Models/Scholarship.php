<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    use HasFactory;

    protected $fillable = [
        'scholarship_name',
        'description',
        'minimum_gwa',
        'deadline',
        'slots_available',
        'grant_amount',
        'renewal_allowed',
        'is_active',
        'created_by',
    ];

    // A scholarship can have many applications
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    // Get users who applied for this scholarship
    public function users()
    {
        return $this->belongsToMany(User::class, 'applications');
    }

    // Conditions (e.g. gwa, disability, income, year_level)
    public function conditions()
    {
        return $this->hasMany(ScholarshipCondition::class);
    }

    // Document requirements
    public function requirements()
    {
        return $this->hasMany(ScholarshipRequirement::class);
    }
}
