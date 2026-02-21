<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Entregador;
use App\Models\OrdemServico;
use App\Models\Veiculo;
use App\Models\OrdemServicoHistorico;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\NotaServicoService;
use App\Services\WhatsGWService;



class OrdemServicoController extends Controller
{
    /**
     * Exibe uma lista das ordens de serviço.
     */
    public function index(Request $request): View
    {

        // Carrega os dados para os filtros
        $empresas = Empresa::orderBy('nome')->get();
        $clientes = Cliente::orderBy('nome')->get();
        $motoristas = Entregador::motoristas()->ativos()->orderBy('nome')->get();

        // Inicializa a consulta com os relacionamentos necessários
        $query = OrdemServico::with([
            'empresa',
            'clienteOrigem',
            'clienteDestino',
            'motorista',
            'veiculo'
        ]);

        // Aplicar filtros se fornecidos
        if ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->filled('cliente_id')) {
            $query->where(function($q) use ($request) {
                $q->where('cliente_origem_id', $request->cliente_id)
                ->orWhere('cliente_destino_id', $request->cliente_id);
            });
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('data_servico', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_servico', '<=', $request->data_fim);
        }

        if ($request->filled('motorista_id')) {
            $query->where('motorista_id', $request->motorista_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('numero_os')) {
            $query->where('numero_os', 'like', '%' . $request->numero_os . '%');
        }

        if ($request->filled('apelido')) {
            $query->whereHas('clienteOrigem', function ($q) use ($request) {
                $q->where('apelido', 'like', '%' . $request->apelido . '%');
            })->orWhereHas('clienteDestino', function ($q) use ($request) {
                $q->where('apelido', 'like', '%' . $request->apelido . '%');
            });
        }

        // Executa a consulta, ordenando os resultados e paginando
        $ordensServico = $query->latest()->paginate(15);

        return view('ordemservicos.index', compact(
            'ordensServico',
            'empresas',
            'clientes',
            'motoristas'
        ));
    }

    /**
     * Exibe o formulário para criar uma nova ordem de serviço.
     */
    public function create(): View
    {
        // Carrega dados necessários para o formulário
        $empresas = Empresa::orderBy('nome')->get();
        $clientes = Cliente::orderBy('nome')->get();
        $motoristas = Entregador::motoristas()->ativos()->orderBy('nome')->get();
        $ajudantes = Entregador::ajudantes()->ativos()->orderBy('nome')->get();

        // Gera o número da OS
        $numero_os = OrdemServico::gerarNumeroOS();
        $datacriacao = now()->format('d/m/Y');

        return view('ordemservicos.create', compact(
            'empresas',
            'clientes',
            'motoristas',
            'ajudantes',
            'numero_os',
            'datacriacao'
        ));
    }

    /**
     * Armazena uma nova ordem de serviço.
     */
    /* public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_id'                            => 'required',
            'cliente_origem_id'                     => 'required',
            'cliente_destino_id'                    => 'required',
            'motorista_id'                          => [
                'required',
                'exists:entregadores,id',
                function ($attribute, $value, $fail) {
                    $motorista = Entregador::find($value);
                    if (!$motorista || $motorista->perfil !== 'Motorista') {
                        $fail('O motorista selecionado não é válido.');
                    }
                }
            ],
            'veiculo_id'                            => 'required|exists:veiculos,id',
            'ajudantes'                             => 'nullable|array',
            'ajudantes.*.id'                        => [
                'required_with:ajudantes',
                'exists:entregadores,id',
                function ($attribute, $value, $fail) {
                    $ajudante = Entregador::find($value);
                    if (!$ajudante || $ajudante->perfil !== 'Ajudante') {
                        $fail('O ajudante selecionado não é válido.');
                    }
                }
            ],
            'ajudantes.*.valor'                      => 'nullable|numeric|min:0',
            'data_servico'                           => 'required|date',
            'hora_inicial'                           => 'nullable|date_format:H:i',
            'hora_final'                             => 'nullable|date_format:H:i',
            'tempo_total'                            => 'nullable',
            'valor_motorista'                        => 'nullable|numeric|min:0',
            'valor_ajudantes'                        => 'nullable|numeric|min:0',
            'valor_perimetro'                       => 'nullable|numeric|min:0',
            'valor_restricao'                        => 'nullable|numeric|min:0',
            'valor_total'                            => 'required|numeric|min:0.01',
            'observacoes'                            => 'nullable|string',
            'contratante_tipo'                       => 'required|in:origem,destino',
            'valor_repassado_motorista'              => 'required|numeric|min:0.01',
            'valor_repassado_ajudantes'              => 'required|numeric|min:0',
            'valor_repasse_resultado'                => 'required|numeric',
            'ajudantes.*.valor_repassado_ajudante'   => 'nullable|numeric|min:0'
        ]);

        if (!empty($validated['hora_inicial']) && !empty($validated['hora_final'])) {
            $inicio = \Carbon\Carbon::parse($validated['hora_inicial']);
            $fim = \Carbon\Carbon::parse($validated['hora_final']);

            if ($fim->lessThan($inicio)) {
                $fim->addDay();
            }

            $validated['tempo_total'] = $inicio->diff($fim)->format('%H:%I');
        } else {
            $validated['tempo_total'] = null;
        }

        // Se precisar usar o ID do cliente contratante em alguma lógica, use auxiliar
        $clienteContratanteId = $validated['contratante_tipo'] === 'origem'
            ? $validated['cliente_origem_id']
            : $validated['cliente_destino_id'];

        $validated['user_id'] = auth()->id();

        DB::beginTransaction();

        try {
            $ordemServico = OrdemServico::create($validated);
            Log::info('Ordem de serviço criada?', ['id' => $ordemServico->id, 'exists' => $ordemServico->exists]);

            if (!empty($validated['ajudantes'])) {
                foreach ($validated['ajudantes'] as $ajudante)
                {
                    $ordemServico->ajudantes()->attach($ajudante['id'], [
                        'valor' => $ajudante['valor'] ?? 0,
                        'valor_repassado_ajudante' => $ajudante['valor_repassado_ajudante'] ?? 0,
                    ]);
                }
            }

            $this->inserirContaPagar($ordemServico);
            $this->inserirContaReceber($ordemServico);

            DB::commit();

            // 🔥 Enviar WhatsApp para o motorista
            $this->enviarWhatsAppMotorista($ordemServico);

            return redirect()->route('ordemservicos.index')
                            ->with('success', 'Ordem de serviço criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar ordem de serviço: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Erro ao criar ordem de serviço: ' . $e->getMessage());
        }
    }
 */



        /**
     * Armazena uma nova ordem de serviço.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_id'                            => 'required',
            'cliente_origem_id'                     => 'required',
            'cliente_destino_id'                    => 'required',
            'motorista_id'                          => [
                'required',
                'exists:entregadores,id',
                function ($attribute, $value, $fail) {
                    $motorista = Entregador::find($value);
                    if (!$motorista || $motorista->perfil !== 'Motorista') {
                        $fail('O motorista selecionado não é válido.');
                    }
                }
            ],
            'veiculo_id'                            => 'required|exists:veiculos,id',
            'ajudantes'                             => 'nullable|array',
            'ajudantes.*.id'                        => [
                'required_with:ajudantes',
                'exists:entregadores,id',
                function ($attribute, $value, $fail) {
                    $ajudante = Entregador::find($value);
                    if (!$ajudante || $ajudante->perfil !== 'Ajudante') {
                        $fail('O ajudante selecionado não é válido.');
                    }
                }
            ],
            'ajudantes.*.valor'                      => 'nullable|numeric|min:0',
            'data_servico'                           => 'required|date',
            'hora_inicial'                           => 'nullable|date_format:H:i',
            'hora_final'                             => 'nullable|date_format:H:i',
            'tempo_total'                            => 'nullable',
            'valor_motorista'                        => 'nullable|numeric|min:0',
            'valor_ajudantes'                        => 'nullable|numeric|min:0',
            'valor_perimetro'                       => 'nullable|numeric|min:0',
            'valor_restricao'                        => 'nullable|numeric|min:0',
            'valor_total'                            => 'required|numeric|min:0.01',
            'observacoes'                            => 'nullable|string',
            'contratante_tipo'                       => 'required|in:origem,destino',
            'valor_repassado_motorista'              => 'required|numeric|min:0.01',
            'valor_repassado_ajudantes'              => 'required|numeric|min:0',
            'valor_repasse_resultado'                => 'required|numeric',
            'ajudantes.*.valor_repassado_ajudante'   => 'nullable|numeric|min:0'
        ]);

        // Calcula o tempo total se ambas as horas foram informadas
        if (!empty($validated['hora_inicial']) && !empty($validated['hora_final'])) {
            $inicio = \Carbon\Carbon::parse($validated['hora_inicial']);
            $fim = \Carbon\Carbon::parse($validated['hora_final']);

            if ($fim->lessThan($inicio)) {
                $fim->addDay();
            }

            $validated['tempo_total'] = $inicio->diff($fim)->format('%H:%I');
        } else {
            $validated['tempo_total'] = null;
        }

        // Define o usuário que está criando a OS
        $validated['user_id'] = auth()->id();

        DB::beginTransaction();

        try {
            // Cria a ordem de serviço
            $ordemServico = OrdemServico::create($validated);

            Log::info('Ordem de serviço criada', [
                'id' => $ordemServico->id,
                'numero_os' => $ordemServico->numero_os
            ]);

            // Associa os ajudantes à OS (se houver)
            if (!empty($validated['ajudantes'])) {
                foreach ($validated['ajudantes'] as $ajudante) {
                    $ordemServico->ajudantes()->attach($ajudante['id'], [
                        'valor' => $ajudante['valor'] ?? 0,
                        'valor_repassado_ajudante' => $ajudante['valor_repassado_ajudante'] ?? 0,
                    ]);
                }
            }

            // Cria as contas financeiras relacionadas
            $this->inserirContaPagar($ordemServico);
            $this->inserirContaReceber($ordemServico);

            DB::commit();

            // Carrega os relacionamentos necessários para o WhatsApp
            $ordemServico->load([
                'motorista',
                'veiculo',
                'clienteOrigem',
                'clienteDestino',
                'ajudantes'
            ]);

            // Envia WhatsApp para o motorista (não quebra o fluxo se falhar)
            $this->enviarWhatsAppMotorista($ordemServico);

            return redirect()->route('ordemservicos.index')
                            ->with('success', 'Ordem de serviço criada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao criar ordem de serviço: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'dados' => $validated
            ]);

            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Erro ao criar ordem de serviço: ' . $e->getMessage());
        }
    }
    /**
     * Exibe os detalhes de uma ordem de serviço específica.
     */
    public function show(OrdemServico $ordemservico): View
    {
        $ordemservico->load([
            'empresa',
            'clienteOrigem',
            'clienteDestino',
            'motorista',
            'veiculo',
            'ajudantes',
            'usuario',
            'historicos.usuario'
        ]);

        $ordemServico = $ordemservico; // renomeando para a variável esperada pela view

        return view('ordemservicos.show', compact('ordemServico'));
    }

    /**
     * Exibe o formulário para editar uma ordem de serviço.
     */
    public function edit(OrdemServico $ordemservico): View
    {
        // Carregar relacionamentos
        $ordemservico->load([
            'empresa',
            'clienteOrigem',
            'clienteDestino',
            'motorista',
            'veiculo',
            'ajudantes'
        ]);

        // Buscar dados para os selects
        $empresas = Empresa::orderBy('nome')->get();
        $clientes = Cliente::orderBy('nome')->get();
        $motoristas = Entregador::motoristas()->ativos()->orderBy('nome')->get();

        // Gerar os valores para o formulário
        $numero_os = $ordemservico->numero_os;
        $datacriacao = \Carbon\Carbon::parse($ordemservico->created_at)->format('d/m/Y H:i');

        // Renomear para manter consistência com a view
        $ordemServico = $ordemservico;

        return view('ordemservicos.edit', compact(
            'ordemServico',  // Usando o nome com S maiúsculo
            'empresas',
            'clientes',
            'motoristas',
            'numero_os',
            'datacriacao'
        ));
    }


    /**
     * Atualiza uma ordem de serviço específica.
     */
    public function update(Request $request, OrdemServico $ordemServico)
    {
        $ordemServicoId = $request->input('ordem_servico_id');

        // Recarrega corretamente com o ID fornecido
        $ordemServico = OrdemServico::findOrFail($ordemServicoId);

        // 🔥 GUARDA OS DADOS ORIGINAIS ANTES DA ATUALIZAÇÃO
        $dadosOriginais = [
            'motorista_id' => $ordemServico->motorista_id,
            'veiculo_id' => $ordemServico->veiculo_id,
            'cliente_origem_id' => $ordemServico->cliente_origem_id,
            'cliente_destino_id' => $ordemServico->cliente_destino_id,
            'data_servico' => $ordemServico->data_servico,
            'hora_inicial' => $ordemServico->hora_inicial,
            'observacoes' => $ordemServico->observacoes,
            'status' => $ordemServico->status,
        ];

        // Converte ajudantes de JSON string para array (caso necessário)
        if ($request->has('ajudantes') && is_string($request->ajudantes)) {
            $request->merge([
                'ajudantes' => json_decode($request->ajudantes, true)
            ]);
        }

        // Validação
        $validated = $request->validate([
            'empresa_id'                  => 'required|exists:empresas,id',
            'cliente_origem_id'           => 'required|exists:clientes,id',
            'cliente_destino_id'          => 'required|exists:clientes,id',
            'motorista_id'                => [
                'required',
                'exists:entregadores,id',
                function ($attribute, $value, $fail) {
                    $motorista = \App\Models\Entregador::find($value);
                    if (!$motorista || $motorista->perfil !== 'Motorista') {
                        $fail('O motorista selecionado não é válido.');
                    }
                }
            ],
            'veiculo_id'                  => 'required|exists:veiculos,id',
            'data_servico'                => 'required|date',
            'hora_inicial'                => 'nullable|date_format:H:i',
            'hora_final'                  => 'nullable|date_format:H:i',
            'valor_motorista'             => 'nullable|numeric|min:0',
            'valor_perimetro'             => 'nullable|numeric|min:0',
            'valor_restricao'             => 'nullable|numeric|min:0',
            'valor_total'                 => 'required|numeric|min:0.01',
            'observacoes'                 => 'nullable|string|max:1000',
            'status'                      => 'required|in:pendente,em_andamento,concluido,cancelado',
            'contratante_tipo'            => 'required|in:origem,destino',
            'valor_repassado_ajudantes'   => 'nullable|numeric|min:0',
            'valor_repassado_motorista'   => 'nullable|numeric|min:0',
            'valor_repasse_resultado'     => 'required|numeric',
            'ajudantes'                   => 'nullable|array',
            'ajudantes.*.id'              => [
                'required',
                'exists:entregadores,id',
                function ($attribute, $value, $fail) {
                    $ajudante = \App\Models\Entregador::find($value);
                    if (!$ajudante || $ajudante->perfil !== 'Ajudante') {
                        $fail('O ajudante selecionado não é válido.');
                    }
                }
            ],
            'ajudantes.*.valor'           => 'nullable|numeric|min:0',
            'ajudantes.*.valor_repassado' => 'nullable|numeric|min:0',
            'data_final'                  => 'nullable|date',
        ]);

        if ($validated['status'] === 'concluido') {
            $ordemServico->data_final = $request->input('data_final') ?? Carbon::now()->toDateString();
            $ordemServico->hora_final = $request->input('hora_final') ?? Carbon::now()->format('H:i');

            // Cálculo do tempo de serviço
            if (!empty($validated['hora_inicial']) && !empty($validated['hora_final'])) {
                $inicio = \Carbon\Carbon::parse($validated['data_servico'] . ' ' . $validated['hora_inicial']);
                $fim = \Carbon\Carbon::parse($ordemServico->data_final . ' ' . $validated['hora_final']);

                if ($fim->lt($inicio)) {
                    $fim->addDay();
                }
                $ordemServico->tempo_total = $inicio->diff($fim)->format('%H:%I');
            }
        }

        // Cálculo do tempo de serviço (quando não for conclusão)
        if (!empty($validated['hora_inicial']) && !empty($validated['hora_final'])) {
            $inicio = \Carbon\Carbon::parse($validated['hora_inicial']);
            $fim = \Carbon\Carbon::parse($validated['hora_final']);
            if ($fim->lt($inicio)) {
                $fim->addDay();
            }
            $validated['tempo_total'] = $inicio->diff($fim)->format('%H:%I');
        } else {
            $validated['tempo_total'] = null;
        }

        DB::beginTransaction();

        try {
            // Atualiza a OS
            $ordemServico->update($validated);

            // Atualiza ajudantes com dados completos
            if (!empty($validated['ajudantes'])) {
                $dadosAjudantes = [];
                foreach ($validated['ajudantes'] as $ajudante) {
                    $dadosAjudantes[$ajudante['id']] = [
                        'valor' => $ajudante['valor'] ?? 0,
                        'valor_repassado_ajudante' => $ajudante['valor_repassado'] ?? 0,
                    ];
                }

                $ordemServico->ajudantes()->sync($dadosAjudantes);
            } else {
                $ordemServico->ajudantes()->detach();
            }

            // ATUALIZAÇÃO DAS CONTAS:
            // Remove contas pendentes anteriores
            $ordemServico->contasPagar()->where('status_pagamento', 'pendente')->delete();
            $ordemServico->contasReceber()->where('status_pagamento', 'pendente')->delete();

            // Reinsere com os novos dados
            $this->inserirContaPagar($ordemServico);
            $this->inserirContaReceber($ordemServico);

            DB::commit();

            // 🔥 VERIFICA ALTERAÇÕES E ENVIA WHATSAPP AUTOMATICAMENTE
            $alteracoesImportantes = $this->verificarAlteracoesImportantes($dadosOriginais, $validated);

            if (!empty($alteracoesImportantes)) {
                // Carrega relacionamentos
                $ordemServico->load([
                    'motorista',
                    'veiculo',
                    'clienteOrigem',
                    'clienteDestino',
                    'ajudantes'
                ]);

                // Envia WhatsApp com as alterações
                $this->enviarWhatsAppAtualizacao($ordemServico, $alteracoesImportantes);
            } else {
                Log::info("Nenhuma alteração importante detectada para envio de WhatsApp", [
                    'os' => $ordemServico->numero_os
                ]);
            }

            return $request->ajax()
                ? response()->json(['success' => 'Ordem de serviço atualizada com sucesso!'])
                : redirect()->route('ordemservicos.index')
                    ->with('success', 'Ordem de serviço atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('❌ Erro ao atualizar OS', [
                'mensagem' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'dados_enviados' => $request->all()
            ]);

            return $request->ajax()
                ? response()->json(['error' => 'Erro ao atualizar ordem de serviço: ' . $e->getMessage()], 500)
                : redirect()->back()
                    ->withInput()
                    ->with('error', 'Erro ao atualizar ordem de serviço: ' . $e->getMessage());
        }
    }


