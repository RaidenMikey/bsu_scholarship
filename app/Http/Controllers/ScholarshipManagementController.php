<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scholarship;
use App\Models\Application;
use App\Models\User;
use App\Models\ScholarshipRequirement;

/**
 * =====================================================
 * SCHOLARSHIP MANAGEMENT CONTROLLER
 * =====================================================
 * 
 * This controller handles all scholarship-related functionality
 * including creation, management, viewing, and requirements
 * for both Central and SFAO roles.
 * 
 * Combined functionality from:
 * - ScholarshipController
 * - ScholarshipRequirementController
 * - ScholarshipConditionController
 */
class ScholarshipManagementController extends Controller
{
    // =====================================================
    // CENTRAL SCHOLARSHIP MANAGEMENT
    // =====================================================

    /**
     * List all scholarships with their conditions & requirements (Central)
     */
    public function centralIndex()
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        $scholarships = Scholarship::with(['conditions', 'requirements'])->get();
        return view('central.scholarships.index', compact('scholarships'));
    }

    /**
     * Show create scholarship form (Central)
     */
    public function create()
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        return view('central.scholarships.create_scholarship');
    }

    /**
     * Store new scholarship (Central)
     */
    public function store(Request $request)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        $request->validate([
            'scholarship_name' => 'required|string|max:255',
            'scholarship_type' => 'required|in:internal,external',
            'description'      => 'required|string',
            'submission_deadline' => 'required|date|after:today',
            'application_start_date' => 'nullable|date|before:submission_deadline',
            'slots_available'  => 'nullable|integer|min:0',
            'grant_amount'     => 'nullable|numeric|min:0',
            'renewal_allowed'  => 'nullable|boolean',
            'grant_type'       => 'required|in:one_time,recurring,discontinued',
            'priority_level'   => 'required|in:high,medium,low',
            'eligibility_notes' => 'nullable|string',
        ]);

        $scholarship = Scholarship::create([
            'scholarship_name' => $request->scholarship_name,
            'scholarship_type' => $request->scholarship_type,
            'description'      => $request->description,
            'submission_deadline' => $request->submission_deadline,
            'application_start_date' => $request->application_start_date,
            'slots_available'  => $request->slots_available,
            'grant_amount'     => $request->grant_amount,
            'renewal_allowed'  => $request->boolean('renewal_allowed'),
            'grant_type'       => $request->grant_type,
            'priority_level'   => $request->priority_level,
            'eligibility_notes' => $request->eligibility_notes,
            'created_by'       => session('user_id'),
        ]);

        // Save conditions
        if ($request->has('conditions')) {
            foreach ($request->conditions as $cond) {
                $scholarship->conditions()->create([
                    'name' => $cond['type'],
                    'value' => $cond['value'],
                    'type' => 'condition',
                ]);
            }
        }

        // Save requirements
        if ($request->has('documents')) {
            foreach ($request->documents as $doc) {
                $scholarship->requirements()->create([
                    'name' => $doc['name'],
                    'type' => 'document',
                    'is_mandatory' => $doc['mandatory'] ?? true,
                ]);
            }
        }

        return redirect()
            ->route('central.dashboard')
            ->with('success', 'Scholarship added successfully.');
    }

    /**
     * Show edit scholarship form (Central)
     */
    public function edit($id)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        $scholarship = Scholarship::with(['conditions', 'requirements'])->findOrFail($id);
        return view('central.scholarships.create_scholarship', compact('scholarship'));
    }

    /**
     * Update scholarship (Central)
     */
    public function update(Request $request, $id)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        $request->validate([
            'scholarship_name' => 'required|string|max:255',
            'scholarship_type' => 'required|in:internal,external',
            'description'      => 'required|string',
            'submission_deadline' => 'required|date|after:today',
            'application_start_date' => 'nullable|date|before:submission_deadline',
            'slots_available'  => 'nullable|integer|min:0',
            'grant_amount'     => 'nullable|numeric|min:0',
            'renewal_allowed'  => 'nullable|boolean',
            'grant_type'       => 'required|in:one_time,recurring,discontinued',
            'priority_level'   => 'required|in:high,medium,low',
            'eligibility_notes' => 'nullable|string',
        ]);

        $scholarship = Scholarship::findOrFail($id);

        $scholarship->update([
            'scholarship_name' => $request->scholarship_name,
            'scholarship_type' => $request->scholarship_type,
            'description'      => $request->description,
            'submission_deadline' => $request->submission_deadline,
            'application_start_date' => $request->application_start_date,
            'slots_available'  => $request->slots_available,
            'grant_amount'     => $request->grant_amount,
            'renewal_allowed'  => $request->boolean('renewal_allowed'),
            'grant_type'       => $request->grant_type,
            'priority_level'   => $request->priority_level,
            'eligibility_notes' => $request->eligibility_notes,
        ]);

        // Refresh conditions
        $scholarship->conditions()->delete();
        if ($request->has('conditions')) {
            foreach ($request->conditions as $cond) {
                $scholarship->conditions()->create([
                    'name' => $cond['type'],
                    'value' => $cond['value'],
                    'type' => 'condition',
                ]);
            }
        }

        // Refresh requirements
        $scholarship->requirements()->delete();
        if ($request->has('documents')) {
            foreach ($request->documents as $doc) {
                $scholarship->requirements()->create([
                    'name' => $doc['name'],
                    'type' => 'document',
                    'is_mandatory' => $doc['mandatory'] ?? true,
                ]);
            }
        }

        return redirect()
            ->route('central.dashboard')
            ->with('success', 'Scholarship updated successfully.');
    }

    /**
     * Delete scholarship (Central)
     */
    public function destroy($id)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        $scholarship = Scholarship::findOrFail($id);

        $scholarship->conditions()->delete();
        $scholarship->requirements()->delete();
        $scholarship->delete();

        return redirect()
            ->route('central.dashboard')
            ->with('success', 'Scholarship removed successfully.');
    }

    // =====================================================
    // SFAO SCHOLARSHIP MANAGEMENT
    // =====================================================

    /**
     * List all scholarships with applicant counts (SFAO)
     */
    public function sfaoIndex()
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::with('campus')->find(session('user_id'));
        $sfaoCampus = $user->campus;
        $campusIds = $sfaoCampus->getAllCampusesUnder()->pluck('id');

        // Get scholarships with application counts filtered by campus
        $scholarships = Scholarship::withCount(['applications' => function($query) use ($campusIds) {
            $query->whereHas('user', function($userQuery) use ($campusIds) {
                $userQuery->whereIn('campus_id', $campusIds);
            });
        }])->get();

        return view('sfao.scholarships', compact('scholarships', 'sfaoCampus'));
    }

    /**
     * Show one scholarship with its applicants (SFAO)
     */
    public function sfaoShow($id)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::with('campus')->find(session('user_id'));
        $sfaoCampus = $user->campus;
        $campusIds = $sfaoCampus->getAllCampusesUnder()->pluck('id');

        $scholarship = Scholarship::with(['applications' => function($query) use ($campusIds) {
            $query->whereHas('user', function($userQuery) use ($campusIds) {
                $userQuery->whereIn('campus_id', $campusIds);
            });
        }, 'applications.user'])->findOrFail($id);

        return view('sfao.scholarship_show', compact('scholarship', 'sfaoCampus'));
    }

    // =====================================================
    // STUDENT SCHOLARSHIP VIEWING
    // =====================================================

    /**
     * Get scholarships for student dashboard
     */
    public function getStudentScholarships($form)
    {
        // Get all scholarships that allow new applications and filter by all conditions
        $allScholarships = Scholarship::where('is_active', true)
            ->with('conditions')
            ->orderBy('submission_deadline')
            ->get();

        // Filter scholarships based on grant type and all requirements
        $scholarships = $allScholarships->filter(function ($scholarship) use ($form) {
            // Check if scholarship allows new applications based on grant type
            if (!$scholarship->allowsNewApplications()) {
                return false;
            }
            
            // Check if student meets all conditions
            return $scholarship->meetsAllConditions($form);
        });

        return $scholarships;
    }

    /**
     * Get scholarship details for student
     */
    public function getScholarshipDetails($id)
    {
        return Scholarship::with(['conditions', 'requirements', 'applications'])
            ->findOrFail($id);
    }

    // =====================================================
    // SCHOLARSHIP REQUIREMENTS MANAGEMENT
    // =====================================================

    /**
     * Add condition to scholarship
     */
    public function addCondition(Request $request, $scholarshipId)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        $request->validate([
            'name' => 'required|string',
            'value' => 'required|string',
        ]);

        ScholarshipRequirement::create([
            'scholarship_id' => $scholarshipId,
            'name' => $request->name,
            'value' => $request->value,
            'type' => 'condition',
        ]);

        return back()->with('success', 'Condition added successfully.');
    }

    /**
     * Add document requirement to scholarship
     */
    public function addDocumentRequirement(Request $request, $scholarshipId)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        $request->validate([
            'name' => 'required|string',
            'is_mandatory' => 'boolean',
        ]);

        ScholarshipRequirement::create([
            'scholarship_id' => $scholarshipId,
            'name' => $request->name,
            'type' => 'document',
            'is_mandatory' => $request->boolean('is_mandatory'),
        ]);

        return back()->with('success', 'Document requirement added successfully.');
    }

    /**
     * Remove requirement from scholarship
     */
    public function removeRequirement($requirementId)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        $requirement = ScholarshipRequirement::findOrFail($requirementId);
        $requirement->delete();

        return back()->with('success', 'Requirement removed successfully.');
    }

    // =====================================================
    // SCHOLARSHIP STATISTICS
    // =====================================================

    /**
     * Get scholarship statistics for dashboard
     */
    public function getScholarshipStats()
    {
        return [
            'total' => Scholarship::count(),
            'active' => Scholarship::where('is_active', true)->count(),
            'accepting_applications' => Scholarship::acceptingApplications()->count(),
            'high_priority' => Scholarship::highPriority()->count(),
        ];
    }

    /**
     * Get application statistics for scholarship
     */
    public function getApplicationStats($scholarshipId)
    {
        $scholarship = Scholarship::findOrFail($scholarshipId);
        
        return [
            'total_applications' => $scholarship->getApplicationCount(),
            'approved_applications' => $scholarship->getApprovedCount(),
            'fill_percentage' => $scholarship->getFillPercentage(),
            'is_full' => $scholarship->isFull(),
        ];
    }
}
