<?php

namespace App\Services;

use App\Models\ContaReceber;
use App\Models\OrdemServicoHistorico;
use App\Mail\NotaServicoMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class NotaServicoService
{
    public function enviarNotaServico(ContaReceber $contaReceber): bool
    {
        $destinatario = $contaReceber->ordemServico->clienteOrigem->email ?? null;

        if (!$destinatario) {
            Log::warning('E-mail do cliente não encontrado para OS #' . $contaReceber->ordem_servico_id);
            return false;
        }

        try {
            $contaReceber->load([
                'ordemServico.empresa',
                'ordemServico.motorista',
                'ordemServico.veiculo',
                'ordemServico.clienteOrigem',
                'ordemServico.clienteDestino',
            ]);

            $ordemServico = $contaReceber->ordemServico;

            $pdf = Pdf::loadView('pdf.nota-servico', [
                'contaReceber' => $contaReceber,
                'ordemServico' => $ordemServico,
            ]);

            Mail::to($destinatario)->send(new NotaServicoMail($contaReceber, $pdf));

            OrdemServicoHistorico::create([
                'ordem_servico_id' => $contaReceber->ordem_servico_id,
                'user_id' => Auth::id(),
                'status_anterior' => 'concluido',
                'status_novo' => 'concluido',
                'observacao' => 'Nota de Serviço enviada para ' . $destinatario,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao enviar nota de serviço: ' . $e->getMessage());
            return false;
        }
    }
}
