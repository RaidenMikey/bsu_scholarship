# Roles and Functions Documentation

This document lists all the functions available to each role in the BSU Scholarship System, based on the application's routing and controller logic.

## 1. Student Role
The Student role is designed for applicants to browse, apply, and track scholarships.

| Feature Area | Functionality | Route / Action |
| :--- | :--- | :--- |
| **Authentication** | Login | `POST /login` |
| | Register | `POST /register` |
| | Forgot Password | `POST /password/email` |
| | Reset Password | `POST /password/reset` |
| | Verify Email | `GET /email/verify` |
| | Change Password | `POST /student/change-password` |
| **Dashboard** | View Dashboard | `GET /student` |
| **Scholarships** | Browse Scholarships | `GET /student/scholarships` |
| **Applications** | View Application Forms (SFAO) | `GET /student/sfao-form` |
| | View Application Forms (TDP) | `GET /student/tdp-form` |
| | View Specific Scholarship Form | `GET /student/form/{scholarship_id}` |
| | Submit Application | `POST /student/submit-application` |
| | View Application History | `GET /student/applications` |
| | Apply for Scholarship | `POST /student/apply` |
| | Withdraw Application | `POST /student/unapply` |
| | Multi-stage Application View | `GET /student/apply/{scholarship_id}` |
| | Submit SFAO Documents (Stage 1) | `POST /student/apply/{scholarship_id}/sfao-documents` |
| | Submit Scholarship Documents (Stage 2) | `POST /student/apply/{scholarship_id}/scholarship-documents` |
| | Final Submission (Stage 3) | `POST /student/apply/{scholarship_id}/final-submission` |
| | Track Progress | `GET /student/apply/{scholarship_id}/progress` |
| **Documents** | Upload Profile Picture | `POST /upload-profile-picture/student` |
| | View Uploaded Document | `GET /document/view/{id}` |
| | Print Application Form | `GET /student/print-application` |
| | Download File | `GET /student/download-file` |

## 2. SFAO (Scholarship and Financial Assistance Office) Role
The SFAO role handles the initial processing, evaluation, and management of applications and scholarships at the campus level.

| Feature Area | Functionality | Route / Action |
| :--- | :--- | :--- |
| **Authentication** | Login | `POST /login` |
| | Password Setup (Post-Verification) | `POST /sfao/password-setup` |
| | Change Password | `POST /sfao/change-password` |
| **Dashboard** | View Dashboard | `GET /sfao` |
| **Application Processing** | View Applicant Documents | `GET /sfao/applicants/{user_id}/documents` |
| | Approve Application | `POST /sfao/applications/{id}/approve` |
| | Reject Application | `POST /sfao/applications/{id}/reject` |
| | Claim Grant for Applicant | `POST /sfao/applications/{id}/claim` |
| **Evaluation System** | View Evaluation Interface | `GET /sfao/evaluation/{user_id}` |
| | Evaluate SFAO Documents | `POST /sfao/evaluation/{user_id}/scholarship/{scholarship_id}/sfao-documents/evaluate` |
| | Evaluate Scholarship Documents | `POST /sfao/evaluation/{user_id}/scholarship/{scholarship_id}/scholarship-documents/evaluate` |
| | Final Evaluation | `POST /sfao/evaluation/{user_id}/scholarship/{scholarship_id}/final/submit` |
| **Scholarship Management** | List Scholarships | `GET /sfao/scholarships` |
| | View Scholarship Details | `GET /sfao/scholarships/{id}` |
| | Create Scholarship | `POST /sfao/scholarships/store` |
| | Update Scholarship | `POST /sfao/scholarships/{id}/update` |
| **Reports** | Create Report | `GET /sfao/reports/create` |
| | Save Report | `POST /sfao/reports` |
| | Edit Report | `GET /sfao/reports/{id}/edit` |
| | Update Report | `PUT /sfao/reports/{id}` |
| | Submit Report | `POST /sfao/reports/{id}/submit` |
| | Submit Summary Report | `POST /sfao/reports/summary-submit` |
| | Delete Report | `DELETE /sfao/reports/{id}` |
| | Generate Report Data | `POST /sfao/reports/generate-data` |
| | View Student Summary | `GET /sfao/student-summary` |
| | View Scholar Summary | `GET /sfao/scholar-summary` |
| | View Grant Summary | `GET /sfao/grant-summary` |
| **Documents** | Upload Profile Picture | `POST /upload-profile-picture/sfao` |

## 3. Central Admin Role
The Central Admin role oversees the entire system, managing analytics, global scholarship definitions, staff, and report reviews.

| Feature Area | Functionality | Route / Action |
| :--- | :--- | :--- |
| **Authentication** | Central Login | `POST /central/login` |
| | Update Name | `POST /central/update-name` |
| | Change Password | `POST /central/change-password` |
| **Dashboard** | View Dashboard | `GET /central` |
| **Analytics** | Filtered Analytics | `POST /central/analytics/filtered` |
| **Scholarship Management** | List Scholarships | `(See SFAO/Public Listings)` |
| | Create Scholarship | `GET /central/scholarships/create` |
| | Store Scholarship | `POST /central/scholarships/store` |
| | Edit Scholarship | `GET /central/scholarships/{id}/edit` |
| | Update Scholarship | `PUT /central/scholarships/{id}` |
| | Delete Scholarship | `DELETE /central/scholarships/{id}` |
| **Application Management** | Approve Application | `POST /central/applications/{id}/approve` |
| | Reject Application | `POST /central/applications/{id}/reject` |
| | Claim Grant | `POST /central/applications/{id}/claim` |
| | Validate Endorsed Application | `GET /central/endorsed-applications/{application}/validate` |
| | Accept Endorsed Application | `POST /central/endorsed-applications/{application}/accept` |
| | Reject Endorsed Application | `POST /central/endorsed-applications/{application}/reject` |
| | View Rejected Applicants | `GET /central/rejected-applicants` |
| **Scholar Management** | List Scholars | `GET /central/scholars` |
| | Create Scholar Manual Entry | `GET /central/scholars/create` |
| | Store Scholar | `POST /central/scholars` |
| | View Scholar Details | `GET /central/scholars/{scholar}` |
| | Edit Scholar | `GET /central/scholars/{scholar}/edit` |
| | Update Scholar | `PUT /central/scholars/{scholar}` |
| | Delete Scholar | `DELETE /central/scholars/{scholar}` |
| | View Scholar Statistics | `GET /central/scholars/statistics` |
| | Add Grant to Scholar | `POST /central/scholars/{scholar}/add-grant` |
| **Staff Management** | Invite Staff | `POST /central/staff/invite` |
| | Deactivate Staff | `POST /central/staff/{id}/deactivate` |
| **Reports** | View Report | `GET /central/reports/{id}` |
| | Review Report | `POST /central/reports/{id}/review` |
| **Documents** | Upload Profile Picture | `POST /upload-profile-picture/central` |

## 4. Shared Functionalities
- **Notifications**: Mark as read, mark all as read, destroy (`/notifications/*`).
- **Document Viewing**: View uploaded DOCX/PDF files (`/document/view/{id}`).
