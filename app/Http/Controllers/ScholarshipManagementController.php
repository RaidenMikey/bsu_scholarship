<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Scholarship;
use App\Models\Application;
use App\Models\User;
use App\Models\ScholarshipRequiredCondition;
use App\Models\ScholarshipRequiredDocument;
use App\Services\NotificationService;
use App\Models\Scholar;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\GrantSlipMail;

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
    public function centralIndex(Request $request)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        return redirect()->route('central.dashboard', ['tab' => 'scholarships']);
    }

    /**
     * Show create scholarship form (Central)
     */
    public function create()
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        $departments = \App\Models\Department::orderBy('short_name')->pluck('short_name');
        return view('central.scholarships.create', compact('departments'));
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
            'scholarship_type' => 'required|in:private,government',
            'description'      => 'required|string',
            'submission_deadline' => 'required|date|after:today',
            'application_start_date' => 'nullable|date|before:submission_deadline',
            'slots_available'  => 'nullable|integer|min:0',
            'grant_amount'     => 'nullable|numeric|min:0',
            'grant_type'       => 'required|in:one_time,recurring,discontinued',
            'eligibility_notes' => 'nullable|string',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Handle background image upload
            $backgroundImagePath = null;
            if ($request->hasFile('background_image')) {
                $file = $request->file('background_image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('scholarship_images', $filename, 'public');
                $backgroundImagePath = $filename;
            }

            // Automatically set renewal_allowed based on grant_type
            $renewalAllowed = $request->grant_type === 'recurring';

            $scholarship = Scholarship::create([
                'scholarship_name' => $request->scholarship_name,
                'scholarship_type' => $request->scholarship_type,
                'description'      => $request->description,
                'submission_deadline' => $request->submission_deadline,
                'application_start_date' => $request->application_start_date,
                'slots_available'  => $request->slots_available,
                'grant_amount'     => $request->grant_amount,
                'renewal_allowed'  => $renewalAllowed,
                'grant_type'       => $request->grant_type,
                'eligibility_notes' => $request->eligibility_notes,
                'background_image' => $backgroundImagePath,
                'is_active'        => true, // Set as active by default
                'allow_existing_scholarship' => $request->has('allow_existing_scholarship'),
                'created_by'       => session('user_id'),
            ]);
            
            Log::info('Scholarship created successfully:', ['id' => $scholarship->id, 'name' => $scholarship->scholarship_name]);
        } catch (\Exception $e) {
            Log::error('Error creating scholarship:', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to create scholarship: ' . $e->getMessage()]);
        }

        // Save conditions
        if ($request->has('conditions')) {
            foreach ($request->conditions as $cond) {
                $scholarship->conditions()->create([
                    'name' => $cond['type'],
                    'value' => $cond['value'],
                    'is_mandatory' => true, // Conditions are always mandatory
                ]);
            }
        }

        // Save document requirements
        if ($request->has('documents')) {
            foreach ($request->documents as $doc) {
                $scholarship->requiredDocuments()->create([
                    'document_name' => strip_tags($doc['name']),
                    'document_type' => $doc['type'] ?? 'pdf',
                    'is_mandatory' => $doc['mandatory'] ?? true,
                ]);
            }
        }

        // Create notifications for all students
        NotificationService::notifyScholarshipCreated($scholarship);

        return redirect()
            ->route('central.dashboard')
            ->with('success', 'Scholarship added successfully! You can view it in the scholarships section.');
    }

    /**
     * Show edit scholarship form (Central)
     */
    public function edit($id)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        try {
            $scholarship = Scholarship::with(['conditions', 'requiredDocuments'])->findOrFail($id);
            
            Log::info('Accessing scholarship edit form:', [
                'id' => $scholarship->id,
                'name' => $scholarship->scholarship_name,
                'accessed_by' => session('user_id')
            ]);
            
            $departments = \App\Models\Department::orderBy('short_name')->pluck('short_name');
            
            return view('central.scholarships.create', compact('scholarship', 'departments'));
            
        } catch (\Exception $e) {
            Log::error('Error accessing scholarship edit form:', [
                'id' => $id,
                'error' => $e->getMessage(),
                'accessed_by' => session('user_id')
            ]);
            
            return redirect()
                ->route('central.dashboard')
                ->with('error', 'Scholarship not found or access denied.');
        }
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
            'scholarship_type' => 'required|in:private,government',
            'description'      => 'required|string',
            'submission_deadline' => 'required|date|after:today',
            'application_start_date' => 'nullable|date|before:submission_deadline',
            'slots_available'  => 'nullable|integer|min:0',
            'grant_amount'     => 'nullable|numeric|min:0',
            'grant_type'       => 'required|in:one_time,recurring,discontinued',
            'eligibility_notes' => 'nullable|string',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $scholarship = Scholarship::findOrFail($id);

        // Handle background image upload
        $backgroundImagePath = $scholarship->background_image; // Keep existing image
        if ($request->hasFile('background_image')) {
            // Delete old image if exists
            if ($scholarship->background_image && Storage::disk('public')->exists('scholarship_images/' . $scholarship->background_image)) {
                Storage::disk('public')->delete('scholarship_images/' . $scholarship->background_image);
            }
            
            $file = $request->file('background_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('scholarship_images', $filename, 'public');
            $backgroundImagePath = $filename;
        }

        // Automatically set renewal_allowed based on grant_type
        $renewalAllowed = $request->grant_type === 'recurring';

        $scholarship->update([
            'scholarship_name' => $request->scholarship_name,
            'scholarship_type' => $request->scholarship_type,
            'description'      => $request->description,
            'submission_deadline' => $request->submission_deadline,
            'application_start_date' => $request->application_start_date,
            'slots_available'  => $request->slots_available,
            'grant_amount'     => $request->grant_amount,
            'renewal_allowed'  => $renewalAllowed,
            'grant_type'       => $request->grant_type,
            'eligibility_notes' => $request->eligibility_notes,
            'background_image' => $backgroundImagePath,
            'allow_existing_scholarship' => $request->has('allow_existing_scholarship'),
        ]);

        // Refresh conditions
        $scholarship->conditions()->delete();
        if ($request->has('conditions')) {
            foreach ($request->conditions as $cond) {
                $scholarship->conditions()->create([
                    'name' => $cond['type'],
                    'value' => $cond['value'],
                    'is_mandatory' => true, // Conditions are always mandatory
                ]);
            }
        }

        // Refresh document requirements
        $scholarship->requiredDocuments()->delete();
        if ($request->has('documents')) {
            foreach ($request->documents as $doc) {
                $scholarship->requiredDocuments()->create([
                    'document_name' => strip_tags($doc['name']),
                    'document_type' => $doc['type'] ?? 'pdf',
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

        try {
            $scholarship = Scholarship::findOrFail($id);
            
            // Log the deletion attempt
            Log::info('Attempting to delete scholarship:', [
                'id' => $scholarship->id,
                'name' => $scholarship->scholarship_name,
                'deleted_by' => session('user_id')
            ]);

            // Delete related data first
            $scholarship->conditions()->delete();
            $scholarship->requiredDocuments()->delete();
            
            // Delete the scholarship
            $scholarship->delete();

            Log::info('Scholarship deleted successfully:', [
                'id' => $id,
                'name' => $scholarship->scholarship_name
            ]);

            return redirect()
                ->route('central.dashboard')
                ->with('success', 'Scholarship "' . $scholarship->scholarship_name . '" removed successfully.');
                
        } catch (\Exception $e) {
            Log::error('Error deleting scholarship:', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->route('central.dashboard')
                ->with('error', 'Failed to delete scholarship: ' . $e->getMessage());
        }
    }

    // =====================================================
    // SFAO SCHOLARSHIP MANAGEMENT
    // =====================================================

    /**
     * Release grant for scholarship (SFAO)
     */
    public function releaseGrant($id)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $scholarship = Scholarship::findOrFail($id);
        
        $user = User::with('campus')->find(session('user_id'));
        $sfaoCampus = $user->campus;
        $campusIds = $sfaoCampus->getAllCampusesUnder()->pluck('id');
        
        // Get active scholars under this scholarship belonging to SFAO's campuses
        $scholars = Scholar::where('scholarship_id', $id)
            ->where('status', 'active')
            ->whereHas('user', function($q) use ($campusIds) {
                $q->whereIn('campus_id', $campusIds);
            })
            ->with('user')
            ->get();
            
        if ($scholars->isEmpty()) {
            return back()->with('error', 'No active scholars found for this scholarship in your campus.');
        }

        $count = 0;
        foreach ($scholars as $scholar) {
            try {
                // Generate PDF
                $pdf = Pdf::loadView('pdf.grant-slip', [
                    'scholar' => $scholar,
                    'scholarship' => $scholarship
                ])->output();
                
                // Send Email
                if ($scholar->user && $scholar->user->email) {
                    Mail::to($scholar->user->email)->send(new GrantSlipMail($scholar, $scholarship, $pdf));
                    $count++;
                }
            } catch (\Exception $e) {
                Log::error('Failed to release grant for scholar ' . $scholar->id . ': ' . $e->getMessage());
            }
        }
        
        return back()->with('success', "Grant released successfully. Sent notifications to {$count} scholars.");
    }

    /**
     * List all scholarships with applicant counts (SFAO)
     */
    public function sfaoIndex(Request $request)
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

        // Apply sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        return redirect()->route('sfao.dashboard', ['tab' => 'scholarships']);
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
                    return $scholarship->scholarship_name;
            }
        }, SORT_REGULAR, $sortOrder === 'desc');
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
        return Scholarship::with(['conditions', 'requiredDocuments', 'applications'])
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

        ScholarshipRequiredCondition::create([
            'scholarship_id' => $scholarshipId,
            'name' => $request->name,
            'value' => $request->value,
            'is_mandatory' => true, // Conditions are always mandatory
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

        ScholarshipRequiredDocument::create([
            'scholarship_id' => $scholarshipId,
            'document_name' => $request->name,
            'type' => 'pdf', // Default to PDF
            'is_mandatory' => $request->boolean('is_mandatory'),
        ]);

        return back()->with('success', 'Document requirement added successfully.');
    }

    /**
     * Remove condition from scholarship
     */
    public function removeCondition($conditionId)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        $condition = ScholarshipRequiredCondition::findOrFail($conditionId);
        $condition->delete();

        return back()->with('success', 'Condition removed successfully.');
    }

    /**
     * Remove document requirement from scholarship
     */
    public function removeDocument($documentId)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        $document = ScholarshipRequiredDocument::findOrFail($documentId);
        $document->delete();

        return back()->with('success', 'Document requirement removed successfully.');
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
