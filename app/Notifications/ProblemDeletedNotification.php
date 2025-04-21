<?php

namespace App\Notifications;

use App\Models\Problems;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProblemDeletedNotification extends Notification
{
    use Queueable;

    public $problem;
    public $reason;

    public function __construct(Problems $problem, $reason)
    {
        $this->problem = $problem;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // Send email and store in database
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Problem Has Been Deleted')
            ->line('Your reported problem "' . $this->problem->title . '" has been deleted by an administrator.')
            ->line('Reason: ' . $this->reason)
            ->line('If you believe this was a mistake, please contact support.')
            ->action('View Portal', url('/'))
            ->line('Thank you for using the Knowledge Portal.');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Your problem "' . $this->problem->title . '" has been deleted.',
            'reason' => $this->reason, // Corrected variable name
            'problem_id' => $this->problem->id,
        ];
    }
}
