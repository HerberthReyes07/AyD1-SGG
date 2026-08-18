<?php

namespace App\Notifications;

use App\Models\MemberMembership;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the member on the day their membership expires (end_date = today).
 * Triggered by the daily ProcessMembershipTasks scheduler command.
 */
class MembershipExpiredNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly MemberMembership $membership,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $expirationDate = $this->membership->end_date->format('d/m/Y');
        $planName       = $this->membership->plan?->name ?? 'tu plan actual';

        return (new MailMessage)
            ->subject('Tu membresía ha vencido')
            ->greeting('Hola ' . $notifiable->first_name . ',')
            ->line("Tu membresía ({$planName}) venció el {$expirationDate}.")
            ->line('Para continuar accediendo a los beneficios del gimnasio, por favor renueva tu membresía.')
            ->line('Si ya realizaste un pago de renovación, puedes ignorar este mensaje.');
    }
}
