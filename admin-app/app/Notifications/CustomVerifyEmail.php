<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmail extends BaseVerifyEmail
{
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
                    ->subject('Metrics | Verifica il tuo indirizzo email')
                    ->greeting('Ciao!')
                    ->line('Grazie Per Esserti Registrato! Per favore clicca sul pulsante qui sotto per verificare il tuo indirizzo email.')
                    ->action('Verifica Email', $verificationUrl)
                    ->line('Se non sei stato tu a creare un account su Metrics, ignora questa email.')
                    ->salutation('Cordiali saluti, Il Team di ' . config('app.name'));
    }
}