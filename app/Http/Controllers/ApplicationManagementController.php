<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Application;
use App\Models\Scholarship;
use App\Models\SfaoRequirement;
use App\Models\StudentSubmittedDocument;
use App\Models\Campus;
use App\Models\Notification;
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
            // Create new application
            Application::create([
                'user_id'        => session('user_id'),
                'scholarship_id' => $request->scholarship_id,
                'status'         => 'pending',
            ]);
            
            $message = 'You have successfully applied for the scholarship.';
                
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

        // Find all submitted documents for this user and scholarship
        $submittedDocuments = StudentSubmittedDocument::where('user_id', $userId)
            ->where('scholarship_id', $scholarshipId)
            ->get();

        // Delete files from storage and then delete the database records
        foreach ($submittedDocuments as $document) {
            if (!empty($document->file_path) && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            $document->delete();
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
     * SFAO Dashboard - Only shows applicants (students with applications), not scholars
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

        // Get sorting parameters
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $campusFilter = $request->get('campus_filter', 'all');
        $statusFilter = $request->get('status_filter', 'all');
        
        // Get active tab to determine status filter if tab-based filtering is used
        $activeTab = $request->get('tab', 'scholarships');
        
        // If tab is a status-specific applicants tab, override status filter
        // Note: 'approved' and 'claimed' are excluded since approved applicants become scholars
        if (str_starts_with($activeTab, 'applicants-')) {
            $statusFromTab = str_replace('applicants-', '', $activeTab);
            if (in_array($statusFromTab, ['not_applied', 'in_progress', 'pending', 'rejected'])) {
                $statusFilter = $statusFromTab;
            }
        }

        // Build the query - SFAO sees all students in their domain
        // Exclude students who are already scholars (they will be shown in Scholars tab)
        $query = User::where('role', 'student')
            ->whereIn('campus_id', $campusIds)
            ->whereDoesntHave('scholars') // Exclude scholars from applicants tab
            ->with(['applications.scholarship', 'form', 'campus'])
            ->leftJoin('student_submitted_documents', function($join) {
                $join->on('users.id', '=', 'student_submitted_documents.user_id')
                     ->where('student_submitted_documents.document_category', '=', 'sfao_required');
            });

        // Apply campus filter
        if ($campusFilter !== 'all') {
            $query->where('users.campus_id', $campusFilter);
        }

        $students = $query->select(
                'users.id as student_id',
                'users.name',
                'users.email',
                'users.created_at',
                'users.campus_id',
                DB::raw('MAX(student_submitted_documents.updated_at) as last_uploaded'),
                DB::raw('COUNT(DISTINCT student_submitted_documents.id) as documents_count')
            )
            ->groupBy('users.id', 'users.name', 'users.email', 'users.created_at', 'users.campus_id')
            ->get();

        // Apply sorting after getting the data
        $students = $students->sortBy(function($student) use ($sortBy) {
            switch ($sortBy) {
                case 'name':
                    return $student->name;
                case 'email':
                    return $student->email;
                case 'date_joined':
                    return $student->created_at;
                case 'last_uploaded':
                    return $student->last_uploaded;
                case 'documents_count':
                    return $student->documents_count;
                default:
                    return $student->name;
            }
        });

        if ($sortOrder === 'desc') {
            $students = $students->reverse();
        }

        // Load applications for each student separately to ensure relationships are loaded
        $studentIds = $students->pluck('student_id');
        $applicationsData = Application::with('scholarship')
            ->whereIn('user_id', $studentIds)
            ->get()
            ->groupBy('user_id');

        // Add application status information to each student
        $students->each(function($student) use ($applicationsData) {
            $studentApplications = $applicationsData->get($student->student_id, collect());
            $student->applications = $studentApplications;
            $student->has_applications = $studentApplications->count() > 0;
            $student->has_documents = $student->documents_count > 0;
            $student->application_status = $studentApplications->pluck('status')->unique()->toArray();
            $student->applied_scholarships = $studentApplications->pluck('scholarship.scholarship_name')->toArray();
            $student->applications_with_types = $studentApplications->map(function($app) {
                return [
                    'id' => $app->id,
                      'scholarship_name' => $app->scholarship->scholarship_name,
                      'status' => $app->status,
                      'grant_count' => $app->grant_count,
                    'grant_count_display' => $app->getGrantCountDisplay(),
                    'grant_count_badge_color' => $app->getGrantCountBadgeColor()
                ];
            });
        });

        // Note: Status filtering is now done client-side via Alpine.js in the view
        // This allows tab switching without page refresh, similar to Scholarships dropdown
        // Only apply status filter if explicitly requested via form submission (not tab-based)
        if ($statusFilter !== 'all' && !str_starts_with($activeTab, 'applicants-')) {
            $students = $students->filter(function($student) use ($statusFilter) {
                if ($statusFilter === 'not_applied') {
                    return !$student->has_applications;
                } else {
                    return in_array($statusFilter, $student->application_status ?? []);
                }
            });
        }

        // Get applications only from students under this SFAO admin's jurisdiction
        $applications = Application::with('user', 'scholarship')
            ->whereHas('user', function($query) use ($campusIds) {
                $query->whereIn('campus_id', $campusIds);
            })
            ->get();
            
        // Get scholarship type filter from tab parameter
        $scholarshipTypeFilter = $request->get('tab', 'scholarships');
        
        // Build scholarships query
        $scholarshipsQuery = Scholarship::withCount(['applications' => function($query) use ($campusIds) {
            $query->whereHas('user', function($userQuery) use ($campusIds) {
                $userQuery->whereIn('campus_id', $campusIds);
            });
        }]);
        
        // Apply scholarship type filter
        if ($scholarshipTypeFilter === 'scholarships-internal') {
            $scholarshipsQuery->where('scholarship_type', 'internal');
        } elseif ($scholarshipTypeFilter === 'scholarships-external') {
            $scholarshipsQuery->where('scholarship_type', 'external');
        } elseif ($scholarshipTypeFilter === 'scholarships-private') {
            $scholarshipsQuery->where('scholarship_type', 'private');
        } elseif ($scholarshipTypeFilter === 'scholarships-government') {
            $scholarshipsQuery->where('scholarship_type', 'government');
        }
        // If 'scholarships' (all), don't apply any type filter
        
        $scholarships = $scholarshipsQuery->get();

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $scholarships = $this->sortScholarships($scholarships, $sortBy, $sortOrder);

        // Get campus options for filtering
        $campusOptions = collect([['id' => 'all', 'name' => 'All Campuses']])
            ->merge($sfaoCampus->getAllCampusesUnder()->map(function($campus) {
                return ['id' => $campus->id, 'name' => $campus->name];
            }));

        // Get reports for the SFAO admin with filtering
        $reportsQuery = \App\Models\Report::where('sfao_user_id', session('user_id'))
            ->with(['campus', 'reviewer']);

        // Apply status filter
        if ($request->has('status') && $request->status !== 'all') {
            $reportsQuery->where('status', $request->status);
        }

        // Apply type filter
        if ($request->has('type') && $request->type !== 'all') {
            $reportsQuery->where('report_type', $request->type);
        }

        $reports = $reportsQuery->orderBy('created_at', 'desc')->paginate(10);

        // Get scholars data for the scholars tab (students who have scholar records)
        $scholarsQuery = \App\Models\Scholar::with(['user', 'scholarship', 'user.campus'])
            ->whereHas('user', function($query) use ($campusIds) {
                $query->whereIn('campus_id', $campusIds);
            });

        // Apply campus filter for scholars
        if ($campusFilter !== 'all') {
            $scholarsQuery->whereHas('user', function($query) use ($campusFilter) {
                $query->where('campus_id', $campusFilter);
            });
        }

        // Apply status filter for scholars (only if not using tab-based filtering)
        // Tab-based filtering (scholars-new, scholars-old) is done client-side
        if ($statusFilter !== 'all' && $statusFilter !== 'not_applied' && !str_starts_with($activeTab, 'scholars-')) {
            $scholarsQuery->where('status', $statusFilter);
        }

        // Apply sorting for scholars
        $scholarsSortBy = $request->get('scholars_sort_by', 'created_at');
        $scholarsSortOrder = $request->get('scholars_sort_order', 'desc');
        
        switch ($scholarsSortBy) {
            case 'name':
                $scholarsQuery->leftJoin('users as sort_users', 'scholars.user_id', '=', 'sort_users.id')
                    ->orderBy('sort_users.name', $scholarsSortOrder)
                    ->select('scholars.*');
                break;
            case 'email':
                $scholarsQuery->leftJoin('users as sort_users', 'scholars.user_id', '=', 'sort_users.id')
                    ->orderBy('sort_users.email', $scholarsSortOrder)
                    ->select('scholars.*');
                break;
            case 'scholarship':
                $scholarsQuery->leftJoin('scholarships as sort_scholarships', 'scholars.scholarship_id', '=', 'sort_scholarships.id')
                    ->orderBy('sort_scholarships.scholarship_name', $scholarsSortOrder)
                    ->select('scholars.*');
                break;
            case 'status':
                $scholarsQuery->orderBy('scholars.status', $scholarsSortOrder);
                break;
            case 'type':
                $scholarsQuery->orderBy('scholars.type', $scholarsSortOrder);
                break;
            default:
                $scholarsQuery->orderBy('scholars.created_at', $scholarsSortOrder);
        }

        $scholars = $scholarsQuery->get();

        // Use the activeTab we already determined earlier (or get from request if not set)
        if (!isset($activeTab)) {
            $activeTab = session('active_tab', $request->get('tab', 'scholarships'));
        }

        // Debug: Log the filtering parameters
        \Illuminate\Support\Facades\Log::info('SFAO Dashboard Filtering', [
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'campus_filter' => $campusFilter,
            'status_filter' => $statusFilter,
            'students_count' => $students->count(),
            'scholars_count' => $scholars->count(),
            'active_tab' => $activeTab
        ]);

        return view('sfao.dashboard', compact('user', 'students', 'applications', 'scholarships', 'sfaoCampus', 'campusOptions', 'sortBy', 'sortOrder', 'campusFilter', 'statusFilter', 'reports', 'activeTab', 'scholars', 'scholarsSortBy', 'scholarsSortOrder'));
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
        $students = DB::table('student_submitted_documents')
            ->join('users', 'student_submitted_documents.user_id', '=', 'users.id')
            ->whereIn('users.campus_id', $campusIds)
            ->where('student_submitted_documents.document_category', 'sfao_required')
            ->select(
                'users.id as student_id',
                'users.name',
                'users.email',
                'users.campus_id',
                DB::raw('MAX(student_submitted_documents.updated_at) as last_uploaded')
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
     * Central Dashboard - Only shows scholars (selected students), not applicants
     */
    public function centralDashboard(Request $request)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        // Get filtering parameters
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $statusFilter = $request->get('status_filter', 'all');
        $campusFilter = $request->get('campus_filter', 'all');
        $scholarshipFilter = $request->get('scholarship_filter', 'all');
        $applicantTypeFilter = $request->get('applicant_type_filter', 'all');

        // Build applications query with filtering
        $applicationsQuery = Application::with(['user', 'scholarship', 'user.campus'])
            ->whereHas('user', function($query) {
                $query->where('role', 'student');
            });

        // Apply status filter
        if ($statusFilter !== 'all') {
            $applicationsQuery->where('status', $statusFilter);
        }
        // If 'all' is selected, don't apply any status filter to show all statuses

        // Apply campus filter
        if ($campusFilter !== 'all') {
            $applicationsQuery->whereHas('user', function($query) use ($campusFilter) {
                $query->where('campus_id', $campusFilter);
            });
        }

        // Apply scholarship filter
        if ($scholarshipFilter !== 'all') {
            $applicationsQuery->where('scholarship_id', $scholarshipFilter);
        }


        // Apply sorting
        switch ($sortBy) {
            case 'name':
                $applicationsQuery->join('users', 'applications.user_id', '=', 'users.id')
                    ->orderBy('users.name', $sortOrder);
                break;
            case 'email':
                $applicationsQuery->join('users', 'applications.user_id', '=', 'users.id')
                    ->orderBy('users.email', $sortOrder);
                break;
            case 'scholarship':
                $applicationsQuery->join('scholarships', 'applications.scholarship_id', '=', 'scholarships.id')
                    ->orderBy('scholarships.scholarship_name', $sortOrder);
                break;
            case 'status':
                $applicationsQuery->orderBy('applications.status', $sortOrder);
                break;
            default:
                $applicationsQuery->orderBy('applications.created_at', $sortOrder);
        }

        $applications = $applicationsQuery->get();

        // Get scholarship type filter from tab parameter
        $scholarshipTypeFilter = $request->get('tab', 'scholarships');
        
        // Build scholarships query
        $scholarshipsQuery = Scholarship::with(['conditions', 'requiredDocuments']);
        
        // Apply scholarship type filter
        if ($scholarshipTypeFilter === 'scholarships-internal') {
            $scholarshipsQuery->where('scholarship_type', 'internal');
        } elseif ($scholarshipTypeFilter === 'scholarships-external') {
            $scholarshipsQuery->where('scholarship_type', 'external');
        } elseif ($scholarshipTypeFilter === 'scholarships-private') {
            $scholarshipsQuery->where('scholarship_type', 'private');
        } elseif ($scholarshipTypeFilter === 'scholarships-government') {
            $scholarshipsQuery->where('scholarship_type', 'government');
        }
        // If 'scholarships' (all), don't apply any type filter
        
        $scholarships = $scholarshipsQuery->get();
        
        // Get all reports with relationships for full functionality
        $query = \App\Models\Report::with(['sfaoUser', 'campus', 'reviewer']);

        // Apply filters if provided
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('report_type', $request->type);
        }

        if ($request->filled('campus') && $request->campus !== 'all') {
            $query->where('campus_id', $request->campus);
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');

        switch ($sortBy) {
            case 'submitted_at':
                $query->orderBy('submitted_at', $sortOrder);
                break;
            case 'title':
                $query->orderBy('title', $sortOrder);
                break;
            case 'campus':
                $query->join('campuses', 'reports.campus_id', '=', 'campuses.id')
                     ->orderBy('campuses.name', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }

        $reports = $query->get();
        $totalReports = \App\Models\Report::count();

        // Group reports by status
        $reportsByStatus = [
            'submitted' => $reports->where('status', 'submitted'),
            'reviewed' => $reports->where('status', 'reviewed'),
            'approved' => $reports->where('status', 'approved'),
            'rejected' => $reports->where('status', 'rejected')
        ];

        // Get report statistics
        $reportStats = [
            'total_reports' => $totalReports,
            'submitted_reports' => $reportsByStatus['submitted']->count(),
            'reviewed_reports' => $reportsByStatus['reviewed']->count(),
            'approved_reports' => $reportsByStatus['approved']->count(),
            'pending_reports' => $reportsByStatus['submitted']->count(),
        ];

        // Generate comprehensive analytics data
        $analytics = $this->generateAnalyticsData();

        // Get all campuses for filter
        $campuses = \App\Models\Campus::all();
        
        // Get filter options for applications
        $campusOptions = $campuses->map(function($campus) {
            return [
                'id' => $campus->id,
                'name' => $campus->name
            ];
        })->toArray();
        
        $scholarshipOptions = $scholarships->map(function($scholarship) {
            return [
                'id' => $scholarship->id,
                'name' => $scholarship->scholarship_name
            ];
        })->toArray();
        
        $statusOptions = [
            ['value' => 'in_progress', 'label' => 'In Progress'],
            ['value' => 'approved', 'label' => 'Approved'],
            ['value' => 'rejected', 'label' => 'Rejected'],
            ['value' => 'pending', 'label' => 'Pending'],
            ['value' => 'claimed', 'label' => 'Claimed'],
            ['value' => 'all', 'label' => 'All Status']
        ];
        
        $applicantTypeOptions = [
            ['value' => 'new', 'label' => 'New Applicant'],
            ['value' => 'continuing', 'label' => 'Continuing Applicant'],
            ['value' => 'all', 'label' => 'All Types']
        ];
        
        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $scholarships = $this->sortScholarships($scholarships, $sortBy, $sortOrder);

        // Get scholars data for the scholars tab
        $scholarsQuery = \App\Models\Scholar::with(['user', 'scholarship', 'user.campus']);

        // Apply scholars filtering
        if ($statusFilter !== 'all') {
            $scholarsQuery->where('status', $statusFilter);
        }

        // Apply campus filter for scholars
        if ($campusFilter !== 'all') {
            $scholarsQuery->whereHas('user', function($query) use ($campusFilter) {
                $query->where('campus_id', $campusFilter);
            });
        }

        // Apply scholarship filter for scholars
        if ($scholarshipFilter !== 'all') {
            $scholarsQuery->where('scholarship_id', $scholarshipFilter);
        }

        // Apply tab-based filtering for scholars (this takes precedence over applicant type filter)
        $tab = $request->get('tab', 'scholarships');
        if ($tab === 'scholars-new') {
            $scholarsQuery->where('type', 'new');
        } elseif ($tab === 'scholars-old') {
            $scholarsQuery->where('type', 'old');
        } elseif ($applicantTypeFilter !== 'all') {
            // Only apply applicant type filter if not on specific scholar tabs
            $scholarsQuery->where('type', $applicantTypeFilter);
        }

        // Apply scholars sorting
        switch ($sortBy) {
            case 'name':
                $scholarsQuery->join('users', 'scholars.user_id', '=', 'users.id')
                    ->orderBy('users.name', $sortOrder);
                break;
            case 'email':
                $scholarsQuery->join('users', 'scholars.user_id', '=', 'users.id')
                    ->orderBy('users.email', $sortOrder);
                break;
            case 'scholarship':
                $scholarsQuery->join('scholarships', 'scholars.scholarship_id', '=', 'scholarships.id')
                    ->orderBy('scholarships.scholarship_name', $sortOrder);
                break;
            case 'status':
                $scholarsQuery->orderBy('scholars.status', $sortOrder);
                break;
            case 'type':
                $scholarsQuery->orderBy('scholars.type', $sortOrder);
                break;
            default:
                $scholarsQuery->orderBy('scholars.created_at', $sortOrder);
        }

        $scholars = $scholarsQuery->get();

        // Get qualified applicants (approved by SFAO but not yet selected as scholars)
        $qualifiedApplicantsQuery = User::with(['applications.scholarship', 'campus'])
            ->where('role', 'student')
            ->whereHas('applications', function($query) {
                $query->where('status', 'approved');
            })
            ->whereDoesntHave('scholars'); // Not already a scholar

        // Apply campus filter for qualified applicants
        if ($campusFilter !== 'all') {
            $qualifiedApplicantsQuery->where('campus_id', $campusFilter);
        }

        // Apply scholarship filter for qualified applicants
        if ($scholarshipFilter !== 'all') {
            $qualifiedApplicantsQuery->whereHas('applications', function($query) use ($scholarshipFilter) {
                $query->where('scholarship_id', $scholarshipFilter);
            });
        }

        // Apply sorting for qualified applicants
        switch ($sortBy) {
            case 'name':
                $qualifiedApplicantsQuery->orderBy('first_name', $sortOrder);
                break;
            case 'campus':
                $qualifiedApplicantsQuery->join('campuses', 'users.campus_id', '=', 'campuses.id')
                    ->orderBy('campuses.name', $sortOrder);
                break;
            case 'scholarship':
                $qualifiedApplicantsQuery->join('applications', 'users.id', '=', 'applications.user_id')
                    ->join('scholarships', 'applications.scholarship_id', '=', 'scholarships.id')
                    ->orderBy('scholarships.scholarship_name', $sortOrder);
                break;
            case 'date_approved':
                $qualifiedApplicantsQuery->join('applications', 'users.id', '=', 'applications.user_id')
                    ->orderBy('applications.updated_at', $sortOrder);
                break;
            default:
                $qualifiedApplicantsQuery->orderBy('users.created_at', $sortOrder);
        }

        $qualifiedApplicants = $qualifiedApplicantsQuery->get();

        // Ensure qualifiedApplicants is always a collection
        if (!$qualifiedApplicants) {
            $qualifiedApplicants = collect();
        }

        // Get endorsed applicants (approved by SFAO and ready for scholar selection)
        $endorsedApplicantsQuery = Application::with(['user', 'scholarship', 'user.campus'])
            ->where('status', 'approved')
            ->whereDoesntHave('user.scholars'); // Not already a scholar

        // Apply campus filter for endorsed applicants
        if ($campusFilter !== 'all') {
            $endorsedApplicantsQuery->whereHas('user', function($query) use ($campusFilter) {
                $query->where('campus_id', $campusFilter);
            });
        }

        // Apply scholarship filter for endorsed applicants
        if ($scholarshipFilter !== 'all') {
            $endorsedApplicantsQuery->where('scholarship_id', $scholarshipFilter);
        }

        // Apply sorting for endorsed applicants
        switch ($sortBy) {
            case 'name':
                $endorsedApplicantsQuery->join('users', 'applications.user_id', '=', 'users.id')
                    ->orderBy('users.name', $sortOrder);
                break;
            case 'email':
                $endorsedApplicantsQuery->join('users', 'applications.user_id', '=', 'users.id')
                    ->orderBy('users.email', $sortOrder);
                break;
            case 'scholarship':
                $endorsedApplicantsQuery->join('scholarships', 'applications.scholarship_id', '=', 'scholarships.id')
                    ->orderBy('scholarships.scholarship_name', $sortOrder);
                break;
            case 'status':
                $endorsedApplicantsQuery->orderBy('applications.status', $sortOrder);
                break;
            default:
                $endorsedApplicantsQuery->orderBy('applications.created_at', $sortOrder);
        }

        $endorsedApplicants = $endorsedApplicantsQuery->get();

        // Ensure endorsedApplicants is always a collection
        if (!$endorsedApplicants) {
            $endorsedApplicants = collect();
        }

        return view('central.dashboard', compact('applications', 'scholars', 'qualifiedApplicants', 'endorsedApplicants', 'scholarships', 'reports', 'reportsByStatus', 'totalReports', 'campuses', 'reportStats', 'analytics', 'campusOptions', 'scholarshipOptions', 'statusOptions', 'applicantTypeOptions', 'sortBy', 'sortOrder', 'statusFilter', 'campusFilter', 'scholarshipFilter', 'applicantTypeFilter'));
    }

    /**
     * Get filtered analytics data for statistics dashboard
     */
    public function getFilteredAnalytics(Request $request)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $filters = $request->input('filters', []);
        $analytics = $this->generateAnalyticsData($filters);
        
        return response()->json([
            'success' => true,
            'analytics' => $analytics
        ]);
    }

    /**
     * Show validation page for an endorsed (approved) application for Central admin.
     */
    public function showEndorsedValidation(\Illuminate\Http\Request $request, \App\Models\Application $application)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        // Eager-load related data needed for profile/validation
        $application->load(['user.campus', 'user.form', 'scholarship']);

        // Get submitted documents if present
        $submittedDocuments = \App\Models\StudentSubmittedDocument::where('user_id', $application->user_id)
            ->where('scholarship_id', $application->scholarship_id)
            ->orderBy('document_category')
            ->orderBy('document_name')
            ->get();

        return view('central.endorsed.validate', [
            'application' => $application,
            'user' => $application->user,
            'scholarship' => $application->scholarship,
            'submittedDocuments' => $submittedDocuments,
        ]);
    }

    /**
     * Generate comprehensive analytics data for the statistics dashboard
     */
    private function generateAnalyticsData($filters = [])
    {
        // Extract filter values
        $timePeriod = $filters['timePeriod'] ?? 'all';
        $campusId = $filters['campus'] ?? 'all';
        
        // Build base query conditions
        $applicationQuery = \App\Models\Application::query();
        $userQuery = \App\Models\User::query();
        $reportQuery = \App\Models\Report::query();
        
        // Apply time period filter
        if ($timePeriod !== 'all') {
            $dateCondition = $this->getDateCondition($timePeriod);
            if ($dateCondition) {
                $applicationQuery->whereBetween('created_at', $dateCondition);
                $userQuery->whereBetween('created_at', $dateCondition);
                $reportQuery->whereBetween('created_at', $dateCondition);
            }
        }
        
        
        // Apply campus filter
        if ($campusId !== 'all') {
            $applicationQuery->whereHas('user', function($query) use ($campusId) {
                $query->where('campus_id', $campusId);
            });
            $userQuery->where('campus_id', $campusId);
            $reportQuery->where('campus_id', $campusId);
        }
        
        // Get basic report statistics
        $totalReports = $reportQuery->count();
        $submittedReports = $reportQuery->where('status', 'submitted')->count();
        $approvedReports = $reportQuery->where('status', 'approved')->count();
        $rejectedReports = $reportQuery->where('status', 'rejected')->count();
        $draftReports = $reportQuery->where('status', 'draft')->count();
        $pendingReviews = $reportQuery->where('status', 'submitted')->count();

        // Get comprehensive application statistics
        $totalApplications = $applicationQuery->count();
        $approvedApplications = (clone $applicationQuery)->where('status', 'approved')->count();
        $rejectedApplications = (clone $applicationQuery)->where('status', 'rejected')->count();
        $pendingApplications = (clone $applicationQuery)->where('status', 'pending')->count();
        $claimedApplications = (clone $applicationQuery)->where('status', 'claimed')->count();

        // Get scholarship statistics
        $totalScholarships = \App\Models\Scholarship::count();
        $activeScholarships = \App\Models\Scholarship::where('is_active', true)->count();
        $acceptingApplicationsScholarships = \App\Models\Scholarship::acceptingApplications()->count();
        $highPriorityScholarships = \App\Models\Scholarship::highPriority()->count();
        $oneTimeScholarships = \App\Models\Scholarship::where('grant_type', 'one_time')->count();
        $recurringScholarships = \App\Models\Scholarship::where('grant_type', 'recurring')->count();
        $discontinuedScholarships = \App\Models\Scholarship::where('grant_type', 'discontinued')->count();

        // Get user statistics
        $totalUsers = \App\Models\User::count();
        $totalStudents = \App\Models\User::where('role', 'student')->count();
        $totalSfaoUsers = \App\Models\User::where('role', 'sfao')->count();
        $totalCentralUsers = \App\Models\User::where('role', 'central')->count();

        // Get demographic statistics from forms table
        $maleStudents = \App\Models\Form::whereHas('user', function($query) {
            $query->where('role', 'student');
        })->where('sex', 'male')->count();
        $femaleStudents = \App\Models\Form::whereHas('user', function($query) {
            $query->where('role', 'student');
        })->where('sex', 'female')->count();
        $studentsWithApplications = \App\Models\User::where('role', 'student')
            ->whereHas('applications')
            ->count();
        $studentsWithoutApplications = $totalStudents - $studentsWithApplications;

        // Get application status by gender (using forms table)
        $maleApplications = \App\Models\Application::whereHas('user.form', function($query) {
            $query->where('sex', 'male');
        })->count();
        $femaleApplications = \App\Models\Application::whereHas('user.form', function($query) {
            $query->where('sex', 'female');
        })->count();

        $maleApprovedApplications = \App\Models\Application::whereHas('user.form', function($query) {
            $query->where('sex', 'male');
        })->where('status', 'approved')->count();
        $femaleApprovedApplications = \App\Models\Application::whereHas('user.form', function($query) {
            $query->where('sex', 'female');
        })->where('status', 'approved')->count();

        $maleRejectedApplications = \App\Models\Application::whereHas('user.form', function($query) {
            $query->where('sex', 'male');
        })->where('status', 'rejected')->count();
        $femaleRejectedApplications = \App\Models\Application::whereHas('user.form', function($query) {
            $query->where('sex', 'female');
        })->where('status', 'rejected')->count();

        $malePendingApplications = \App\Models\Application::whereHas('user.form', function($query) {
            $query->where('sex', 'male');
        })->where('status', 'pending')->count();
        $femalePendingApplications = \App\Models\Application::whereHas('user.form', function($query) {
            $query->where('sex', 'female');
        })->where('status', 'pending')->count();

        // Get year level distribution from forms table
        $yearLevelStats = \App\Models\Form::whereHas('user', function($query) {
            $query->where('role', 'student');
        })->selectRaw('year_level, COUNT(*) as count')
            ->groupBy('year_level')
            ->get();

        // Normalize year level labels to standard format
        $yearLevelMapping = [
            '1st Year' => '1st Year',
            'First Year' => '1st Year',
            '1st' => '1st Year',
            '2nd Year' => '2nd Year', 
            'Second Year' => '2nd Year',
            '2nd' => '2nd Year',
            '3rd Year' => '3rd Year',
            'Third Year' => '3rd Year', 
            '3rd' => '3rd Year',
            '4th Year' => '4th Year',
            'Fourth Year' => '4th Year',
            '4th' => '4th Year',
        ];

        // Group and normalize the data
        $normalizedYearLevels = [];
        foreach ($yearLevelStats as $stat) {
            $normalizedLabel = $yearLevelMapping[$stat->year_level] ?? $stat->year_level;
            if (!isset($normalizedYearLevels[$normalizedLabel])) {
                $normalizedYearLevels[$normalizedLabel] = 0;
            }
            $normalizedYearLevels[$normalizedLabel] += $stat->count;
        }

        // Sort by year level order
        $yearLevelOrder = ['1st Year', '2nd Year', '3rd Year', '4th Year'];
        $sortedYearLevels = [];
        foreach ($yearLevelOrder as $level) {
            if (isset($normalizedYearLevels[$level])) {
                $sortedYearLevels[$level] = $normalizedYearLevels[$level];
            }
        }

        $yearLevelLabels = array_keys($sortedYearLevels);
        $yearLevelCounts = array_values($sortedYearLevels);

        // Get program distribution from forms table
        $programStats = \App\Models\Form::whereHas('user', function($query) {
            $query->where('role', 'student');
        })->selectRaw('program, COUNT(*) as count')
            ->groupBy('program')
            ->orderBy('count', 'desc')
            ->limit(10) // Top 10 programs
            ->get();

        $programLabels = $programStats->pluck('program')->toArray();
        $programCounts = $programStats->pluck('count')->toArray();

        // Get application status by year level (using forms table)
        $yearLevelApplicationStats = [];
        $standardYearLevels = ['1st Year', '2nd Year', '3rd Year', '4th Year'];
        
        foreach ($standardYearLevels as $standardYearLevel) {
            // Get all possible variations for this year level
            $yearLevelVariations = [];
            foreach ($yearLevelMapping as $original => $normalized) {
                if ($normalized === $standardYearLevel) {
                    $yearLevelVariations[] = $original;
                }
            }
            
            // Get applications for all variations of this year level
            $yearLevelApplications = \App\Models\Application::whereHas('user.form', function($query) use ($yearLevelVariations) {
                $query->whereIn('year_level', $yearLevelVariations);
            })->get();

            $yearLevelApplicationStats[] = [
                'year_level' => $standardYearLevel,
                'total_applications' => $yearLevelApplications->count(),
                'approved_applications' => $yearLevelApplications->where('status', 'approved')->count(),
                'rejected_applications' => $yearLevelApplications->where('status', 'rejected')->count(),
                'pending_applications' => $yearLevelApplications->where('status', 'pending')->count(),
                'claimed_applications' => $yearLevelApplications->where('status', 'claimed')->count()
            ];
        }

        // Get monthly trends (last 6 months)
        $monthlyLabels = [];
        $monthlyReports = [];
        $monthlyApplications = [];
        $monthlyApprovedApplications = [];
        $monthlyRejectedApplications = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyLabels[] = $date->format('M');
            
            $monthlyReports[] = \App\Models\Report::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
                
            $monthlyApplications[] = \App\Models\Application::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $monthlyApprovedApplications[] = \App\Models\Application::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status', 'approved')
                ->count();

            $monthlyRejectedApplications[] = \App\Models\Application::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status', 'rejected')
                ->count();
        }

        // Get campus performance data with applications
        $campuses = \App\Models\Campus::withCount(['reports', 'users' => function($query) {
            $query->where('role', 'student');
        }])->get();
        
        $campusNames = $campuses->pluck('name')->toArray();
        $campusReports = $campuses->pluck('reports_count')->toArray();
        $campusStudents = $campuses->pluck('users_count')->toArray();

        // Get campus application statistics
        $campusApplicationStats = [];
        foreach ($campuses as $campus) {
            $campusApplications = \App\Models\Application::whereHas('user', function($query) use ($campus) {
                $query->where('campus_id', $campus->id);
            })->get();

            // Get total students for this campus
            $campusStudents = \App\Models\User::where('role', 'student')
                ->where('campus_id', $campus->id)
                ->count();

            // Get students with applications for this campus
            $campusStudentsWithApplications = \App\Models\User::where('role', 'student')
                ->where('campus_id', $campus->id)
                ->whereHas('applications')
                ->count();

            // Get gender statistics for this campus
            $campusMaleStudents = \App\Models\Form::whereHas('user', function($query) use ($campus) {
                $query->where('role', 'student')->where('campus_id', $campus->id);
            })->where('sex', 'male')->count();
            
            $campusFemaleStudents = \App\Models\Form::whereHas('user', function($query) use ($campus) {
                $query->where('role', 'student')->where('campus_id', $campus->id);
            })->where('sex', 'female')->count();

            // Get year level statistics for this campus
            $campusYearLevelStats = \App\Models\Form::whereHas('user', function($query) use ($campus) {
                $query->where('role', 'student')->where('campus_id', $campus->id);
            })->selectRaw('year_level, COUNT(*) as count')
                ->groupBy('year_level')
                ->get();

            // Normalize year level data for this campus
            $campusYearLevelMapping = [
                '1st Year' => '1st Year',
                'First Year' => '1st Year',
                '1st' => '1st Year',
                '2nd Year' => '2nd Year', 
                'Second Year' => '2nd Year',
                '2nd' => '2nd Year',
                '3rd Year' => '3rd Year',
                'Third Year' => '3rd Year', 
                '3rd' => '3rd Year',
                '4th Year' => '4th Year',
                'Fourth Year' => '4th Year',
                '4th' => '4th Year',
            ];

            $campusNormalizedYearLevels = [];
            foreach ($campusYearLevelStats as $stat) {
                $normalizedLabel = $campusYearLevelMapping[$stat->year_level] ?? $stat->year_level;
                if (!isset($campusNormalizedYearLevels[$normalizedLabel])) {
                    $campusNormalizedYearLevels[$normalizedLabel] = 0;
                }
                $campusNormalizedYearLevels[$normalizedLabel] += $stat->count;
            }

            // Sort by year level order
            $campusYearLevelOrder = ['1st Year', '2nd Year', '3rd Year', '4th Year'];
            $campusSortedYearLevels = [];
            foreach ($campusYearLevelOrder as $level) {
                if (isset($campusNormalizedYearLevels[$level])) {
                    $campusSortedYearLevels[$level] = $campusNormalizedYearLevels[$level];
                }
            }

            // Generate campus-specific monthly trends
            $campusMonthlyTrends = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthKey = $date->format('M');
                
                $campusMonthlyApplications = \App\Models\Application::whereHas('user', function($query) use ($campus) {
                    $query->where('campus_id', $campus->id);
                })->whereYear('created_at', $date->year)
                  ->whereMonth('created_at', $date->month)
                  ->get();
                
                $campusMonthlyTrends[strtolower($monthKey)] = [
                    'total_applications' => $campusMonthlyApplications->count(),
                    'approved_applications' => $campusMonthlyApplications->where('status', 'approved')->count(),
                    'rejected_applications' => $campusMonthlyApplications->where('status', 'rejected')->count(),
                    'pending_applications' => $campusMonthlyApplications->where('status', 'pending')->count(),
                ];
            }

            $campusApplicationStats[] = [
                'campus_id' => $campus->id,
                'campus_name' => $campus->name,
                'total_students' => $campusStudents,
                'total_applications' => $campusApplications->count(),
                'approved_applications' => $campusApplications->where('status', 'approved')->count(),
                'rejected_applications' => $campusApplications->where('status', 'rejected')->count(),
                'pending_applications' => $campusApplications->where('status', 'pending')->count(),
                'claimed_applications' => $campusApplications->where('status', 'claimed')->count(),
                'students_with_applications' => $campusStudentsWithApplications,
                'students_without_applications' => $campusStudents - $campusStudentsWithApplications,
                'approval_rate' => $campusApplications->count() > 0 ? 
                    round(($campusApplications->where('status', 'approved')->count() / $campusApplications->count()) * 100, 2) : 0,
                // Add gender statistics
                'male_students' => $campusMaleStudents,
                'female_students' => $campusFemaleStudents,
                // Add year level statistics
                'year_level_labels' => array_keys($campusSortedYearLevels),
                // Add campus-specific monthly trends
                'monthly_trends' => $campusMonthlyTrends,
                'year_level_counts' => array_values($campusSortedYearLevels)
            ];
        }

        // Get scholarship distribution by type
        $scholarshipTypes = \App\Models\Scholarship::selectRaw('scholarship_type, COUNT(*) as count')
            ->groupBy('scholarship_type')
            ->get();
        
        $scholarshipTypeNames = $scholarshipTypes->pluck('scholarship_type')->toArray();
        $scholarshipTypeCounts = $scholarshipTypes->pluck('count')->toArray();

        // Get scholarship performance data
        $scholarshipPerformance = \App\Models\Scholarship::withCount(['applications', 'applications as approved_applications_count' => function($query) {
            $query->where('status', 'approved');
        }])->get()->map(function($scholarship) {
        return [
                'name' => $scholarship->scholarship_name,
                'type' => $scholarship->scholarship_type,
                'total_applications' => $scholarship->applications_count,
                'approved_applications' => $scholarship->approved_applications_count,
                'slots_available' => $scholarship->slots_available,
                'grant_amount' => $scholarship->grant_amount,
                'fill_percentage' => $scholarship->slots_available > 0 ? 
                    min(($scholarship->applications_count / $scholarship->slots_available) * 100, 100) : 0,
                'approval_rate' => $scholarship->applications_count > 0 ? 
                    round(($scholarship->approved_applications_count / $scholarship->applications_count) * 100, 2) : 0
            ];
        });

        // Get application status distribution
        $applicationStatusData = [
            'approved' => $approvedApplications,
            'rejected' => $rejectedApplications,
            'pending' => $pendingApplications,
            'claimed' => $claimedApplications
        ];


        // Get scholarship grant type distribution
        $grantTypeData = [
            'one_time' => $oneTimeScholarships,
            'recurring' => $recurringScholarships,
            'discontinued' => $discontinuedScholarships
        ];

        // Calculate approval rates
        $overallApprovalRate = $totalApplications > 0 ? round(($approvedApplications / $totalApplications) * 100, 2) : 0;
        $overallRejectionRate = $totalApplications > 0 ? round(($rejectedApplications / $totalApplications) * 100, 2) : 0;

        return [
            // Report Statistics
            'total_reports' => $totalReports,
            'submitted_reports' => $submittedReports,
            'approved_reports' => $approvedReports,
            'rejected_reports' => $rejectedReports,
            'draft_reports' => $draftReports,
            'pending_reviews' => $pendingReviews,
            
            // Application Statistics
            'total_applications' => $totalApplications,
            'approved_applications' => $approvedApplications,
            'rejected_applications' => $rejectedApplications,
            'pending_applications' => $pendingApplications,
            'claimed_applications' => $claimedApplications,
            'overall_approval_rate' => $overallApprovalRate,
            'overall_rejection_rate' => $overallRejectionRate,
            
            // Scholarship Statistics
            'total_scholarships' => $totalScholarships,
            'active_scholarships' => $activeScholarships,
            'accepting_applications_scholarships' => $acceptingApplicationsScholarships,
            'high_priority_scholarships' => $highPriorityScholarships,
            'one_time_scholarships' => $oneTimeScholarships,
            'recurring_scholarships' => $recurringScholarships,
            'discontinued_scholarships' => $discontinuedScholarships,
            
            // User Statistics
            'total_users' => $totalUsers,
            'total_students' => $totalStudents,
            'total_sfao_users' => $totalSfaoUsers,
            'total_central_users' => $totalCentralUsers,
            
            // Demographic Statistics
            'male_students' => $maleStudents,
            'female_students' => $femaleStudents,
            'students_with_applications' => $studentsWithApplications,
            'students_without_applications' => $studentsWithoutApplications,
            
            // Gender-based Application Statistics
            'male_applications' => $maleApplications,
            'female_applications' => $femaleApplications,
            'male_approved_applications' => $maleApprovedApplications,
            'female_approved_applications' => $femaleApprovedApplications,
            'male_rejected_applications' => $maleRejectedApplications,
            'female_rejected_applications' => $femaleRejectedApplications,
            'male_pending_applications' => $malePendingApplications,
            'female_pending_applications' => $femalePendingApplications,
            
            // Year Level and Program Data
            'year_level_labels' => $yearLevelLabels,
            'year_level_counts' => $yearLevelCounts,
            'program_labels' => $programLabels,
            'program_counts' => $programCounts,
            'year_level_application_stats' => $yearLevelApplicationStats,
            
            // Monthly Trends
            'monthly_labels' => $monthlyLabels,
            'monthly_reports' => $monthlyReports,
            'monthly_applications' => $monthlyApplications,
            'monthly_approved_applications' => $monthlyApprovedApplications,
            'monthly_rejected_applications' => $monthlyRejectedApplications,
            
            // Campus Data
            'campus_names' => $campusNames,
            'campus_reports' => $campusReports,
            'campus_students' => $campusStudents,
            'campus_application_stats' => $campusApplicationStats,
            
            // Scholarship Data
            'scholarship_types' => $scholarshipTypeNames,
            'scholarship_counts' => $scholarshipTypeCounts,
            'scholarship_performance' => $scholarshipPerformance,
            
            // Distribution Data
            'application_status_data' => $applicationStatusData,
            'grant_type_data' => $grantTypeData
        ];
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

    // =====================================================
    // DOCUMENT EVALUATION SYSTEM
    // =====================================================

    /**
     * Show evaluation overview - Stage 1: Select Scholarship
     */
    public function showEvaluation($userId)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $student = User::with(['applications.scholarship', 'campus'])->findOrFail($userId);
        
        // Get SFAO admin's campus to verify jurisdiction
        $sfaoAdmin = User::with('campus')->find(session('user_id'));
        $campusIds = $sfaoAdmin->campus->getAllCampusesUnder()->pluck('id')->toArray();
        
        if (!in_array($student->campus_id, $campusIds)) {
            return redirect()->route('sfao.dashboard')->with('error', 'You do not have permission to evaluate this student.');
        }

        // Get applications with scholarship data
        $applications = $student->applications()->with('scholarship')->get();
        
        return view('sfao.evaluation.stage1-scholarship-selection', compact('student', 'applications'));
    }

    /**
     * Show SFAO documents evaluation - Stage 2: Evaluate SFAO Documents
     */
    public function evaluateSfaoDocuments($userId, $scholarshipId)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $student = User::with(['campus'])->findOrFail($userId);
        $scholarship = Scholarship::with(['conditions', 'requiredDocuments'])->findOrFail($scholarshipId);
        
        // Verify SFAO has jurisdiction
        $sfaoAdmin = User::with('campus')->find(session('user_id'));
        $campusIds = $sfaoAdmin->campus->getAllCampusesUnder()->pluck('id')->toArray();
        
        if (!in_array($student->campus_id, $campusIds)) {
            return redirect()->route('sfao.dashboard')->with('error', 'You do not have permission to evaluate this student.');
        }

        // Get only SFAO required documents for this scholarship
        $sfaoDocuments = StudentSubmittedDocument::where('user_id', $userId)
            ->where('scholarship_id', $scholarshipId)
            ->where('document_category', 'sfao_required')
            ->with('evaluator')
            ->get();

        return view('sfao.evaluation.stage2-sfao-documents', compact(
            'student', 
            'scholarship', 
            'sfaoDocuments'
        ));
    }

    /**
     * Show scholarship documents evaluation - Stage 3: Evaluate Scholarship Documents
     */
    public function evaluateScholarshipDocuments($userId, $scholarshipId)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $student = User::with(['campus'])->findOrFail($userId);
        $scholarship = Scholarship::with(['conditions', 'requiredDocuments'])->findOrFail($scholarshipId);
        
        // Verify SFAO has jurisdiction
        $sfaoAdmin = User::with('campus')->find(session('user_id'));
        $campusIds = $sfaoAdmin->campus->getAllCampusesUnder()->pluck('id')->toArray();
        
        if (!in_array($student->campus_id, $campusIds)) {
            return redirect()->route('sfao.dashboard')->with('error', 'You do not have permission to evaluate this student.');
        }

        // Get only scholarship required documents for this scholarship
        $scholarshipDocuments = StudentSubmittedDocument::where('user_id', $userId)
            ->where('scholarship_id', $scholarshipId)
            ->where('document_category', 'scholarship_required')
            ->with('evaluator')
            ->get();

        return view('sfao.evaluation.stage3-scholarship-documents', compact(
            'student', 
            'scholarship', 
            'scholarshipDocuments'
        ));
    }

    /**
     * Submit SFAO documents evaluation
     */
    public function submitSfaoEvaluation(Request $request, $userId, $scholarshipId)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $request->validate([
            'evaluations' => 'required|array',
            'evaluations.*.document_id' => 'required|exists:student_submitted_documents,id',
            'evaluations.*.status' => 'required|in:approved,pending,rejected',
        ]);

        $evaluatorId = session('user_id');
        $evaluatedAt = now();

        foreach ($request->evaluations as $evaluation) {
            StudentSubmittedDocument::where('id', $evaluation['document_id'])
                ->where('user_id', $userId)
                ->where('scholarship_id', $scholarshipId)
                ->where('document_category', 'sfao_required')
                ->update([
                    'evaluation_status' => $evaluation['status'],
                    'evaluated_by' => $evaluatorId,
                    'evaluated_at' => $evaluatedAt,
                ]);
        }

        return redirect()->route('sfao.evaluation.scholarship-documents', ['user_id' => $userId, 'scholarship_id' => $scholarshipId])
            ->with('success', 'SFAO documents evaluation completed. Proceeding to scholarship documents.');
    }

    /**
     * Submit scholarship documents evaluation
     */
    public function submitScholarshipEvaluation(Request $request, $userId, $scholarshipId)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $request->validate([
            'evaluations' => 'required|array',
            'evaluations.*.document_id' => 'required|exists:student_submitted_documents,id',
            'evaluations.*.status' => 'required|in:approved,pending,rejected',
        ]);

        $evaluatorId = session('user_id');
        $evaluatedAt = now();

        foreach ($request->evaluations as $evaluation) {
            StudentSubmittedDocument::where('id', $evaluation['document_id'])
                ->where('user_id', $userId)
                ->where('scholarship_id', $scholarshipId)
                ->where('document_category', 'scholarship_required')
                ->update([
                    'evaluation_status' => $evaluation['status'],
                    'evaluated_by' => $evaluatorId,
                    'evaluated_at' => $evaluatedAt,
                ]);
        }

        return redirect()->route('sfao.evaluation.final', ['user_id' => $userId, 'scholarship_id' => $scholarshipId])
            ->with('success', 'Scholarship documents evaluation completed. Proceeding to final review.');
    }

    /**
     * Show final evaluation - Stage 4: Final Review
     */
    public function finalEvaluation($userId, $scholarshipId)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $student = User::with(['campus'])->findOrFail($userId);
        $scholarship = Scholarship::with(['conditions', 'requiredDocuments'])->findOrFail($scholarshipId);
        
        // Verify SFAO has jurisdiction
        $sfaoAdmin = User::with('campus')->find(session('user_id'));
        $campusIds = $sfaoAdmin->campus->getAllCampusesUnder()->pluck('id')->toArray();
        
        if (!in_array($student->campus_id, $campusIds)) {
            return redirect()->route('sfao.dashboard')->with('error', 'You do not have permission to evaluate this student.');
        }

        // Get all evaluated documents
        $evaluatedDocuments = StudentSubmittedDocument::where('user_id', $userId)
            ->where('scholarship_id', $scholarshipId)
            ->with('evaluator')
            ->get();

        // Get application for this scholarship
        $application = Application::where('user_id', $userId)
            ->where('scholarship_id', $scholarshipId)
            ->first();

        return view('sfao.evaluation.stage4-final-review', compact(
            'student', 
            'scholarship', 
            'evaluatedDocuments',
            'application'
        ));
    }

    /**
     * Submit final evaluation with remarks
     */
    public function submitFinalEvaluation(Request $request, $userId, $scholarshipId)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $request->validate([
            'action' => 'required|in:approve,reject,pending',
            'remarks' => 'nullable|string|max:1000',
        ]);

        // Get the application
        $application = Application::where('user_id', $userId)
            ->where('scholarship_id', $scholarshipId)
            ->first();

        if (!$application) {
            return redirect()->back()->with('error', 'Application not found.');
        }

        // Update application with remarks and status (including pending)
        $newStatus = match($request->action) {
            'approve' => 'approved',
            'reject' => 'rejected',
            'pending' => 'pending',
            default => $application->status,
        };

        $application->update([
            'status' => $newStatus,
            'remarks' => $request->remarks,
        ]);

        // Get document evaluation status for this application
        $documents = StudentSubmittedDocument::where('user_id', $userId)
            ->where('scholarship_id', $scholarshipId)
            ->get();

        $documentStatus = [
            'pending' => $documents->where('evaluation_status', 'pending')->count(),
            'rejected' => $documents->where('evaluation_status', 'rejected')->count(),
            'approved' => $documents->where('evaluation_status', 'approved')->count(),
        ];

        $pendingDocuments = $documents->where('evaluation_status', 'pending')->pluck('document_name')->toArray();
        $rejectedDocuments = $documents->where('evaluation_status', 'rejected')->pluck('document_name')->toArray();

        // Create notification for student
        $notificationTitle = match($request->action) {
            'approve' => 'Application Approved',
            'reject' => 'Application Rejected', 
            'pending' => 'Application Status Updated',
            default => 'Application Status Updated'
        };

        $notificationMessage = match($request->action) {
            'approve' => 'Your application for ' . $application->scholarship->scholarship_name . ' has been approved.',
            'reject' => 'Your application for ' . $application->scholarship->scholarship_name . ' has been rejected.',
            'pending' => 'Your application for ' . $application->scholarship->scholarship_name . ' has been set back to pending for further review.',
            default => 'Your application status has been updated.'
        };

        // Add document information to message if there are pending or rejected documents
        if ($request->action === 'pending' && (count($pendingDocuments) > 0 || count($rejectedDocuments) > 0)) {
            $documentInfo = [];
            if (count($pendingDocuments) > 0) {
                $documentInfo[] = 'Pending documents: ' . implode(', ', $pendingDocuments);
            }
            if (count($rejectedDocuments) > 0) {
                $documentInfo[] = 'Rejected documents: ' . implode(', ', $rejectedDocuments);
            }
            $notificationMessage .= ' ' . implode('. ', $documentInfo) . '.';
        }

        Notification::create([
            'user_id' => $userId,
            'type' => 'application_status',
            'title' => $notificationTitle,
            'message' => $notificationMessage,
            'data' => [
                'application_id' => $application->id,
                'scholarship_id' => $scholarshipId,
                'scholarship_name' => $application->scholarship->scholarship_name,
                'status' => $newStatus,
                'remarks' => $request->remarks,
                'document_status' => $documentStatus,
                'pending_documents' => $pendingDocuments,
                'rejected_documents' => $rejectedDocuments,
            ]
        ]);

        $message = match($request->action) {
            'approve' => 'Application approved successfully.',
            'reject' => 'Application rejected successfully.',
            'pending' => 'Application set to pending successfully.',
            default => 'Application status updated successfully.'
        };

        return redirect()->route('sfao.dashboard')
            ->with('success', $message);
    }
    
    /**
     * Get date condition for time period filter
     */
    private function getDateCondition($timePeriod)
    {
        $now = now();
        
        switch ($timePeriod) {
            case 'this_month':
                return [
                    $now->copy()->startOfMonth(),
                    $now->copy()->endOfMonth()
                ];
            case 'last_3_months':
                return [
                    $now->copy()->subMonths(3)->startOfMonth(),
                    $now->copy()->endOfMonth()
                ];
            case 'this_year':
                return [
                    $now->copy()->startOfYear(),
                    $now->copy()->endOfYear()
                ];
            default:
                return null;
        }
    }
}
