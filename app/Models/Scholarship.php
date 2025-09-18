<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    use HasFactory;

    protected $fillable = [
        'scholarship_name',
        'scholarship_type',
        'description',
        'submission_deadline',
        'application_start_date',
        'slots_available',
        'grant_amount',
        'renewal_allowed',
        'is_active',
        'priority_level',
        'eligibility_notes',
        'created_by',
    ];

    protected $casts = [
        'submission_deadline' => 'date',
        'application_start_date' => 'date',
        'grant_amount' => 'decimal:2',
        'renewal_allowed' => 'boolean',
        'is_active' => 'boolean',
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

    // All requirements (both conditions and documents)
    public function requirements()
    {
        return $this->hasMany(ScholarshipRequirement::class);
    }

    // Conditions only (e.g. gwa, disability, income, year_level)
    public function conditions()
    {
        return $this->hasMany(ScholarshipRequirement::class)->where('type', 'condition');
    }

    // Document requirements only
    public function documentRequirements()
    {
        return $this->hasMany(ScholarshipRequirement::class)->where('type', 'document');
    }

    // Get GWA requirement from conditions
    public function getGwaRequirement()
    {
        $gwaCondition = $this->conditions()->where('name', 'gwa')->first();
        return $gwaCondition ? $gwaCondition->value : null;
    }

    // Check if student meets GWA requirement
    public function meetsGwaRequirement($studentGwa)
    {
        $requiredGwa = $this->getGwaRequirement();
        
        // If no GWA requirement, student qualifies
        if ($requiredGwa === null) {
            return true;
        }
        
        // If student has INC, they don't qualify (unless scholarship allows it)
        if ($studentGwa === null || $studentGwa === 'INC') {
            return false;
        }
        
        // Compare GWA (lower is better)
        return floatval($studentGwa) <= floatval($requiredGwa);
    }

    // Check if student meets all conditions
    public function meetsAllConditions($formData)
    {
        $conditions = $this->conditions;
        
        foreach ($conditions as $condition) {
            if (!$this->meetsCondition($condition, $formData)) {
                return false;
            }
        }
        
        return true;
    }

    // Check if student meets a specific condition
    public function meetsCondition($condition, $formData)
    {
        switch ($condition->name) {
            case 'gwa':
                return $this->meetsGwaRequirement($formData->gwa);
                
            case 'year_level':
                return $this->meetsYearLevelRequirement($condition->value, $formData->year_level);
                
            case 'income':
                return $this->meetsIncomeRequirement($condition->value, $formData->monthly_allowance);
                
            case 'disability':
                return $this->meetsDisabilityRequirement($condition->value, $formData->disability);
                
            case 'program':
                return $this->meetsProgramRequirement($condition->value, $formData->program);
                
            case 'campus':
                return $this->meetsCampusRequirement($condition->value, $formData->campus);
                
            case 'age':
                return $this->meetsAgeRequirement($condition->value, $formData->age);
                
            case 'sex':
                return $this->meetsSexRequirement($condition->value, $formData->sex);
                
            default:
                return true; // Unknown condition, allow
        }
    }

    // Year level requirement matching
    public function meetsYearLevelRequirement($requiredLevel, $studentLevel)
    {
        if ($requiredLevel === null || $studentLevel === null) {
            return true;
        }
        
        // Convert year levels to comparable format
        $yearLevels = ['1st Year' => 1, '2nd Year' => 2, '3rd Year' => 3, '4th Year' => 4, '5th Year' => 5];
        
        $requiredNum = $yearLevels[$requiredLevel] ?? null;
        $studentNum = $yearLevels[$studentLevel] ?? null;
        
        if ($requiredNum === null || $studentNum === null) {
            return $requiredLevel === $studentLevel; // Exact match if not in standard format
        }
        
        return $studentNum >= $requiredNum; // Student must be at or above required level
    }

    // Income requirement matching (maximum income)
    public function meetsIncomeRequirement($maxIncome, $studentIncome)
    {
        if ($maxIncome === null || $studentIncome === null) {
            return true;
        }
        
        return floatval($studentIncome) <= floatval($maxIncome);
    }

    // Disability requirement matching
    public function meetsDisabilityRequirement($requiredDisability, $studentDisability)
    {
        if ($requiredDisability === null) {
            return true; // No disability requirement
        }
        
        if ($requiredDisability === 'none' || $requiredDisability === 'no') {
            return $studentDisability === null || $studentDisability === '';
        }
        
        if ($requiredDisability === 'any' || $requiredDisability === 'yes') {
            return $studentDisability !== null && $studentDisability !== '';
        }
        
        return $requiredDisability === $studentDisability; // Exact match
    }

    // Program requirement matching
    public function meetsProgramRequirement($requiredProgram, $studentProgram)
    {
        if ($requiredProgram === null || $studentProgram === null) {
            return true;
        }
        
        return strtolower($requiredProgram) === strtolower($studentProgram);
    }

    // Campus requirement matching
    public function meetsCampusRequirement($requiredCampus, $studentCampus)
    {
        if ($requiredCampus === null || $studentCampus === null) {
            return true;
        }
        
        return strtolower($requiredCampus) === strtolower($studentCampus);
    }

    // Age requirement matching
    public function meetsAgeRequirement($requiredAge, $studentAge)
    {
        if ($requiredAge === null || $studentAge === null) {
            return true;
        }
        
        return intval($studentAge) >= intval($requiredAge);
    }

    // Sex requirement matching
    public function meetsSexRequirement($requiredSex, $studentSex)
    {
        if ($requiredSex === null || $studentSex === null) {
            return true;
        }
        
        return strtolower($requiredSex) === strtolower($studentSex);
    }

    // Get matching criteria for display
    public function getMatchingCriteria($formData)
    {
        $matchingCriteria = [];
        $conditions = $this->conditions;
        
        foreach ($conditions as $condition) {
            $matches = $this->meetsCondition($condition, $formData);
            $matchingCriteria[] = [
                'name' => $condition->name,
                'value' => $condition->value,
                'matches' => $matches,
                'display_name' => $this->getConditionDisplayName($condition->name),
                'student_value' => $this->getStudentValue($condition->name, $formData)
            ];
        }
        
        return $matchingCriteria;
    }

    // Get display name for condition
    private function getConditionDisplayName($conditionName)
    {
        $displayNames = [
            'gwa' => 'GWA',
            'year_level' => 'Year Level',
            'income' => 'Monthly Income',
            'disability' => 'Disability Status',
            'program' => 'Program',
            'campus' => 'Campus',
            'age' => 'Age',
            'sex' => 'Gender'
        ];
        
        return $displayNames[$conditionName] ?? ucfirst(str_replace('_', ' ', $conditionName));
    }

    // Get student value for condition
    private function getStudentValue($conditionName, $formData)
    {
        switch ($conditionName) {
            case 'gwa':
                return $formData->gwa ?? 'Not specified';
            case 'year_level':
                return $formData->year_level ?? 'Not specified';
            case 'income':
                return $formData->monthly_allowance ? '₱' . number_format($formData->monthly_allowance, 2) : 'Not specified';
            case 'disability':
                return $formData->disability ?? 'None';
            case 'program':
                return $formData->program ?? 'Not specified';
            case 'campus':
                return $formData->campus ?? 'Not specified';
            case 'age':
                return $formData->age ?? 'Not specified';
            case 'sex':
                return $formData->sex ?? 'Not specified';
            default:
                return 'Not specified';
        }
    }

    // Check if scholarship is currently accepting applications
    public function isAcceptingApplications()
    {
        $now = now();
        
        // Check if scholarship is active
        if (!$this->is_active) {
            return false;
        }
        
        // Check if application period has started
        if ($this->application_start_date && $now->lt($this->application_start_date)) {
            return false;
        }
        
        // Check if submission deadline has passed
        if ($now->gt($this->submission_deadline)) {
            return false;
        }
        
        // Check if slots are available
        if ($this->slots_available !== null && $this->slots_available <= 0) {
            return false;
        }
        
        return true;
    }

    // Get days remaining until deadline
    public function getDaysUntilDeadline()
    {
        $deadline = $this->submission_deadline;
        $now = now();
        
        if ($now->gt($deadline)) {
            return 0; // Deadline has passed
        }
        
        return $now->diffInDays($deadline);
    }

    // Get application count
    public function getApplicationCount()
    {
        return $this->applications()->count();
    }

    // Get approved application count
    public function getApprovedCount()
    {
        return $this->applications()->where('status', 'approved')->count();
    }

    // Get fill percentage
    public function getFillPercentage()
    {
        if ($this->slots_available === null || $this->slots_available <= 0) {
            return 0;
        }
        
        $applicationsCount = $this->getApplicationCount();
        return min(($applicationsCount / $this->slots_available) * 100, 100);
    }

    // Check if scholarship is full
    public function isFull()
    {
        if ($this->slots_available === null) {
            return false; // Unlimited slots
        }
        
        return $this->getApplicationCount() >= $this->slots_available;
    }

    // Get priority badge color
    public function getPriorityBadgeColor()
    {
        return match($this->priority_level) {
            'high' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'low' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
        };
    }

    // Get scholarship type badge color
    public function getScholarshipTypeBadgeColor()
    {
        return match($this->scholarship_type) {
            'internal' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'external' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
        };
    }

    // Get status badge
    public function getStatusBadge()
    {
        if (!$this->is_active) {
            return ['text' => 'Inactive', 'color' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'];
        }
        
        if (!$this->isAcceptingApplications()) {
            return ['text' => 'Closed', 'color' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'];
        }
        
        if ($this->isFull()) {
            return ['text' => 'Full', 'color' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200'];
        }
        
        return ['text' => 'Open', 'color' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'];
    }

    // Scope for active scholarships
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for scholarships accepting applications
    public function scopeAcceptingApplications($query)
    {
        $now = now();
        return $query->where('is_active', true)
                    ->where(function($q) use ($now) {
                        $q->whereNull('application_start_date')
                          ->orWhere('application_start_date', '<=', $now);
                    })
                    ->where('submission_deadline', '>=', $now)
                    ->where(function($q) {
                        $q->whereNull('slots_available')
                          ->orWhere('slots_available', '>', 0);
                    });
    }

    // Scope for high priority scholarships
    public function scopeHighPriority($query)
    {
        return $query->where('priority_level', 'high');
    }
}
