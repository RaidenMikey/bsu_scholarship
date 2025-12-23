<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApplicationForm;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ApplicationFormController extends Controller
{
    /**
     * Show all application forms (SFAO)
     */
    public function index()
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::with('campus')->find(session('user_id'));
        $sfaoCampus = $user->campus;
        $campusIds = $sfaoCampus->getAllCampusesUnder()->pluck('id');

        $forms = ApplicationForm::with(['campus', 'uploader'])
            ->whereIn('campus_id', $campusIds)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('sfao.application-forms.index', compact('forms', 'user', 'sfaoCampus'));
    }

    /**
     * Show upload form (SFAO)
     */
    public function create()
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::with('campus')->find(session('user_id'));
        $sfaoCampus = $user->campus;
        
        return view('sfao.application-forms.upload', compact('user', 'sfaoCampus'));
    }

    /**
     * Store uploaded form (SFAO)
     */
    public function store(Request $request)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $request->validate([
            'form_name' => 'required|string|max:255',
            'form_type' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240', // 10MB max
        ]);

        $user = User::with('campus')->find(session('user_id'));

        try {
            // Store file
            $file = $request->file('file');
            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $path = $file->storeAs('application_forms', $filename);

            // Create record
            ApplicationForm::create([
                'form_name' => $request->form_name,
                'form_type' => $request->form_type,
                'description' => $request->description,
                'file_path' => $path,
                'file_type' => $file->getClientOriginalExtension(),
                'campus_id' => $user->campus_id,
                'uploaded_by' => $user->id,
            ]);


            return redirect()->route('sfao.dashboard', ['tabs' => 'all-app-forms'])
                ->with('success', 'Application form uploaded successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to upload application form: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to upload form. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Delete form (SFAO)
     */
    public function destroy($id)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::with('campus')->find(session('user_id'));
        $campusIds = $user->campus->getAllCampusesUnder()->pluck('id');

        $form = ApplicationForm::findOrFail($id);

        // Verify SFAO has permission
        if (!$campusIds->contains($form->campus_id)) {
            return back()->with('error', 'You do not have permission to delete this form.');
        }

        try {
            // Delete file
            if (Storage::exists($form->file_path)) {
                Storage::delete($form->file_path);
            }

            // Delete record
            $form->delete();

            return back()->with('success', 'Application form deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to delete application form: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete form. Please try again.');
        }
    }

    /**
     * Show forms list for students
     */
    public function studentIndex()
    {
        if (!session()->has('user_id') || session('role') !== 'student') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::with('campus')->find(session('user_id'));

        // Get forms from student's campus
        $forms = ApplicationForm::with(['campus', 'uploader'])
            ->where('campus_id', $user->campus_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.application-forms.index', compact('forms', 'user'));
    }

    /**
     * Download form
     */
    public function download($id)
    {
        if (!session()->has('user_id')) {
            return redirect('/login')->with('session_expired', true);
        }

        $form = ApplicationForm::findOrFail($id);
        $user = User::with('campus')->find(session('user_id'));

        // Verify access
        if (session('role') === 'student' && $form->campus_id !== $user->campus_id) {
            return back()->with('error', 'You do not have permission to download this form.');
        }

        if (session('role') === 'sfao') {
            $campusIds = $user->campus->getAllCampusesUnder()->pluck('id');
            if (!$campusIds->contains($form->campus_id)) {
                return back()->with('error', 'You do not have permission to download this form.');
            }
        }

        // Increment download count
        $form->incrementDownloads();

        // Download file
        if (Storage::exists($form->file_path)) {
            return Storage::download($form->file_path, $form->form_name . '.' . $form->file_type);
        }

        return back()->with('error', 'File not found.');
    }
}
