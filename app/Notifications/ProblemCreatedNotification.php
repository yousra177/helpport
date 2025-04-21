<?php

namespace App\Notifications;

use App\Models\Problems;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProblemCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Problems $problem;
    protected string $action;

    /**
     * Create a new notification instance.
     *
     * @param Problems $problem
     * @param string $action ('created' or 'approved')
     */
    public function __construct(Problems $problem, string $action = 'created')
    {
        $this->problem = $problem;
        $this->action = $action;
    }

    /**
     * Get the notification delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $subject = $this->action === 'approved' ? 'A Problem Has Been Approved' : 'New Problem Submitted';
        $line = $this->action === 'approved'
            ? 'A problem in your department has just been approved.'
            : 'A new problem was submitted in your department.';

        return (new MailMessage)
            ->subject($subject)
            ->line($line)
            ->action('View Problem', url('/problems/' . $this->problem->id));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'problem_id' => $this->problem->id,
            'title' => $this->problem->title,
            'user' => $this->problem->user?->name ?? 'Unknown',
            'action' => $this->action,
        ];
    }
}
