<?php

namespace App\Notifications;

use App\Enums\TwoFactorChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class TwoFactorCodeNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $code,
        public readonly TwoFactorChannel $channel = TwoFactorChannel::Email,
    ) {}

    public function via(object $notifiable): array
    {
        return match ($this->channel) {
            TwoFactorChannel::Sms => [TwilioChannel::class],
            TwoFactorChannel::Email => ['mail'],
        };
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu código de verificación')
            ->markdown('emails.two-factor-code', [
                'name' => $notifiable->first_name,
                'code' => $this->code,
            ]);
    }

    public function toTwilio(object $notifiable): TwilioSmsMessage
    {
        return (new TwilioSmsMessage)->content(
            'Tu codigo de verificacion de '.config('app.name')." es: {$this->code}. Vence en 10 minutos."
        );
    }
}
