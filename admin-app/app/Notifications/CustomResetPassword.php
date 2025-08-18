<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;

class CustomResetPassword extends BaseResetPassword
{
    /**
     * Costruttore per ricevere il token.
     *
     * @param  string  $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        // Costruisci l'URL per il reset della password
        $resetUrl = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset()
        ]);

        return (new MailMessage)
            ->subject('Richiesta di reset della password')
            ->greeting('Ciao ' . $notifiable->name . ',')
            ->line('Hai ricevuto questa email perché abbiamo ricevuto una richiesta di reset della password per il tuo account.')
            ->action('Resetta la Password', $resetUrl)
            ->line('Se non hai richiesto il reset della password, ignora questa email.')
            ->salutation('Cordiali saluti, ' . config('app.name'));
    }
}