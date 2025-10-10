<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Campus;
use App\Models\SfaoRequirement;
use App\Models\Application;
use App\Models\Notification;
use App\Models\Form;
use App\Models\Scholarship;
use App\Models\Invitation;
use App\Models\StudentSubmittedDocument;
use App\Models\ScholarshipRequiredDocument;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * =====================================================
 * USER MANAGEMENT CONTROLLER
 * =====================================================
 * 
 * This controller handles all user-related functionality
 * including authentication, profile management, document
 * uploads, and user administration.
 * 
 * Combined functionality from:
 * - AuthController
 * - StudentController (user-specific methods)
 * - SFAODocumentController
 * - CentralController (staff management)
 * - InvitationController
 */
class UserManagementController extends Controller
{
    // =====================================================
    // AUTHENTICATION METHODS
    // =====================================================

    /**
     * Show login form
     */
    public function showLogin()
    {
        if (session()->has('user_id')) {
            return redirect(match (session('role')) {
                'student' => route('student.dashboard'),
                'sfao'    => '/sfao',
                'central' => '/central',
                default   => '/'
            });
        }
        return view('auth.auth');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'campus_id' => 'required|exists:campuses,id',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['Invalid credentials']);
        }

        if (!$user->hasVerifiedEmail()) {
            return back()->withErrors(['Your email is not verified. Please check your inbox.']);
        }

        if ($request->campus_id != $user->campus_id) {
            return back()->withErrors(['The selected campus does not match your account.']);
        }

        session([
            'user_id' => $user->id,
            'role' => $user->role,
        ]);

        return redirect(match ($user->role) {
            'student' => '/student',
            'sfao'    => '/sfao',
            'central' => '/central',
            default   => '/'
        });
    }

    /**
     * Handle logout
     */
    public function logout()
    {
        session()->flush();
        return redirect('/login')->with('logged_out', true);
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => ['required','email','unique:users,email','regex:/^[a-zA-Z0-9._%+-]+@g\.batstate-u\.edu\.ph$/'],
            'password'  => 'required|string|confirmed|min:6',
            'role'      => 'required|string',
            'campus_id' => 'required|exists:campuses,id',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'campus_id' => $request->campus_id,
        ]);

        $user->sendEmailVerificationNotification();

        return redirect('/login')->with('status', 'Account created! Please verify your email before logging in.');
    }

    /**
     * Show email verification notice
     */
    public function showVerificationNotice()
    {
        return view('auth.verify-email');
    }

    /**
     * Handle email verification
     */
    public function verifyEmail($id, $hash, Request $request)
    {
        $user = User::findOrFail($id);

        if (!\Illuminate\Support\Facades\URL::hasValidSignature($request)) {
            abort(403);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        // For SFAO users, update invitation status and redirect to password setup
        if ($user->role === 'sfao') {
            // Update invitation status to active
            $invitation = Invitation::where('email', $user->email)->first();
            if ($invitation) {
                $invitation->accept(); // This sets status to 'active' and accepted_at to now
            }
            
            // Log the user in temporarily for password setup
            session([
                'user_id' => $user->id,
                'role' => 'sfao',
                'name' => $user->name,
                'campus_id' => $user->campus_id,
            ]);
            
            return redirect('/sfao/password-setup')->with('success', 'Email verified! Please set up your password to complete your account setup.');
        }

        return redirect('/login')->with('verified', true);
    }

    /**
     * Resend verification email
     */
    public function resendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['Email not found.']);
        }

        if ($user->hasVerifiedEmail()) {
            return back()->with('message', 'Your email is already verified.');
        }

        $user->sendEmailVerificationNotification();
        return back()->with('message', 'Verification email sent!');
    }

    // =====================================================
    // STUDENT DASHBOARD METHODS
    // =====================================================

    /**
     * Student dashboard
     */
    public function studentDashboard(Request $request)
    {
        if (!session()->has('user_id') || session('role') !== 'student') {
            return redirect('/login')->with('session_expired', true);
        }

        $userId = session('user_id');
        $user = User::with('appliedScholarships')->find($userId);
        $form = Form::where('user_id', $userId)->first();

        $hasApplication = $form !== null;
        $gwa = $form ? floatval($form->gwa) : null;

        $scholarships = collect();
        if ($hasApplication) {
            // Get all scholarships that allow new applications and filter by all conditions
            $allScholarships = Scholarship::where('is_active', true)
                ->with('conditions')
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

            // Apply sorting
            $sortBy = $request->get('sort_by', 'submission_deadline');
            $sortOrder = $request->get('sort_order', 'asc');
            
            $scholarships = $this->sortScholarships($scholarships, $sortBy, $sortOrder);

            $appliedIds = $user->appliedScholarships->pluck('id')->toArray();
            foreach ($scholarships as $scholarship) {
                $scholarship->applied = in_array($scholarship->id, $appliedIds);
            }
        }

        $applications = $user ? $user->appliedScholarships : collect();
        
        // Get detailed application tracking data with enhanced information
        $applicationTracking = Application::where('user_id', $userId)
            ->with(['scholarship' => function($query) {
                $query->with(['conditions', 'requiredDocuments']);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        // Add additional data to each application
        $applicationTracking->each(function($application) {
            $application->scholarship->status_badge = $this->getStatusBadge($application->status);
            $application->scholarship->type_badge = $this->getTypeBadge($application->type);
            $application->scholarship->days_remaining = $this->getDaysRemaining($application->scholarship->submission_deadline);
            $application->scholarship->grant_amount_formatted = $application->scholarship->grant_amount ? '₱' . number_format($application->scholarship->grant_amount, 2) : 'Not specified';
        });

        // Get notifications for the student
        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $unreadCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return view('student.dashboard', compact('hasApplication', 'scholarships', 'gwa', 'applications', 'applicationTracking', 'form', 'notifications', 'unreadCount'));
    }

    /**
     * Show application form
     */
    public function showApplicationForm()
    {
        if (!session()->has('user_id') || session('role') !== 'student') {
            return redirect('/login')->with('session_expired', true);
        }

        $existingApplication = Form::where('user_id', session('user_id'))->first();
        $existingLevels = $existingApplication ? json_decode($existingApplication->level, true) : [];

        return view('student.forms.application_form', compact('existingApplication', 'existingLevels'));
    }

    /**
     * Print application
     */
    public function printApplication()
    {
        if (!session()->has('user_id') || session('role') !== 'student') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::find(session('user_id'));
        $form = Form::where('user_id', $user->id)->first();

        $pdf = Pdf::loadView('student.forms.application_form_pdf', compact('user', 'form'))
                  ->setPaper('A4', 'portrait');
        return $pdf->stream('application_form.pdf');
    }

    /**
     * Show scholarships for student
     */
    public function scholarships(Request $request)
    {
        if (!session()->has('user_id') || session('role') !== 'student') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::with('appliedScholarships')->find(session('user_id'));
        $form = Form::where('user_id', $user->id)->first();
        $gwa = $form?->gwa;

        $scholarships = collect();

        if ($form) {
            // Get all scholarships that allow new applications and filter by all conditions
            $allScholarships = Scholarship::where('is_active', true)
                ->with('conditions')
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

            // Apply sorting
            $sortBy = $request->get('sort_by', 'submission_deadline');
            $sortOrder = $request->get('sort_order', 'asc');
            
            $scholarships = $this->sortScholarships($scholarships, $sortBy, $sortOrder);
        }

        // Mark applied scholarships
        $appliedIds = $user->appliedScholarships->pluck('id')->toArray();
        foreach ($scholarships as $scholarship) {
            $scholarship->applied = in_array($scholarship->id, $appliedIds);
        }

        return view('student.partials.tabs.scholarships', compact('scholarships', 'gwa', 'form'));
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
                case 'priority_level':
                    $priorityOrder = ['high' => 1, 'medium' => 2, 'low' => 3];
                    return $priorityOrder[$scholarship->priority_level] ?? 4;
                case 'scholarship_type':
                    return $scholarship->scholarship_type;
                case 'grant_type':
                    return $scholarship->grant_type;
                case 'slots_available':
                    return $scholarship->slots_available ?? 999999;
                case 'gwa_requirement':
                    return $scholarship->getGwaRequirement() ?? 999;
                default:
                    return $scholarship->submission_deadline;
            }
        }, SORT_REGULAR, $sortOrder === 'desc');
    }

    // =====================================================
    // DOCUMENT MANAGEMENT METHODS
    // =====================================================

    /**
     * Show document upload form
     */
    public function showUploadForm($scholarship_id)
    {
        if (!session()->has('user_id') || session('role') !== 'student') {
            return redirect('/login')->with('session_expired', true);
        }

        $scholarship = Scholarship::findOrFail($scholarship_id);
        return view('student.upload-documents', compact('scholarship'));
    }

    /**
     * Handle document uploads
     */
    public function uploadDocuments(Request $request, $scholarship_id)
    {
        if (!session()->has('user_id') || session('role') !== 'student') {
            return redirect('/login')->with('session_expired', true);
        }

        $request->validate([
            'form_137'         => 'required|file|mimes:pdf,jpg,png|max:10240',
            'grades'           => 'required|file|mimes:pdf,jpg,png|max:10240',
            'certificate'      => 'nullable|file|mimes:pdf,jpg,png|max:10240',
            'application_form' => 'required|file|mimes:pdf,jpg,png|max:10240',
        ]);

        $userId = session('user_id');

        $files = [
            'form_137'         => $request->file('form_137'),
            'grades'           => $request->file('grades'),
            'certificate'      => $request->file('certificate'),
            'application_form' => $request->file('application_form'),
        ];

        $filePaths = [];
        foreach ($files as $key => $file) {
            if ($file) {
                $filePaths[$key] = $file->store("documents/{$userId}", 'public');
            }
        }

        // Ensure scholarship_id is stored
        $filePaths['scholarship_id'] = $scholarship_id;

        SfaoRequirement::updateOrCreate(
            ['user_id' => $userId, 'scholarship_id' => $scholarship_id],
            $filePaths
        );

        // Mark student as applied
        Application::updateOrCreate(
            [
                'user_id'        => $userId,
                'scholarship_id' => $scholarship_id,
            ],
            [
                'status' => 'pending',
            ]
        );

        return redirect()
            ->route('student.dashboard')
            ->with('success', 'Documents uploaded successfully and you are now applied to this scholarship.');
    }

    // =====================================================
    // MULTI-STAGE APPLICATION METHODS
    // =====================================================

    /**
     * Show multi-stage application form
     */
    public function showMultiStageApplication($scholarship_id)
    {
        if (!session()->has('user_id') || session('role') !== 'student') {
            return redirect('/login')->with('session_expired', true);
        }

        $scholarship = Scholarship::with(['requiredDocuments'])->findOrFail($scholarship_id);
        $userId = session('user_id');
        
        // Get existing submitted documents
        $submittedDocuments = StudentSubmittedDocument::byUserAndScholarship($userId, $scholarship_id)->get();
        
        // Check application status
        $application = Application::where('user_id', $userId)
            ->where('scholarship_id', $scholarship_id)
            ->first();

        // Determine current stage based on submitted documents
        $currentStage = 1;
        $sfaoDocsCount = $submittedDocuments->where('document_category', 'sfao_required')->count();
        $scholarshipDocsCount = $submittedDocuments->where('document_category', 'scholarship_required')->count();
        
        if ($sfaoDocsCount >= 3) { // At least 3 mandatory SFAO docs
            $currentStage = 2;
        }
        
        if ($scholarship->requiredDocuments->where('is_mandatory', true)->count() == 0 || $scholarshipDocsCount >= $scholarship->requiredDocuments->where('is_mandatory', true)->count()) {
            if ($currentStage == 2) {
                $currentStage = 3;
            }
        }
        
        if ($application) {
            $currentStage = 3; // Application already submitted
        }

        return view('student.multi-stage-application', compact('scholarship', 'submittedDocuments', 'application', 'currentStage'));
    }

    /**
     * Handle Stage 1: SFAO Required Documents
     */
    public function submitSfaoDocuments(Request $request, $scholarship_id)
    {
        if (!session()->has('user_id') || session('role') !== 'student') {
            return redirect('/login')->with('session_expired', true);
        }

        $request->validate([
            'form_137'         => 'required|file|mimes:pdf,jpg,png|max:10240',
            'grades'           => 'required|file|mimes:pdf,jpg,png|max:10240',
            'certificate'      => 'nullable|file|mimes:pdf,jpg,png|max:10240',
            'application_form' => 'required|file|mimes:pdf,jpg,png|max:10240',
        ]);

        $userId = session('user_id');
        
        // Define SFAO required documents
        $sfaoDocuments = [
            'form_137' => ['name' => 'Form 137', 'mandatory' => true],
            'grades' => ['name' => 'Grades', 'mandatory' => true],
            'certificate' => ['name' => 'Certificate', 'mandatory' => false],
            'application_form' => ['name' => 'Application Form', 'mandatory' => true],
        ];

        foreach ($sfaoDocuments as $field => $config) {
            if ($request->hasFile($field) && $request->file($field)) {
                $file = $request->file($field);
                $filePath = $file->store("documents/{$userId}/sfao", 'public');
                
                StudentSubmittedDocument::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'scholarship_id' => $scholarship_id,
                        'document_category' => 'sfao_required',
                        'document_name' => $config['name'],
                    ],
                    [
                        'file_path' => $filePath,
                        'original_filename' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientOriginalExtension(),
                        'file_size' => $file->getSize(),
                        'is_mandatory' => $config['mandatory'],
                    ]
                );
            }
        }

        return redirect()->route('student.apply', $scholarship_id)
            ->with('success', 'SFAO required documents uploaded successfully!');
    }

    /**
     * Handle Stage 2: Scholarship Required Documents
     */
    public function submitScholarshipDocuments(Request $request, $scholarship_id)
    {
        if (!session()->has('user_id') || session('role') !== 'student') {
            return redirect('/login')->with('session_expired', true);
        }

        $scholarship = Scholarship::with(['requiredDocuments'])->findOrFail($scholarship_id);
        $userId = session('user_id');
        
        // Get scholarship required documents
        $requiredDocs = $scholarship->requiredDocuments;
        
        if ($requiredDocs->isEmpty()) {
            return redirect()->route('student.apply', $scholarship_id)
                ->with('success', 'No additional documents required for this scholarship.');
        }

        // Validate files based on scholarship requirements
        $validationRules = [];
        foreach ($requiredDocs as $doc) {
            $fieldName = 'scholarship_doc_' . $doc->id;
            $rule = $doc->is_mandatory ? 'required' : 'nullable';
            $rule .= '|file|mimes:pdf,jpg,png|max:10240';
            $validationRules[$fieldName] = $rule;
        }

        $request->validate($validationRules);

        // Process each scholarship required document
        foreach ($requiredDocs as $doc) {
            $fieldName = 'scholarship_doc_' . $doc->id;
            
            if ($request->hasFile($fieldName) && $request->file($fieldName)) {
                $file = $request->file($fieldName);
                $filePath = $file->store("documents/{$userId}/scholarship", 'public');
                
                StudentSubmittedDocument::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'scholarship_id' => $scholarship_id,
                        'document_category' => 'scholarship_required',
                        'document_name' => $doc->document_name,
                    ],
                    [
                        'file_path' => $filePath,
                        'original_filename' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientOriginalExtension(),
                        'file_size' => $file->getSize(),
                        'is_mandatory' => $doc->is_mandatory,
                        'description' => $doc->description,
                    ]
                );
            }
        }

        return redirect()->route('student.apply', $scholarship_id)
            ->with('success', 'Scholarship required documents uploaded successfully!');
    }

    /**
     * Handle Stage 3: Final Confirmation and Application Submission
     */
    public function submitFinalApplication(Request $request, $scholarship_id)
    {
        if (!session()->has('user_id') || session('role') !== 'student') {
            return redirect('/login')->with('session_expired', true);
        }

        $userId = session('user_id');
        
        // Check if all required documents are submitted
        $sfaoDocs = StudentSubmittedDocument::byUserAndScholarship($userId, $scholarship_id)
            ->sfaoRequired()
            ->mandatory()
            ->count();
        
        $scholarship = Scholarship::with(['requiredDocuments'])->findOrFail($scholarship_id);
        $requiredScholarshipDocs = $scholarship->requiredDocuments->where('is_mandatory', true)->count();
        $submittedScholarshipDocs = StudentSubmittedDocument::byUserAndScholarship($userId, $scholarship_id)
            ->scholarshipRequired()
            ->mandatory()
            ->count();

        // Check if all mandatory documents are submitted
        if ($sfaoDocs < 3) { // form_137, grades, application_form are mandatory
            return redirect()->route('student.apply', $scholarship_id)
                ->with('error', 'Please submit all required SFAO documents first.');
        }

        if ($requiredScholarshipDocs > 0 && $submittedScholarshipDocs < $requiredScholarshipDocs) {
            return redirect()->route('student.apply', $scholarship_id)
                ->with('error', 'Please submit all required scholarship documents first.');
        }

        // Create or update application
        $hasClaimedGrant = Application::hasClaimedGrant($userId, $scholarship_id);
        $applicationType = $hasClaimedGrant ? 'continuing' : 'new';
        
        Application::updateOrCreate(
            [
                'user_id' => $userId,
                'scholarship_id' => $scholarship_id,
            ],
            [
                'type' => $applicationType,
                'status' => 'pending',
            ]
        );

        return redirect()->route('student.dashboard')
            ->with('success', 'Application submitted successfully! Your application is now under review.');
    }

    /**
     * Get application progress for a specific scholarship
     */
    public function getApplicationProgress($scholarship_id)
    {
        if (!session()->has('user_id') || session('role') !== 'student') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = session('user_id');
        $scholarship = Scholarship::with(['requiredDocuments'])->findOrFail($scholarship_id);
        
        // Check SFAO documents progress
        $sfaoSubmitted = StudentSubmittedDocument::byUserAndScholarship($userId, $scholarship_id)
            ->sfaoRequired()
            ->count();
        $sfaoRequired = 4; // form_137, grades, certificate, application_form
        
        // Check scholarship documents progress
        $scholarshipRequired = $scholarship->requiredDocuments->where('is_mandatory', true)->count();
        $scholarshipSubmitted = StudentSubmittedDocument::byUserAndScholarship($userId, $scholarship_id)
            ->scholarshipRequired()
            ->mandatory()
            ->count();
        
        // Check if application is submitted
        $application = Application::where('user_id', $userId)
            ->where('scholarship_id', $scholarship_id)
            ->first();

        return response()->json([
            'stage1' => [
                'completed' => $sfaoSubmitted >= 3, // At least 3 mandatory SFAO docs
                'progress' => $sfaoSubmitted,
                'required' => 3
            ],
            'stage2' => [
                'completed' => $scholarshipRequired == 0 || $scholarshipSubmitted >= $scholarshipRequired,
                'progress' => $scholarshipSubmitted,
                'required' => $scholarshipRequired
            ],
            'stage3' => [
                'completed' => $application !== null,
                'progress' => $application ? 1 : 0,
                'required' => 1
            ]
        ]);
    }

    /**
     * Upload profile picture
     */
    public function uploadProfilePicture(Request $request, $role)
    {
        if (!session()->has('user_id') || session('role') !== $role) {
            return redirect('/login')->with('session_expired', true);
        }

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::find(session('user_id'));

        if ($user->profile_picture && Storage::exists('public/profile_pictures/' . $user->profile_picture)) {
            Storage::delete('public/profile_pictures/' . $user->profile_picture);
        }

        $file = $request->file('profile_picture');
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/profile_pictures', $filename);

        $user->profile_picture = $filename;
        $user->save();

        return back()->with('success', 'Profile picture updated.');
    }

    // =====================================================
    // STAFF MANAGEMENT METHODS (CENTRAL)
    // =====================================================

    /**
     * Send invitation to new SFAO admin
     */
    public function inviteStaff(Request $request)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'campus_id' => 'required|exists:campuses,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if user already exists
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return back()->with('error', 'A user with this email already exists.');
        }

        try {
            // Create invitation record first
            $invitation = Invitation::createInvitation(
                $request->email,
                $request->name,
                $request->campus_id,
                session('user_id')
            );

            // Create SFAO user account with unverified email
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make(\Illuminate\Support\Str::random(16)), // Temporary password
                'role' => 'sfao',
                'campus_id' => $request->campus_id,
                'email_verified_at' => null, // Not verified yet
            ]);

            // Send account created email with verification link
            try {
                Mail::to($user->email)->send(new \App\Mail\SFAOAccountCreatedMail($user));
                Log::info("SFAO account created and email sent to: {$user->email}");
            } catch (\Exception $mailException) {
                Log::error("Failed to send email to {$user->email}: " . $mailException->getMessage());
                // Continue even if email fails - user account is created
            }

            return back()->with('success', "SFAO account created for {$request->name} ({$request->email}). Verification email sent - they need to verify their email and set up their password.");
        } catch (\Exception $e) {
            Log::error("Failed to create SFAO account: " . $e->getMessage());
            return back()->with('error', 'Failed to create SFAO account. Please try again.');
        }
    }

    /**
     * Deactivate SFAO staff member (remove from users table)
     */
    public function deactivateStaff(Request $request, $id)
    {
        if (!session()->has('user_id') || session('role') !== 'central') {
            return redirect('/login')->with('session_expired', true);
        }

        try {
            $user = User::findOrFail($id);
            
            // Check if user is SFAO staff
            if ($user->role !== 'sfao') {
                return back()->with('error', 'Only SFAO staff can be deactivated.');
            }

            // Store user information for logging before deletion
            $userName = $user->name;
            $userEmail = $user->email;
            $userCampusId = $user->campus_id;

            // Delete related applications first (if any)
            $user->applications()->delete();
            
            // Delete the user's form (if any)
            $user->form()->delete();
            
            // Update invitation status to 'removed' before deleting user
            $invitation = Invitation::where('email', $userEmail)->first();
            if ($invitation) {
                $invitation->markAsRemoved();
                Log::info("Invitation status updated to 'removed' for: {$userName} ({$userEmail})");
            }
            
            // Delete the user record completely
            $user->delete();
            
            Log::info("SFAO staff completely removed: {$userName} ({$userEmail}) by user ID: " . session('user_id'));

            return back()->with('success', "SFAO staff member {$userName} has been completely removed from the system. Their account can no longer be used to login.");
        } catch (\Exception $e) {
            Log::error("Failed to remove SFAO staff: " . $e->getMessage());
            return back()->with('error', 'Failed to remove staff member. Please try again.');
        }
    }

    /**
     * Note: Reactivate functionality removed since deactivated users are now completely deleted.
     * To restore access, a new SFAO account must be created using the invite functionality.
     */

    // =====================================================
    // SFAO PASSWORD SETUP METHODS
    // =====================================================


    /**
     * Show SFAO password setup form after email verification
     */
    public function showSFAOPasswordSetup()
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::find(session('user_id'));
        
        // Check if user has verified email and needs to set up password
        if (!$user->hasVerifiedEmail()) {
            return redirect('/email/verify')->with('error', 'Please verify your email first.');
        }

        return view('auth.sfao-password-setup', compact('user'));
    }

    /**
     * Handle SFAO password setup after email verification
     */
    public function setupSFAOPassword(Request $request)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::find(session('user_id'));

        if (!$user->hasVerifiedEmail()) {
            return redirect('/email/verify')->with('error', 'Please verify your email first.');
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ]);

        try {
            // Update the user's password
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return redirect('/sfao')->with('success', 'Account setup completed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to set up password. Please try again.');
        }
    }

    // =====================================================
    // USER PROFILE METHODS
    // =====================================================

    /**
     * Get user profile information
     */
    public function getUserProfile($userId)
    {
        return User::with(['campus', 'form', 'applications.scholarship'])->find($userId);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect('/login')->with('session_expired', true);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . session('user_id'),
        ]);

        $user = User::find(session('user_id'));
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect('/login')->with('session_expired', true);
        }

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::find(session('user_id'));

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password changed successfully.');
    }

    /**
     * Get status badge for application
     */
    private function getStatusBadge($status)
    {
        return match($status) {
            'pending' => ['text' => 'Pending', 'color' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'],
            'approved' => ['text' => 'Approved', 'color' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'],
            'rejected' => ['text' => 'Rejected', 'color' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'],
            'claimed' => ['text' => 'Claimed', 'color' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'],
            default => ['text' => 'Unknown', 'color' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200']
        };
    }

    /**
     * Get type badge for application
     */
    private function getTypeBadge($type)
    {
        return match($type) {
            'new' => ['text' => 'New Applicant', 'color' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'],
            'continuing' => ['text' => 'Continuing', 'color' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'],
            default => ['text' => 'Unknown', 'color' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200']
        };
    }

    /**
     * Get days remaining until deadline
     */
    private function getDaysRemaining($deadline)
    {
        if (!$deadline) return null;
        
        $now = now();
        $deadlineDate = \Carbon\Carbon::parse($deadline);
        
        if ($now->gt($deadlineDate)) {
            return 0; // Deadline has passed
        }
        
        return $now->diffInDays($deadlineDate);
    }
}
