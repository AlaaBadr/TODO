<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Invitation;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class InvitationMail extends Notification
{
    use Queueable;

    public $i;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Invitation $i)
    {
        $this->i = $i;
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
                    ->line($this->i->senderId.' invited you to the private task: '.$this->i->taskId)
                    ->action('Invitation', url('http://localhost:8000/api/invitations/invitation/'.$this->i->id))
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
