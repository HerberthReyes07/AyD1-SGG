<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorCodeNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $code,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu código de verificación')
            ->greeting('Hola ' . $notifiable->first_name . ',')
            ->line('Tu código de verificación es:')
            ->line('## ' . $this->code)
            ->line('Este código vence en 10 minutos.')
            ->line('Si no intentaste iniciar sesión, puedes ignorar este mensaje.');
    }
}
