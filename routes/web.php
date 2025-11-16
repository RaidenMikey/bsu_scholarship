<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ApplicationManagementController;
use App\Http\Controllers\ScholarshipManagementController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FormPrintController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentApplicationController;
use App\Http\Controllers\SFAOEvaluationController;
use App\Http\Controllers\CentralApplicationController;
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

// Central Admin Login
Route::get('/central/login', [AuthController::class, 'showCentralLogin'])->name('central.login');
Route::post('/central/login', [AuthController::class, 'centralLogin']);

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
Route::post('/upload-profile-picture/{role}', [UserManagementController::class, 'uploadProfilePicture'])
    ->whereIn('role', ['student', 'sfao', 'central']);

// Document Viewer (for DOCX files)
Route::get('/document/view/{id}', [UserManagementController::class, 'viewDocument'])->name('document.view');

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
    Route::get('/form/{scholarship_id}', [UserManagementController::class, 'showApplicationForm'])->name('forms.application_form.scholarship');
    Route::post('/submit-application', [FormController::class, 'submit'])->name('submit');
    
    // Applications
    Route::get('/applications', [StudentApplicationController::class, 'index'])->name('applications');
    Route::post('/apply', [StudentApplicationController::class, 'apply'])->name('apply');
    Route::post('/unapply', [StudentApplicationController::class, 'withdraw'])->name('unapply');
    
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
    Route::get('/print-application', [FormPrintController::class, 'printApplication'])->name('print-application');
    Route::get('/print-application/{scholarship_id}', [FormPrintController::class, 'printApplication'])->name('print-application.scholarship');
    Route::get('/download-file', [FormPrintController::class, 'downloadFile'])->name('download-file');
});

// --------------------------------------------------
// SFAO ROUTES
// --------------------------------------------------

