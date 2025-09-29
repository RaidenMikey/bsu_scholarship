<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ApplicationManagementController;
use App\Http\Controllers\ScholarshipManagementController;
use App\Http\Controllers\FormController;
use App\Models\Form;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

// --------------------------------------------------
// PUBLIC ROUTES
// --------------------------------------------------

Route::get('/', fn() => view('home'));

// --------------------------------------------------
// AUTHENTICATION ROUTES
// --------------------------------------------------

Route::get('/login', [UserManagementController::class, 'showLogin'])->name('login');
Route::post('/login', [UserManagementController::class, 'login']);
Route::get('/logout', [UserManagementController::class, 'logout']);

Route::get('/register', [UserManagementController::class, 'showRegister'])->name('register');
Route::post('/register', [UserManagementController::class, 'register']);

// Email Verification
Route::get('/email/verify', [UserManagementController::class, 'showVerificationNotice'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [UserManagementController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/email/verification-notification', [UserManagementController::class, 'resendVerification'])->name('verification.send');

// --------------------------------------------------
// SHARED ROUTES
// --------------------------------------------------

// Profile Picture Upload
Route::post('/upload-profile-picture/{role}', [UserManagementController::class, 'uploadProfilePicture'])
    ->whereIn('role', ['student', 'sfao', 'central']);

// --------------------------------------------------
// STUDENT ROUTES
// --------------------------------------------------

Route::middleware(['web', 'checkUserExists'])->prefix('student')->name('student.')->group(function () {
    
    // Dashboard
    Route::get('/', [UserManagementController::class, 'studentDashboard'])->name('dashboard');
    
    // Scholarships
    Route::get('/scholarships', [UserManagementController::class, 'scholarships'])->name('scholarships');
    
    // Application Form
    Route::get('/form', [UserManagementController::class, 'showApplicationForm'])->name('forms.application_form');
    Route::post('/submit-application', [FormController::class, 'submit'])->name('submit');
    
    // Applications
    Route::get('/applications', [ApplicationManagementController::class, 'studentApplications'])->name('applications');
    Route::post('/apply', [ApplicationManagementController::class, 'apply'])->name('apply');
    Route::post('/unapply', [ApplicationManagementController::class, 'unapply'])->name('unapply');
    
    // Document Uploads (Legacy - Redirect to new multi-stage application)
    Route::get('/upload-documents/{scholarship_id}', function($scholarship_id) {
        return redirect()->route('student.apply', ['scholarship_id' => $scholarship_id]);
    })->name('upload-documents');
    Route::post('/upload-documents/{scholarship_id}', [UserManagementController::class, 'uploadDocuments'])->name('upload-documents.submit');
    
    // Multi-Stage Application
    Route::get('/apply/{scholarship_id}', [UserManagementController::class, 'showMultiStageApplication'])->name('apply');
    Route::post('/apply/{scholarship_id}/sfao-documents', [UserManagementController::class, 'submitSfaoDocuments'])->name('apply.sfao-documents');
    Route::post('/apply/{scholarship_id}/scholarship-documents', [UserManagementController::class, 'submitScholarshipDocuments'])->name('apply.scholarship-documents');
    Route::post('/apply/{scholarship_id}/final-submission', [UserManagementController::class, 'submitFinalApplication'])->name('apply.final-submission');
    Route::get('/apply/{scholarship_id}/progress', [UserManagementController::class, 'getApplicationProgress'])->name('apply.progress');
    
    // Print Application
    Route::get('/print-application', [UserManagementController::class, 'printApplication'])->name('print-application');
});

// --------------------------------------------------
// SFAO ROUTES
// --------------------------------------------------

Route::middleware(['web', 'checkUserExists:sfao'])->prefix('sfao')->name('sfao.')->group(function () {

    // Dashboard
    Route::get('/', [ApplicationManagementController::class, 'sfaoDashboard'])->name('dashboard');
    
    // Applicants
    Route::get('/applicants/{user_id}/documents', [ApplicationManagementController::class, 'viewDocuments'])->name('viewDocuments');
    
    // Application Management
    Route::post('/applications/{id}/approve', [ApplicationManagementController::class, 'sfaoApproveApplication'])->name('applications.approve');
    Route::post('/applications/{id}/reject', [ApplicationManagementController::class, 'sfaoRejectApplication'])->name('applications.reject');
    Route::post('/applications/{id}/claim', [ApplicationManagementController::class, 'sfaoClaimGrant'])->name('applications.claim');

    // Scholarships Management
    Route::post('/scholarships/store', [ScholarshipManagementController::class, 'store'])->name('scholarships.store');
    Route::post('/scholarships/{id}/update', [ScholarshipManagementController::class, 'update'])->name('scholarships.update');
    Route::get('/scholarships', [ScholarshipManagementController::class, 'sfaoIndex'])->name('scholarships.index');
    Route::get('/scholarships/{id}', [ScholarshipManagementController::class, 'sfaoShow'])->name('scholarships.show');
});

// --------------------------------------------------
// CENTRAL ROUTES
// --------------------------------------------------

Route::middleware(['web', 'checkUserExists:central'])
    ->prefix('central')
    ->name('central.')
    ->group(function () {

        // Dashboard
        Route::get('/', [ApplicationManagementController::class, 'centralDashboard'])->name('dashboard');

        // Scholarships
        Route::prefix('scholarships')->name('scholarships.')->group(function () {
            Route::get('/create', [ScholarshipManagementController::class, 'create'])->name('create');
            Route::post('/store', [ScholarshipManagementController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [ScholarshipManagementController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ScholarshipManagementController::class, 'update'])->name('update');
            Route::delete('/{id}', [ScholarshipManagementController::class, 'destroy'])->name('destroy');
        });

        
        // Application Management
        Route::post('/applications/{id}/approve', [ApplicationManagementController::class, 'centralApproveApplication'])->name('applications.approve');
        Route::post('/applications/{id}/reject', [ApplicationManagementController::class, 'centralRejectApplication'])->name('applications.reject');
        Route::post('/applications/{id}/claim', [ApplicationManagementController::class, 'centralClaimGrant'])->name('applications.claim');
        
        // Staff Management
        Route::post('/staff/invite', [UserManagementController::class, 'inviteStaff'])->name('staff.invite');
        Route::post('/staff/{id}/deactivate', [UserManagementController::class, 'deactivateStaff'])->name('staff.deactivate');
    });


// --------------------------------------------------
// SFAO PASSWORD SETUP ROUTES
// --------------------------------------------------

// SFAO password setup after email verification
Route::get('/sfao/password-setup', [UserManagementController::class, 'showSFAOPasswordSetup'])->name('sfao.password.setup');
Route::post('/sfao/password-setup', [UserManagementController::class, 'setupSFAOPassword'])->name('sfao.password.setup');

// --------------------------------------------------
// TEST ROUTES (Forms)
// --------------------------------------------------

// Show raw HTML form
Route::get('/application-form', function () {
    $existingApplication = Form::where('user_id', auth()->id())->first();

    if (!$existingApplication) {
        return redirect()->back()->with('error', 'No form found for this user.');
    }

    return view('forms.application_form', compact('existingApplication'));
});

// Handle PDF download
Route::post('/application-form/download', function (Request $request) {
    $request->validate([
        'form_id' => 'required|integer|exists:forms,id',
    ]);

    $form = Form::findOrFail($request->form_id);

    $pdf = Pdf::loadView('student.forms.application_form_pdf', compact('form'))
              ->setPaper('A4', 'portrait');

    return $pdf->download('application_form.pdf');
});

// =====================================================
// NOTIFICATION ROUTES
// =====================================================

// Mark notification as read
Route::post('/notifications/{id}/mark-read', function ($id) {
    if (!session()->has('user_id')) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    $notification = \App\Models\Notification::where('id', $id)
        ->where('user_id', session('user_id'))
        ->first();
    
    if (!$notification) {
        return response()->json(['error' => 'Notification not found'], 404);
    }
    
    $notification->markAsRead();
    
    return response()->json(['success' => true]);
});

// Mark all notifications as read
Route::post('/notifications/mark-all-read', function () {
    if (!session()->has('user_id')) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    \App\Models\Notification::where('user_id', session('user_id'))
        ->where('is_read', false)
        ->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    
    return response()->json(['success' => true]);
});