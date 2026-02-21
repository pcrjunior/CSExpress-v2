<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\{ContaPagar, ContaReceber, Cliente, Empresa, Motorista, OrdemServico, Entregador};
use App\Exports\ContasReceberExport;
use App\Exports\ContasPagarExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MotoristasExport;
use App\Exports\OrdemServicoExport;
use App\Exports\EntregadoresExport;
use App\Exports\ClientesAtendidosExport;





class RelatorioController extends Controller
{
    public function ordensServico(Request $request)
    {
        $query = OrdemServico::with(['clienteOrigem', 'clienteDestino', 'motorista', 'empresa']);

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('data_servico', [$request->data_inicio, $request->data_fim]);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->filled('motorista_id')) {
            $query->where('motorista_id', $request->motorista_id);
        }

        if ($request->filled('cliente_id')) {
            $clienteId = $request->cliente_id;
            $somenteContratante = $request->has('somente_contratante');

            if ($somenteContratante) {
                // Se checkbox marcado: busca apenas quando o cliente for contratante
                $query->where(function($q) use ($clienteId) {
                    $q->where(function($sub) use ($clienteId) {
                        // Cliente é origem E é o contratante
                        $sub->where('cliente_origem_id', $clienteId)
                            ->where('contratante_tipo', 'origem');
                    })->orWhere(function($sub) use ($clienteId) {
                        // Cliente é destino E é o contratante
                        $sub->where('cliente_destino_id', $clienteId)
                            ->where('contratante_tipo', 'destino');
                    });
                });
            } else {
                // Se checkbox desmarcado: busca o cliente em origem OU destino (independente de ser contratante)
                $query->where(function($q) use ($clienteId) {
                    $q->where('cliente_origem_id', $clienteId)
                    ->orWhere('cliente_destino_id', $clienteId);
                });
            }
        }

        if ($request->filled('cliente_apelido')) {
            $apelido = $request->cliente_apelido;

            $query->where(function ($q) use ($apelido) {
                $q->whereHas('clienteOrigem', function ($sub) use ($apelido) {
                    $sub->where('apelido', 'like', '%' . $apelido . '%');
                })->orWhereHas('clienteDestino', function ($sub) use ($apelido) {
                    $sub->where('apelido', 'like', '%' . $apelido . '%');
                });
            });
        }

        if ($request->filled('ajudante_id')) {
            $query->whereHas('ajudantes', function ($q) use ($request) {
                $q->where('entregadores.id', $request->ajudante_id);
            });
        }

        $ordens = $query->latest()->paginate(15)->appends($request->query());

        $entregadores = Entregador::orderBy('nome')->get();
        $empresas = Empresa::orderBy('nome')->get();
        $motoristas = Entregador::where('perfil', 'motorista')->orderBy('nome')->get();
        $ajudantes = Entregador::where('perfil', 'ajudante')->orderBy('nome')->get();
        $clientes = Cliente::orderBy('nome')->get();

