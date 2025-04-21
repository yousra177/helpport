<?php

namespace App\Notifications;

use App\Models\Solutions;
use App\Models\Problems;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SolutionDeletedNotification extends Notification
{
    use Queueable;

    public $solution;
    public $problem;
    public $reason;

    public function __construct(Solutions $solution, Problems $problem, $reason)
{
    $this->solution = $solution;
    $this->problem = $problem;
    $this->reason = $reason;
}


    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
{
    return (new MailMessage)
        ->subject('Your Solution Has Been Deleted')
        ->line('Your solution to the problem "' . $this->problem->title . '" has been deleted by an administrator.')
        ->line('Reason: ' . $this->reason)
        ->action('View Problem', url('/problems/' . $this->problem->id))
        ->line('Thank you for using the Knowledge Portal.');
}


public function toArray($notifiable)
{
    return [
        'message' => 'Your solution to "' . $this->problem->title . '" has been deleted. Reason: ' . $this->reason,
        'problem_id' => $this->problem->id,
        'solution_id' => $this->solution->id,
    ];
}

}
