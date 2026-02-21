<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotaServicoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contaReceber;
    public $pdf;

    public function __construct($contaReceber, $pdf)
    {
        $this->contaReceber = $contaReceber;
        $this->pdf = $pdf;
    }

    public function build()
    {
        return $this->subject('Nota de Serviço - OS #' . $this->contaReceber->ordemServico->numero_os)
                    ->view('emails.nota-servico')
                    ->attachData($this->pdf->output(), 'nota-servico.pdf');
    }
}
