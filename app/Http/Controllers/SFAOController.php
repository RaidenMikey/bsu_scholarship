<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Application;
use App\Models\Scholarship;
use App\Models\StudentDocument;
use Illuminate\Support\Facades\DB;

class SFAOController extends Controller
{
    public function dashboard()
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
            ->leftJoin('student_documents', 'users.id', '=', 'student_documents.user_id')
            ->select(
                'users.id as student_id',
                'users.name',
                'users.email',
                'users.created_at',
                'users.campus_id',
                DB::raw('MAX(student_documents.updated_at) as last_uploaded'),
                DB::raw('COUNT(DISTINCT student_documents.id) as documents_count')
            )
            ->groupBy('users.id', 'users.name', 'users.email', 'users.created_at', 'users.campus_id')
            ->get();

        // Add application status information to each student
        $students->each(function($student) {
            $student->has_applications = $student->applications->count() > 0;
            $student->has_documents = $student->documents_count > 0;
            $student->application_status = $student->applications->pluck('status')->unique()->toArray();
            $student->applied_scholarships = $student->applications->pluck('scholarship.scholarship_name')->toArray();
        });

        // Get applications only from students under this SFAO admin's jurisdiction
        $applications = Application::with('user', 'scholarship')
            ->whereHas('user', function($query) use ($campusIds) {
                $query->whereIn('campus_id', $campusIds);
            })
            ->get();
            
        $scholarships = Scholarship::all();

        return view('sfao.dashboard', compact('user', 'students', 'applications', 'scholarships', 'sfaoCampus'));
    }

    public function applicants()
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::with('campus')->find(session('user_id'));
        
        // Get the SFAO admin's campus and all campuses under it
        $sfaoCampus = $user->campus;
        $campusIds = $sfaoCampus->getAllCampusesUnder()->pluck('id');

        // Get students who have uploaded at least one document, only from this SFAO admin's jurisdiction
        $students = DB::table('student_documents')
            ->join('users', 'student_documents.user_id', '=', 'users.id')
            ->whereIn('users.campus_id', $campusIds)
            ->select(
                'users.id as student_id',
                'users.name',
                'users.email',
                'users.campus_id',
                DB::raw('MAX(student_documents.updated_at) as last_uploaded')
            )
            ->groupBy('users.id', 'users.name', 'users.email', 'users.campus_id')
            ->get();

        return view('sfao.partials.applicants', compact('students', 'sfaoCampus'));
    }

    /**
     * View student documents
     */
    public function viewDocuments($user_id)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $student = User::findOrFail($user_id);
        $documents = StudentDocument::where('user_id', $user_id)->get();

        return view('sfao.partials.view-documents', compact('student', 'documents'));
    }

    /**
     * Approve application
     */
    public function approveApplication($id)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $application = Application::findOrFail($id);
        $application->status = 'Approved';
        $application->save();

        return back()->with('success', 'Application approved.');
    }

    /**
     * Reject application
     */
    public function rejectApplication($id)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $application = Application::findOrFail($id);
        $application->status = 'Rejected';
        $application->save();

        return back()->with('success', 'Application rejected.');
    }

}
