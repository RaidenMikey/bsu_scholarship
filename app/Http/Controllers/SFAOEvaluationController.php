<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Application;
use App\Models\Scholarship;
use App\Models\StudentSubmittedDocument;
use App\Models\Notification;
use App\Services\NotificationService;

/**
 * SFAO Evaluation Controller
 * 
 * Handles the 4-stage document evaluation process:
 * - Stage 1: Scholarship selection
 * - Stage 2: SFAO documents evaluation
 * - Stage 3: Scholarship documents evaluation
 * - Stage 4: Final review and decision
 */
class SFAOEvaluationController extends Controller
{
    /**
     * Show evaluation - Stage 1: Select student and scholarship
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
     * Show SFAO documents evaluation - Stage 2
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
     * Show scholarship documents evaluation - Stage 3
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
}

