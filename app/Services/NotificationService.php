<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Scholarship;
use App\Models\Application;

class NotificationService
{
    /**
     * Create notification for new scholarship
     */
    public static function notifyScholarshipCreated(Scholarship $scholarship)
    {
        // Get all students
        $students = User::where('role', 'student')->get();
        
        foreach ($students as $student) {
            Notification::create([
                'user_id' => $student->id,
                'type' => 'scholarship_created',
                'title' => 'New Scholarship Available',
                'message' => "A new scholarship '{$scholarship->scholarship_name}' has been posted. Check it out!",
                'data' => [
                    'scholarship_id' => $scholarship->id,
                    'scholarship_name' => $scholarship->scholarship_name,
                    'deadline' => $scholarship->submission_deadline
                ]
            ]);
        }
    }

    /**
     * Create notification for SFAO comment
     */
    public static function notifySfaoComment(User $student, string $comment, $applicationId = null)
    {
        Notification::create([
            'user_id' => $student->id,
            'type' => 'sfao_comment',
            'title' => 'SFAO Comment on Your Application',
            'message' => $comment,
            'data' => [
                'application_id' => $applicationId,
                'commenter_role' => 'sfao'
            ]
        ]);
    }

    /**
     * Create notification for application status change
     */
    public static function notifyApplicationStatusChange(Application $application, string $status, string $message = null)
    {
        $statusMessages = [
            'approved' => 'Congratulations! Your application has been approved.',
            'rejected' => 'Your application has been reviewed and unfortunately not approved.',
            'pending' => 'Your application is being reviewed.',
            'under_review' => 'Your application is currently under review.'
        ];

        $defaultMessage = $statusMessages[$status] ?? 'Your application status has been updated.';
        $finalMessage = $message ?: $defaultMessage;

        Notification::create([
            'user_id' => $application->user_id,
            'type' => 'application_status',
            'title' => 'Application Status Update',
            'message' => $finalMessage,
            'data' => [
                'application_id' => $application->id,
                'scholarship_id' => $application->scholarship_id,
                'status' => $status,
                'scholarship_name' => $application->scholarship->scholarship_name ?? 'Unknown Scholarship'
            ]
        ]);
    }

    /**
     * Mark notification as read
     */
    public static function markAsRead($notificationId, $userId)
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    /**
     * Mark all notifications as read for a user
     */
    public static function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    /**
     * Get unread count for user
     */
    public static function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }
}