    /**
     * Remove uma ordem de serviço específica.
     */
    public function destroy(OrdemServico $ordemservico): RedirectResponse
    {
        try {

            // Deleta contas associadas antes de deletar a OS
            $ordemservico->contasPagar()->delete();
            $ordemservico->contasReceber()->delete();

            $ordemservico->delete();

            return redirect()->route('ordemservicos.index')
                ->with('success', 'Ordem de serviço excluída com sucesso!');



        } catch (\Exception $e) {
            return redirect()->route('ordemservicos.index')
                ->with('error', 'Erro ao excluir ordem de serviço: ' . $e->getMessage());
        }
    }

    /**
     * Obtém os veículos associados a um motorista via AJAX.
     */
    public function getVeiculosPorMotorista(Request $request)
    {
        $motoristaId = $request->motorista_id;

        if (!$motoristaId) {
            return response()->json(['veiculos' => []]);
        }

        $veiculos = Veiculo::where('motorista_id', $motoristaId)
            ->select('id', 'placa', 'modelo')
            ->orderBy('modelo')
            ->get()
            ->map(function ($veiculo) {
                return [
                    'id' => $veiculo->id,
                    'text' => $veiculo->modelo . ' - ' . $veiculo->placa
                ];
            });

        return response()->json(['veiculos' => $veiculos]);
    }

