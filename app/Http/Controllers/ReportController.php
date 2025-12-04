<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Report;
use App\Models\User;
use App\Models\Campus;
use App\Models\Application;
use App\Models\Scholarship;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    // =====================================================
    // SFAO REPORT METHODS
    // =====================================================


    /**
     * Show create report form
     */
    public function createReport()
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::with('campus')->find(session('user_id'));
        $campus = $user->campus;
        
        // Get campuses that this SFAO can monitor (their constituent campus and its extensions)
        $monitoredCampuses = $campus->getAllCampusesUnder();
        
        // Add a special option for "Constituent + Extensions" if the user's campus is a constituent
        $campusOptions = collect();
        
        // Add individual campuses
        foreach ($monitoredCampuses as $campusOption) {
            $campusOptions->push($campusOption);
        }
        
        // Add "Constituent + Extensions" option if the user's campus is a constituent
        if ($campus->type === 'constituent' && $campus->extensionCampuses->count() > 0) {
            $constituentWithExtensions = new \stdClass();
            $constituentWithExtensions->id = 'constituent_with_extensions';
            $constituentWithExtensions->name = $campus->name . ' + Extensions';
            $constituentWithExtensions->type = 'constituent_with_extensions';
            $campusOptions->push($constituentWithExtensions);
        }
        
        return view('sfao.reports.create', compact('campus', 'monitoredCampuses', 'campusOptions'));
    }

    /**
     * Generate and preview report data
     */
    public function generateReportData(Request $request)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $request->validate([
            'report_type' => 'required|in:monthly,quarterly,annual,custom',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'report_period_start' => 'required|date',
            'report_period_end' => 'required|date|after_or_equal:report_period_start',
            'campus_id' => 'required'
        ]);

        $user = User::with('campus')->find(session('user_id'));
        $campus = $user->campus;

        // Generate report data
        $reportData = Report::generateReportData(
            $request->campus_id,
            $request->report_period_start,
            $request->report_period_end
        );

        return response()->json([
            'success' => true,
            'data' => $reportData
        ]);
    }

    /**
     * Store new report
     */
    public function storeReport(Request $request)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $request->validate([
            'report_type' => 'required|in:monthly,quarterly,annual,custom',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'report_period_start' => 'required|date',
            'report_period_end' => 'required|date|after_or_equal:report_period_start',
            'notes' => 'nullable|string',
            'submit_immediately' => 'boolean',
            'campus_id' => 'required'
        ]);

        $user = User::with('campus')->find(session('user_id'));
        $campus = $user->campus;
        
        // Get campuses that this SFAO can monitor
        $monitoredCampuses = $campus->getAllCampusesUnder();
        $monitoredCampusIds = $monitoredCampuses->pluck('id')->toArray();
        
        // Validate that the selected campus is within the SFAO's jurisdiction
        if ($request->campus_id !== 'constituent_with_extensions' && !in_array($request->campus_id, $monitoredCampusIds)) {
            return redirect()->back()->withErrors(['campus_id' => 'You can only create reports for campuses under your jurisdiction.'])->withInput();
        }

        // Generate report data with error handling
        try {
            $reportData = Report::generateReportData(
                $request->campus_id,
                $request->report_period_start,
                $request->report_period_end
            );
        } catch (\Exception $e) {
            // If report data generation fails, create a basic structure
            $reportData = [
                'summary' => [
                    'total_applications' => 0,
                    'approved_applications' => 0,
                    'rejected_applications' => 0,
                    'pending_applications' => 0,
                    'claimed_applications' => 0,
                    'approval_rate' => 0
                ]
            ];
        }

        // Create report
        // For "constituent_with_extensions", use the user's campus ID
        $reportCampusId = $request->campus_id === 'constituent_with_extensions' ? $campus->id : $request->campus_id;
        
        $report = Report::create([
            'sfao_user_id' => session('user_id'),
            'campus_id' => $reportCampusId,
            'original_campus_selection' => $request->campus_id, // Store original selection
            'report_type' => $request->report_type,
            'title' => $request->title,
            'description' => $request->description,
            'report_period_start' => $request->report_period_start,
            'report_period_end' => $request->report_period_end,
            'report_data' => $reportData,
            'notes' => $request->notes,
            'status' => $request->submit_immediately ? 'submitted' : 'draft',
            'submitted_at' => $request->submit_immediately ? now() : null
        ]);

        if ($request->submit_immediately) {
            // Send notification to central admin
            $this->notifyCentralAdmin($report);
        }

        $message = $request->submit_immediately 
            ? 'Report created and submitted successfully!' 
            : 'Report saved as draft successfully!';

        return redirect()->route('sfao.dashboard')
            ->with('success', $message)
            ->with('active_tab', 'reports');
    }

    /**
     * Show report details
     */
    public function showReport($id)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $report = Report::where('sfao_user_id', session('user_id'))
            ->with(['campus', 'reviewer'])
            ->findOrFail($id);

        // If report_data is null or empty, regenerate it
        if (empty($report->report_data)) {
            try {
                $reportData = Report::generateReportData(
                    $report->campus_id,
                    $report->report_period_start,
                    $report->report_period_end
                );
                $report->update(['report_data' => $reportData]);
                $report->refresh();
            } catch (\Exception $e) {
                // If regeneration fails, create a basic structure
                $report->report_data = [
                    'summary' => [
                        'total_applications' => 0,
                        'approved_applications' => 0,
                        'rejected_applications' => 0,
                        'pending_applications' => 0,
                        'claimed_applications' => 0,
                        'approval_rate' => 0
                    ]
                ];
            }
        }

        return view('sfao.reports.show', compact('report'));
    }

    /**
     * Edit report
     */
    public function editReport($id)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $report = Report::where('sfao_user_id', session('user_id'))
            ->where('status', 'draft')
            ->findOrFail($id);

        $user = User::with('campus')->find(session('user_id'));
        $campus = $user->campus;
        
        // Get campuses that this SFAO can monitor (their constituent campus and its extensions)
        $monitoredCampuses = $campus->getAllCampusesUnder();
        
        // Add a special option for "Constituent + Extensions" if the user's campus is a constituent
        $campusOptions = collect();
        
        // Add individual campuses
        foreach ($monitoredCampuses as $campusOption) {
            $campusOptions->push($campusOption);
        }
        
        // Add "Constituent + Extensions" option if the user's campus is a constituent
        if ($campus->type === 'constituent' && $campus->extensionCampuses->count() > 0) {
            $constituentWithExtensions = new \stdClass();
            $constituentWithExtensions->id = 'constituent_with_extensions';
            $constituentWithExtensions->name = $campus->name . ' + Extensions';
            $constituentWithExtensions->type = 'constituent_with_extensions';
            $campusOptions->push($constituentWithExtensions);
        }

        return view('sfao.reports.edit', compact('report', 'campus', 'monitoredCampuses', 'campusOptions'));
    }

    /**
     * Update report
     */
    public function updateReport(Request $request, $id)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $report = Report::where('sfao_user_id', session('user_id'))
            ->where('status', 'draft')
            ->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'submit_immediately' => 'boolean',
            'campus_id' => 'required'
        ]);

        $user = User::with('campus')->find(session('user_id'));
        $campus = $user->campus;
        
        // Get campuses that this SFAO can monitor
        $monitoredCampuses = $campus->getAllCampusesUnder();
        $monitoredCampusIds = $monitoredCampuses->pluck('id')->toArray();
        
        // Validate that the selected campus is within the SFAO's jurisdiction
        if ($request->campus_id !== 'constituent_with_extensions' && !in_array($request->campus_id, $monitoredCampusIds)) {
            return redirect()->back()->withErrors(['campus_id' => 'You can only create reports for campuses under your jurisdiction.'])->withInput();
        }

        // For "constituent_with_extensions", use the user's campus ID
        $reportCampusId = $request->campus_id === 'constituent_with_extensions' ? $campus->id : $request->campus_id;
        
        $report->update([
            'title' => $request->title,
            'description' => $request->description,
            'notes' => $request->notes,
            'campus_id' => $reportCampusId,
            'original_campus_selection' => $request->campus_id, // Store original selection
            'status' => $request->submit_immediately ? 'submitted' : 'draft',
            'submitted_at' => $request->submit_immediately ? now() : null
        ]);

        if ($request->submit_immediately) {
            // Regenerate report data using original campus selection
            $reportData = Report::generateReportData(
                $request->campus_id,
                $report->report_period_start,
                $report->report_period_end
            );
            $report->update(['report_data' => $reportData]);

            // Send notification to central admin
            $this->notifyCentralAdmin($report);
        }

        $message = $request->submit_immediately 
            ? 'Report updated and submitted successfully!' 
            : 'Report updated successfully!';

        return redirect()->route('sfao.dashboard')
            ->with('success', $message)
            ->with('active_tab', 'reports');
    }

    /**
     * Submit draft report
     */
    public function submitReport($id)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $report = Report::where('sfao_user_id', session('user_id'))
            ->where('status', 'draft')
            ->findOrFail($id);

        // Regenerate report data using original campus selection
        $campusId = $report->original_campus_selection ?? $report->campus_id;
        $reportData = Report::generateReportData(
            $campusId,
            $report->report_period_start,
            $report->report_period_end
        );

        $report->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'report_data' => $reportData
        ]);

        // Send notification to central admin
        $this->notifyCentralAdmin($report);

        return redirect()->route('sfao.dashboard')
            ->with('success', 'Report submitted successfully!')
            ->with('active_tab', 'reports');
    }

    /**
     * Delete report
     */
    public function deleteReport($id)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $report = Report::where('sfao_user_id', session('user_id'))
            ->where('status', 'draft')
            ->findOrFail($id);

        $report->delete();

        return redirect()->route('sfao.dashboard')
            ->with('success', 'Report deleted successfully!')
            ->with('active_tab', 'reports');
    }

    /**
     * Student Summary Report Page
     */
    /**
     * Student Summary Report Page
     */
    public function studentSummary(Request $request)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::with('campus')->find(session('user_id'));
        $campus = $user->campus;
        $monitoredCampuses = $campus->getAllCampusesUnder();
        
        // Filter by campus if requested
        if ($request->has('campus_id') && $request->campus_id != 'all') {
            $monitoredCampuses = $monitoredCampuses->where('id', $request->campus_id);
        }

        $reportData = [];
        $remarksData = [];
        $summaryStats = [
            'accepted' => 0,
            'rejected' => 0,
            'pending' => 0,
            'not_applied' => 0,
            'total' => 0
        ];

        foreach ($monitoredCampuses as $camp) {
            $campusData = [
                'campus' => $camp,
                'departments' => []
            ];
            // Eager load departments for the campus
            $departments = $camp->departments;

            foreach ($departments as $dept) {
                // Fetch students for this campus and department
                // Matching User 'college' column with Department 'name' or 'short_name'
                $students = User::where('campus_id', $camp->id)
                    ->where(function($q) use ($dept) {
                        $q->where('college', $dept->name)
                          ->orWhere('college', $dept->short_name);
                    })
                    ->where('role', 'student')
                    ->with(['applications' => function($q) {
                        $q->latest();
                    }, 'form'])
                    ->get();

                $processedStudents = [];

                foreach ($students as $student) {
                    $latestApp = $student->applications->first();
                    $status = $latestApp ? $latestApp->status : 'Not Applied';
                    
                    // Normalize status for summary
                    $normalizedStatus = strtolower($status);
                    if (in_array($normalizedStatus, ['approved', 'accepted', 'claimed'])) {
                        $summaryStats['accepted']++;
                    } elseif (in_array($normalizedStatus, ['rejected', 'disapproved'])) {
                        $summaryStats['rejected']++;
                    } elseif ($normalizedStatus == 'not applied') {
                        $summaryStats['not_applied']++;
                    } else {
                        $summaryStats['pending']++;
                    }
                    $summaryStats['total']++;

                    $processedStudents[] = [
                        'name' => $student->last_name . ', ' . $student->first_name,
                        'sex' => $student->sex,
                        'status' => ucfirst($status),
                        'remarks' => $student->form ? $student->form->reviewer_remarks : null
                    ];

                    // Collect remarks
                    if ($student->form && $student->form->reviewer_remarks) {
                        $remarksData[] = [
                            'campus' => $camp->name,
                            'name' => $student->last_name . ', ' . $student->first_name,
                            'status' => ucfirst($status),
                            'remarks' => $student->form->reviewer_remarks
                        ];
                    }
                }

                $campusData['departments'][] = [
                    'department' => $dept,
                    'students' => $processedStudents
                ];
            }
            
            // Also catch students who don't match any department (optional, but good for data integrity)
            // For now, we'll stick to the requested structure.
            
            $reportData[] = $campusData;
        }

        return view('sfao.reports.student-summary', compact('user', 'monitoredCampuses', 'reportData', 'summaryStats', 'remarksData'));
    }

    /**
     * Scholar Summary Report Page
     */
    public function scholarSummary(Request $request)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::with('campus')->find(session('user_id'));
        $campus = $user->campus;
        $monitoredCampuses = $campus->getAllCampusesUnder();
        
        // Filter by campus if selected
        if ($request->has('campus_id') && $request->campus_id != 'all') {
            $monitoredCampuses = $monitoredCampuses->where('id', $request->campus_id);
        }

        $reportData = [];
        $summaryStats = [
            'old_scholars' => 0,
            'new_scholars' => 0,
            'non_scholars' => 0,
            'total' => 0
        ];

        foreach ($monitoredCampuses as $camp) {
            $campusData = [
                'campus' => $camp,
                'scholars' => [],
                'non_scholars' => []
            ];

            // Fetch all students for this campus
            $students = User::where('campus_id', $camp->id)
                ->where('role', 'student')
                ->with(['scholars' => function($q) {
                    $q->where('status', 'active');
                }])
                ->get();

            foreach ($students as $student) {
                $activeScholar = $student->scholars->first();

                $studentData = [
                    'name' => $student->last_name . ', ' . $student->first_name,
                    'department' => $student->college ?? 'N/A', // Using college as department/program
                    'year_level' => $student->year_level ?? 'N/A',
                ];

                if ($activeScholar) {
                    $type = $activeScholar->type; // 'old' or 'new'
                    $studentData['status'] = ucfirst($type) . ' Scholar';
                    $campusData['scholars'][] = $studentData;

                    if ($type === 'old') {
                        $summaryStats['old_scholars']++;
                    } else {
                        $summaryStats['new_scholars']++;
                    }
                } else {
                    $studentData['status'] = 'Non-Scholar';
                    $campusData['non_scholars'][] = $studentData;
                    $summaryStats['non_scholars']++;
                }
                $summaryStats['total']++;
            }

            $reportData[] = $campusData;
        }

        return view('sfao.reports.scholar-summary', compact('user', 'monitoredCampuses', 'reportData', 'summaryStats'));
    }

    /**
     * Grant Summary Report Page
     */
    public function grantSummary(Request $request)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::with('campus')->find(session('user_id'));
        $campus = $user->campus;
        $monitoredCampuses = $campus->getAllCampusesUnder();
        $campusIds = $monitoredCampuses->pluck('id');

        // Base query for approved/claimed applications (Grants)
        $query = Application::whereIn('status', ['approved', 'claimed'])
            ->whereHas('user', function($q) use ($campusIds) {
                $q->whereIn('campus_id', $campusIds);
            });

        // Apply filters
        if ($request->has('campus_id') && $request->campus_id != 'all') {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('campus_id', $request->campus_id);
            });
        }

        $totalGrants = (clone $query)->count();

        // Claimed vs Unclaimed
        $statusStats = (clone $query)->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Grants by Scholarship Type
        $typeStats = (clone $query)
            ->join('scholarships', 'applications.scholarship_id', '=', 'scholarships.id')
            ->select('scholarships.scholarship_type', DB::raw('count(*) as total'))
            ->groupBy('scholarships.scholarship_type')
            ->pluck('total', 'scholarship_type')
            ->toArray();

        return view('sfao.reports.grant-summary', compact('user', 'monitoredCampuses', 'totalGrants', 'statusStats', 'typeStats'));
    }

    // =====================================================
    // CENTRAL ADMIN REPORT METHODS
    // =====================================================


    /**
     * Show report details for central admin
     */
    public function centralShowReport($id)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        $report = Report::with(['sfaoUser', 'campus', 'reviewer'])
            ->findOrFail($id);

        return view('central.reports.show', compact('report'));
    }

    /**
     * Review report
     */
    public function reviewReport(Request $request, $id)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        $request->validate([
            'status' => 'required|in:reviewed,approved',
            'feedback' => 'nullable|string|max:1000'
        ]);

        $report = Report::findOrFail($id);

        $report->update([
            'status' => $request->status,
            'central_feedback' => $request->feedback,
            'reviewed_at' => now(),
            'reviewed_by' => session('user_id')
        ]);

        // Send notification to SFAO admin
        $this->notifySfaoAdmin($report);

        $message = $request->status === 'approved' 
            ? 'Report approved successfully!' 
            : 'Report marked as reviewed successfully!';

        return redirect()->route('central.dashboard')
            ->with('success', $message);
    }

    // =====================================================
    // PRIVATE HELPER METHODS
    // =====================================================

    /**
     * Notify central admin about new report
     */
    private function notifyCentralAdmin(Report $report)
    {
        // Get all central admins
        $centralAdmins = User::where('role', 'central')->get();

        foreach ($centralAdmins as $admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'type' => 'report_submitted',
                'title' => 'New SFAO Report Submitted',
                'message' => "A new {$report->getReportTypeDisplayName()} has been submitted by {$report->sfaoUser->name} from {$report->campus->name}.",
                'data' => [
                    'report_id' => $report->id,
                    'sfao_user_id' => $report->sfao_user_id,
                    'campus_id' => $report->campus_id,
                    'report_type' => $report->report_type,
                    'title' => $report->title
                ]
            ]);
        }
    }

    /**
     * Notify SFAO admin about report review
     */
    private function notifySfaoAdmin(Report $report)
    {
        \App\Models\Notification::create([
            'user_id' => $report->sfao_user_id,
            'type' => 'report_reviewed',
            'title' => 'Report Review Update',
            'message' => "Your report '{$report->title}' has been {$report->status} by Central Administration.",
            'data' => [
                'report_id' => $report->id,
                'status' => $report->status,
                'feedback' => $report->central_feedback,
                'reviewed_by' => $report->reviewer->name ?? 'Central Admin'
            ]
        ]);
    }

}
