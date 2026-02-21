<?php

namespace App\Http\Controllers;

use App\Models\OrdemServico;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use App\Models\Entregador;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotaServicoMail;
use App\Models\OrdemServicoHistorico;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FinanceiroController extends Controller
{
    // Dashboard Financeiro
    public function index()
    {
        $saldoLiberado = 
        ContaReceber::whereHas('ordemServico', function ($query) {
            $query->where('status', 'concluido');
        })->sum('valor_total') -
        OrdemServico::where('status', 'concluido')
            ->sum(DB::raw('COALESCE(valor_repassado_motorista, 0) + COALESCE(valor_repassado_ajudantes, 0)'));

        $saldoFuturo = ContaReceber::whereHas('ordemServico', function ($query) {
            $query->whereIn('status', ['pendente', 'em_andamento']);
        })->sum('valor_total')
        -
        OrdemServico::whereIn('status', ['pendente', 'em_andamento'])
            ->sum(DB::raw('COALESCE(valor_repassado_motorista, 0) + COALESCE(valor_repassado_ajudantes, 0)'));


        $totalReceberLiberado = ContaReceber::whereHas('ordemServico', function ($query) {
            $query->where('status', 'concluido');
        })->sum('valor_total');

        $totalPagarLiberado = ContaPagar::whereHas('ordemServico', function ($query) {
            $query->where('status', 'concluido');
        })->sum('valor_total');

        $recebimentosPendentes = ContaReceber::whereHas('ordemServico', function ($query) {
            $query->where('status', 'concluido');
        })->where('status_pagamento', 'pendente')->sum('valor_total');

        $pagamentosPendentes = ContaPagar::whereHas('ordemServico', function ($query) {
            $query->where('status', 'concluido');
        })->where('status_pagamento', 'pendente')->sum('valor_total');

        return view('financeiro.index', compact(
            'saldoLiberado',
            'saldoFuturo',
            'totalReceberLiberado',
            'totalPagarLiberado',
            'recebimentosPendentes',
            'pagamentosPendentes'
        ));
    }

    // Contas a Pagar
    public function contasPagar()
    {
        $contasPagar = ContaPagar::with(['ordemServico', 'entregador'])
            ->whereHas('ordemServico', function ($query) {
                $query->where('status', 'concluido');
            })


            ->orderByRaw("CASE WHEN tipo = 'motorista' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('financeiro.contas_pagar.index', compact('contasPagar'));
    }

    // Filtrar Contas a Pagar
    public function filtrarContasPagar(Request $request)
    {
        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');
        $status = $request->input('status');
        $entregadorId = $request->input('entregador_id');

        $query = ContaPagar::with(['ordemServico', 'entregador'])
            ->whereHas('ordemServico', function ($query) {
                $query->where('status', 'concluido');
            });

        if ($dataInicio && $dataFim) {
            $query->whereBetween('created_at', [$dataInicio, $dataFim]);
        }

        if ($status) {
            $query->where('status_pagamento', $status);
        }

        if ($entregadorId) {
            $query->where('entregador_id', $entregadorId);
        }

        $contasPagar = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('financeiro.contas_pagar.index', compact('contasPagar'));
    }


    // Selecionar OS para pagamento
    public function selecionarOSPagamento(Request $request)
    {
        $query = OrdemServico::where('status', 'concluido')
            ->whereDoesntHave('contasPagar');

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('data_servico', [$request->data_inicio, $request->data_fim]);
        }

        if ($request->filled('cliente_id')) {
            $query->where(function($q) use ($request) {
                $q->where('cliente_origem_id', $request->cliente_id)
                  ->orWhere('cliente_destino_id', $request->cliente_id);
            });
        }

        $ordensServico = $query->orderBy('data_servico', 'desc')->paginate(15);

        return view('financeiro.contas_pagar.selecionar_os', compact('ordensServico'));
    }

    // Criar pagamento baseado nas OS selecionadas
    public function gerarPagamentos(Request $request)
    {
        $ordensServicoIds = $request->input('ordens_servico');

        if (empty($ordensServicoIds)) {
            return redirect()->back()->with('error', 'Nenhuma OS selecionada');
        }

        DB::beginTransaction();
        try {
            foreach ($ordensServicoIds as $osId) {
                $os = OrdemServico::findOrFail($osId);

                // Pagamento motorista
                if ($os->motorista_id) {
                    ContaPagar::create([
                        'ordem_servico_id' => $os->id,
                        'entregador_id' => $os->motorista_id,
                        'tipo' => 'motorista',
                        'valor_total' => $os->valor_motorista,
                        'data_vencimento' => Carbon::now()->addDays(15),
                        'status_pagamento' => 'pendente',
                        'observacao' => 'Pagamento para motorista da OS #' . $os->id
                    ]);
                }

                // Pagamento ajudantes
                foreach ($os->ajudantes as $ajudante) {
                    ContaPagar::create([
                        'ordem_servico_id' => $os->id,
                        'entregador_id' => $ajudante->id,
                        'tipo' => 'ajudante',
                        'valor_total' => $ajudante->pivot->valor,
                        'data_vencimento' => Carbon::now()->addDays(15),
                        'status_pagamento' => 'pendente',
                        'observacao' => 'Pagamento para ajudante da OS #' . $os->id
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('financeiro.contas-pagar')->with('success', 'Pagamentos gerados com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro ao gerar pagamentos: ' . $e->getMessage());
        }
    }

    public function realizarPagamento(Request $request, $id)
    {
        $contaPagar = ContaPagar::findOrFail($id);

        if ($contaPagar->status_pagamento == 'pago') {
            return redirect()->back()->with('error', 'Esta conta já foi paga');
        }

        // Pagar motorista
        $contaPagar->status_pagamento = 'pago';
        $contaPagar->data_pagamento = now();
        $contaPagar->save();

        // Pagar ajudantes se solicitado
        if ($contaPagar->tipo === 'motorista' && $request->input('pagar_ajudantes') === 'sim') {
            $ajudantes = $contaPagar->ordemServico->ajudantes;
            foreach ($ajudantes as $ajudante) {
                $jaPago = ContaPagar::where('ordem_servico_id', $contaPagar->ordem_servico_id)
                    ->where('entregador_id', $ajudante->id)
                    ->where('tipo', 'ajudante')
                    ->where('status_pagamento', 'pago')
                    ->exists();

                if (!$jaPago) {
                    ContaPagar::where('ordem_servico_id', $contaPagar->ordem_servico_id)
                        ->where('entregador_id', $ajudante->id)
                        ->where('tipo', 'ajudante')
                        ->update([
                            'status_pagamento' => 'pago',
                            'data_pagamento' => now()
                        ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Pagamento realizado com sucesso!');
    }

    public function contasReceber()
    {
        // Filtra apenas contas que possuem ordem de serviço com clientes vinculados
        $contasReceber = ContaReceber::whereHas('ordemServico', function ($query) {
            $query->whereNotNull('cliente_origem_id')
                  ->whereNotNull('cliente_destino_id');
        })
        ->with([
            'ordemServico',
            'ordemServico.clienteOrigem',
            'ordemServico.clienteDestino'
        ])
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return view('financeiro.contas_receber.index', compact('contasReceber'));
    }

    public function filtrarContasReceber(Request $request)
    {
        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');
        $status = $request->input('status');
        $clienteId = $request->input('cliente_id');

        // Aqui está a linha que você deve substituir
        $query = ContaReceber::with([
            'ordemServico',
            'ordemServico.clienteOrigem',
            'ordemServico.clienteDestino'
        ]);

        // O resto do seu código permanece igual
        if ($dataInicio && $dataFim) {
            $query->whereHas('ordemServico', function ($q) use ($dataInicio, $dataFim) {
                $q->whereBetween('data_servico', [$dataInicio, $dataFim]);
            });
        }

        if ($status) {
            $query->where('status_pagamento', $status);
        }

        if ($clienteId) {
            $query->whereHas('ordemServico', function ($q) use ($clienteId) {
                $q->where(function($subq) use ($clienteId) {
                    $subq->where(function ($qq) use ($clienteId) {
                        $qq->where('contratante_tipo', 'origem')
                        ->where('cliente_origem_id', $clienteId);
                    })->orWhere(function ($qq) use ($clienteId) {
                        $qq->where('contratante_tipo', 'destino')
                        ->where('cliente_destino_id', $clienteId);
                    });
                });
            });
        }

        $contasReceber = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('financeiro.contas_receber.index', compact('contasReceber'));
    }

    // Selecionar OS para recebimento
    public function selecionarOSRecebimento(Request $request)
    {
        $query = OrdemServico::where('status', 'concluido')
            ->whereDoesntHave('contasReceber');

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('data_servico', [$request->data_inicio, $request->data_fim]);
        }

        if ($request->filled('cliente_id')) {
            $query->where(function($q) use ($request) {
                $q->where('cliente_origem_id', $request->cliente_id)
                  ->orWhere('cliente_destino_id', $request->cliente_id);
            });
        }

        $ordensServico = $query->orderBy('data_servico', 'desc')->paginate(15);

        return view('financeiro.contas_receber.selecionar_os', compact('ordensServico'));
    }

    // Gerar contas a receber baseadas nas OS
    public function gerarRecebimentos(Request $request)
    {
        $ordensServicoIds = $request->input('ordens_servico');

        if (empty($ordensServicoIds)) {
            return redirect()->back()->with('error', 'Nenhuma OS selecionada');
        }

        DB::beginTransaction();
        try {
            foreach ($ordensServicoIds as $osId) {
                $os = OrdemServico::findOrFail($osId);

                // Verificar se já existe conta a receber para esta OS
                $existente = ContaReceber::where('ordem_servico_id', $os->id)->first();
                if ($existente) {
                    continue; // Pula esta OS se já existir conta a receber
                }

                ContaReceber::create([
                    'ordem_servico_id' => $os->id,
                    'valor_total' => $os->valor_total,
                    'data_vencimento' => Carbon::now()->addDays(30),
                    'status_pagamento' => 'pendente',
                    'observacao' => 'Faturamento da OS #' . $os->id
                ]);
            }

            DB::commit();
            return redirect()->route('financeiro.contas-receber')->with('success', 'Recebimentos gerados com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro ao gerar recebimentos: ' . $e->getMessage());
        }
    }

    // Registrar recebimento
    public function registrarRecebimento($id)
    {
        $contaReceber = ContaReceber::findOrFail($id);

        if ($contaReceber->status_pagamento == 'recebido') {
            return redirect()->back()->with('error', 'Esta conta já foi recebida');
        }

        $contaReceber->status_pagamento = 'recebido';
        $contaReceber->data_recebimento = Carbon::now();
        $contaReceber->save();

        OrdemServicoHistorico::create([
            'ordem_servico_id' => $contaReceber->ordem_servico_id,
            'user_id' => Auth::id(),
            'status_anterior' => 'concluido',
            'status_novo' => 'concluido',
            'observacao' => 'Nota de serviço recebida',
        ]);

        return redirect()->back()->with('success', 'Recebimento registrado com sucesso!');
    }

    // Relatório Financeiro
    public function relatorio()
    {
        return view('financeiro.relatorio.index');
    }

    // Gerar relatório financeiro
    public function gerarRelatorio(Request $request)
    {
        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');

        if (!$dataInicio || !$dataFim) {
            return redirect()->back()->with('error', 'Período inválido');
        }

        // Dados de contas a receber no período
        $recebimentos = ContaReceber::whereHas('ordemServico', function ($query) use ($dataInicio, $dataFim) {
            $query->whereBetween('data_servico', [$dataInicio, $dataFim]);
        })->get();

        // Dados de contas a pagar no período
        $pagamentos = ContaPagar::whereHas('ordemServico', function ($query) use ($dataInicio, $dataFim) {
            $query->whereBetween('data_servico', [$dataInicio, $dataFim]);
        })->get();

        // Calcular totais
        $totalRecebido = $recebimentos->where('status_pagamento', 'recebido')->sum('valor_total');
        $totalPendentesReceber = $recebimentos->where('status_pagamento', 'pendente')->sum('valor_total');
        $totalPago = $pagamentos->where('status_pagamento', 'pago')->sum('valor_total');
        $totalPendentesPagar = $pagamentos->where('status_pagamento', 'pendente')->sum('valor_total');

        $lucroRealizado = $totalRecebido - $totalPago;
        $lucroPrevisto = ($totalRecebido + $totalPendentesReceber) - ($totalPago + $totalPendentesPagar);

        // Dados de OS no período
        $osCount = OrdemServico::whereBetween('data_servico', [$dataInicio, $dataFim])->count();
        $osConcluidas = OrdemServico::where('status', 'concluido')
            ->whereBetween('data_servico', [$dataInicio, $dataFim])->count();

        return view('financeiro.relatorio.resultado', compact(
            'dataInicio',
            'dataFim',
            'totalRecebido',
            'totalPendentesReceber',
            'totalPago',
            'totalPendentesPagar',
            'lucroRealizado',
            'lucroPrevisto',
            'osCount',
            'osConcluidas'
        ));
    }

    public function visualizarNotaServico(ContaReceber $contaReceber)
    {
        // Força o carregamento dos relacionamentos necessários
        $contaReceber->load([
            'ordemServico.clienteOrigem',
            'ordemServico.clienteDestino',
            'ordemServico.motorista',
            'ordemServico.veiculo',
            'ordemServico.ajudantes'
        ]);

        return view('financeiro.contas_receber.nota_servico', compact('contaReceber'));

    }

    public function enviarNotaServico(ContaReceber $contaReceber)
    {
        $destinatario = $contaReceber->ordemServico->clienteOrigem->email ?? null;

        // Carrega os relacionamentos necessários
        $contaReceber->load([
            'ordemServico.empresa',
            'ordemServico.motorista',
            'ordemServico.veiculo',
            'ordemServico.clienteOrigem',
            'ordemServico.clienteDestino',
        ]);

        // Agora sim garantimos que a variável existe
        $ordemServico = $contaReceber->ordemServico;

        if (!$destinatario) {
            return back()->with('error', 'E-mail do cliente de origem não encontrado.');
        }

        try {
            // Corrige: passa explicitamente as duas variáveis
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.nota-servico', [
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

            return back()->with('success', 'Nota de serviço enviada com sucesso para ' . $destinatario);

        } catch (\Exception $e) {
            Log::error('Erro ao enviar nota de serviço: ' . $e->getMessage());

            return back()->with('error', 'Erro ao enviar a Nota de Serviço. Por favor, tente novamente mais tarde.');
        }
    }




}
