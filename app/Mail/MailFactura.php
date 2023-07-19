<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\HtmlString;
use Illuminate\Mail\Mailables\Envelope;


class MailFactura extends Mailable
{
    use Queueable, SerializesModels;

    protected $fromEmail;
    protected $document;

    /**
     * Create a new message instance.
     *
     * @return void
     */


    public function __construct($inmobiliaria, $cliente, $fromEmail, $document)
    {


        $this->subject('Factura para ' . $cliente);
        $this->fromEmail = $fromEmail;
        if($inmobiliaria == 'sayco'){
            $this->from($fromEmail, 'Inmobiliaria SAYCO');
        }else{
            $this->from($fromEmail, 'Inmobiliaria SANCER');
        }
        $this->document = $document;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->fromEmail)->text(new HtmlString('Here is the plain text content'))->attach($this->document);
    }
}
