<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Application;
use App\Models\Scholarship;
use App\Models\Report;
use App\Models\Scholar;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Unified Dashboard Entry Point
     */
    public function index(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect('/login')->with('session_expired', true);
        }

        $role = session('role');

        return match($role) {
            'sfao' => $this->sfaoDashboard($request),
            'central' => $this->centralDashboard($request),
            'student' => $this->studentDashboard($request),
            default => redirect('/login')
        };
    }

    /**
     * SFAO Dashboard Logic
     */
    private function sfaoDashboard(Request $request)
    {

        $user = User::with('campus')->find(session('user_id'));
        $sfaoCampus = $user->campus;
        $monitoredCampuses = $sfaoCampus->getAllCampusesUnder();
        $campusIds = $monitoredCampuses->pluck('id');

        // --- DASHBOARD ANALYTICS ---
        $analytics = $this->getAnalytics($campusIds);
        
        // --- DATA LOADERS ---
        // These will eventually be moved to AJAX calls for better performance,
        // but for now we load them to maintain current functionality.
        
        // 1. Applicants Data (Delegated to ApplicantController logic if needed, but keeping here for dashboard view composition)
        $limit = 5; // Dashboard preview limit
        
        // 2. Scholarships Data
        $scholarshipsAll = Scholarship::withCount([
            'applications' => function($query) use ($campusIds) {
                $query->whereHas('user', function($userQuery) use ($campusIds) {
                    $userQuery->whereIn('campus_id', $campusIds);
                });
            },
            'scholars' => function($query) use ($campusIds) {
                $query->whereHas('user', function($userQuery) use ($campusIds) {
                    $userQuery->whereIn('campus_id', $campusIds);
                });
            }
        ])->get();
        // Sorting and Pagination logic for scholarships... (Simplified for initial migration)
        $scholarshipsAll = $this->paginate($scholarshipsAll, 5, $request->get('page_all', 1), 'page_all');
        $scholarshipsPrivate = $this->paginate($scholarshipsAll->getCollection()->where('scholarship_type', 'private'), 5, $request->get('page_private', 1), 'page_private');
        $scholarshipsGov = $this->paginate($scholarshipsAll->getCollection()->where('scholarship_type', 'government'), 5, $request->get('page_gov', 1), 'page_gov');

        // 3. Scholars Data
        $scholars = Scholar::with(['user', 'scholarship'])
            ->whereHas('user', function($query) use ($campusIds) {
                $query->whereIn('campus_id', $campusIds);
            })->get();

        // 4. Reports
        $reports = Report::where('sfao_user_id', session('user_id'))->latest()->paginate(5);

        // View Parameters
        $activeTab = $request->get('tabs', $request->get('tab', 'analytics'));
        $campusOptions = collect([['id' => 'all', 'name' => 'All Campus']])
            ->merge($monitoredCampuses->map(function($c) { return ['id' => $c->id, 'name' => $c->name]; }));

        // Pass empty collections for the "Detailed Lists" that are handled by AJAX or specific tabs
        // This prevents the view from crashing while we transition.
        $students = collect(); 
        $studentsAll = $this->paginate(collect(), 10, 1, 'page_applicants'); 
        
        // NOTE: We need to pull the full ApplicantController logic here TEMPORARILY 
        // because the dashboard view expects ALL variables to be present. 
        // In a full refactor, we would use View Composers or separate routes.
        // For now, I will use a helper method to keep this clean.
        $applicantData = $this->getApplicantData($request, $campusIds);

        // --- AJAX HANDLER ---
        if ($request->ajax()) {
            $view = '';
            $data = [];
            
            if ($activeTab === 'scholarships' || str_starts_with($activeTab, 'scholarships-')) {
                // Re-query with filters if needed (already handled by paginate params above if passed correctly)
                // For now, simple return of the list view with the updated data
                // Note: The $scholarshipsAll above is already filtered by page/type if params exist
                // We just need to determine WHICH list to return.
                
                // Simplified: Reuse the main collection logic. 
                // In a perfect refactor, we'd move this query logic to a service or dedicated method.
                
                // Recalculate based on specific request filters to be safe
                $query = Scholarship::withCount([
                    'applications' => function($query) use ($campusIds) {
                        $query->whereHas('user', function($userQuery) use ($campusIds) {
                            $userQuery->whereIn('campus_id', $campusIds);
                        });
                    },
                    'scholars' => function($query) use ($campusIds) {
                        $query->whereHas('user', function($userQuery) use ($campusIds) {
                            $userQuery->whereIn('campus_id', $campusIds);
                        });
                    }
                ]);
                
                if ($request->has('scholars_sort_by')) {
                     // This is actually for scholars tab, ignore
                }
                 
                if ($request->get('type_filter') && $request->get('type_filter') !== 'all') {
                    $query->where('scholarship_type', $request->get('type_filter'));
                }
                
                $sort = $request->get('sort_by', 'name');
                if ($sort === 'name') $sort = 'scholarship_name'; // Fix mapped column name
                
                $order = $request->get('sort_order', 'asc');
                $query->orderBy($sort, $order);
                
                $scholarships = $this->paginate($query->get(), 5, $request->get('page_scholarships', 1), 'page_scholarships');
                
                $view = 'sfao.scholarships.list';
                $data = ['scholarships' => $scholarships];

                return response()->json([
                    'html' => view($view, $data)->render()
                ]);

            } elseif ($activeTab === 'applicants' || str_starts_with($activeTab, 'applicants-')) {
                 // DEPRECATED: Logic moved to ApplicationController::sfaoApplicantsList
                 // This block should ideally not be hit if frontend is updated, 
                 // but we can fallback or return empty to avoid errors.
                 return response()->json(['error' => 'Endpoint moved to sfao.applicants.list'], 404);

            } elseif ($activeTab === 'scholars' || str_starts_with($activeTab, 'scholars-')) {
                // Handle Scholar Filtering
                $query = Scholar::with(['user', 'scholarship'])
                    ->whereHas('user', function($q) use ($campusIds) {
                        $q->whereIn('campus_id', $campusIds);
                    });
                    
                if ($request->get('campus_filter') && $request->get('campus_filter') !== 'all') {
                    $query->whereHas('user', function($q) use ($request) {
                        $q->where('campus_id', $request->get('campus_filter'));
                    });
                }
                
                if ($request->get('scholarship_filter') && $request->get('scholarship_filter') !== 'all') {
                    $query->where('scholarship_id', $request->get('scholarship_filter'));
                }
                
                 if ($request->get('type_filter') && $request->get('type_filter') !== 'all') {
                    $query->where('type', $request->get('type_filter'));
                }

                $scholarsList = $query->get();
                $scholarsList = $this->paginate($scholarsList, 5, $request->get('page_scholarships', 1), 'page_scholarships');
                
                $view = 'sfao.scholars.list';
                $data = ['scholars' => $scholarsList];
                
                
                // Get filtered scholars for accurate counts
                $filteredScholars = $query->get();
                
                $counts = [
                    'total' => $filteredScholars->count(),
                    'active' => $filteredScholars->where('status', 'active')->count(),
                    'new' => $filteredScholars->where('type', 'new')->count(),
                    'old' => $filteredScholars->where('type', 'old')->count()
                ];

                return response()->json([
                    'html' => view($view, $data)->render(),
                    'counts' => $counts
                ]);
            }

            if ($view) {
                return response()->json(['html' => view($view, $data)->render()]);
            }
        }


        // 5. Application Forms
        $forms = \App\Models\ApplicationForm::with(['campus', 'uploader'])
            ->whereIn('campus_id', $campusIds)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('sfao.index', array_merge([
            'user' => $user,
            'sfaoCampus' => $sfaoCampus,
            'monitoredCampuses' => $monitoredCampuses,
            'analytics' => $analytics,
            'scholarshipsAll' => $scholarshipsAll,
            'scholarshipsPrivate' => $scholarshipsPrivate,
            'scholarshipsGov' => $scholarshipsGov,
            'scholars' => $scholars,
            'reports' => $reports,
            'forms' => $forms,
            'activeTab' => $activeTab,
            'campusOptions' => $campusOptions,
            'sortBy' => 'name', 'sortOrder' => 'asc', 'campusFilter' => 'all', 'statusFilter' => 'all',
            'scholarsSortBy' => 'name', 'scholarsSortOrder' => 'asc'
        ], $applicantData));
    }

    private function getAnalytics($campusIds)
    {
        $analytics = [];
        
        // 1. Basic Counts
        $totalStudents = User::where('role', 'student')
            ->whereIn('campus_id', $campusIds)
            ->count();
            
        $studentsWithApplications = Application::whereHas('user', function($query) use ($campusIds) {
                $query->whereIn('campus_id', $campusIds);
            })
            ->distinct('user_id')
            ->count('user_id');
            
        $pendingApplications = Application::whereHas('user', function($query) use ($campusIds) {
                $query->whereIn('campus_id', $campusIds);
            })
            ->where('status', 'pending')
            ->count();
            
        $approvedApplications = Application::whereHas('user', function($query) use ($campusIds) {
                $query->whereIn('campus_id', $campusIds);
            })
            ->where('status', 'approved')
            ->count();
            
        $rejectedApplications = Application::whereHas('user', function($query) use ($campusIds) {
                $query->whereIn('campus_id', $campusIds);
            })
            ->where('status', 'rejected')
            ->count();

        $analytics['total_students'] = $totalStudents;
        $analytics['students_with_applications'] = $studentsWithApplications;
        $analytics['pending_applications'] = $pendingApplications;
        $analytics['approved_applications'] = $approvedApplications;
        $analytics['rejected_applications'] = $rejectedApplications;
        $analytics['approval_rate'] = $studentsWithApplications > 0 ? round(($approvedApplications / $studentsWithApplications) * 100, 1) : 0;

        // 2. Department Statistics
        // Get all departments
        $allDepartments = \App\Models\Department::all();
        // Fetch available scholarships for filter
        $availableScholarships = Scholarship::where('is_active', true)
            ->select('id', 'scholarship_name', 'scholarship_type')
            ->orderBy('scholarship_name')
            ->get();
        $analytics['available_scholarships'] = $availableScholarships;
        
        $analytics['all_departments'] = $allDepartments;
        
        // Map campuses to departments
        $sfaoUser = User::find(session('user_id'));
        $sfaoCampus = $sfaoUser->campus;
        
        $campusDepartments = [];
        foreach ($sfaoCampus->getAllCampusesUnder() as $camp) {
            $campusDepartments[$camp->id] = $camp->departments->pluck('short_name')->toArray();
        }
        $analytics['campus_departments'] = $campusDepartments;

        // Get Programs per Department (for filters)
        $dbProgramsData = User::where('role', 'student')
            ->whereIn('campus_id', $campusIds)
            ->whereNotNull('program')
            ->select('college', 'program')
            ->distinct()
            ->get()
            ->groupBy('college')
            ->map(function ($items) {
                return $items->pluck('program')->unique()->values()->all();
            })->toArray();
            
        // Merge with Standard Programs to ensure filters are populated
        $standardPrograms = $this->getStandardPrograms();
        $mergedPrograms = $standardPrograms;
        
        foreach ($dbProgramsData as $college => $programs) {
            if (isset($mergedPrograms[$college])) {
                $mergedPrograms[$college] = array_unique(array_merge($mergedPrograms[$college], $programs));
                sort($mergedPrograms[$college]);
            } else {
                $mergedPrograms[$college] = $programs;
            }
        }
        
        $analytics['department_programs'] = $mergedPrograms;

        // Calculate stats per department
        $departmentStats = [];
        foreach ($allDepartments as $dept) {
            // Count students in this department
            $deptStudentsCount = User::where('role', 'student')
                ->whereIn('campus_id', $campusIds)
                ->where('college', $dept->short_name)
                ->count();
                
            // Count applications for students in this department
            $deptApplicationsCount = Application::whereHas('user', function($query) use ($campusIds, $dept) {
                    $query->whereIn('campus_id', $campusIds)
                           ->where('college', $dept->short_name);
                })
                ->count();
                
            $deptApprovedCount = Application::whereHas('user', function($query) use ($campusIds, $dept) {
                    $query->whereIn('campus_id', $campusIds)
                           ->where('college', $dept->short_name);
                })
                ->where('status', 'approved')
                ->count();

            $deptPendingCount = Application::whereHas('user', function($query) use ($campusIds, $dept) {
                    $query->whereIn('campus_id', $campusIds)
                           ->where('college', $dept->short_name);
                })
                ->where('status', 'pending')
                ->count();

            $deptRejectedCount = Application::whereHas('user', function($query) use ($campusIds, $dept) {
                    $query->whereIn('campus_id', $campusIds)
                           ->where('college', $dept->short_name);
                })
                ->where('status', 'rejected')
                ->count();

            if ($deptStudentsCount > 0 || $deptApplicationsCount > 0) {
                $departmentStats[] = [
                    'name' => $dept->short_name,
                    'full_name' => $dept->name,
                    'total_students' => $deptStudentsCount,
                    'total_applications' => $deptApplicationsCount,
                    'approved_applications' => $deptApprovedCount,
                    'pending_applications' => $deptPendingCount,
                    'rejected_applications' => $deptRejectedCount,
                    'approval_rate' => $deptApplicationsCount > 0 ? round(($deptApprovedCount / $deptApplicationsCount) * 100, 1) : 0
                ];
            }
        }
        $analytics['department_stats'] = $departmentStats;

        // 3. All Students Data for Client-side Filtering (Gender Chart)
        $allStudentsData = User::where('role', 'student')
            ->whereIn('campus_id', $campusIds)
            ->select('campus_id', 'college', 'sex', 'program')
            ->get();
            
        $analytics['all_students_data'] = $allStudentsData;

        // 4. All Applications Data for Client-side Filtering (Scholarship Type Chart & Stacked Bar)
        $allApplicationsData = Application::join('users', 'applications.user_id', '=', 'users.id')
            ->join('scholarships', 'applications.scholarship_id', '=', 'scholarships.id')
            ->leftJoin('scholars', function($join) {
                $join->on('users.id', '=', 'scholars.user_id')
                     ->on('scholarships.id', '=', 'scholars.scholarship_id');
            })
            ->where('users.role', 'student') // Only count students
            ->whereIn('users.campus_id', $campusIds)
            ->select(
                'users.id as user_id', 
                'users.sex', 
                'users.campus_id', 
                'users.college', 
                'users.program', 
                'scholarships.scholarship_type', 
                'scholarships.scholarship_name as scholarship_name', 
                'applications.status', 
                'applications.created_at', 
                'scholars.id as scholar_id', 
                'scholars.status as scholar_status', 
                'scholars.type as scholar_type',
                DB::raw('(SELECT COUNT(*) FROM scholars as s WHERE s.user_id = users.id) as is_global_scholar')
            )
            ->distinct()
            ->get();
            
        $analytics['all_applications_data'] = $allApplicationsData;


        return $analytics;
    }

    private function getApplicantData($request, $campusIds)
    {
        // Base Query: Students in SFAO jurisdiction
        $baseQuery = User::where('role', 'student')
            ->whereIn('campus_id', $campusIds)
            ->with(['campus', 'applications.scholarship']);

        // Helper to clone and paginate
        $paginate = function($query, $pageName) use ($request) {
            return $query->paginate(10, ['*'], $pageName)->withQueryString();
        };

        // 1. All Students (Dictionary)
        $studentsAll = clone $baseQuery;

        // 2. Not Applied
        $studentsNotApplied = (clone $baseQuery)->whereDoesntHave('applications');

        // 3. In Progress (Assuming 'in_progress' status exists or strictly partial)
        $studentsInProgress = (clone $baseQuery)->whereHas('applications', function($q) {
            $q->where('status', 'in_progress');
        });

        // 4. Pending
        $studentsPending = (clone $baseQuery)->whereHas('applications', function($q) {
            $q->where('status', 'pending');
        });

        // 5. Approved
        $studentsApproved = (clone $baseQuery)->whereHas('applications', function($q) {
            $q->where('status', 'approved');
        });

        // 6. Rejected
        $studentsRejected = (clone $baseQuery)->whereHas('applications', function($q) {
            $q->where('status', 'rejected');
        });
        
        // Applications for counters/references if needed
        $applications = Application::whereIn('user_id', (clone $baseQuery)->select('id'))
            ->get();

        return [
            'students' => $paginate(clone $baseQuery, 'page_applicants'), // Default list
            'studentsAll' => $paginate($studentsAll, 'page_applicants'),
            'studentsNotApplied' => $paginate($studentsNotApplied, 'page_not_applied'),
            'studentsInProgress' => $paginate($studentsInProgress, 'page_in_progress'),
            'studentsPending' => $paginate($studentsPending, 'page_pending'),
            'studentsApproved' => $paginate($studentsApproved, 'page_approved'),
            'studentsRejected' => $paginate($studentsRejected, 'page_rejected'),
            'applications' => $applications,
        ];
    }

    private function paginate($items, $perPage, $page, $pageName)
    {
        $items = $items instanceof \Illuminate\Support\Collection ? $items : collect($items);
        $paginator = new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'pageName' => $pageName]
        );
        return $paginator->appends(request()->query());
    }
    /**
     * Central Dashboard Logic
     */
    private function centralDashboard(Request $request)
    {
        // TODO: Migrated from CentralController
        return view('central.analytics.index');
    }

    /**
     * Student Dashboard Logic
     */
    private function studentDashboard(Request $request)
    {
        $user = \App\Models\User::find(session('user_id'));
        
        // 1. Fetch Application Forms
        $forms = \App\Models\ApplicationForm::where('campus_id', $user->campus_id)
            ->with('uploader')
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Fetch Scholarships
        $scholarships = \App\Models\Scholarship::where('is_active', true)
            ->withCount('applications')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
            
        // Append user-specific status to scholarships
        foreach($scholarships as $scholarship) {
             $application = \App\Models\Application::where('user_id', $user->id)
                 ->where('scholarship_id', $scholarship->id)
                 ->first();
             
             $scholarship->applied = $application ? true : false;
             $scholarship->application_status = $application ? $application->status : null;
             
             $scholar = \App\Models\Scholar::where('user_id', $user->id)
                  ->where('scholarship_id', $scholarship->id)
                  ->exists();
             $scholarship->is_scholar = $scholar;
        }

        // 3. Check for any pending application
        $hasPendingApplication = \App\Models\Application::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        // 4. Notification Counts
        $unreadCount = \App\Models\Notification::where('user_id', $user->id)->where('is_read', false)->count();
        $unreadCountScholarships = \App\Models\Notification::where('user_id', $user->id)->where('is_read', false)->where('type', 'scholarship_created')->count();
        $unreadCountStatus = \App\Models\Notification::where('user_id', $user->id)->where('is_read', false)->where('type', 'application_status')->count();
        $unreadCountComments = \App\Models\Notification::where('user_id', $user->id)->where('is_read', false)->where('type', 'sfao_comment')->count();

        // 5. Scholarship Counts for Empty States
        $privateScholarshipsCount = \App\Models\Scholarship::where('is_active', true)->where('scholarship_type', 'Private')->count();
        $governmentScholarshipsCount = \App\Models\Scholarship::where('is_active', true)->where('scholarship_type', 'Government')->count();

        // 6. User's Filled Application Form (for SFAO/TDP tabs)
        $form = \App\Models\Form::where('user_id', $user->id)->first();

        // 7. Student Applications
        $applications = \App\Models\Application::where('user_id', $user->id)
            ->with('scholarship')
            ->orderBy('created_at', 'desc')
            ->get();

        // 8. Notifications
        $notifications = \App\Models\Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // 9. My Scholarships
        $scholarshipIds = \App\Models\Scholar::where('user_id', $user->id)->pluck('scholarship_id');
        $myScholarships = \App\Models\Scholarship::whereIn('id', $scholarshipIds)->get();

        return view('student.index', compact(
            'forms', 
            'user', 
            'scholarships', 
            'applications',
            'notifications',
            'myScholarships',
            'hasPendingApplication', 
            'unreadCount', 
            'unreadCountScholarships', 
            'unreadCountStatus', 
            'unreadCountComments',
            'privateScholarshipsCount',
            'governmentScholarshipsCount',
            'form'
        ));
    }

    private function getStandardPrograms()
    {
        // Fetch from Programs Table (Grouped by College)
        $programs = \App\Models\Program::all();
        $grouped = [];
        
        foreach ($programs as $prog) {
            // Using Full Name as requested (Reverted from Short Name)
            $grouped[$prog->college][] = $prog->name;
        }
        
        return $grouped;
    }
}