        return view('relatorios.ordens-servico', compact(
            'ordens',
            'entregadores',
            'empresas',
            'motoristas',
            'ajudantes',
            'clientes'
        ));
    }

    public function relatorioEntregadores(Request $request)
    {
        $query = DB::table('ordem_servico_ajudante as eos')
            ->join('entregadores as e', 'e.id', '=', 'eos.ajudante_id')
            ->join('ordem_servicos as os', 'os.id', '=', 'eos.ordem_servico_id')
            ->whereNull('e.deleted_at')
            ->whereNull('os.deleted_at')
            ->select(
                'e.id',
                'e.nome',
                DB::raw('COUNT(os.id) as total_ordens'),
                DB::raw('SUM(eos.valor) as total_pago'),
                DB::raw('SUM(os.valor_total) as total_os_valor')
            )
            ->groupBy('e.id', 'e.nome')
            ->orderBy('e.nome');

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('os.data_servico', [$request->data_inicio, $request->data_fim]);
        }

        if ($request->filled('entregador_id')) {
            $query->where('e.id', $request->entregador_id);
        }

        // Paginação
        $paginado = $query->paginate(15)->appends($request->query());

        // Cálculo do total pendente (no Laravel Collection)
        $dados = $paginado->getCollection()->map(function ($item) {
            $item->total_pendente = ($item->total_os_valor ?? 0) - ($item->total_pago ?? 0);
            return $item;
        });

        // Atualiza os itens paginados com os valores calculados
        $paginado->setCollection($dados);

        // Carrega apenas ajudantes no filtro
        $entregadores = Entregador::whereRaw("LOWER(perfil) = 'ajudante'")
            ->orderBy('nome')
            ->get();

        return view('relatorios.entregadores', [
            'dados' => $paginado,
            'entregadores' => $entregadores
        ]);
    }

    public function relatorioMotoristas(Request $request)
    {
        // Coleta os dados do relatório
        $dados = collect($this->gerarRelatorioMotoristas($request));

        // Configuração da paginação manual
        $paginaAtual = $request->get('page', 1);
        $porPagina = 15;


        $dadosPaginados = new LengthAwarePaginator(
            $dados->forPage($paginaAtual, $porPagina),
            $dados->count(),
            $porPagina,
            $paginaAtual,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Lista de motoristas para o filtro
        $motoristas = Entregador::whereRaw("LOWER(perfil) = 'motorista'")
            ->orderBy('nome')
            ->get();


        // Retorna a view com dados paginados
        return view('relatorios.motoristas', [
            'dados' => $dadosPaginados,
            'motoristas' => $motoristas
        ]);
    }

    public function relatorioContasPagar(Request $request)
    {
        // Inicia a query apenas com registros ativos
        $query = ContaPagar::query()
            ->with(['ordemServico.clienteOrigem', 'ordemServico.clienteDestino']);

        // Filtro por prazo de vencimento
        if ($request->filled('data_vencimento_inicio') && $request->filled('data_vencimento_fim')) {
            $query->whereBetween('data_vencimento', [
                $request->data_vencimento_inicio,
                $request->data_vencimento_fim
            ]);
        }

        // Filtro por prazo de pagamento
        if ($request->filled('data_pagamento_inicio') && $request->filled('data_pagamento_fim')) {
            $query->whereBetween('data_pagamento', [
                $request->data_pagamento_inicio,
                $request->data_pagamento_fim
            ]);
        }

        // Filtro por cliente (origem ou destino)
        if ($request->filled('cliente_id')) {
            $query->whereHas('ordemServico', function ($q) use ($request) {
                $q->where('cliente_origem_id', $request->cliente_id)
                ->orWhere('cliente_destino_id', $request->cliente_id);
            });
        }

        // Paginação
        $contas = $query->paginate(15)->appends($request->query());

        // Lista de clientes
        $clientes = Cliente::orderBy('nome')->get();

        return view('relatorios.contas-pagar', compact('contas', 'clientes'));
    }

    public function relatorioContasReceber(Request $request)
    {
        $query = ContaReceber::query()
            ->with(['ordemServico' => function ($q) {
                $q->whereNull('deleted_at'); // Garante que a OS não esteja excluída logicamente
            }, 'ordemServico.clienteOrigem', 'ordemServico.clienteDestino']);

        // Filtro por data de vencimento
        if ($request->filled('data_vencimento_de') && $request->filled('data_vencimento_ate')) {
            $query->whereBetween('data_vencimento', [
                $request->data_vencimento_de,
                $request->data_vencimento_ate
            ]);
        }

        // Filtro por data de pagamento
        if ($request->filled('data_recebimento_de') && $request->filled('data_recebimento_ate')) {
            $query->whereBetween('data_pagamento', [
                $request->data_recebimento_de,
                $request->data_recebimento_ate
            ]);
        }

        // Filtro por apelido
        if ($request->filled('apelido')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('clienteOrigem', function ($sub) use ($request) {
                    $sub->where('apelido', 'like', '%' . $request->apelido . '%');
                })
                ->orWhereHas('clienteDestino', function ($sub) use ($request) {
                    $sub->where('apelido', 'like', '%' . $request->apelido . '%');
                });
            });
        }


        // Filtro por cliente
        if ($request->filled('cliente_id')) {
            $query->whereHas('ordemServico', function ($q) use ($request) {
                $q->whereNull('deleted_at') // Garante que a OS relacionada não esteja deletada
                ->where(function ($sub) use ($request) {
                    $sub->where('cliente_origem_id', $request->cliente_id)
                        ->orWhere('cliente_destino_id', $request->cliente_id);
                });
            });
        }

        $contas = $query->orderByDesc('data_vencimento')->paginate(15)->appends($request->query());
        $clientes = Cliente::orderBy('nome')->get();

        return view('relatorios.contas-receber', compact('contas', 'clientes'));
    }

    public function relatorioClientesAtendidos(Request $request)
    {
        $query = OrdemServico::query()
            ->whereIn('contratante_tipo', ['origem', 'destino'])
            ->whereNull('deleted_at');

        // Filtros de datas
        if ($request->filled('data_inicio')) {
            $query->whereDate('data_servico', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_servico', '<=', $request->data_fim);
        }

        // Filtro por cliente específico
        if ($request->filled('cliente_id')) {
            $query->where(function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('contratante_tipo', 'origem')
                    ->where('cliente_origem_id', $request->cliente_id);
                })->orWhere(function ($q2) use ($request) {
                    $q2->where('contratante_tipo', 'destino')
                    ->where('cliente_destino_id', $request->cliente_id);
                });
            });
        }

        $ordens = $query->get();

        // Agrupar por cliente contratante
        $clientesAgrupados = [];

        foreach ($ordens as $os) {
            if ($os->contratante_tipo === 'origem') {
                $cliente = $os->clienteOrigem;
            } elseif ($os->contratante_tipo === 'destino') {
                $cliente = $os->clienteDestino;
            } else {
                continue;
            }

            if ($cliente) {
                $id = $cliente->id;

                if (!isset($clientesAgrupados[$id])) {
                    $clientesAgrupados[$id] = [
                        'nome' => $cliente->nome,
                        'total_os' => 0,
                        'valor_total' => 0,
                        'ultima_os' => null,
                    ];
                }

                $clientesAgrupados[$id]['total_os']++;
                $clientesAgrupados[$id]['valor_total'] += $os->valor_total ?? 0;
                $clientesAgrupados[$id]['ultima_os'] = max(
                    $clientesAgrupados[$id]['ultima_os'] ?? '1900-01-01',
                    $os->data_servico
                );
            }
        }

        // Paginação manual
        $dados = collect($clientesAgrupados)->sortBy('nome');
        $paginaAtual = $request->get('page', 1);
        $porPagina = 15;

        $dadosPaginados = new LengthAwarePaginator(
            $dados->forPage($paginaAtual, $porPagina),
            $dados->count(),
            $porPagina,
            $paginaAtual,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $clientes = Cliente::orderBy('nome')->get();

        return view('relatorios.clientes-atendidos', [
            'dados' => $dadosPaginados,
            'clientes' => $clientes
        ]);
    }

    private function gerarRelatorioMotoristas(Request $request): \Illuminate\Support\Collection
    {
        // Monta a query base com relacionamento do entregador
        $query = ContaPagar::with('entregador')
            ->whereNotNull('entregador_id');

        // Filtros de data (com base na data de criação ou vencimento)
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        // Filtro por entregador específico
        if ($request->filled('entregador_id')) {
            $query->where('entregador_id', $request->entregador_id);
        }

        // Filtro por status do pagamento (opcional)
        if ($request->filled('status_pagamento')) {
            $query->where('status_pagamento', $request->status_pagamento);
        }

        // Busca os dados do banco
        $contas = $query->get();

        // Agrupa por entregador e calcula os totais
        return $contas->groupBy('entregador_id')->map(function ($grupo) {
            $entregador = $grupo->first()->entregador;

            $total_pago = $grupo->where('status_pagamento', 'pago')->sum('valor_total');
            $total_pendente = $grupo->where('status_pagamento', 'pendente')->sum('valor_total');

            return (object) [
                'id' => $entregador->id ?? null,
                'nome' => $entregador->nome ?? 'N/A',
                'total_contas' => $grupo->count(),
                'total_pago' => $total_pago,
                'total_pendente' => $total_pendente,
            ];
        })->values();
    }



    public function exportarEntregadoresPDF(Request $request)
    {
        $query = DB::table('ordem_servico_ajudante as eos')
            ->join('entregadores as e', 'e.id', '=', 'eos.ajudante_id')
            ->join('ordem_servicos as os', 'os.id', '=', 'eos.ordem_servico_id')
            ->leftJoin('contas_pagar as cp', function ($join) {
                $join->on('cp.ordem_servico_id', '=', 'os.id')
                    ->where('cp.status_pagamento', '=', 'pendente');
            })
            ->whereNull('e.deleted_at')
            ->whereNull('os.deleted_at')
            ->select(
                'e.id',
                'e.nome',
                DB::raw('COUNT(os.id) as total_ordens'),
                DB::raw('SUM(eos.valor) as total_pago'),
                DB::raw('SUM(cp.valor_total) as total_pendente')
            )
            ->groupBy('e.id', 'e.nome')
            ->orderBy('e.nome');

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('os.data_servico', [$request->data_inicio, $request->data_fim]);
        }

        if ($request->filled('entregador_id')) {
            $query->where('e.id', $request->entregador_id);
        }

        $dados = $query->get()->map(function ($linha) {
            $linha->total_pago = $linha->total_pago ?? 0;
            $linha->total_pendente = $linha->total_pendente ?? 0;
            return $linha;
        });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.entregadores', [
            'dados' => $dados,
            'filtros' => $request->all(),
            'dataAtual' => now()->format('d/m/Y'),
        ]);

        return $pdf->download('relatorio-entregadores.pdf');
    }

    public function exportarOSPDF(Request $request)
    {
        $query = OrdemServico::with(['clienteOrigem', 'clienteDestino', 'motorista', 'empresa', 'ajudantes']);

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('data_servico', [$request->data_inicio, $request->data_fim]);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('entregador_id')) {
            $query->where('motorista_id', $request->entregador_id);
        }

        // ✅ NOVO: Filtro unificado de cliente
        if ($request->filled('cliente_id')) {
            $query->where(function($q) use ($request) {
                $q->where('cliente_origem_id', $request->cliente_id)
                ->orWhere('cliente_destino_id', $request->cliente_id);
            });
        }

        // ❌ REMOVER: Filtros separados
        // if ($request->filled('cliente_origem_id')) {
        //     $query->where('cliente_origem_id', $request->cliente_origem_id);
        // }
        // if ($request->filled('cliente_destino_id')) {
        //     $query->where('cliente_destino_id', $request->cliente_destino_id);
        // }

        if ($request->filled('cliente_apelido')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('clienteOrigem', function ($sub) use ($request) {
                    $sub->where('apelido', 'like', '%' . $request->cliente_apelido . '%');
                })->orWhereHas('clienteDestino', function ($sub) use ($request) {
                    $sub->where('apelido', 'like', '%' . $request->cliente_apelido . '%');
                });
            });
        }

        if ($request->filled('ajudante_id')) {
            $query->whereHas('ajudantes', function ($q) use ($request) {
                $q->where('entregadores.id', $request->ajudante_id);
            });
        }

        $ordens = $query->latest()->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.ordens-servico', compact('ordens'));

        return $pdf->download('relatorio-ordens-servico.pdf');
    }

    public function exportarClientesAtendidosPDF(Request $request)
    {
        $query = OrdemServico::query()
            ->with(['clienteOrigem', 'clienteDestino'])
            ->whereIn('contratante_tipo', ['origem', 'destino'])
            ->whereNull('deleted_at');

        // Filtro por data
        if ($request->filled('data_inicio')) {
            $query->whereDate('data_servico', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_servico', '<=', $request->data_fim);
        }

        // Filtro por cliente contratante
        if ($request->filled('cliente_id')) {
            $query->where(function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('contratante_tipo', 'origem')
                    ->where('cliente_origem_id', $request->cliente_id);
                })->orWhere(function ($q2) use ($request) {
                    $q2->where('contratante_tipo', 'destino')
                    ->where('cliente_destino_id', $request->cliente_id);
                });
            });
        }

        $ordens = $query->get();

        // Agrupar por cliente contratante
        $clientesAgrupados = [];

        foreach ($ordens as $os) {
            if ($os->contratante_tipo === 'origem') {
                $cliente = $os->clienteOrigem;
            } elseif ($os->contratante_tipo === 'destino') {
                $cliente = $os->clienteDestino;
            } else {
                continue;
            }

            if ($cliente) {
                $id = $cliente->id;

                if (!isset($clientesAgrupados[$id])) {
                    $clientesAgrupados[$id] = [
                        'nome' => $cliente->nome,
                        'total_os' => 0,
                        'valor_total' => 0,
                        'ultima_os' => null,
                    ];
                }

                $clientesAgrupados[$id]['total_os']++;
                $clientesAgrupados[$id]['valor_total'] += $os->valor_total ?? 0;
                $clientesAgrupados[$id]['ultima_os'] = max(
                    $clientesAgrupados[$id]['ultima_os'] ?? '1900-01-01',
                    $os->data_servico
                );
            }
        }

        $dados = collect($clientesAgrupados)->sortBy('nome');

        $pdf = Pdf::loadView('pdf.clientes-atendidos', [
            'dados' => $dados,
            'dataAtual' => now()->format('d/m/Y'),
            'filtros' => $request->all()
        ]);

        return $pdf->download('relatorio-clientes-atendidos.pdf');
    }

    public function exportarContasReceberExcel(Request $request)
    {
        return Excel::download(new ContasReceberExport($request), 'contas-receber.xlsx');
    }

    public function exportarContasPagarPDF(Request $request)
    {
        $query = ContaPagar::with([
            'ordemServico',
            'ordemServico.clienteOrigem',
            'ordemServico.clienteDestino'
        ]);

        // Filtros por datas
        if ($request->filled('data_vencimento_inicio')) {
            $query->where('data_vencimento', '>=', $request->data_vencimento_inicio);
        }

        if ($request->filled('data_vencimento_fim')) {
            $query->where('data_vencimento', '<=', $request->data_vencimento_fim);
        }

        if ($request->filled('data_pagamento_inicio')) {
            $query->where('data_pagamento', '>=', $request->data_pagamento_inicio);
        }

        if ($request->filled('data_pagamento_fim')) {
            $query->where('data_pagamento', '<=', $request->data_pagamento_fim);
        }

        // Filtro por cliente (origem ou destino)
        if ($request->filled('cliente_id')) {
            $query->whereHas('ordemServico', function ($q) use ($request) {
                $q->where('cliente_origem_id', $request->cliente_id)
                ->orWhere('cliente_destino_id', $request->cliente_id);
            });
        }

        $contas = $query->get();

        // Carrega a view com os dados
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.contas-pagar', [
            'contas' => $contas,
            'filtros' => $request->all(),
        ]);

        return $pdf->download('relatorio-contas-a-pagar.pdf');
    }

    public function exportarContasReceberPDF(Request $request)
    {
        $query = ContaReceber::with(['ordemServico.clienteOrigem', 'ordemServico.clienteDestino']);

        if ($request->filled('cliente_id')) {
            $query->whereHas('ordemServico', function ($q) use ($request) {
                $q->where('cliente_origem_id', $request->cliente_id)
                ->orWhere('cliente_destino_id', $request->cliente_id);
            });
        }

        if ($request->filled('vencimento_de')) {
            $query->where('data_vencimento', '>=', $request->vencimento_de);
        }
        if ($request->filled('vencimento_ate')) {
            $query->where('data_vencimento', '<=', $request->vencimento_ate);
        }
        if ($request->filled('recebimento_de')) {
            $query->where('data_recebimento', '>=', $request->recebimento_de);
        }
        if ($request->filled('recebimento_ate')) {
            $query->where('data_recebimento', '<=', $request->recebimento_ate);
        }

        $contas = $query->get();

        $pdf = Pdf::loadView('pdf.contas-receber', compact('contas'));

        return $pdf->download('relatorio-contas-a-receber.pdf');
    }

    public function exportarContasPagarExcel(Request $request)
    {
        return Excel::download(new ContasPagarExport($request), 'contas-pagar.xlsx');
    }

    public function exportarOrdensServicoExcel(Request $request)
    {
        return Excel::download(new OrdemServicoExport($request), 'ordem-servico.xlsx');
    }

    public function exportarMotoristasExcel(Request $request)
    {
        $dados = collect($this->gerarRelatorioMotoristas($request));
        return Excel::download(new MotoristasExport($dados), 'relatorio-motoristas.xlsx');
    }

    public function exportarEntregadoresExcel(Request $request)
    {
        return Excel::download(new EntregadoresExport($request), 'relatorio-entregadores.xlsx');
    }

    public function exportarClientesAtendidosExcel(Request $request)
    {
        return Excel::download(new ClientesAtendidosExport($request), 'clientes-atendidos.xlsx');
    }


}
