<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\StudentSubmittedDocument;
use App\Models\RejectedApplicant;
use App\Models\Scholar;
use App\Services\NotificationService;

/**
 * Central Application Controller
 * 
 * Handles Central Office application management:
 * - Viewing endorsed applicants
 * - Approving/rejecting applications
 * - Grant claim processing
 * - Endorsed application validation
 */
class CentralApplicationController extends Controller
{
    /**
     * View all applicants - Only SFAO-approved applications
     */


    /**
     * Approve application (Central)
     */
    public function approve($id)
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
    public function reject($id)
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
    public function claimGrant($id)
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

    /**
     * Show endorsed application validation
     */
    public function showEndorsedValidation(\Illuminate\Http\Request $request, \App\Models\Application $application)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        // Load necessary relationships
        $application->load(['user', 'scholarship', 'user.campus', 'user.form']);
        
        // Get the user and scholarship for easier access in view
        $user = $application->user;
        $scholarship = $application->scholarship;
        
        // Load submitted documents for this application
        $submittedDocuments = StudentSubmittedDocument::where('user_id', $user->id)
            ->where('scholarship_id', $scholarship->id)
            ->get();


        // DEBUG: Verify the user being passed
        // dd('DEBUG: checking user', $user->toArray(), 'Is this the admin?', $user->name);
        
        return view('central.endorsed.validate', compact('application', 'user', 'scholarship', 'submittedDocuments'));
    }

    /**
     * Accept an endorsed application (Central)
     */
    public function acceptEndorsed(Application $application)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        // Ensure the application is in approved status (endorsed by SFAO)
        if ($application->status !== 'approved') {
            return back()->with('error', 'Only SFAO-approved applications can be accepted.');
        }

        // Load necessary relationships
        $application->load(['user', 'scholarship']);

        // Check if scholar already exists for this user and scholarship
        $existingScholar = Scholar::where('user_id', $application->user_id)
            ->where('scholarship_id', $application->scholarship_id)
            ->first();

        if ($existingScholar) {
            return back()->with('error', 'A scholar record already exists for this application.');
        }

        // Calculate scholarship dates
        $startDate = now()->startOfMonth();
        $endDate = $application->scholarship->renewal_allowed 
            ? $startDate->copy()->addYear() 
            : $startDate->copy()->addMonths(6);

        // Create scholar record as 'new' scholar
        Scholar::create([
            'user_id' => $application->user_id,
            'scholarship_id' => $application->scholarship_id,
            'application_id' => $application->id,
            'type' => 'new', // Always new when accepted from endorsed applicants
            'grant_count' => 0, // No grants received yet
            'total_grant_received' => 0.00,
            'scholarship_start_date' => $startDate,
            'scholarship_end_date' => $endDate,
            'status' => 'active',
            'notes' => 'Created from accepted endorsed application',
        ]);

        // Application remains in 'approved' status - it's now validated by Central
        
        // Create notification for student
        NotificationService::notifyApplicationStatusChange($application, 'approved');

        return redirect()->route('central.dashboard', ['tab' => 'endorsed_applicants'])
            ->with('success', 'Application has been accepted successfully. Scholar record has been created.');
    }

    /**
     * Reject an endorsed application (Central)
     */
    public function rejectEndorsed(Request $request, Application $application)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        // Ensure the application is in approved status (endorsed by SFAO)
        if ($application->status !== 'approved') {
            return back()->with('error', 'Only SFAO-approved applications can be rejected.');
        }

        // Update application status to rejected
        $application->status = 'rejected';
        $application->save();

        // Store in rejected_applicants table to prevent re-application
        RejectedApplicant::create([
            'user_id' => $application->user_id,
            'scholarship_id' => $application->scholarship_id,
            'application_id' => $application->id,
            'rejected_by' => 'central',
            'rejected_by_user_id' => session('user_id'),
            'rejection_reason' => $request->rejection_reason,
            'remarks' => $request->remarks ?? null,
            'rejected_at' => now(),
        ]);

        // Create notification for student
        NotificationService::notifyApplicationStatusChange($application, 'rejected');

        return redirect()->route('central.dashboard', ['tab' => 'endorsed-applicants'])
            ->with('success', 'Application has been rejected. The student will not be able to apply to this scholarship again.');
    }

    /**
     * View rejected applicants list (Central)
     */
    public function viewRejectedApplicants()
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        $rejectedApplicants = RejectedApplicant::with(['user', 'scholarship', 'rejectedByUser'])
            ->where('rejected_by', 'central')
            ->orderBy('rejected_at', 'desc')
            ->get();

        return view('central.partials.tabs.rejected-applicants', compact('rejectedApplicants'));
    }
}

