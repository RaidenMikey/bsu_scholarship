<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Application;
use App\Models\Scholarship;
use App\Models\SfaoRequirement;
use App\Models\Campus;
use App\Services\NotificationService;

/**
 * =====================================================
 * APPLICATION MANAGEMENT CONTROLLER
 * =====================================================
 * 
 * This controller handles all application-related functionality
 * including student applications, applicant management, and
 * application processing for both SFAO and Central roles.
 * 
 * Combined functionality from:
 * - ApplicationController
 * - ApplicantsController  
 * - StudentController (application methods)
 * - SFAOController (application management)
 * - CentralController (application management)
 */
class ApplicationManagementController extends Controller
{
    // =====================================================
    // STUDENT APPLICATION METHODS
    // =====================================================

    /**
     * Show student's applications
     */
    public function studentApplications()
    {
        if (!session()->has('user_id') || session('role') !== 'student') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::with('appliedScholarships')->find(session('user_id'));
        $applications = $user->appliedScholarships;

        return view('student.applications', compact('applications'));
    }

    /**
     * Apply for a scholarship (Student)
     */
    public function apply(Request $request)
    {
        if (!session()->has('user_id') || session('role') !== 'student') {
            return redirect('/login')->with('session_expired', true);
        }

        $request->validate([
            'scholarship_id' => 'required|exists:scholarships,id',
        ]);

        $application = Application::where('user_id', session('user_id'))
            ->where('scholarship_id', $request->scholarship_id)
            ->first();

        if ($application) {
            // If it already exists, update status (if you want to allow re-applying)
            $application->update(['status' => 'pending']);
            return back()->with('success', 'Your application has been updated.');
        } else {
            // Determine if this is a new or continuing application
            $hasClaimedGrant = Application::hasClaimedGrant(session('user_id'), $request->scholarship_id);
            $applicationType = $hasClaimedGrant ? 'continuing' : 'new';
            
            // Create new application
            Application::create([
                'user_id'        => session('user_id'),
                'scholarship_id' => $request->scholarship_id,
                'type'           => $applicationType,
                'status'         => 'pending',
            ]);
            
            $message = $hasClaimedGrant 
                ? 'You have successfully applied for scholarship renewal.'
                : 'You have successfully applied for the scholarship.';
                
            return back()->with('success', $message);
        }
    }

    /**
     * Unapply from a scholarship (Student)
     */
    public function unapply(Request $request)
    {
        if (!session()->has('user_id') || session('role') !== 'student') {
            return redirect('/login')->with('session_expired', true);
        }

        $request->validate([
            'scholarship_id' => 'required|exists:scholarships,id',
        ]);

        $userId = session('user_id');
        $scholarshipId = $request->scholarship_id;

        // Delete application entry
        Application::where('user_id', $userId)
            ->where('scholarship_id', $scholarshipId)
            ->delete();

        // Find the student document row
        $studentDocument = SfaoRequirement::where('user_id', $userId)
            ->where('scholarship_id', $scholarshipId)
            ->first();

        if ($studentDocument) {
            // Delete files from storage if they exist
            foreach (['form_137', 'grades', 'certificate', 'application_form'] as $field) {
                if (!empty($studentDocument->$field) && Storage::disk('public')->exists($studentDocument->$field)) {
                    Storage::disk('public')->delete($studentDocument->$field);
                }
            }

            // Delete the DB row
            $studentDocument->delete();
        }

        return back()->with('success', 'You have successfully un-applied, and your documents were removed.');
    }

    // =====================================================
    // APPLICANT MANAGEMENT METHODS
    // =====================================================

    /**
     * View all applicants (Central Admin) - Only SFAO-approved applications
     */
    public function viewApplicants()
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        // Retrieve only SFAO-approved applications with related user and scholarship info
        $applications = Application::with(['user', 'scholarship'])
            ->where('status', 'approved') // Only show SFAO-approved applications
            ->orderBy('created_at', 'desc')
            ->get();

