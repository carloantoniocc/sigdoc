<?php

namespace sigdoc\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

class ResetPassword extends ResetPasswordNotification
{
    public function toMail($notifiable)
    {
		return (new MailMessage)
            ->subject('Recuperar Contraseña')
			->greeting('Buenos Días')
			->line('Usted ha recibido este correo electrónico para el cambio de su contraseña en SIGDOC.')
            ->action('Cambiar Contraseña', url(config('app.url').route('password.reset', $this->token, false)))
            ->line('Si usted no ha solicitado el cambio de contraseña, por favor omita este mensaje.')
			->salutation('Saludos Cordiales,');
    }
}
