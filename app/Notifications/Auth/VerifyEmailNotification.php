<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;

class VerifyEmailNotification extends VerifyEmailBase implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $verificationUrl = $this->getVerificationUrl($notifiable);

        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
        }

        return (new MailMessage)
            ->subject('Verify email address')
            ->line('Please click the button below to verify your email address on the NewNews project website.')
            ->line('Attention: the link will only be valid for one hour.')
            ->action('Verify email address', $verificationUrl)
            ->line('If you did not create an account, no further action is required.')
            ->line('Future looks bright.');
    }

    /**
     * Get the verification URL for the given notification.
     *
     * User's account who clicked this link will be marked as confirmed.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function getVerificationUrl($notifiable)
    {
        $route = 'auth.verification.verify';
        $expires = Carbon::now()->addMinutes(config('auth.verification.expire'));
        $data = [
            'id' => $notifiable->getKey()
        ];

        $url = URL::temporarySignedRoute($route, $expires, $data);

        return $url;
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
