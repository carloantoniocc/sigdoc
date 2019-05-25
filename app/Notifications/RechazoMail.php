<?php

namespace sigdoc\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Clase Notification para Envio de Mails en el caso de rechazo de Documentos Tributarios
 */
class RechazoMail extends Notification
{
    use Queueable;
	public $document;
	public $proveedor;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($proveedor, $document)
    {
         $this->document = $document;
		 $this->proveedor = $proveedor;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Documento Rechazado')
			->greeting('Buenos Días')
			->line('Se ha realizado el rechazo del siguiente documento:')
			->line('Proveedor: '.$this->proveedor)
			->line('Número de Documento: '.$this->document)
            ->action('Acceder a SIGDOC', url(config('app.url')))
			->salutation('Saludos Cordiales,');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
