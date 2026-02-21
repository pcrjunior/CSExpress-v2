<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url('/reset-password/' . $this->token . '?email=' . urlencode($notifiable->email));

        return (new MailMessage)
        ->subject('🔐 Redefinição de Senha - Sistema de Gerenciamento de OS')
        ->view('emails.password-reset', [
            'user' => $notifiable,
            'actionUrl' => $url
        ]);
    }
}

