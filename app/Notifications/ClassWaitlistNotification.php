<?php

namespace App\Notifications;

use App\Models\ClassSession;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the next eligible waitlisted member when a spot opens in a class session.
 * Triggered by GroupClassEnrollmentService::promoteNextMember().
 *
 * NOTE: This notification does NOT enroll the member automatically.
 * The member must use the normal enrollment flow to claim the spot.
 */
class ClassWaitlistNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly ClassSession $session,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $className = $this->session->groupClass->name;
        $date      = $this->session->starts_at->format('d/m/Y');
        $time      = $this->session->starts_at->format('H:i');

        return (new MailMessage)
            ->subject('Se liberó un cupo en tu clase de espera')
            ->greeting('Hola ' . $notifiable->first_name . ',')
            ->line("Se liberó un cupo en la clase \"{$className}\" del {$date} a las {$time}.")
            ->line('Este aviso no te inscribe automáticamente. Ingresa a la plataforma e inscríbete antes de que el cupo se ocupe.')
            ->line('Si ya no deseas asistir, puedes ignorar este mensaje.');
    }
}
