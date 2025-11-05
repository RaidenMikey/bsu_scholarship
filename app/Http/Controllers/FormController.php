<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function submit(Request $request)
    {
        $userId = session('user_id');

        // 🧩 Combine birthdate parts if form has separate MM/DD/YYYY fields
        // This handles the case where JavaScript might not have combined them
        if ($request->filled('birthdate')) {
            // Birthdate already combined by JavaScript (from hidden field)
            $birthdate = $request->birthdate;
        } elseif ($request->filled(['birth_mm', 'birth_dd', 'birth_yyyy'])) {
            // Fallback: Combine separate inputs server-side
            try {
                $month = str_pad($request->birth_mm, 2, '0', STR_PAD_LEFT);
                $day = str_pad($request->birth_dd, 2, '0', STR_PAD_LEFT);
                $year = $request->birth_yyyy;
                
                if (checkdate($month, $day, $year)) {
                    $birthdate = sprintf('%d-%s-%s', $year, $month, $day);
                } else {
                    $birthdate = null;
                }
            } catch (\Exception $e) {
                $birthdate = null;
            }
        } else {
            $birthdate = null;
        }

        // Merge birthdate into request if it was constructed
        if ($birthdate !== null && !$request->filled('birthdate')) {
            $request->merge(['birthdate' => $birthdate]);
        }

        // Recalculate age from birthdate if birthdate is provided
        // This ensures age is always current, matching the JavaScript calculation
        if ($request->filled('birthdate')) {
            try {
                $birthdateObj = \Carbon\Carbon::parse($request->birthdate);
                $today = now();
                $age = $today->year - $birthdateObj->year;
                $monthDiff = $today->month - $birthdateObj->month;
                if ($monthDiff < 0 || ($monthDiff === 0 && $today->day < $birthdateObj->day)) {
                    $age--;
                }
                if ($age > 0) {
                    $request->merge(['age' => $age]);
                }
            } catch (\Exception $e) {
                // If birthdate parsing fails, keep the submitted age value
            }
        }

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
            'mother_status'        => 'nullable|in:living,deceased',
            'mother_name'          => 'nullable|string',
            'mother_address'       => 'nullable|string',
            'mother_contact'       => 'nullable|string',
            'mother_occupation'    => 'nullable|string',
            'estimated_gross_annual_income' => 'nullable|string|in:not_over_250000,over_250000_not_over_400000,over_400000_not_over_800000,over_800000_not_over_2000000,over_2000000_not_over_8000000,over_8000000',
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

        // If scholarship_id is provided in the form, set scholarship_applied field
        if ($request->filled('scholarship_id')) {
            $scholarship = \App\Models\Scholarship::find($request->scholarship_id);
            if ($scholarship) {
                $validated['scholarship_applied'] = $scholarship->scholarship_name;
            }
        }

        // Create or update form for this user (one form per user)
        Form::updateOrCreate(
            [
                'user_id' => $userId
            ],
            $validated
        );

        // If print flag is set, redirect to print after saving
        if ($request->has('print_after_save') && $request->print_after_save) {
            // If scholarship_id exists in request, redirect to scholarship-specific print
            if ($request->filled('scholarship_id')) {
                return redirect()->route('student.print-application.scholarship', ['scholarship_id' => $request->scholarship_id])->with('success', 'Application saved successfully. Preparing your document...');
            } else {
                // Just print and stay on form page
                return redirect()->route('student.print-application')->with('success', 'Application saved successfully. Preparing your document...');
            }
        }

        return redirect('/student')->with('success', 'Application saved successfully.');
    }

}
