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
            'age'                 => 'nullable|integer',
            'sex'                 => 'nullable|in:male,female',
            'civil_status'        => 'nullable|string',
            'birthdate'           => 'nullable|date',
            'birthplace'          => 'nullable|string',
            'email'               => 'nullable|email',
            'contact_number'      => 'nullable|string',
            'street_barangay'     => 'nullable|string',
            'town_city'           => 'nullable|string',
            'province'            => 'nullable|string',
            'zip_code'            => 'nullable|string',
            'citizenship'         => 'nullable|string',
            'disability'          => 'nullable|string',
            'tribe'               => 'nullable|string',

            // ------------------- Academic Data -------------------
            'sr_code'                      => 'nullable|string',
            'education_level'              => 'nullable|in:Undergraduate,Graduate School,Integrated School',
            'program'                      => 'nullable|string',
            'college_department'           => 'nullable|string',
            'year_level'                   => 'nullable|string',
            'campus'                       => 'nullable|string',
            'previous_gwa'                 => 'nullable|numeric|between:1.00,5.00',
            'honors_received'              => 'nullable|string',
            'units_enrolled'               => 'nullable|integer',
            'scholarship_applied'          => 'nullable|string',
            'semester'                     => 'nullable|string',
            'academic_year'                => 'nullable|string',
            'has_existing_scholarship'     => 'nullable|boolean',
            'existing_scholarship_details' => 'nullable|string',

            // ------------------- Family Data -------------------
            'father_status'        => 'nullable|in:living,deceased',
            'father_name'          => 'nullable|string',
            'father_address'       => 'nullable|string',
            'father_contact'       => 'nullable|string',
            'father_occupation'    => 'nullable|string',
            'father_income_bracket' => 'nullable|string',
            'mother_status'        => 'nullable|in:living,deceased',
            'mother_name'          => 'nullable|string',
            'mother_address'       => 'nullable|string',
            'mother_contact'       => 'nullable|string',
            'mother_occupation'    => 'nullable|string',
            'mother_income_bracket' => 'nullable|string',
            'siblings_count'       => 'nullable|integer',

            // ------------------- Essay / Question -------------------
            'reason_for_applying' => 'nullable|string',

            // ------------------- Certification -------------------
            'student_signature' => 'nullable|string',
            'date_signed'       => 'nullable|date',
        ]);

        // ✅ Ensure empty string → NULL for GWA
        $validated['previous_gwa'] = $request->filled('previous_gwa') ? $request->previous_gwa : null;

        // Ensure proper boolean conversion
        $validated['has_existing_scholarship'] = $request->boolean('has_existing_scholarship');

        // Set form_status to 'submitted' when form is submitted
        $validated['form_status'] = 'submitted';

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
