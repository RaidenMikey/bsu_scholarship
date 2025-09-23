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

        // Address broken down
        'street_barangay',
        'town_city',
        'province',
        'zip_code',

        'age',
        'sex',
        'civil_status',
        'disability',
        'tribe',
        'citizenship',
        'birthdate',
        'birthplace',
        'birth_order',
        'email',
        'telephone',
        'religion',
        'highschool_type',
        'monthly_allowance',
        'living_arrangement',
        'living_arrangement_other',
        'transportation',
        'transportation_other',


        // ------------------- Academic Data -------------------
        'education_level',
        'program',
        'college',
        'year_level',
        'campus',
        'gwa',
        'honors',
        'units_enrolled',
        'academic_year',
        'has_existing_scholarship',
        'existing_scholarship_details',

        // ------------------- Family Data -------------------
        // Father
        'father_living',
        'father_name',
        'father_age',
        'father_residence',
        'father_education',
        'father_contact',
        'father_occupation',
        'father_company',
        'father_company_address',
        'father_employment_status',

        // Mother
        'mother_living',
        'mother_name',
        'mother_age',
        'mother_residence',
        'mother_education',
        'mother_contact',
        'mother_occupation',
        'mother_company',
        'mother_company_address',
        'mother_employment_status',

        // ------------------- Additional Family Information -------------------
        'family_members_count',
        'siblings_count',
        'family_form',

        // ------------------- Income Information -------------------
        'monthly_family_income_bracket',
        'other_income_sources',

        // ------------------- House Profile & Utilities -------------------
        'vehicle_ownership',
        'appliances',
        'house_ownership',
        'house_material',
        'house_type',
        'cooking_utilities',
        'water_source',
        'electricity_source',
        'monthly_bills_electric',
        'monthly_bills_telephone',
        'monthly_bills_internet',

        // ------------------- Certification -------------------
        'student_signature',
        'date_signed',
    ];

    protected $casts = [
        'has_existing_scholarship' => 'boolean',
        'father_living' => 'boolean',
        'mother_living' => 'boolean',
        'gwa' => 'float',
        'birthdate' => 'date',
        'date_signed' => 'date',
        'monthly_bills_electric' => 'decimal:2',
        'monthly_bills_telephone' => 'decimal:2',
        'monthly_bills_internet' => 'decimal:2',
    ];

    /**
     * Get the user that owns this form.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
