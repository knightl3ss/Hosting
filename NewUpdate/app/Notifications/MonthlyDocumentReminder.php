<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class MonthlyDocumentReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Reminder: Please submit your required document for this month. Submission is needed every first day of the month.',
        ];
    }
}