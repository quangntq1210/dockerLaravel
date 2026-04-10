<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendNewPasswordNotification extends Notification
{
    use Queueable;
public $newPassword;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
  public function __construct($newPassword)
{
    $this->newPassword = $newPassword;
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
       return (new \Illuminate\Notifications\Messages\MailMessage)
        ->subject(__('auth.password_reset_subject'))
        ->greeting(__('auth.greeting'))
        ->line(__('auth.intro'))
        ->line(__('auth.new_password_line', ['password' => $this->newPassword]))
        ->action(__('auth.action'), url('/login'))
        ->line(__('auth.outro'));
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
