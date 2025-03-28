<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Wotz\VerificationCode\Notifications\VerificationCodeCreatedInterface;

class EmailVerification extends Notification implements VerificationCodeCreatedInterface
{
    use Queueable;
    public $code;
    /**
     * Create a new notification instance.
     */
    public function __construct(string $code)
    {
        $this->code = $code;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->from('korkapp.team@gmail.com', 'Kork App Team')
            ->subject('Your verification code')
            ->greeting('Hello!')
            ->line('Your verification code: ' . $this->code)
            ->salutation("Best regards,\n\nKork App");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

}
