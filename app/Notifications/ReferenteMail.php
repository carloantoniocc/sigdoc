<?php

namespace sigdoc\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Clase Notification para Envio de Mails al generar Asignación de Documentos Tributarios a Referente Técnico
 */
class ReferenteMail extends Notification
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
            ->subject('Documento para validar')
			->greeting('Buenos Días')
			->line('Usted posee documento por validar en su bandeja de entrada')
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