    public function finalizacaoOS(Request $request, OrdemServico $ordemservico)
    {
        // Garante que os dados mínimos estejam presentes no request
        $request->merge([
            'status' => 'concluido',
            'hora_final' => $request->hora_final ?? now()->format('H:i:s'),
            'tempo_total' => $request->tempo_total ?? null
        ]);

        // Reaproveita toda a lógica de alteração de status
        return $this->alterarStatusOrdem($request, $ordemservico);
    }


    // Dentro de OrdemServicoController.php
    protected function inserirContaPagar(OrdemServico $ordemservico)
    {

       // Log::debug('Inserindo registro na conta a pagar para OS #' . $ordemservico->id);

        // Supondo que você tenha um model ContaPagar configurado corretamente
        \App\Models\ContaPagar::create([
            'ordem_servico_id' => $ordemservico->id,
            'entregador_id' => $ordemservico->motorista_id,
            'tipo' => 'motorista',
            'valor_total' => $ordemservico->valor_repassado_motorista,
            'data_vencimento' => Carbon::now()->addDays(7),
            'status_pagamento' => 'pendente',
            'observacao' => 'Pagamento para motorista da OS #' . $ordemservico->id
        ]);


        // Pagamento para cada ajudante associado à OS
        foreach ($ordemservico->ajudantes as $ajudante) {
            \App\Models\ContaPagar::create([
                'ordem_servico_id' => $ordemservico->id,
                'entregador_id'    => $ajudante->id,
                'tipo'             => 'ajudante',
                'valor_total'      => $ajudante->pivot->valor_repassado_ajudante,
                'data_vencimento'  => \Carbon\Carbon::now()->addDays(5),
                'status_pagamento' => 'pendente',
                'observacao'       => 'Pagamento para ajudante da OS #' . $ordemservico->id,
            ]);
        }
    }

