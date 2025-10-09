<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Campus;
use App\Models\Scholarship;
use App\Models\Application;
use App\Models\Applicant;
use App\Models\DocumentEvaluation;
use App\Models\StudentSubmittedDocument;
use App\Models\ScholarshipRequiredCondition;
use App\Models\ScholarshipRequiredDocument;
use App\Models\Form;
use App\Models\Report;
use Faker\Factory as Faker;

class ComprehensiveSystemSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Create campuses
        $mainCampus = Campus::create([
            'name' => 'Main Campus',
            'type' => 'constituent',
            'has_sfao_admin' => true,
        ]);

        $extension1 = Campus::create([
            'name' => 'Extension Campus 1',
            'type' => 'extension',
            'parent_campus_id' => $mainCampus->id,
            'has_sfao_admin' => false,
        ]);

        $extension2 = Campus::create([
            'name' => 'Extension Campus 2',
            'type' => 'extension',
            'parent_campus_id' => $mainCampus->id,
            'has_sfao_admin' => false,
        ]);

        // Create users
        $users = [];

        // Create Central Admin
        $centralAdmin = User::create([
            'name' => 'Central Administrator',
            'email' => 'central@bsu.edu',
            'password' => Hash::make('password'),
            'role' => 'central',
            'campus_id' => 1,
        ]);
        $users[] = $centralAdmin;

        // Create SFAO Admins
        for ($i = 1; $i <= 3; $i++) {
            $sfaoAdmin = User::create([
                'name' => "SFAO Admin {$i}",
                'email' => "sfao{$i}@bsu.edu",
                'password' => Hash::make('password'),
                'role' => 'sfao',
                'campus_id' => $i,
            ]);
            $users[] = $sfaoAdmin;
        }

        // Create Students
        for ($i = 1; $i <= 50; $i++) {
            $studentId = $faker->unique()->numberBetween(100000, 999999);
            $studentEmail = sprintf("99-%06d@g.batstate-u.edu.ph", $studentId);
            
            $student = User::create([
                'name' => $faker->name,
                'email' => $studentEmail,
                'password' => Hash::make('password'),
                'role' => 'student',
                'campus_id' => $faker->numberBetween(1, 3),
            ]);
            $users[] = $student;

            // Create form for student
            Form::create([
                'user_id' => $student->id,
                'last_name' => $faker->lastName,
                'first_name' => $faker->firstName,
                'middle_name' => $faker->optional(0.7)->firstName,
                'gwa' => $faker->randomFloat(2, 1.0, 3.0),
                'year_level' => $faker->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year']),
                'program' => $faker->randomElement(['Computer Science', 'Engineering', 'Business', 'Education']),
                'sex' => $faker->randomElement(['male', 'female']),
                'age' => $faker->numberBetween(18, 25),
                'monthly_allowance' => $faker->numberBetween(5000, 50000),
                'disability' => $faker->optional(0.1)->randomElement(['Visual', 'Hearing', 'Mobility']),
                'campus' => $student->campus->name,
            ]);
        }

        // Create scholarships
        $scholarships = [
            [
                'scholarship_name' => 'Academic Excellence Scholarship',
                'scholarship_type' => 'internal',
                'description' => 'For students with outstanding academic performance',
                'submission_deadline' => now()->addMonths(2),
                'application_start_date' => now()->subDays(30),
                'slots_available' => 20,
                'grant_amount' => 15000,
                'renewal_allowed' => true,
                'grant_type' => 'recurring',
                'is_active' => true,
                'priority_level' => 'high',
                'created_by' => $centralAdmin->id,
            ],
            [
                'scholarship_name' => 'Financial Assistance Grant',
                'scholarship_type' => 'internal',
                'description' => 'For students with financial need',
                'submission_deadline' => now()->addMonths(1),
                'application_start_date' => now()->subDays(15),
                'slots_available' => 30,
                'grant_amount' => 10000,
                'renewal_allowed' => true,
                'grant_type' => 'recurring',
                'is_active' => true,
                'priority_level' => 'medium',
                'created_by' => $centralAdmin->id,
            ],
            [
                'scholarship_name' => 'One-time Achievement Award',
                'scholarship_type' => 'external',
                'description' => 'One-time award for exceptional achievement',
                'submission_deadline' => now()->addDays(15),
                'application_start_date' => now()->subDays(10),
                'slots_available' => 5,
                'grant_amount' => 25000,
                'renewal_allowed' => false,
                'grant_type' => 'one_time',
                'is_active' => true,
                'priority_level' => 'high',
                'created_by' => $centralAdmin->id,
            ],
        ];

        foreach ($scholarships as $scholarshipData) {
            $scholarship = Scholarship::create($scholarshipData);

            // Add conditions
            ScholarshipRequiredCondition::create([
                'scholarship_id' => $scholarship->id,
                'name' => 'gwa',
                'value' => '2.0',
                'description' => 'GWA requirement',
            ]);

            // Add document requirements
            ScholarshipRequiredDocument::create([
                'scholarship_id' => $scholarship->id,
                'document_name' => 'Form 137',
                'document_type' => 'academic',
                'is_required' => true,
            ]);

            ScholarshipRequiredDocument::create([
                'scholarship_id' => $scholarship->id,
                'document_name' => 'Certificate of Good Moral Character',
                'document_type' => 'character',
                'is_required' => true,
            ]);
        }

        // Create applications
        $students = User::where('role', 'student')->get();
        $scholarshipIds = Scholarship::pluck('id');

        foreach ($students as $student) {
            $numApplications = $faker->numberBetween(1, 3);
            $appliedScholarships = $faker->randomElements($scholarshipIds, $numApplications);

            foreach ($appliedScholarships as $scholarshipId) {
                $application = Application::create([
                    'user_id' => $student->id,
                    'scholarship_id' => $scholarshipId,
                ]);

                // Create submitted documents
                $scholarship = Scholarship::find($scholarshipId);
                $requiredDocs = $scholarship->requiredDocuments;

                foreach ($requiredDocs as $doc) {
                    StudentSubmittedDocument::create([
                        'user_id' => $student->id,
                        'scholarship_id' => $scholarshipId,
                        'document_name' => $doc->document_name,
                        'document_category' => 'scholarship_required',
                        'file_path' => 'documents/' . $faker->uuid . '.pdf',
                        'original_filename' => $doc->document_name . '.pdf',
                        'file_type' => 'application/pdf',
                        'file_size' => $faker->numberBetween(100000, 5000000),
                        'submitted_at' => $faker->dateTimeBetween('-30 days', 'now'),
                    ]);
                }

                // Create SFAO required documents
                StudentSubmittedDocument::create([
                    'user_id' => $student->id,
                    'scholarship_id' => $scholarshipId,
                    'document_name' => 'Form 137',
                    'document_category' => 'sfao_required',
                    'file_path' => 'documents/' . $faker->uuid . '.pdf',
                    'original_filename' => 'Form137.pdf',
                    'file_type' => 'application/pdf',
                    'file_size' => $faker->numberBetween(100000, 5000000),
                    'submitted_at' => $faker->dateTimeBetween('-30 days', 'now'),
                ]);

                // Create document evaluations
                $documents = StudentSubmittedDocument::where('user_id', $student->id)
                    ->where('scholarship_id', $scholarshipId)
                    ->get();

                foreach ($documents as $document) {
                    $evaluationStatus = $faker->randomElement(['approved', 'rejected', 'pending']);
                    $evaluatorId = $faker->randomElement(User::where('role', 'sfao')->pluck('id'));

                    DocumentEvaluation::create([
                        'application_id' => $application->id,
                        'user_id' => $student->id,
                        'scholarship_id' => $scholarshipId,
                        'document_id' => $document->id,
                        'evaluator_id' => $evaluatorId,
                        'evaluation_status' => $evaluationStatus,
                        'evaluation_notes' => $evaluationStatus === 'rejected' ? $faker->sentence : null,
                        'evaluated_at' => $evaluationStatus !== 'pending' ? $faker->dateTimeBetween('-20 days', 'now') : null,
                    ]);
                }

                // Create applicant record if approved
                $hasRejected = DocumentEvaluation::where('application_id', $application->id)
                    ->where('evaluation_status', 'rejected')
                    ->exists();

                if (!$hasRejected && $faker->boolean(70)) { // 70% approval rate
                    Applicant::create([
                        'user_id' => $student->id,
                        'scholarship_id' => $scholarshipId,
                        'application_id' => $application->id,
                        'status' => $faker->randomElement(['ready_for_claim', 'claimed']),
                        'grant_count' => $faker->numberBetween(1, 3),
                        'claimed_at' => $faker->optional(0.3)->dateTimeBetween('-10 days', 'now'),
                    ]);
                }
            }
        }

        // Create reports
        $sfaoAdmins = User::where('role', 'sfao')->get();
        foreach ($sfaoAdmins as $sfaoAdmin) {
            for ($i = 1; $i <= 3; $i++) {
                Report::create([
                    'sfao_user_id' => $sfaoAdmin->id,
                    'campus_id' => $sfaoAdmin->campus_id,
                    'report_type' => $faker->randomElement(['monthly', 'quarterly', 'annual']),
                    'title' => "Report {$i} - {$sfaoAdmin->campus->name}",
                    'description' => $faker->paragraph,
                    'report_period_start' => $faker->dateTimeBetween('-3 months', '-1 month'),
                    'report_period_end' => $faker->dateTimeBetween('-1 month', 'now'),
                    'status' => $faker->randomElement(['draft', 'submitted', 'reviewed', 'approved']),
                    'submitted_at' => $faker->optional(0.7)->dateTimeBetween('-2 months', 'now'),
                ]);
            }
        }
    }
}
