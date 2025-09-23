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
    
    // Document Uploads
    Route::get('/upload-documents/{scholarship_id}', [UserManagementController::class, 'showUploadForm'])->name('upload-documents');
    Route::post('/upload-documents/{scholarship_id}', [UserManagementController::class, 'uploadDocuments'])->name('upload-documents.submit');
    
    // Print Application
    Route::get('/print-application', [UserManagementController::class, 'printApplication']);
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

        // View Form
        Route::get('/form/{id}', [FormController::class, 'show'])->name('form.view');
        
        // Application Management
        Route::post('/applications/{id}/approve', [ApplicationManagementController::class, 'centralApproveApplication'])->name('applications.approve');
        Route::post('/applications/{id}/reject', [ApplicationManagementController::class, 'centralRejectApplication'])->name('applications.reject');
        Route::post('/applications/{id}/claim', [ApplicationManagementController::class, 'centralClaimGrant'])->name('applications.claim');
        
        // Staff Management
        Route::post('/staff/invite', [UserManagementController::class, 'inviteStaff'])->name('staff.invite');
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