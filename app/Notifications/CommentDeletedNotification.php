<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\comments;
use App\Models\Problem;
use App\Models\Problems;
use App\Models\Solution;
use App\Models\Solutions;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CommentDeletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $comment;
    public $problem;
    public $solution;
    public $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(comments $comment, Problems $problem, Solutions $solution, $reason)
    {
        $this->comment = $comment;
        $this->problem = $problem;
        $this->solution = $solution;
        $this->reason = $reason;
    }

    /**
     * Get the notification delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail', 'database']; // Send email and store in database
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Comment Has Been Deleted')
            ->line('Your comment on the solution for problem "' . $this->problem->title . '" has been deleted by an administrator.')
            ->line('Reason: ' . $this->reason)
            ->line('If you believe this was a mistake, please contact support.')
            ->action('View Problem', route('problems.show', $this->problem))
            ->line('Thank you for using the Knowledge Portal.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'message' => 'Your comment on the solution for problem "' . $this->problem->title . '" has been deleted.',
            'reason' => $this->reason,
            'comment_id' => $this->comment->id,
            'problem_id' => $this->problem->id,
            'solution_id' => $this->solution->id,
            'link' => route('problems.show', $this->problem),
        ];
    }
}
