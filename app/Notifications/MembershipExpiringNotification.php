<?php

namespace App\Notifications;

use App\Models\MemberMembership;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the member exactly 5 days before their membership expires.
 * Triggered by the daily ProcessMembershipTasks scheduler command.
 */
class MembershipExpiringNotification extends Notification
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
            ->subject('Tu membresía vence en 5 días')
            ->greeting('Hola ' . $notifiable->first_name . ',')
            ->line("Tu membresía ({$planName}) vencerá el {$expirationDate}.")
            ->line('Recuerda renovarla para continuar disfrutando de los beneficios del gimnasio.')
            ->line('Si ya realizaste un pago de renovación, puedes ignorar este mensaje.');
    }
}
