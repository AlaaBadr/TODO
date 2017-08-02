<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\FollowContainer;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TaskFollowedMail extends Notification
{
    use Queueable;

    public $f;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(FollowContainer $f)
    {
        $this->f = $f;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line($this->f->senderId.' followed your task: '.$this->f->taskId)
                    ->action('Followed Task', url('http://localhost:8000/api/tasks/task/'.$this->f->taskId))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