        return view('central.partials.tabs.applicants', compact('applications'));
    }

    /**
     * SFAO Dashboard
     */
    public function sfaoDashboard(Request $request)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::with('campus')->find(session('user_id'));
        
        // Get the SFAO admin's campus and all campuses under it
        $sfaoCampus = $user->campus;
        $campusIds = $sfaoCampus->getAllCampusesUnder()->pluck('id');

        // Get students only from the SFAO admin's campus and its extensions
        $students = User::where('role', 'student')
            ->whereIn('campus_id', $campusIds)
            ->with(['applications.scholarship', 'form', 'campus'])
            ->leftJoin('sfao_requirements', 'users.id', '=', 'sfao_requirements.user_id')
            ->select(
                'users.id as student_id',
                'users.name',
                'users.email',
                'users.created_at',
                'users.campus_id',
                DB::raw('MAX(sfao_requirements.updated_at) as last_uploaded'),
                DB::raw('COUNT(DISTINCT sfao_requirements.id) as documents_count')
            )
            ->groupBy('users.id', 'users.name', 'users.email', 'users.created_at', 'users.campus_id')
            ->get();

        // Add application status information to each student
        $students->each(function($student) {
            $student->has_applications = $student->applications->count() > 0;
            $student->has_documents = $student->documents_count > 0;
            $student->application_status = $student->applications->pluck('status')->unique()->toArray();
            $student->applied_scholarships = $student->applications->pluck('scholarship.scholarship_name')->toArray();
            $student->applications_with_types = $student->applications->map(function($app) {
                return [
                    'id' => $app->id,
                    'scholarship_name' => $app->scholarship->scholarship_name,
                    'status' => $app->status,
                    'type' => $app->type,
                    'type_display' => $app->getApplicantTypeDisplayName(),
                    'type_badge_color' => $app->getApplicantTypeBadgeColor(),
                    'grant_count' => $app->grant_count,
                    'grant_count_display' => $app->getGrantCountDisplay(),
                    'grant_count_badge_color' => $app->getGrantCountBadgeColor()
                ];
            });
        });

        // Get applications only from students under this SFAO admin's jurisdiction
        $applications = Application::with('user', 'scholarship')
            ->whereHas('user', function($query) use ($campusIds) {
                $query->whereIn('campus_id', $campusIds);
            })
            ->get();
            
        $scholarships = Scholarship::withCount(['applications' => function($query) use ($campusIds) {
            $query->whereHas('user', function($userQuery) use ($campusIds) {
                $userQuery->whereIn('campus_id', $campusIds);
            });
        }])->get();

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $scholarships = $this->sortScholarships($scholarships, $sortBy, $sortOrder);

        return view('sfao.dashboard', compact('user', 'students', 'applications', 'scholarships', 'sfaoCampus'));
    }

    /**
     * View applicants for SFAO (Campus-specific)
     */
    public function sfaoApplicants()
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::with('campus')->find(session('user_id'));
        $sfaoCampus = $user->campus;
        $campusIds = $sfaoCampus->getAllCampusesUnder()->pluck('id');

        // Get students who have uploaded at least one document, only from this SFAO admin's jurisdiction
        $students = DB::table('sfao_requirements')
            ->join('users', 'sfao_requirements.user_id', '=', 'users.id')
            ->whereIn('users.campus_id', $campusIds)
            ->select(
                'users.id as student_id',
                'users.name',
                'users.email',
                'users.campus_id',
                DB::raw('MAX(sfao_requirements.updated_at) as last_uploaded')
            )
            ->groupBy('users.id', 'users.name', 'users.email', 'users.campus_id')
            ->get();

        return view('sfao.partials.tabs.applicants', compact('students', 'sfaoCampus'));
    }

    /**
     * View student documents (SFAO)
     */
    public function viewDocuments($user_id)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $student = User::findOrFail($user_id);
        $documents = SfaoRequirement::where('user_id', $user_id)->get();

        return view('sfao.partials.view-documents', compact('student', 'documents'));
    }

    // =====================================================
    // APPLICATION PROCESSING METHODS
    // =====================================================

    /**
     * Approve application (SFAO)
     */
    public function sfaoApproveApplication($id)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $application = Application::findOrFail($id);
        $application->status = 'approved';
        $application->save();

        // Create notification for student
        NotificationService::notifyApplicationStatusChange($application, 'approved');

        return back()->with('success', 'Application approved.');
    }

    /**
     * Reject application (SFAO)
     */
    public function sfaoRejectApplication($id)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $application = Application::findOrFail($id);
        $application->status = 'rejected';
        $application->save();

        // Create notification for student
        NotificationService::notifyApplicationStatusChange($application, 'rejected');

        return back()->with('success', 'Application rejected.');
    }

    /**
     * Mark application as claimed (SFAO)
     */
    public function sfaoClaimGrant($id)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $application = Application::findOrFail($id);
        
        // Only allow claiming if application is approved
        if ($application->status !== 'approved') {
            return back()->with('error', 'Only approved applications can be marked as claimed.');
        }
        
        // Calculate the grant count for this specific scholarship
        $grantCount = Application::getNextGrantCount($application->user_id, $application->scholarship_id);
        
        $application->status = 'claimed';
        $application->grant_count = $grantCount;
        $application->save();

        return back()->with('success', "Grant has been marked as claimed ({$grantCount}th grant). Student is now eligible for renewals.");
    }

    /**
     * Approve application (Central)
     */
    public function centralApproveApplication($id)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        $application = Application::findOrFail($id);
        $application->status = 'approved';
        $application->save();

        // Create notification for student
        NotificationService::notifyApplicationStatusChange($application, 'approved');

        return back()->with('success', 'Application approved.');
    }

    /**
     * Reject application (Central)
     */
    public function centralRejectApplication($id)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        $application = Application::findOrFail($id);
        $application->status = 'rejected';
        $application->save();

        // Create notification for student
        NotificationService::notifyApplicationStatusChange($application, 'rejected');

        return back()->with('success', 'Application rejected.');
    }

    /**
     * Mark application as claimed (Central)
     */
    public function centralClaimGrant($id)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        $application = Application::findOrFail($id);
        
        // Only allow claiming if application is approved
        if ($application->status !== 'approved') {
            return back()->with('error', 'Only approved applications can be marked as claimed.');
        }
        
        // Calculate the grant count for this specific scholarship
        $grantCount = Application::getNextGrantCount($application->user_id, $application->scholarship_id);
        
        $application->status = 'claimed';
        $application->grant_count = $grantCount;
        $application->save();

        return back()->with('success', "Grant has been marked as claimed ({$grantCount}th grant). Student is now eligible for renewals.");
    }

    // =====================================================
    // APPLICATION TRACKING METHODS
    // =====================================================

    /**
     * Get application tracking data for student
     */
    public function getApplicationTracking($userId)
    {
        return Application::where('user_id', $userId)
            ->with('scholarship')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get applications for SFAO dashboard
     */
    public function getSfaoApplications($campusIds)
    {
        return Application::with('user', 'scholarship')
            ->whereHas('user', function($query) use ($campusIds) {
                $query->whereIn('campus_id', $campusIds);
            })
            ->get();
    }

    /**
     * Central Dashboard - Only shows SFAO-approved applications
     */
    public function centralDashboard(Request $request)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        // Only show SFAO-approved applications to Central admin
        $applications = Application::with(['user', 'scholarship'])
            ->where('status', 'approved') // Only SFAO-approved applications
            ->orderBy('created_at', 'desc')
            ->get();

        $scholarships = Scholarship::with(['conditions', 'requirements'])->get();
        
        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $scholarships = $this->sortScholarships($scholarships, $sortBy, $sortOrder);

        return view('central.dashboard', compact('applications', 'scholarships'));
    }

    /**
     * Sort scholarships based on various criteria
     */
    private function sortScholarships($scholarships, $sortBy, $sortOrder)
    {
        return $scholarships->sortBy(function ($scholarship) use ($sortBy) {
            switch ($sortBy) {
                case 'name':
                    return $scholarship->scholarship_name;
                case 'created_at':
                    return $scholarship->created_at;
                case 'submission_deadline':
                    return $scholarship->submission_deadline;
                case 'grant_amount':
                    return $scholarship->grant_amount ?? 0;
                case 'priority_level':
                    $priorityOrder = ['high' => 1, 'medium' => 2, 'low' => 3];
                    return $priorityOrder[$scholarship->priority_level] ?? 4;
                case 'scholarship_type':
                    return $scholarship->scholarship_type;
                case 'grant_type':
                    return $scholarship->grant_type;
                case 'slots_available':
                    return $scholarship->slots_available ?? 999999;
                case 'gwa_requirement':
                    return $scholarship->getGwaRequirement() ?? 999;
                case 'applications_count':
                    return $scholarship->applications_count ?? 0;
                default:
                    return $scholarship->created_at;
            }
        }, SORT_REGULAR, $sortOrder === 'desc');
    }

    /**
     * Get applications for Central dashboard
     */
    public function getCentralApplications()
    {
        return Application::with(['user', 'scholarship'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
