<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmailBase
{
    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     */
    public function toMail($notifiable): MailMessage
    {
        if (!($notifiable instanceof User)) {
            return (new MailMessage)
                ->subject('Invalid Notification Context')
                ->line('Unable to send verification email. User context missing.');
        }

        /** @var User $notifiable */
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->from('no-reply@e-commerce.test', 'e-commerce Team')
            ->subject('Verify Your Email Address - e-commerce')
            ->greeting('Hello ' . e($notifiable->name) . ',')
            ->line('Thanks for signing up to e-commerce! Please click below to verify your email.')
            ->action('Verify Email', $verificationUrl)
            ->line('This link will expire in 60 minutes.')
            ->line('If you did not create an account, no further action is required.')
            ->salutation('Best regards, e-commerce Team');
    }

    /**
     * Generate the verification URL.
     *
     */
    protected function verificationUrl($notifiable): string
    {
        if (!($notifiable instanceof User)) {
            return '';
        }
        
        $expirationConfig = Config::get('auth.verification.expire', 60);
        $expiration = is_numeric($expirationConfig) ? (int) $expirationConfig : 60;

        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes($expiration),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1((string) $notifiable->getEmailForVerification()),
            ]
        );
    }
}
