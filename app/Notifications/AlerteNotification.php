<?php

namespace App\Notifications;

use App\Models\Alerte;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlerteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Alerte $alerte) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $niveauLabel = match ($this->alerte->niveau) {
            'critique'  => '[CRITIQUE]',
            'attention' => '[ATTENTION]',
            'info'      => '[INFO]',
            default     => '[' . strtoupper($this->alerte->niveau) . ']',
        };

        $mail = (new MailMessage)
            ->subject("{$niveauLabel} {$this->alerte->titre}")
            ->greeting("Bonjour {$notifiable->prenom} {$notifiable->name},")
            ->line("Une alerte de niveau **{$this->alerte->niveau}** a été générée dans le système TB-PAPA.")
            ->line("**{$this->alerte->titre}**")
            ->line($this->alerte->message);

        if ($this->alerte->papa) {
            $mail->line("PAPA concerné : **{$this->alerte->papa->code}** — {$this->alerte->papa->libelle}");
        }

        return $mail
            ->action("Voir l'alerte", route('alertes.show', $this->alerte))
            ->line('Merci de traiter cette alerte dans les meilleurs délais.')
            ->salutation('Commission de la CEEAC — Système TB-PAPA');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'alerte_id' => $this->alerte->id,
            'type'      => $this->alerte->type_alerte,
            'niveau'    => $this->alerte->niveau,
            'titre'     => $this->alerte->titre,
            'message'   => $this->alerte->message,
        ];
    }
}
