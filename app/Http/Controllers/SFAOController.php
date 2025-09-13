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

        $user = User::find(session('user_id'));

        $students = DB::table('student_documents')
            ->join('users', 'student_documents.user_id', '=', 'users.id')
            ->select(
                'users.id as student_id',
                'users.name',
                'users.email',
                DB::raw('MAX(student_documents.updated_at) as last_uploaded')
            )
            ->groupBy('users.id', 'users.name', 'users.email')
            ->get();

        $applications = Application::with('user', 'scholarship')->get();
        $scholarships = Scholarship::all();

        return view('sfao.dashboard', compact('user', 'students', 'applications', 'scholarships'));
    }

    public function applicants()
    {
        // Get students who have uploaded at least one document
        $students = DB::table('student_documents')
            ->join('users', 'student_documents.user_id', '=', 'users.id')
            ->select(
                'users.id as student_id',
                'users.name',
                'users.email',
                DB::raw('MAX(student_documents.updated_at) as last_uploaded')
            )
            ->groupBy('users.id', 'users.name', 'users.email')
            ->get();

        return view('sfao.partials.applicants', compact('students'));
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
