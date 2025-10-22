<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'scholarship_id',
        'type',
        'grant_count',
        'status',
    ];

    /**
     * Set default attribute values.
     */
    protected $attributes = [
        'type' => 'new', // default if not provided
        'grant_count' => 0, // default if not provided
        'status' => 'pending', // default if not provided
    ];

    /**
     * Casts for specific fields.
     */
    protected $casts = [
        'user_id' => 'integer',
        'scholarship_id' => 'integer',
    ];

    /**
     * Relationships
     */

    // Application belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Application belongs to a scholarship
    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }

    // Application can have one scholar record (when approved)
    public function scholar()
    {
        return $this->hasOne(Scholar::class);
    }

    /**
     * Check if this application is from a new applicant
     */
    public function isNewApplicant()
    {
        return $this->type === 'new';
    }

    /**
     * Check if this application is from a continuing applicant
     */
    public function isContinuingApplicant()
    {
        return $this->type === 'continuing';
    }

    /**
     * Check if the student has previously claimed a grant for this scholarship
     */
    public static function hasClaimedGrant($userId, $scholarshipId)
    {
        return self::where('user_id', $userId)
                   ->where('scholarship_id', $scholarshipId)
                   ->where('status', 'claimed')
                   ->exists();
    }

    /**
     * Get the applicant type display name
     */
    public function getApplicantTypeDisplayName()
    {
        return $this->type === 'new' ? 'New Applicant' : 'Continuing Applicant';
    }

    /**
     * Get the applicant type badge color
     */
    public function getApplicantTypeBadgeColor()
    {
        return $this->type === 'new' 
            ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
            : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
    }

    /**
     * Scope for new applicants
     */
    public function scopeNewApplicants($query)
    {
        return $query->where('type', 'new');
    }

    /**
     * Scope for continuing applicants
     */
    public function scopeContinuingApplicants($query)
    {
        return $query->where('type', 'continuing');
    }

    /**
     * Get the total grant count for a student in a specific scholarship
     */
    public static function getTotalGrantCount($userId, $scholarshipId)
    {
        return self::where('user_id', $userId)
                   ->where('scholarship_id', $scholarshipId)
                   ->where('status', 'claimed')
                   ->sum('grant_count');
    }

    /**
     * Get the next grant count for a student in a specific scholarship
     */
    public static function getNextGrantCount($userId, $scholarshipId)
    {
        $totalGrantCount = self::getTotalGrantCount($userId, $scholarshipId);
        return $totalGrantCount + 1; // Next grant will be this number
    }

    /**
     * Get grant count display text
     */
    public function getGrantCountDisplay()
    {
        if ($this->grant_count <= 0) {
            return 'No grants received';
        }
        
        return $this->grant_count === 1 
            ? '1st grant' 
            : $this->grant_count . 'th grant';
    }

    /**
     * Get grant count badge color
     */
    public function getGrantCountBadgeColor()
    {
        if ($this->grant_count <= 0) {
            return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
        }
        
        return match($this->grant_count) {
            1 => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            2 => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            3 => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
            default => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200'
        };
    }
}
