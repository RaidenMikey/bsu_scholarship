<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $table = 'forms';

    protected $fillable = [
        // ------------------- Personal Data -------------------
        'user_id',
        'last_name',
        'first_name',
        'middle_name',
        'age',
        'sex',
        'civil_status',
        'birthdate',
        'birthplace',
        'email',
        'contact_number',
        'street_barangay',
        'town_city',
        'province',
        'zip_code',
        'citizenship',
        'disability',
        'tribe',

        // ------------------- Academic Data -------------------
        'sr_code',
        'education_level',
        'program',
        'college_department',
        'year_level',
        'campus',
        'previous_gwa',
        'honors_received',
        'units_enrolled',
        'scholarship_applied',
        'semester',
        'academic_year',
        'has_existing_scholarship',
        'existing_scholarship_details',

        // ------------------- Family Data -------------------
        'father_status',
        'father_name',
        'father_address',
        'father_contact',
        'father_occupation',
        'mother_status',
        'mother_name',
        'mother_address',
        'mother_contact',
        'mother_occupation',
        'estimated_gross_annual_income',
        'siblings_count',

        // ------------------- Essay / Question -------------------
        'reason_for_applying',

        // ------------------- Certification -------------------
        'student_signature',
        'date_signed',

        // ------------------- Status / Meta -------------------
        'form_status',
        'reviewer_remarks',
        'reviewed_by',
    ];

    protected $casts = [
        'has_existing_scholarship' => 'boolean',
        'previous_gwa' => 'float',
        'birthdate' => 'date',
        'date_signed' => 'date',
    ];

    /**
     * Get the user that owns this form.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
