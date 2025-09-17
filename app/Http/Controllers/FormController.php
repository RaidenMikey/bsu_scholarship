<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function submit(Request $request)
    {
        $userId = session('user_id');

        $validated = $request->validate([
            // ------------------- Personal Data -------------------
            'last_name'           => 'nullable|string',
            'first_name'          => 'nullable|string',
            'middle_name'         => 'nullable|string',
            'street_barangay'     => 'nullable|string',
            'town_city'           => 'nullable|string',
            'province'            => 'nullable|string',
            'zip_code'            => 'nullable|digits:4',
            'age'                 => 'nullable|integer',
            'sex'                 => 'nullable|string',
            'civil_status'        => 'nullable|string',
            'disability'          => 'nullable|string',
            'tribe'               => 'nullable|string',
            'citizenship'         => 'nullable|string',
            'birthdate'           => 'nullable|date',
            'birthplace'          => 'nullable|string',
            'birth_order'         => 'nullable|string',
            'email'               => 'nullable|email',
            'telephone'           => 'nullable|string',
            'religion'            => 'nullable|string',
            'highschool_type'     => 'nullable|string',
            'monthly_allowance'   => 'nullable|string',
            'living_arrangement'  => 'nullable|string',
            'living_arrangement_other' => 'nullable|string',
            'transportation'      => 'nullable|string',
            'transportation_other' => 'nullable|string',

            // ------------------- Academic Data -------------------
            'education_level'              => 'nullable|string',
            'program'                      => 'nullable|string',
            'college'                      => 'nullable|string',
            'year_level'                   => 'nullable|string',
            'campus'                       => 'nullable|string',
            'gwa'                          => 'nullable|numeric|between:1.00,5.00',
            'honors'                       => 'nullable|string',
            'units_enrolled'               => 'nullable|string',
            'academic_year'                => 'nullable|string',
            'has_existing_scholarship'     => 'nullable|boolean',
            'existing_scholarship_details' => 'nullable|string',

            // ------------------- Family Data -------------------
            // Father
            'father_living'            => 'nullable|boolean',
            'father_name'              => 'nullable|string',
            'father_age'               => 'nullable|integer',
            'father_residence'         => 'nullable|string',
            'father_education'         => 'nullable|string',
            'father_contact'           => 'nullable|string',
            'father_occupation'        => 'nullable|string',
            'father_company'           => 'nullable|string',
            'father_company_address'   => 'nullable|string',
            'father_employment_status' => 'nullable|string',

            // Mother
            'mother_living'            => 'nullable|boolean',
            'mother_name'              => 'nullable|string',
            'mother_age'               => 'nullable|integer',
            'mother_residence'         => 'nullable|string',
            'mother_education'         => 'nullable|string',
            'mother_contact'           => 'nullable|string',
            'mother_occupation'        => 'nullable|string',
            'mother_company'           => 'nullable|string',
            'mother_company_address'   => 'nullable|string',
            'mother_employment_status' => 'nullable|string',
        ]);

        // ✅ Ensure empty string → NULL for GWA
        $validated['gwa'] = $request->filled('gwa') ? $request->gwa : null;

        // Ensure proper boolean conversion
        $validated['has_existing_scholarship'] = $request->boolean('has_existing_scholarship');
        $validated['father_living'] = $request->boolean('father_living');
        $validated['mother_living'] = $request->boolean('mother_living');

        // Inject user_id into validated data
        $validated['user_id'] = $userId;

        // Insert or update the form based on user_id
        Form::updateOrCreate(
            ['user_id' => $userId],
            $validated
        );

        return redirect('/student')->with('success', 'Application saved successfully.');
    }
}