    protected function inserirContaReceber(OrdemServico $ordemservico)
    {
        //Log::debug('Inserindo registro na conta a receber para OS #' . $ordemservico->id);
        return \App\Models\ContaReceber::create([
            'ordem_servico_id' => $ordemservico->id,
            'valor_total'      => $ordemservico->valor_total,
            'data_vencimento'  => \Carbon\Carbon::now()->addDays(5), // exemplo: vencimento em 15 dias
            // 'data_recebimento' fica nula até que o recebimento seja efetivado
            'status_pagamento' => 'pendente', // conforme o default definido
            'observacao'       => 'Recebimento referente à OS #' . $ordemservico->id,
        ]);


    }

    public function alterarStatusOrdem(Request $request, OrdemServico $ordemservico)
    {
        Log::info('Início da alteração de status da OS #' . $ordemservico->id);

        $validated = $request->validate([
            'status' => 'required|in:pendente,em_andamento,concluido,cancelado',
            'hora_final' => 'nullable|date_format:H:i:s',
            'data_final' => 'nullable|date',
            'tempo_total' => 'nullable|string'
        ]);

        try {
            $oldStatus = $ordemservico->status;
            $newStatus = $validated['status'];

            if ($oldStatus !== $newStatus) {
                $ordemservico->status = $newStatus;

                if ($newStatus === 'concluido') {
                    // Atualiza data/hora final e tempo total
                    $ordemservico->hora_final = $validated['hora_final'] ?? now()->format('H:i:s');
                    $ordemservico->data_final = $validated['data_final'] ?? now()->toDateString();
                    $ordemservico->tempo_total = $validated['tempo_total'] ?? null;

                    Log::info('Finalização manual', [
                        'data_final' => $ordemservico->data_final,
                        'hora_final' => $ordemservico->hora_final,
                        'tempo_total' => $ordemservico->tempo_total
                    ]);

                    // Envia nota de serviço (se aplicável)
                    if ($ordemservico->contaReceber) {
                        try {
                            $ordemservico->load([
                                'contaReceber',
                                'empresa',
                                'motorista',
                                'veiculo',
                                'clienteOrigem',
                                'clienteDestino'
                            ]);

                            $ordemServico = $ordemservico; // Alias para view
                            $contaReceber = $ordemservico->contaReceber;

                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.nota-servico', compact('ordemServico', 'contaReceber'));

                            \Mail::to($contaReceber->ordemServico->clienteOrigem->email)
                                ->send(new \App\Mail\NotaServicoMail($contaReceber, $pdf));

                            OrdemServicoHistorico::create([
                                'ordem_servico_id' => $ordemservico->id,
                                'user_id' => \Auth::id(),
                                'status_anterior' => $oldStatus,
                                'status_novo' => $newStatus,
                                'observacao' => 'Nota de Serviço enviada automaticamente ao concluir.',
                            ]);

                            Log::info("Nota de Serviço enviada para a OS #{$ordemservico->id}");
                        } catch (\Exception $e) {
                            Log::error("Erro ao enviar nota de serviço: " . $e->getMessage());
                        }
                    }

                    // Registra histórico da finalização manual
                    OrdemServicoHistorico::create([
                        'ordem_servico_id' => $ordemservico->id,
                        'user_id' => \Auth::id(),
                        'status_anterior' => $oldStatus,
                        'status_novo' => $newStatus,
                        'observacao' => 'Ordem de serviço finalizada manualmente. Data: ' .
                                        ($ordemservico->data_final ?? '-') . ' Hora: ' .
                                        ($ordemservico->hora_final ?? '-') . '.',
                    ]);
                } else {
                    // Outros status: registra histórico padrão
                    OrdemServicoHistorico::create([
                        'ordem_servico_id' => $ordemservico->id,
                        'user_id' => \Auth::id(),
                        'status_anterior' => $oldStatus,
                        'status_novo' => $newStatus,
                        'observacao' => 'Status alterado manualmente via sistema.',
                    ]);
                }

                //Salva OS com os novos dados
                $ordemservico->save();

                Log::info("Status da OS #{$ordemservico->id} alterado de {$oldStatus} para {$newStatus}");
            } else {
                Log::info("Status da OS #{$ordemservico->id} já era {$newStatus}, nenhuma alteração feita.");
            }

            return response()->json(['success' => 'Status atualizado com sucesso!']);

        } catch (\Exception $e) {
            Log::error('Erro ao alterar status da OS: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao alterar o status.'], 500);
        }
    }


    public function colaborador()
    {
        return $this->belongsTo(Entregador::class, 'entregador_id');
    }

    /**
     * Envia WhatsApp para o motorista sobre a nova OS
     *
     * @param OrdemServico $ordemServico
     * @return void
     */
    private function enviarWhatsAppMotorista(OrdemServico $ordemServico): void
    {
        // Verifica se o envio está habilitado
        if (!config('services.whatsgw.enabled', false)) {
            Log::info("Envio de WhatsApp desabilitado. OS #{$ordemServico->numero_os}");
            return;
        }

        try {
            // Obtém o telefone do motorista
            $telefoneMotorista = $ordemServico->motorista->telefone ?? null;

            // Valida se o motorista tem telefone
            if (empty($telefoneMotorista)) {
                Log::warning("Motorista sem telefone cadastrado", [
                    'os' => $ordemServico->numero_os,
                    'motorista_id' => $ordemServico->motorista_id,
                    'motorista_nome' => $ordemServico->motorista->nome ?? 'N/A'
                ]);
                return;
            }

            // Instancia o serviço
            $whatsappService = new WhatsGWService();

            // Gera a mensagem formatada
            $mensagem = $whatsappService->gerarMensagemNovaOS($ordemServico);

            // Envia a mensagem com ID customizado para rastreamento
            $customId = "OS_{$ordemServico->numero_os}";
            $resultado = $whatsappService->enviarMensagemTexto(
                $telefoneMotorista,
                $mensagem,
                $customId
            );

            if ($resultado['success']) {
                Log::info("WhatsApp enviado com sucesso", [
                    'os' => $ordemServico->numero_os,
                    'motorista' => $ordemServico->motorista->nome ?? 'N/A',
                    'telefone' => $telefoneMotorista,
                    'message_id' => $resultado['message_id'] ?? null
                ]);

                // Registra no histórico da OS
                OrdemServicoHistorico::create([
                    'ordem_servico_id' => $ordemServico->id,
                    'user_id' => auth()->id() ?? $ordemServico->user_id,
                    'status_anterior' => $ordemServico->status,
                    'status_novo' => $ordemServico->status,
                    'observacao' => "WhatsApp enviado para o motorista {$ordemServico->motorista->nome}. ID: " . ($resultado['message_id'] ?? 'N/A'),
                ]);

            } else {
                Log::error("Falha ao enviar WhatsApp", [
                    'os' => $ordemServico->numero_os,
                    'motorista' => $ordemServico->motorista->nome ?? 'N/A',
                    'telefone' => $telefoneMotorista,
                    'erro' => $resultado['error'] ?? 'Erro desconhecido'
                ]);
            }

        } catch (\Exception $e) {
            // Não quebra o fluxo se o WhatsApp falhar
            Log::error("Exceção ao enviar WhatsApp para motorista", [
                'os_id' => $ordemServico->id ?? null,
                'os_numero' => $ordemServico->numero_os ?? 'N/A',
                'motorista_id' => $ordemServico->motorista_id ?? null,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

        /**
     * Verifica se houve alterações importantes que justificam envio de WhatsApp
     *
     * @param array $dadosOriginais
     * @param array $dadosNovos
     * @return array
     */
    private function verificarAlteracoesImportantes(array $dadosOriginais, array $dadosNovos): array
    {
        $alteracoes = [];

        // Campos importantes para monitorar
        $camposImportantes = [
            'motorista_id' => 'Motorista',
            'veiculo_id' => 'Veículo',
            'cliente_origem_id' => 'Local de Coleta',
            'cliente_destino_id' => 'Local de Entrega',
            'data_servico' => 'Data do Serviço',
            'hora_inicial' => 'Hora de Início',
            'status' => 'Status',
        ];

        foreach ($camposImportantes as $campo => $descricao) {
            if (isset($dadosNovos[$campo]) && $dadosOriginais[$campo] != $dadosNovos[$campo]) {
                $alteracoes[$campo] = [
                    'descricao' => $descricao,
                    'anterior' => $dadosOriginais[$campo],
                    'novo' => $dadosNovos[$campo]
                ];
            }
        }

        // Verifica mudanças nas observações
        if (isset($dadosNovos['observacoes'])) {
            $obsOriginal = trim($dadosOriginais['observacoes'] ?? '');
            $obsNova = trim($dadosNovos['observacoes']);

            if ($obsOriginal !== $obsNova) {
                $alteracoes['observacoes'] = [
                    'descricao' => 'Observações',
                    'anterior' => $obsOriginal,
                    'novo' => $obsNova
                ];
            }
        }

        return $alteracoes;
    }

    /**
     * Envia WhatsApp ao motorista sobre atualizações na OS
     *
     * @param OrdemServico $ordemServico
     * @param array $alteracoes
     * @return void
     */
    private function enviarWhatsAppAtualizacao(OrdemServico $ordemServico, array $alteracoes): void
    {
        // Verifica se o envio está habilitado
        if (!config('services.whatsgw.enabled', false)) {
            Log::info("Envio de WhatsApp desabilitado. OS #{$ordemServico->numero_os}");
            return;
        }

        try {
            $telefoneMotorista = $ordemServico->motorista->telefone ?? null;

            if (empty($telefoneMotorista)) {
                Log::warning("Motorista sem telefone cadastrado", [
                    'os' => $ordemServico->numero_os,
                    'motorista_id' => $ordemServico->motorista_id,
                    'motorista_nome' => $ordemServico->motorista->nome ?? 'N/A'
                ]);
                return;
            }

            $whatsappService = new WhatsGWService();
            $mensagem = $this->gerarMensagemAtualizacao($ordemServico, $alteracoes);

            $customId = "OS_UPDATE_{$ordemServico->numero_os}_" . time();
            $resultado = $whatsappService->enviarMensagemTexto(
                $telefoneMotorista,
                $mensagem,
                $customId
            );

            if ($resultado['success']) {
                Log::info("WhatsApp de atualização enviado com sucesso", [
                    'os' => $ordemServico->numero_os,
                    'motorista' => $ordemServico->motorista->nome ?? 'N/A',
                    'telefone' => $telefoneMotorista,
                    'message_id' => $resultado['message_id'] ?? null,
                    'alteracoes' => count($alteracoes)
                ]);

                // Registra no histórico
                $observacao = "WhatsApp de atualização enviado. Alterações: " .
                            implode(', ', array_column($alteracoes, 'descricao'));

                OrdemServicoHistorico::create([
                    'ordem_servico_id' => $ordemServico->id,
                    'user_id' => auth()->id() ?? $ordemServico->user_id,
                    'status_anterior' => $ordemServico->status,
                    'status_novo' => $ordemServico->status,
                    'observacao' => $observacao,
                ]);

            } else {
                Log::error("Falha ao enviar WhatsApp de atualização", [
                    'os' => $ordemServico->numero_os,
                    'motorista' => $ordemServico->motorista->nome ?? 'N/A',
                    'telefone' => $telefoneMotorista,
                    'erro' => $resultado['error'] ?? 'Erro desconhecido'
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Exceção ao enviar WhatsApp de atualização", [
                'os_id' => $ordemServico->id ?? null,
                'os_numero' => $ordemServico->numero_os ?? 'N/A',
                'motorista_id' => $ordemServico->motorista_id ?? null,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Gera mensagem formatada para atualização da OS
     *
     * @param OrdemServico $ordemServico
     * @param array $alteracoes
     * @return string
     */
    private function gerarMensagemAtualizacao(OrdemServico $ordemServico, array $alteracoes): string
    {
        $nomeMotorista = $ordemServico->motorista->nome ?? 'Motorista';
        $numeroOS = $ordemServico->numero_os;

        $mensagem = "⚠️ *ATUALIZAÇÃO DE ORDEM DE SERVIÇO* ⚠️\n";
        $mensagem .= "━━━━━━━━━━━━━━━━━━━━━━\n\n";
        $mensagem .= "Olá *{$nomeMotorista}*!\n\n";
        $mensagem .= "A OS *#{$numeroOS}* foi atualizada.\n\n";
        $mensagem .= "📝 *ALTERAÇÕES REALIZADAS:*\n";
        $mensagem .= "━━━━━━━━━━━━━━━━━━━━━━\n\n";

        foreach ($alteracoes as $campo => $info) {
            $mensagem .= "🔹 *{$info['descricao']}*\n";

            // Formatar os valores de acordo com o tipo
            $valorAnterior = $this->formatarValorAlteracao($campo, $info['anterior'], $ordemServico);
            $valorNovo = $this->formatarValorAlteracao($campo, $info['novo'], $ordemServico);

            $mensagem .= "   ❌ Antes: {$valorAnterior}\n";
            $mensagem .= "   ✅ Agora: {$valorNovo}\n\n";
        }

        // Adiciona informações atualizadas da OS
        $mensagem .= "━━━━━━━━━━━━━━━━━━━━━━\n";
        $mensagem .= "📋 *INFORMAÇÕES ATUALIZADAS:*\n";
        $mensagem .= "━━━━━━━━━━━━━━━━━━━━━━\n\n";

        $dataServico = \Carbon\Carbon::parse($ordemServico->data_servico)->format('d/m/Y');
        $horaInicial = $ordemServico->hora_inicial ?? 'A definir';

        $mensagem .= "📅 *Data:* {$dataServico}\n";
        $mensagem .= "🕐 *Hora:* {$horaInicial}\n";
        $mensagem .= "🚗 *Veículo:* " . ($ordemServico->veiculo ? "{$ordemServico->veiculo->modelo} - {$ordemServico->veiculo->placa}" : 'N/A') . "\n\n";

        $mensagem .= "📍 *COLETA:* " . ($ordemServico->clienteOrigem->nome ?? 'N/A');
        if ($ordemServico->clienteOrigem && $ordemServico->clienteOrigem->apelido) {
            $mensagem .= " _{$ordemServico->clienteOrigem->apelido}_";
        }
        $mensagem .= "\n";

        $mensagem .= "🎯 *ENTREGA:* " . ($ordemServico->clienteDestino->nome ?? 'N/A');
        if ($ordemServico->clienteDestino && $ordemServico->clienteDestino->apelido) {
            $mensagem .= " _{$ordemServico->clienteDestino->apelido}_";
        }
        $mensagem .= "\n\n";

        if ($ordemServico->observacoes) {
            $mensagem .= "📝 *Observações:*\n{$ordemServico->observacoes}\n\n";
        }

        $mensagem .= "━━━━━━━━━━━━━━━━━━━━━━\n";
        $mensagem .= "✅ *Fique atento às mudanças!*";

        return $mensagem;
    }

    /**
     * Formata valores de acordo com o tipo de campo
     *
     * @param string $campo
     * @param mixed $valor
     * @param OrdemServico $ordemServico
     * @return string
     */
    private function formatarValorAlteracao(string $campo, $valor, OrdemServico $ordemServico): string
    {
        if (is_null($valor) || $valor === '') {
            return 'Não definido';
        }

        switch ($campo) {
            case 'motorista_id':
                $motorista = Entregador::find($valor);
                return $motorista ? $motorista->nome : "ID: {$valor}";

            case 'veiculo_id':
                $veiculo = Veiculo::find($valor);
                return $veiculo ? "{$veiculo->modelo} - {$veiculo->placa}" : "ID: {$valor}";

            case 'cliente_origem_id':
            case 'cliente_destino_id':
                $cliente = Cliente::find($valor);
                return $cliente ? ($cliente->apelido ?? $cliente->nome) : "ID: {$valor}";

            case 'data_servico':
                return \Carbon\Carbon::parse($valor)->format('d/m/Y');

            case 'hora_inicial':
            case 'hora_final':
                return \Carbon\Carbon::parse($valor)->format('H:i');

            case 'status':
                return OrdemServico::STATUS[$valor] ?? $valor;

            case 'observacoes':
                return strlen($valor) > 50 ? substr($valor, 0, 50) . '...' : $valor;

            default:
                return $valor;
        }
    }
}
