<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SFAOController;
use App\Http\Controllers\CentralController;
use App\Http\Controllers\ScholarshipController;
use App\Http\Controllers\ScholarshipRequirementController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\SFAODocumentController;
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

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Email Verification
Route::get('/email/verify', [AuthController::class, 'showVerificationNotice'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])->name('verification.send');

// --------------------------------------------------
// SHARED ROUTES
// --------------------------------------------------

// Profile Picture Upload
Route::post('/upload-profile-picture/{role}', [SFAODocumentController::class, 'uploadProfilePicture'])
    ->whereIn('role', ['student', 'sfao', 'central']);

// --------------------------------------------------
// STUDENT ROUTES
// --------------------------------------------------

Route::middleware(['web', 'checkUserExists'])->prefix('student')->name('student.')->group(function () {
    
    // Dashboard
    Route::get('/', [StudentController::class, 'dashboard'])->name('dashboard');
    
    // Scholarships
    Route::get('/scholarships', [StudentController::class, 'scholarships'])->name('scholarships');
    
    // Application Form
    Route::get('/form', [StudentController::class, 'showApplicationForm'])->name('forms.application_form');
    Route::post('/submit-application', [FormController::class, 'submit'])->name('submit');
    
    // Applications
    Route::get('/applications', [StudentController::class, 'applications'])->name('applications');
    Route::post('/apply', [StudentController::class, 'apply'])->name('apply');
    Route::post('/unapply', [StudentController::class, 'unapply'])->name('unapply');
    
    // Document Uploads
    Route::get('/upload-documents/{scholarship_id}', [SFAODocumentController::class, 'showUploadForm'])->name('upload-documents');
    Route::post('/upload-documents/{scholarship_id}', [SFAODocumentController::class, 'uploadDocuments'])->name('upload-documents.submit');
    
    // Print Application
    Route::get('/print-application', [StudentController::class, 'printApplication']);
});

// --------------------------------------------------
// SFAO ROUTES
// --------------------------------------------------

Route::middleware(['web'])->prefix('sfao')->name('sfao.')->group(function () {

    // Dashboard
    Route::get('/', [SFAOController::class, 'dashboard'])->name('dashboard');
    
    // Applicants
    Route::get('/applicants/{user_id}/documents', [SFAOController::class, 'viewDocuments'])->name('viewDocuments');
    
    // Application Management
    Route::post('/applications/{id}/approve', [SFAOController::class, 'approveApplication'])->name('applications.approve');
    Route::post('/applications/{id}/reject', [SFAOController::class, 'rejectApplication'])->name('applications.reject');
    Route::post('/applications/{id}/claim', [SFAOController::class, 'claimGrant'])->name('applications.claim');

    // Scholarships Management
    Route::post('/scholarships/store', [ScholarshipController::class, 'store'])->name('scholarships.store');
    Route::post('/scholarships/{id}/update', [ScholarshipController::class, 'update'])->name('scholarships.update');
    Route::get('/scholarships', [ScholarshipController::class, 'sfaoIndex'])->name('scholarships.index');
    Route::get('/scholarships/{id}', [ScholarshipController::class, 'sfaoShow'])->name('scholarships.show');
});

// --------------------------------------------------
// CENTRAL ROUTES
// --------------------------------------------------

Route::middleware(['web', 'checkUserExists:central'])
    ->prefix('central')
    ->name('central.')
    ->group(function () {

        // Dashboard
        Route::get('/', [CentralController::class, 'dashboard'])->name('dashboard');

        // Scholarships
        Route::prefix('scholarships')->name('scholarships.')->group(function () {
            Route::get('/create', [ScholarshipController::class, 'create'])->name('create');
            Route::post('/store', [ScholarshipController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [ScholarshipController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ScholarshipController::class, 'update'])->name('update');
            Route::delete('/{id}', [ScholarshipController::class, 'destroy'])->name('destroy');
        });

        // View Form
        Route::get('/form/{id}', [FormController::class, 'show'])->name('form.view');
        
        // Application Management
        Route::post('/applications/{id}/approve', [CentralController::class, 'approveApplication'])->name('applications.approve');
        Route::post('/applications/{id}/reject', [CentralController::class, 'rejectApplication'])->name('applications.reject');
        Route::post('/applications/{id}/claim', [CentralController::class, 'claimGrant'])->name('applications.claim');
        
        // Staff Management
        Route::post('/staff/invite', [CentralController::class, 'inviteStaff'])->name('staff.invite');
        Route::post('/staff/invitations/{id}/cancel', [CentralController::class, 'cancelInvitation'])->name('staff.invitations.cancel');
    });

// --------------------------------------------------
// INVITATION ROUTES (Public)
// --------------------------------------------------

// Invitation acceptance routes (public access)
Route::get('/invitation/{token}', [App\Http\Controllers\InvitationController::class, 'show'])->name('invitation.show');
Route::post('/invitation/{token}/accept', [App\Http\Controllers\InvitationController::class, 'accept'])->name('invitation.accept');

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