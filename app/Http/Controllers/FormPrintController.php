<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Form;
use PhpOffice\PhpWord\TemplateProcessor;

class FormPrintController extends Controller
{
    /**
     * Print application form as DOCX
     */
    public function printApplication()
    {
        if (!session()->has('user_id') || session('role') !== 'student') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::find(session('user_id'));
        $form = Form::where('user_id', $user->id)->first();

        if (!$form) {
            return redirect()->back()->with('error', 'Application form not found.');
        }

        // Path to your Word template
        // Primary location: storage/app/templates/ (secure, not web-accessible)
        $templatePath = storage_path('app/templates/BatStateU-FO-SFA-01_Application Form_Template_Final.docx');
        
        if (!file_exists($templatePath)) {
            // Fallback to alternative filename
            $templatePath = storage_path('app/templates/application_form_template.docx');
        }
        
        if (!file_exists($templatePath)) {
            // Last fallback: resources/forms/ (for backward compatibility)
            $templatePath = resource_path('forms/BatStateU-FO-SFA-01_Application Form_Template_Final.docx');
        }
        
        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'Template file not found. Please ensure the template exists at: storage/app/templates/BatStateU-FO-SFA-01_Application Form_Template_Final.docx');
        }

        // Load the template
        $templateProcessor = new TemplateProcessor($templatePath);

        // Map estimated_gross_annual_income to readable text
        $incomeLabels = [
            'not_over_250000' => 'Not over P 250,000.00',
            'over_250000_not_over_400000' => 'Over P 250,000 but not over P 400,000',
            'over_400000_not_over_800000' => 'Over P 400,000 but not over P 800,000',
            'over_800000_not_over_2000000' => 'Over P 800,000 but not over P 2,000,000',
            'over_2000000_not_over_8000000' => 'Over P 2,000,000 but not over P 8,000,000',
            'over_8000000' => 'Over P 8,000,000'
        ];

        // Format birthdate for display (mm/dd/yyyy)
        $birthdateFormatted = $form->birthdate ? $form->birthdate->format('m/d/Y') : '';
        $birthdateMonth = $form->birthdate ? $form->birthdate->format('m') : '';
        $birthdateDay = $form->birthdate ? $form->birthdate->format('d') : '';
        $birthdateYear = $form->birthdate ? $form->birthdate->format('Y') : '';

        // Format date_signed
        $dateSignedFormatted = $form->date_signed ? $form->date_signed->format('m/d/Y') : '';

        // Handle education level checkboxes
        $edu_undergrad = ' ';
        $edu_grad = ' ';
        $edu_integrated = ' ';

        switch ($form->education_level) {
            case 'Undergraduate':
                $edu_undergrad = '/';
                break;
            case 'Graduate School':
                $edu_grad = '/';
                break;
            case 'Integrated School':
                $edu_integrated = '/';
                break;
        }

        // Handle existing scholarship checkboxes
        $scholarship_yes = ' ';
        $scholarship_no = ' ';
        
        if ($form->has_existing_scholarship) {
            $scholarship_yes = '/';
        } else {
            $scholarship_no = '/';
        }

        // Initialize all income brackets as blank
        $inc_1 = $inc_2 = $inc_3 = $inc_4 = $inc_5 = $inc_6 = ' ';

        switch ($form->estimated_gross_annual_income) {
            case 'not_over_250000':
                $inc_1 = '/';
                break;
            case 'over_250000_not_over_400000':
                $inc_2 = '/';
                break;
            case 'over_400000_not_over_800000':
                $inc_3 = '/';
                break;
            case 'over_800000_not_over_2000000':
                $inc_4 = '/';
                break;
            case 'over_2000000_not_over_8000000':
                $inc_5 = '/';
                break;
            case 'over_8000000':
                $inc_6 = '/';
                break;
        }

        // Initialize all parent status checkboxes as blank
        $father_living = $father_deceased = $mother_living = $mother_deceased = ' ';

        // Father status
        if ($form->father_status === 'living') {
            $father_living = '/';
        } elseif ($form->father_status === 'deceased') {
            $father_deceased = '/';
        }

        // Mother status
        if ($form->mother_status === 'living') {
            $mother_living = '/';
        } elseif ($form->mother_status === 'deceased') {
            $mother_deceased = '/';
        }

        // Replace all placeholders with form data
        $replacements = [
            // Personal Data
            '{{last_name}}' => $form->last_name ?? '',
            '{{first_name}}' => $form->first_name ?? '',
            '{{middle_name}}' => $form->middle_name ?? '',
            '{{age}}' => $form->age ?? '',
            '{{sex}}' => ucfirst($form->sex ?? ''),
            '{{civil_status}}' => $form->civil_status ?? '',
            '{{birthdate}}' => $birthdateFormatted,
            '{{birthdate_month}}' => $birthdateMonth,
            '{{birthdate_day}}' => $birthdateDay,
            '{{birthdate_year}}' => $birthdateYear,
            '{{birth_mm}}' => $birthdateMonth,
            '{{birth_dd}}' => $birthdateDay,
            '{{birth_yyyy}}' => $birthdateYear,
            '{{birthplace}}' => $form->birthplace ?? '',
            '{{email}}' => $form->email ?? '',
            '{{contact_number}}' => $form->contact_number ?? '',
            '{{street_barangay}}' => $form->street_barangay ?? '',
            '{{town_city}}' => $form->town_city ?? '',
            '{{province}}' => $form->province ?? '',
            '{{zip_code}}' => $form->zip_code ?? '',
            '{{citizenship}}' => $form->citizenship ?? '',
            '{{disability}}' => $form->disability ?? '',
            '{{tribe}}' => $form->tribe ?? '',

            // Academic Data
            '{{sr_code}}' => $form->sr_code ?? '',
            '{{education_level}}' => $form->education_level ?? '',
            '{{edu_undergrad}}' => $edu_undergrad,
            '{{edu_grad}}' => $edu_grad,
            '{{edu_integrated}}' => $edu_integrated,
            '{{program}}' => $form->program ?? '',
            '{{college_department}}' => $form->college_department ?? '',
            '{{year_level}}' => $form->year_level ?? '',
            '{{campus}}' => $form->campus ?? '',
            '{{previous_gwa}}' => $form->previous_gwa ?? '',
            '{{honors_received}}' => $form->honors_received ?? '',
            '{{units_enrolled}}' => $form->units_enrolled ?? '',
            '{{scholarship_applied}}' => trim($form->existing_scholarship_details ?? ($form->scholarship_applied ?? '')),
            '{{semester}}' => $form->semester ?? '',
            '{{academic_year}}' => $form->academic_year ?? '',
            '{{has_existing_scholarship}}' => $form->has_existing_scholarship ? 'Yes' : 'No',
            '{{scholarship_yes}}' => $scholarship_yes,
            '{{scholarship_no}}' => $scholarship_no,
            '{{existing_scholarship_details}}' => $form->existing_scholarship_details ?? '',

            // Family Data
            '{{father_status}}' => ucfirst($form->father_status ?? ''),
            '{{father_living}}' => $father_living,
            '{{father_deceased}}' => $father_deceased,
            '{{father_name}}' => $form->father_name ?? '',
            '{{father_address}}' => $form->father_address ?? '',
            '{{father_contact}}' => $form->father_contact ?? '',
            '{{father_occupation}}' => $form->father_occupation ?? '',
            '{{mother_status}}' => ucfirst($form->mother_status ?? ''),
            '{{mother_living}}' => $mother_living,
            '{{mother_deceased}}' => $mother_deceased,
            '{{mother_name}}' => $form->mother_name ?? '',
            '{{mother_address}}' => $form->mother_address ?? '',
            '{{mother_contact}}' => $form->mother_contact ?? '',
            '{{mother_occupation}}' => $form->mother_occupation ?? '',
            '{{estimated_gross_annual_income}}' => $incomeLabels[$form->estimated_gross_annual_income] ?? ($form->estimated_gross_annual_income ?? ''),
            '{{inc_1}}' => $inc_1,
            '{{inc_2}}' => $inc_2,
            '{{inc_3}}' => $inc_3,
            '{{inc_4}}' => $inc_4,
            '{{inc_5}}' => $inc_5,
            '{{inc_6}}' => $inc_6,
            '{{siblings_count}}' => $form->siblings_count ?? '',

            // Essay / Question
            '{{reason_for_applying}}' => $form->reason_for_applying ?? '',

            // Certification
            '{{student_signature}}' => $form->student_signature ?? '',
            '{{date_signed}}' => $dateSignedFormatted,
        ];

        // Replace all placeholders in the template
        // PHPWord TemplateProcessor expects ${variable} format in the Word document
        // We extract the variable name from {{variable}} or [[variable]] format
        foreach ($replacements as $placeholder => $value) {
            // Extract variable name by removing {{, }}, [[, ]], or ${, }
            // Remove opening delimiters: {{, [[, ${
            $variableName = preg_replace('/^(?:\{\{|\[\[|\$\{)/', '', $placeholder);
            // Remove closing delimiters: }}, ]], }
            $variableName = preg_replace('/(?:\}\}|\]\]|})$/', '', $variableName);
            // PHPWord setValue expects just the variable name (without ${})
            $templateProcessor->setValue($variableName, $value ?? '');
        }

        // Generate filename
        $filename = 'application_form_' . $user->id . '_' . date('Y-m-d') . '.docx';
        
        // Save the processed document
        $outputPath = storage_path('app/temp/' . $filename);
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $templateProcessor->saveAs($outputPath);

        // Download the file
        return response()->download($outputPath, $filename)->deleteFileAfterSend(true);
    }
}

