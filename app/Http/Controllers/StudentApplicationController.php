<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Application;
use App\Models\StudentSubmittedDocument;

/**
 * Student Application Controller
 * 
 * Handles student-specific application operations:
 * - Viewing applications
 * - Applying to scholarships
 * - Withdrawing applications
 */
class StudentApplicationController extends Controller
{
    /**
     * Show student's applications
     */
    public function index()
    {
        if (!session()->has('user_id') || session('role') !== 'student') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::with('appliedScholarships')->find(session('user_id'));
        $applications = $user->appliedScholarships;

        return view('student.applications', compact('applications'));
    }

    /**
     * Apply for a scholarship
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
     * Withdraw from a scholarship application
     */
    public function withdraw(Request $request)
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
}