Route::middleware(['web', 'checkUserExists:sfao'])->prefix('sfao')->name('sfao.')->group(function () {

    // Dashboard
    Route::get('/', [ApplicationManagementController::class, 'sfaoDashboard'])->name('dashboard');
    
    // Applicants
    Route::get('/applicants/{user_id}/documents', [ApplicationManagementController::class, 'viewDocuments'])->name('viewDocuments');
    
    // Document Evaluation System (4-Stage Process)
    Route::get('/evaluation/{user_id}', [SFAOEvaluationController::class, 'showEvaluation'])->name('evaluation.show');
    Route::get('/evaluation/{user_id}/scholarship/{scholarship_id}/sfao-documents', [SFAOEvaluationController::class, 'evaluateSfaoDocuments'])->name('evaluation.sfao-documents');
    Route::post('/evaluation/{user_id}/scholarship/{scholarship_id}/sfao-documents/evaluate', [SFAOEvaluationController::class, 'submitSfaoEvaluation'])->name('evaluation.sfao-submit');
    Route::get('/evaluation/{user_id}/scholarship/{scholarship_id}/scholarship-documents', [SFAOEvaluationController::class, 'evaluateScholarshipDocuments'])->name('evaluation.scholarship-documents');
    Route::post('/evaluation/{user_id}/scholarship/{scholarship_id}/scholarship-documents/evaluate', [SFAOEvaluationController::class, 'submitScholarshipEvaluation'])->name('evaluation.scholarship-submit');
    Route::get('/evaluation/{user_id}/scholarship/{scholarship_id}/final', [SFAOEvaluationController::class, 'finalEvaluation'])->name('evaluation.final');
    Route::post('/evaluation/{user_id}/scholarship/{scholarship_id}/final/submit', [SFAOEvaluationController::class, 'submitFinalEvaluation'])->name('evaluation.final-submit');
    
    // Application Management
    Route::post('/applications/{id}/approve', [ApplicationManagementController::class, 'sfaoApproveApplication'])->name('applications.approve');
    Route::post('/applications/{id}/reject', [ApplicationManagementController::class, 'sfaoRejectApplication'])->name('applications.reject');
    Route::post('/applications/{id}/claim', [ApplicationManagementController::class, 'sfaoClaimGrant'])->name('applications.claim');

    // Scholarships Management
    Route::post('/scholarships/store', [ScholarshipManagementController::class, 'store'])->name('scholarships.store');
    Route::post('/scholarships/{id}/update', [ScholarshipManagementController::class, 'update'])->name('scholarships.update');
    Route::get('/scholarships', [ScholarshipManagementController::class, 'sfaoIndex'])->name('scholarships.index');
    Route::get('/scholarships/{id}', [ScholarshipManagementController::class, 'sfaoShow'])->name('scholarships.show');
    
    // Reports Management
    Route::get('/reports/create', [ReportController::class, 'createReport'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'storeReport'])->name('reports.store');
    Route::get('/reports/{id}', [ReportController::class, 'showReport'])->name('reports.show');
    Route::get('/reports/{id}/edit', [ReportController::class, 'editReport'])->name('reports.edit');
    Route::put('/reports/{id}', [ReportController::class, 'updateReport'])->name('reports.update');
    Route::post('/reports/{id}/submit', [ReportController::class, 'submitReport'])->name('reports.submit');
    Route::delete('/reports/{id}', [ReportController::class, 'deleteReport'])->name('reports.delete');
    Route::post('/reports/generate-data', [ReportController::class, 'generateReportData'])->name('reports.generate-data');
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
        
        // Analytics
        Route::post('/analytics/filtered', [ApplicationManagementController::class, 'getFilteredAnalytics'])->name('analytics.filtered');

        // Scholarships
        Route::prefix('scholarships')->name('scholarships.')->group(function () {
            Route::get('/create', [ScholarshipManagementController::class, 'create'])->name('create');
            Route::post('/store', [ScholarshipManagementController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [ScholarshipManagementController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ScholarshipManagementController::class, 'update'])->name('update');
            Route::delete('/{id}', [ScholarshipManagementController::class, 'destroy'])->name('destroy');
        });

        
        // Application Management
        Route::post('/applications/{id}/approve', [CentralApplicationController::class, 'approve'])->name('applications.approve');
        Route::post('/applications/{id}/reject', [CentralApplicationController::class, 'reject'])->name('applications.reject');
        Route::post('/applications/{id}/claim', [CentralApplicationController::class, 'claimGrant'])->name('applications.claim');
        
        // Staff Management
        Route::post('/staff/invite', [UserManagementController::class, 'inviteStaff'])->name('staff.invite');
        Route::post('/staff/{id}/deactivate', [UserManagementController::class, 'deactivateStaff'])->name('staff.deactivate');
        
        // Reports Management
        Route::get('/reports/{id}', [ReportController::class, 'centralShowReport'])->name('reports.show');
        Route::post('/reports/{id}/review', [ReportController::class, 'reviewReport'])->name('reports.review');
        
        // Scholars Management
        Route::prefix('scholars')->name('scholars.')->group(function () {
            Route::get('/', [App\Http\Controllers\ScholarController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\ScholarController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\ScholarController::class, 'store'])->name('store');
            Route::get('/{scholar}', [App\Http\Controllers\ScholarController::class, 'show'])->name('show');
            Route::get('/{scholar}/edit', [App\Http\Controllers\ScholarController::class, 'edit'])->name('edit');
            Route::put('/{scholar}', [App\Http\Controllers\ScholarController::class, 'update'])->name('update');
            Route::delete('/{scholar}', [App\Http\Controllers\ScholarController::class, 'destroy'])->name('destroy');
            Route::get('/statistics', [App\Http\Controllers\ScholarController::class, 'statistics'])->name('statistics');
            Route::post('/{scholar}/add-grant', [App\Http\Controllers\ScholarController::class, 'addGrant'])->name('add-grant');
        });

        // Endorsed Applicants Validation
        Route::get('/endorsed-applications/{application}/validate', [CentralApplicationController::class, 'showEndorsedValidation'])->name('endorsed.validate');
        Route::post('/endorsed-applications/{application}/accept', [CentralApplicationController::class, 'acceptEndorsed'])->name('endorsed.accept');
        Route::post('/endorsed-applications/{application}/reject', [CentralApplicationController::class, 'rejectEndorsed'])->name('endorsed.reject');
        
        // Rejected Applicants
        Route::get('/rejected-applicants', [CentralApplicationController::class, 'viewRejectedApplicants'])->name('rejected-applicants');
    });


// --------------------------------------------------
// SFAO PASSWORD SETUP ROUTES
// --------------------------------------------------

// SFAO password setup after email verification
Route::get('/sfao/password-setup', [UserManagementController::class, 'showSFAOPasswordSetup'])->name('sfao.password.setup');
Route::post('/sfao/password-setup', [UserManagementController::class, 'setupSFAOPassword'])->name('sfao.password.setup');

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