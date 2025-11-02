<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\StudentSubmittedDocument;
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

        return view('central.endorsed.validate', compact('application', 'user', 'scholarship', 'submittedDocuments'));
    }
}

