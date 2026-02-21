@extends('layouts.app')

@section('title', 'Editar Ordem de Serviço')

@section('content')

<div class="container-fluid py-4">

    <div class="row">
        <!-- Card de título -->
        <div class="col-12 mb-4">
            <div class="card bg-light shadow-sm">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-1"><i class="fas fa-edit me-2"></i>Editar Ordem de Serviço</h5>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-12">
                <form id="formOS" method="POST" action="{{ route('ordemservicos.update', $ordemServico->id) }}">
                    <!-- <form action="{{ route('ordemservicos.index') }}" method="GET" id="formFiltros"> -->

                    @csrf
                    <input type="hidden" name="ordem_servico_id" id="ordem_servico_id" value="{{ $ordemServico->id }}">
                    @method('PUT')

                    <!-- Status da OS -->

                    <div class="card mb-2">
                        <div class="card-header bg-header-blue">
                            Status da Ordem de Serviço
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Status -->
                                <div class="col-md-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="pendente" {{ $ordemServico->status == 'pendente' ? 'selected' : '' }}>Pendente</option>
                                        <option value="em_andamento" {{ $ordemServico->status == 'em_andamento' ? 'selected' : '' }}>Em Andamento</option>
                                        <option value="concluido" {{ $ordemServico->status == 'concluido' ? 'selected' : '' }}>Concluído</option>
                                        <option value="cancelado" {{ $ordemServico->status == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                    </select>
                                </div>
                                <!-- Data Final (novo campo) -->
                                <div class="col-md-2" id="campo_data_final">
                                    <label for="data_final" class="form-label">Data Final</label>
                                    <input type="date" class="form-control" id="data_final" name="data_final" value="{{ $ordemServico->data_final }}" {{ $ordemServico->status === 'concluido' ? '' : 'disabled' }}>
                                </div>
                                <!-- Hora Final -->
                                <div class="col-md-2" id="campo_hora_final">
                                    <label for="hora_final" class="form-label">Hora Final</label>
                                    <input
                                        type="time"
                                        class="form-control"
                                        id="hora_final"
                                        name="hora_final"
                                        value="{{ $ordemServico->hora_final ? \Carbon\Carbon::parse($ordemServico->hora_final)->format('H:i') : '' }}"
                                        {{ $ordemServico->status === 'concluido' ? '' : 'disabled' }}>
                                </div>


                                <!-- Tempo Total -->
                                <div class="col-md-2">
                                    <label for="tempo_total" class="form-label">Tempo Total</label>
                                    <input type="text" class="form-control" id="tempo_total" name="tempo_total" value="{{ $ordemServico->tempo_total }}" readonly disabled>
                                </div>
                                <!-- Botão de Ação -->
                                <div class="col-md-3 d-flex align-items-end justify-content-end">
                                    <button type="submit" class="btn btn-primary" id="btnFinalizarOS">atualizar situação OS</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cabeçalho da OS -->
                    <div class="card mb-2">
                        <div class="card-header bg-header-blue">
                            Informações Básicas
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="empresa" class="form-label required">Empresa</label>
                                    <select class="form-select" id="empresa" name="empresa_id" required>
                                        @forelse($empresas as $empresa)
                                            <option value="{{ $empresa->id }}" {{ $ordemServico->empresa_id == $empresa->id ? 'selected' : '' }}>
                                                {{ $empresa->nome }}
                                            </option>
                                        @empty
                                            <option value="">Nenhuma empresa encontrada</option>
                                        @endforelse
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label for="numero_os" class="form-label">Número OS</label>
                                    <input type="text" class="form-control" id="numero_os" name="numero_os" value="{{ $ordemServico->numero_os }}" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label for="data_criacao" class="form-label">Data de Criação</label>
                                    <input type="text" class="form-control" id="data_criacao" name="data_criacao" value="{{ \Carbon\Carbon::parse($ordemServico->created_at)->format('d/m/Y H:i') }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="contratante_tipo" class="form-label required">Cliente Contratante</label>
                                    <select class="form-select" id="contratante_tipo" name="contratante_tipo" required>
                                        <option value="">Selecione</option>
                                        <option value="origem" {{ old('contratante_tipo', $ordemServico->contratante_tipo) == 'origem' ? 'selected' : '' }}>Cliente de Origem</option>
                                        <option value="destino" {{ old('contratante_tipo', $ordemServico->contratante_tipo) == 'destino' ? 'selected' : '' }}>Cliente de Destino</option>
                                    </select>
                                </div>

                            </div>

                            <!-- Clientes Origem e Destino -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header bg-header-blue">Cliente Origem</div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="cliente_origem" class="form-label required">Cliente</label>
                                                <select class="form-select" id="cliente_origem" name="cliente_origem_id" required>
                                                    <option value="">Selecione o cliente de origem</option>
                                                    @forelse($clientes as $cliente)
                                                        <option value="{{ $cliente->id }}" data-apelido="{{ $cliente->apelido }}"
                                                            {{ $ordemServico->cliente_origem_id == $cliente->id ? 'selected' : '' }}>
                                                            {{ $cliente->nome }} {{ $cliente->apelido ? "({$cliente->apelido})" : '' }}
                                                        </option>
                                                    @empty
                                                        <option value="">Nenhum cliente encontrado</option>
                                                    @endforelse
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="responsavel_origem" class="form-label">Responsável</label>
                                                        <input type="text" class="form-control" id="responsavel_origem" name="responsavel_origem" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="telefone_origem" class="form-label">Telefone</label>
                                                        <input type="text" class="form-control" id="telefone_origem" name="telefone_origem" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="email_origem" class="form-label">E-mail</label>
                                                <input type="email" class="form-control" id="email_origem" name="email_origem" readonly>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="cep_origem" class="form-label">CEP</label>
                                                        <input type="text" class="form-control" id="cep_origem" name="cep_origem" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="mb-3">
                                                        <label for="endereco_origem" class="form-label">Endereço</label>
                                                        <input type="text" class="form-control" id="endereco_origem" name="endereco_origem" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="mb-2">
                                                        <label for="numero_origem" class="form-label">Nº</label>
                                                        <input type="text" class="form-control" id="numero_origem" name="numero_origem" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="cidade_uf_origem" class="form-label">Cidade/UF</label>
                                                <input type="text" class="form-control" id="cidade_uf_origem" name="cidade_uf_origem" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header bg-header-blue">Cliente Destino</div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="cliente_destino" class="form-label required">Cliente</label>
                                                <select class="form-select" id="cliente_destino" name="cliente_destino_id" required>
                                                    <option value="">Selecione o cliente de destino</option>
                                                    @forelse($clientes as $cliente)
                                                        <option value="{{ $cliente->id }}" data-apelido="{{ $cliente->apelido }}"
                                                            {{ $ordemServico->cliente_destino_id == $cliente->id ? 'selected' : '' }}>
                                                            {{ $cliente->nome }} {{ $cliente->apelido ? "({$cliente->apelido})" : '' }}
                                                        </option>
                                                    @empty
                                                        <option value="">Nenhum cliente encontrado</option>
                                                    @endforelse
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="responsavel_destino" class="form-label">Responsável</label>
                                                        <input type="text" class="form-control" id="responsavel_destino" name="responsavel_destino" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="telefone_destino" class="form-label">Telefone</label>
                                                        <input type="text" class="form-control" id="telefone_destino" name="telefone_destino" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="email_destino" class="form-label">E-mail</label>
                                                <input type="email" class="form-control" id="email_destino" name="email_destino" readonly>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="cep_destino" class="form-label">CEP</label>
                                                        <input type="text" class="form-control" id="cep_destino" name="cep_destino" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="mb-3">
                                                        <label for="endereco_destino" class="form-label">Endereço</label>
                                                        <input type="text" class="form-control" id="endereco_destino" name="endereco_destino" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="mb-2">
                                                        <label for="numero_destino" class="form-label">Nº</label>
                                                        <input type="text" class="form-control" id="numero_destino" name="numero_destino" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="cidade_uf_destino" class="form-label">Cidade/UF</label>
                                                <input type="text" class="form-control" id="cidade_uf_destino" name="cidade_uf_destino" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informações do Serviço -->
                    <div class="card mb-2">
                        <div class="card-header bg-header-blue">
                            Informações do Serviço
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="data_servico" class="form-label required">Data do Serviço</label>
                                    <input type="date" class="form-control" id="data_servico" name="data_servico" value="{{ \Carbon\Carbon::parse($ordemServico->data_servico)->format('Y-m-d') }}" required>

                                </div>
                                <div class="col-md-3">
                                    <label for="hora_inicial" class="form-label">Hora Inicial</label>
                                    <input type="time" class="form-control" id="hora_inicial" name="hora_inicial" value="{{ \Carbon\Carbon::parse($ordemServico->hora_inicial)->format('H:i') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Motorista e Veículo -->
                    <div class="card mb-2">
                        <div class="card-header bg-header-blue">
                            Motorista e Veículo
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="motorista" class="form-label required">Motorista</label>
                                    <select class="form-select" id="motorista" name="motorista_id" required>
                                        <option value="">Selecione o motorista</option>
                                        @foreach($motoristas as $motorista)
                                            <option value="{{ $motorista->id }}" {{ $ordemServico->motorista_id == $motorista->id ? 'selected' : '' }}>
                                                {{ $motorista->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="veiculo" class="form-label required">Veículo</label>
                                    <select id="veiculos" name="veiculo_id" class="form-select" required>
                                        <option value="">Carregando veículos...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Entregadores/Ajudantes -->
                    <div class="card mb-2">
                        <div class="card-header bg-header-blue d-flex justify-content-between align-items-center">
                            <span>Entregadores/Ajudantes</span>
                            <button type="button" class="btn btn-sm btn-success" id="btnAddAjudante">
                                <i class="fas fa-plus"></i> Adicionar Ajudante
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="tabelaAjudantes">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Telefone</th>
                                            <th style="width: 180px;">Valor Serviço</th>
                                            <th style="width: 180px;">Valor Repasse</th>
                                            <th style="width: 100px;" class="text-center">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="corpoTabelaAjudantes">
                                        <!-- Os ajudantes serão adicionados aqui dinamicamente -->
                                        <tr class="sem-registros">
                                            <td colspan="5" class="text-center">Nenhum ajudante adicionado</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-2">
                        <div class="card-header bg-header-blue">
                            Valores do Serviço
                        </div>
                        <div class="card-body">

                            <!-- Bloco 1: Custos Operacionais -->
                            <!-- <h6 class="mb-3">Custos Operacionais</h6> -->
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label for="valor_ajudantes" class="form-label">Ajudantes</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" class="form-control" id="valor_ajudantes" name="valor_ajudantes" step="0.01" min="0" placeholder="0,00" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label for="valor_motorista" class="form-label">Motorista</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" class="form-control" id="valor_motorista" name="valor_motorista" step="0.01" min="0" placeholder="0,00">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label for="valor_perimetro" class="form-label">Taxa de Perimetro</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" class="form-control" id="valor_perimetro" name="valor_perimetro" step="0.01" min="0" placeholder="0,00">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label for="valor_restricao" class="form-label">Taxa de Restrição</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" class="form-control" id="valor_restricao" name="valor_restricao" step="0.01" min="0" placeholder="0,00">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label for="valor_total" class="form-label">Valor Total</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" class="form-control" id="valor_total" name="valor_total" step="0.01" min="0" placeholder="0,00" readonly>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>

                    <!-- Seção: Repasse aos Entregadores -->
                    <div class="card mb-2">
                        <div class="card-header bg-header-blue">
                            Repasses
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">

                                <div class="col-md-2">
                                    <label for="valor_repassado_ajudantes" class="form-label">Repassar aos Ajudantes</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" class="form-control" id="valor_repassado_ajudantes" name="valor_repassado_ajudantes" step="0.01" min="0" placeholder="0,00" readonly>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <label for="valor_repassado_motorista" class="form-label">Repassar ao Motorista</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" class="form-control" id="valor_repassado_motorista" name="valor_repassado_motorista" step="0.01" min="0" placeholder="0,00">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <label for="valor_repasse_resultado" class="form-label">Total Após Repasse</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" class="form-control fw-bold" id="valor_repasse_resultado" name="valor_repasse_resultado" step="0.01" min="0" placeholder="0,00" readonly>
                                    </div>
                                </div>
                        </div>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="card mb-2">
                        <div class="card-header bg-header-blue">
                            Observações
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <textarea class="form-control" id="observacoes" name="observacoes" rows="3" placeholder="Informe aqui observações relevantes sobre o serviço">{{ $ordemServico->observacoes }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Informações do registro -->
                    <div class="card mb-2">
                        <div class="card-header bg-header-blue">
                            Informações do Registro
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="usuario_criador" class="form-label">Usuário</label>
                                    <input type="text" class="form-control" id="usuario_criador" value="{{ $ordemServico->usuario->name ?? '' }}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="created_at" class="form-label">Criado em</label>
                                    <input type="text" class="form-control" id="created_at" value="{{ \Carbon\Carbon::parse($ordemServico->created_at)->format('d/m/Y H:i') }}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="updated_at" class="form-label">Última atualização</label>
                                    <input type="text" class="form-control" id="updated_at" value="{{ \Carbon\Carbon::parse($ordemServico->updated_at)->format('d/m/Y H:i') }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="d-flex justify-content-end gap-2 mb-4">
                        <a href="{{ route('ordemservicos') }}" class="btn btn-secondary" id="btnCancelar">Cancelar</a>
                        <button type="submit" class="btn btn-primary" id="btnSalvar">Atualizar Ordem de Serviço</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Adicionar Ajudante -->
<div class="modal fade" id="modalAjudante" tabindex="-1" role="dialog" aria-labelledby="modalAjudanteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalAjudanteLabel">Adicionar Ajudante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body p-4 pt-2">
                <form id="formAjudante">
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <i class="fas fa-user text-primary"></i>
                        <span><strong>Motorista:</strong>
                            <span id="motoristaSelecionado" class="text-primary"></span>
                        </span>
                    </div>

                    <div class="mb-3">
                        <label for="ajudante" class="form-label">Ajudante</label>
                        <select id="ajudante" name="ajudante" class="form-select" required>
                            <option value="">Selecione um ajudante</option>
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="valor_ajudante" class="form-label">💰 Valor do Serviço (R$)</label>
                            <input type="text" id="valor_ajudante" name="valor_ajudante" class="form-control" placeholder="0,00" required>
                        </div>
                        <div class="col-md-6">
                            <label for="valor_repassado_aj" class="form-label">💸 Valor Repassado (R$)</label>
                            <input type="text" id="valor_repassado_aj" name="valor_repassado_aj" class="form-control" placeholder="0,00" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" id="btnConfirmarAjudante">
                            <i class="bi bi-person-plus-fill"></i> Adicionar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Finalizar Ordem de Serviço</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                Deseja realmente finalizar a ordem de serviço?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btnModalCancel" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnModalConfirm">Finalizar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Taxa de Restrição -->
<div class="modal fade" id="modalTaxaRestricao" role="dialog" tabindex="-2" aria-labelledby="modalTaxaRestricaoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-warning" id="modalTaxaRestricaoLabel">Atenção</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                O horário de finalização é após as <strong>16:00</strong> e a <strong>Taxa de Restrição</strong> está zerada.<br>
                Deseja adicionar uma taxa agora?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="recusarTaxa">Não, continuar mesmo assim</button>
                <button type="button" class="btn btn-primary" id="aceitarTaxa">Sim, adicionar taxa</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Novo Cliente Avulso - Destino -->
<div class="modal fade" id="modalClienteAvulsoDestino" tabindex="-1" aria-labelledby="modalClienteAvulsoDestinoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalClienteAvulsoDestinoLabel">Cadastrar Cliente Avulso (Destino)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form id="formClienteAvulsoDestino">
                <div class="mb-3">
                    <label class="form-label">Responsável (Nome)</label>
                    <input type="text" class="form-control" name="nome_avulso" id="nome_avulso" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Telefone</label>
                    <input type="text" class="form-control" name="telefone_avulso" id="telefone_avulso" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Endereço</label>
                    <input type="text" class="form-control" name="endereco_avulso" id="endereco_avulso" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nº</label>
                    <input type="text" class="form-control" name="numero_avulso" id="numero_avulso" required>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary" id="btnSalvarClienteAvulsoDestino">Salvar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Bootstrap e jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="{{ asset('js/os_ajudante.js') }}"></script>
<script src="{{ asset('js/os_cliente.js') }}"></script>
<script src="{{ asset('js/os_veiculo.js') }}"></script>

<script>
    // Variável global com a URL da rota
    var urlClientesDados = "{{ route('clientes.dados', ['clienteId' => 'CLIENTE_ID_PLACEHOLDER']) }}";
    var urlAjudantes = "{{ route('ajudantes.index') }}";
    var urlVeiculos = "{{ route('entregadores.veiculos', ['id' => 'MOTORISTA_ID_PLACEHOLDER']) }}";

    $(document).ready(function() {

        // Inicialização de máscaras
         // Aplicando máscaras com IDs padronizados
        $('#valor_ajudantes').mask('#.###.##0,00', {reverse: true});
        $('#valor_motorista').mask('#.###.##0,00', {reverse: true});
        $('#valor_perimetro').mask('#.###.##0,00', {reverse: true});
        $('#valor_restricao').mask('#.###.##0,00', {reverse: true});
        $('#valor_total').mask('#.###.##0,00', {reverse: true});


        $('#valor_repassado_ajudantes').mask('#.###.##0,00', {reverse: true});
        $('#valor_repassado_motorista').mask('#.###.##0,00', {reverse: true});
        $('#valor_repasse_resultado').mask('#.###.##0,00', {reverse: true});

        //Modal
        $('#valor_repassado_aj').mask('#.###.##0,00', {reverse: true});
        $('#valor_ajudante').mask('#.###.##0,00', {reverse: true});


        // Eventos para recalcular o valor total do serviço
        $('#valor_ajudantes,  #valor_motorista, #valor_perimetro, #valor_restricao, #valor_repassado_ajudantes, #valor_repassado_motorista, #valor_repasse_resultado, #valor_repassado_aj, #valor_ajudante').on('blur change', function(){
            console.log('alterou')
            calcularValorTotalServico();
        });

        // Evento ao selecionar cliente origem
        $('#cliente_origem').change(function() {
            const clienteId = $(this).val();
            if (clienteId) {
                CarregarDetalhesClientes('origem', clienteId);
            } else {
                limparDetalhesClientes('origem');
            }
        });

        // Evento ao selecionar cliente destino
        $('#cliente_destino').change(function() {
            const clienteId = $(this).val();
            if (clienteId) {
                CarregarDetalhesClientes('destino', clienteId);
            } else {
                limparDetalhesClientes('destino');
            }
        });

        // Evento ao selecionar motorista
        $('#motorista').on('change', function() {
            const motoristaId = $(this).val();
            if (motoristaId) {
                carregarVeiculosMotorista(motoristaId);
            } else {
                $('#veiculos').html('<option value="">Selecione um motorista para carregar os veículos</option>');
            }
        });

        $('#btnAddAjudante').on('click', function() {

            const nomeMotorista = $('#motorista option:selected').text();

            if (!nomeMotorista || $('#motorista').val() === '') {
                alert("Por favor, selecione um motorista antes de adicionar ajudantes.");
                return;
            }

            // Atualiza o nome dentro do modal
            $('#motoristaSelecionado').text(nomeMotorista);

            carregarAjudantes();

            // Limpar campos do modal
            $('#ajudante').val('');
            $('#valor_ajudante').val('');
            $('#valor_repassado_aj').val('');

            $('#modalAjudante').modal('show');

        });

        // Evento para hora final
        $('#hora_final').on('change', function() {
            calcularTempoTotal();
        });

        // Evento para confirmar a adição do ajudante
        $('#btnConfirmarAjudante').on('click', function() {
            const ajudanteId = $('#ajudante').val();
            if (!ajudanteId) {
                alert('Por favor, selecione um ajudante.');
                return;
            }

            const nome = $('#ajudante option:selected').text();
            const telefone = $('#ajudante option:selected').data('telefone');
            const valor = $('#valor_ajudante').val();
            const valorRepassado = $('#valor_repassado_aj').val();

            if (!valor || limparFormatoValor(valor) <= 0) {
                alert('Por favor, informe o valor do serviço.');
                $('#valor_ajudante').focus();
                return;
            }

            if (!valorRepassado || limparFormatoValor(valorRepassado) <= 0) {
                alert('Por favor, informe o valor repassado.');
                $('#valor_repassado_aj').focus();
                return;
            }

            adicionarAjudanteTabela(ajudanteId, nome, telefone, valor, valorRepassado);
            $('#modalAjudante').modal('hide');

            atualizarObservacoesAuto();

        });

        // Evento de remoção de ajudante (delegado)
        $(document).on('click', '.btn-remover-ajudante', function() {
            if (confirm('Deseja realmente remover este ajudante?')) {
                $(this).closest('tr').remove();

                // Verificar se ficou vazio
                if ($('#tabelaAjudantes tbody tr').length === 0) {
                    $('#tabelaAjudantes tbody').html(`
                        <tr class="sem-registros">
                            <td colspan="5" class="text-center">Nenhum ajudante adicionado</td>
                        </tr>
                    `);
                }

                calcularValorTotalServico();
                atualizarObservacoesAuto();
            }
        });


        $('#status').change(function() {

            const status = $(this).val();
            const now = new Date();

            const $dataFinal = $('#data_final');
            const $horaFinal = $('#hora_final');


            if (status === "concluido") {
                // Faça algo específico para "Concluido"

                //$('#campo_data_final, #campo_hora_final').removeClass('d-none');

                 // Preencher automaticamente
                const hora = now.getHours().toString().padStart(2, '0');
                const minuto = now.getMinutes().toString().padStart(2, '0');
                const data = now.toISOString().split('T')[0];

                $dataFinal.val(data).prop('disabled', false);
                $horaFinal.val(`${hora}:${minuto}`).prop('disabled', false);

                //toggleCamposFinalizacao(true);


                //setCurrentHoraFinal();
                calcularTempoTotal();
            } else {
                // Faça algo para outros status
                //toggleCamposFinalizacao(false);

                $dataFinal.prop('disabled', true).val('');
                $horaFinal.prop('disabled', true).val('');
                $('#tempo_total').val('');

                setCurrentHoraFinal();
                calcularTempoTotal();
            }
        });


        // Evento de submissão do formulário (opção AJAX)
        $('#formOS').on('submit', function(e) {
            e.preventDefault(); // Remover se quiser submissão tradicional
            atualizarOS();
        });

        // Carregar dados iniciais
        carregarDadosIniciais();

        $('#btnFinalizarOS').on('click', function(e) {
            e.preventDefault(); // impede a submissão do formulário se for o caso
            btnFinalizarOS(); // chama a função definida
        });
    });

    // Função para carregar dados iniciais
    function carregarDadosIniciais() {
        // Carregar detalhes dos clientes
        if ($('#cliente_origem').val()) {
            CarregarDetalhesClientes('origem', $('#cliente_origem').val());
        }

        if ($('#cliente_destino').val()) {
            CarregarDetalhesClientes('destino', $('#cliente_destino').val());
        }

        // Carregar veículos do motorista selecionado
        if ($('#motorista').val()) {
            carregarVeiculosMotorista($('#motorista').val(), {{ $ordemServico->veiculo_id ?? 'null' }});
        }

        // Carregar ajudantes existentes - CORRIGIDO
        @if(isset($ordemServico->ajudantes) && count($ordemServico->ajudantes) > 0)
            @foreach($ordemServico->ajudantes as $ajudante)
                @php
                    // Formatar valores corretamente no backend
                    $valorFormatado = number_format($ajudante->pivot->valor ?? 0, 2, ',', '.');
                    $valorRepasseFormatado = number_format($ajudante->pivot->valor_repassado_ajudante ?? 0, 2, ',', '.');
                @endphp

                adicionarAjudanteTabela(
                    {{ $ajudante->id }},
                    "{{ addslashes($ajudante->nome) }}",
                    "{{ $ajudante->telefone ?? '' }}",
                    "{{ $valorFormatado }}",
                    "{{ $valorRepasseFormatado }}"
                );
            @endforeach
        @endif

        const valorMotorista = parseFloat('{{ number_format($ordemServico->valor_motorista, 2, '.', '') }}') || 0;
        const valorPerimetro = parseFloat('{{ number_format($ordemServico->valor_perimetro, 2, '.', '') }}') || 0;
        const valorRestricao = parseFloat('{{ number_format($ordemServico->valor_restricao, 2, '.', '') }}') || 0;
        const valorTotal = parseFloat('{{ number_format($ordemServico->valor_total, 2, '.', '') }}') || 0;
        const valor_repassado_motorista = parseFloat('{{ number_format($ordemServico->valor_repassado_motorista, 2, '.', '') }}') || 0;
        const valor_repassado_ajudantes = parseFloat('{{ number_format($ordemServico->valor_repassado_ajudantes, 2, '.', '') }}') || 0;
        const valor_repasse_resultado =parseFloat('{{ number_format($ordemServico->valor_repasse_resultado, 2, '.', '') }}') || 0;

        // Preencher campos de valores
        $('#valor_motorista').val(valorMotorista.toFixed(2).replace('.', ','));
        $('#valor_perimetro').val(valorPerimetro.toFixed(2).replace('.', ','));
        $('#valor_restricao').val(valorRestricao.toFixed(2).replace('.', ','));
        $('#valor_total').val(valorTotal.toFixed(2).replace('.', ','));

        $('#valor_repassado_motorista').val(valor_repassado_motorista.toFixed(2).replace('.', ','));
        $('#valor_repassado_ajudantes').val(valor_repassado_ajudantes.toFixed(2).replace('.', ','));
        $('#valor_repasse_resultado').val(valor_repasse_resultado.toFixed(2).replace('.', ','));


        calcularValorTotalServico();

        setTimeout(() => window.scrollTo(0, 0), 100);

    }


    function adicionarAjudanteTabela(ajudanteId, nome, telefone, valor, valorRepassado) {
        // Remove a linha "sem registros" se existir
        $('#tabelaAjudantes tbody .sem-registros').remove();

        // Verifica se o ajudante já foi adicionado
        if ($(`#tabelaAjudantes tbody tr[data-id="${ajudanteId}"]`).length > 0) {
            alert('Este ajudante já foi adicionado.');
            return;
        }

        // Garantir que os valores estejam no formato correto (com vírgula)
        const valorFormatado = valor.toString().replace('.', ',');
        const valorRepassadoFormatado = valorRepassado.toString().replace('.', ',');

        const row = `
            <tr data-id="${ajudanteId}">
                <td>${nome}</td>
                <td>${telefone}</td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">R$</span>
                        <input type="text"
                            class="form-control valor-ajudante-edit"
                            data-ajudante-id="${ajudanteId}"
                            value="${valorFormatado}">
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">R$</span>
                        <input type="text"
                            class="form-control valor-repasse-ajudante-edit"
                            data-ajudante-id="${ajudanteId}"
                            value="${valorRepassadoFormatado}">
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger btn-remover-ajudante" data-id="${ajudanteId}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#tabelaAjudantes tbody').append(row);

        // Aplicar máscaras nos novos campos (usar setTimeout para garantir que o DOM está pronto)
        setTimeout(function() {
            $(`.valor-ajudante-edit[data-ajudante-id="${ajudanteId}"]`).mask('#.###.##0,00', {reverse: true});
            $(`.valor-repasse-ajudante-edit[data-ajudante-id="${ajudanteId}"]`).mask('#.###.##0,00', {reverse: true});

            // Recalcular totais quando os valores mudarem
            $(`.valor-ajudante-edit[data-ajudante-id="${ajudanteId}"], .valor-repasse-ajudante-edit[data-ajudante-id="${ajudanteId}"]`)
                .on('blur change keyup', function() {
                    calcularValorTotalServico();
                });
        }, 100);

        calcularValorTotalServico();
    }
    function atualizarObservacoesAuto() {
        const origemOption = $('#cliente_origem option:selected');
        const destinoOption = $('#cliente_destino option:selected');

        const apelidoOrigem = origemOption.data('apelido');
        const apelidoDestino = destinoOption.data('apelido');

        // Conta quantos ajudantes foram adicionados
        const qtdAjudantes = $('#tabelaAjudantes tbody tr').not('.sem-registros').length;

        $('#observacoes').val(''); // limpa antes

        if (apelidoOrigem && apelidoDestino) {
            let texto = `Retirar em [${apelidoOrigem}] e entregar em [${apelidoDestino}]`;

            if (qtdAjudantes > 0) {
                texto += ` com ${qtdAjudantes} ajudante${qtdAjudantes > 1 ? 's' : ''}`;
            }

            texto += '.\n';

            const atual = $('#observacoes').val();

            if (!atual.includes(texto)) {
                $('#observacoes').val(texto + atual);
            }
        }
    }

    function atualizarOS() {
        if (!validarFormulario()) return;

        const observacoes = $('#observacoes').val().trim();

        // Verificar se o campo de observações contém a frase obrigatória
        //if (!observacoes.toLowerCase().includes('retirar em')) {
        //    alert('⚠️ O campo ade observações está incompleto.\nÉ necessário conter as informações do serviço (Entrega/Retirada) para continuar.');
        //    $('#observacoes').focus();
        //    return;
        //}

        const ordemServicoId = $('#ordem_servico_id').val();
        if (!ordemServicoId) {
            alert("Erro: ID da Ordem de Serviço não encontrado!");
            return;
        }

        const formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('_method', 'PUT');

        // Campos do formulário
        formData.append('ordem_servico_id', ordemServicoId);
        formData.append('empresa_id', $('#empresa').val() || '');
        formData.append('cliente_origem_id', $('#cliente_origem').val() || '');
        formData.append('cliente_destino_id', $('#cliente_destino').val() || '');
        formData.append('motorista_id', $('#motorista').val() || '');
        formData.append('veiculo_id', $('#veiculos').val() || '');
        formData.append('data_servico', $('#data_servico').val() || '');
        formData.append('hora_inicial', $('#hora_inicial').val() || '');
        formData.append('hora_final', $('#hora_final').val() || '');
        formData.append('observacoes', observacoes);
        formData.append('status', $('#status').val() || '');
        formData.append('contratante_tipo', $('#contratante_tipo').val() || '');

        formData.append('valor_motorista', limparFormatoValor($('#valor_motorista').val()) || 0);
        formData.append('valor_perimetro', limparFormatoValor($('#valor_perimetro').val()) || 0);
        formData.append('valor_restricao', limparFormatoValor($('#valor_restricao').val()) || 0);
        formData.append('valor_total', limparFormatoValor($('#valor_total').val()) || 0);
        formData.append('valor_repassado_motorista', limparFormatoValor($('#valor_repassado_motorista').val()) || 0);
        formData.append('valor_repassado_ajudantes', limparFormatoValor($('#valor_repassado_ajudantes').val()) || 0);
        formData.append('valor_repasse_resultado', limparFormatoValor($('#valor_repasse_resultado').val()) || 0);
        formData.append('data_final', $('#data_final').val() || '');


        // Ajudantes - capturar valores dos inputs inline
        const ajudantes = [];
        $('#tabelaAjudantes tbody tr:not(.sem-registros)').each(function () {
            const id = $(this).data('id');
            const valorTexto = $(this).find('.valor-ajudante-edit').val();
            const valorRepasseTexto = $(this).find('.valor-repasse-ajudante-edit').val();
            const valor = limparFormatoValor(valorTexto);
            const valorRepasse = limparFormatoValor(valorRepasseTexto);

            if (id) {
                ajudantes.push({
                    id: id,
                    valor: valor,
                    valor_repassado: valorRepasse
                });
            }
        });

        if (ajudantes.length > 0) {
            formData.append('ajudantes', JSON.stringify(ajudantes));
        }

        // Enviar via fetch
        fetch('/ordemservicos/' + ordemServicoId, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error('Erro ' + response.status + ': ' + text);
                });
            }
            return response.json();
        })
        .then(data => {
            alert('✅ Ordem de serviço atualizada com sucesso!');
            window.location.href = '/ordemservicos';
        })
        .catch(error => {
            console.error('❌ Erro ao atualizar:', error);
            alert('Erro ao atualizar: ' + error.message);
        });
    }

    function validarFormulario() {
        let isValido = true;
        let camposFaltantes = [];

        // Array de campos obrigatórios com seus IDs e nomes de exibição
        const camposObrigatorios = [
            { id: 'empresa', nome: 'Empresa' },
            { id: 'cliente_origem', nome: 'Cliente Origem' },
            { id: 'cliente_destino', nome: 'Cliente Destino' },
            { id: 'data_servico', nome: 'Data do Serviço' },
            { id: 'motorista', nome: 'Motorista' },
            { id: 'veiculos', nome: 'Veículo' }
        ];

        // Verificar cada campo obrigatório
        camposObrigatorios.forEach(campo => {
            if (!$('#' + campo.id).val()) {
                isValido = false;
                camposFaltantes.push(campo.nome);
                $('#' + campo.id).addClass('is-invalid');
            } else {
                $('#' + campo.id).removeClass('is-invalid');
            }
        });

        if (!isValido) {
            let mensagem = 'Por favor, preencha os seguintes campos obrigatórios: ' + camposFaltantes.join(', ');
            alert(mensagem);
            return false;
        }

        // Verificar se o valor total é maior que zero
        const valorTotal = parseFloat(($('#valor_total').val() || '0').replace(/\./g,'').replace(',', '.')) || 0;
        if (valorTotal <= 0) {
            alert('O valor total do serviço deve ser maior que zero');
            $('#valor_total').addClass('is-invalid').focus();
            return false;
        } else {
            $('#valor_total').removeClass('is-invalid');
        }

        return true;
    }

    function limparFormatoValor(valor) {

        // Verifica se o valor é indefinido, nulo ou vazio
        if (valor === undefined || valor === null || valor === '') {
            return 0; // Retorna 0 ou outro valor padrão
        }

        // Se o valor não for string, converte-o para string
        if (typeof valor !== 'string') {
            valor = valor.toString();
        }

        // Remove os pontos (separador de milhar) e substitui a vírgula pelo ponto (separador decimal)
        // Em seguida, converte a string resultante em float
        return parseFloat(valor.replace(/\./g, '').replace(',', '.')) || 0;
    }

    function calcularTempoTotal() {
        var serviceDateStr = $('#data_servico').val();
        var horaInicialStr = $('#hora_inicial').val();
        var horaFinalStr = $('#hora_final').val();

        if (!serviceDateStr || !horaInicialStr || !horaFinalStr) {
            $('#tempo_total').val('');
            return;
        }

        var serviceDate = new Date(serviceDateStr);
        if (isNaN(serviceDate.getTime())) {
            console.error("Data do serviço inválida:", serviceDateStr);
            $('#tempo_total').val('');
            return;
        }

        var startParts = horaInicialStr.split(':');
        var startDate = new Date(serviceDate.getFullYear(), serviceDate.getMonth(), serviceDate.getDate(), parseInt(startParts[0]), parseInt(startParts[1]));

        var finalParts = horaFinalStr.split(':');
        var endDate = new Date(serviceDate.getFullYear(), serviceDate.getMonth(), serviceDate.getDate(), parseInt(finalParts[0]), parseInt(finalParts[1]));

        // Se hora final for menor que a inicial, assume virada de dia
        if (endDate < startDate) {
            endDate.setDate(endDate.getDate() + 1);
        }

        var diffMs = endDate - startDate;
        var diffMinutes = Math.floor(diffMs / 60000);
        var hours = Math.floor(diffMinutes / 60);
        var minutes = diffMinutes % 60;

        var formattedTime = ('0' + hours).slice(-2) + ':' + ('0' + minutes).slice(-2);
        $('#tempo_total').val(formattedTime);
    }

    function calcularValorTotalServico() {
        // Calcular soma dos valores dos ajudantes diretamente dos inputs
        let somaValoresAjudantes = 0;
        let somaRepasseAjudantes = 0;

        $('#tabelaAjudantes tbody tr:not(.sem-registros)').each(function() {
            const valorAjudante = limparFormatoValor($(this).find('.valor-ajudante-edit').val() || '0');
            const valorRepasse = limparFormatoValor($(this).find('.valor-repasse-ajudante-edit').val() || '0');

            somaValoresAjudantes += valorAjudante;
            somaRepasseAjudantes += valorRepasse;
        });

        // Atualizar os campos de total
        $('#valor_ajudantes').val(somaValoresAjudantes.toLocaleString('pt-BR', { minimumFractionDigits: 2 }));
        $('#valor_repassado_ajudantes').val(somaRepasseAjudantes.toLocaleString('pt-BR', { minimumFractionDigits: 2 }));

        const valorMotorista = parseFloat(($('#valor_motorista').val() || '0').replace(/\./g, '').replace(',', '.')) || 0;
        const valorPerimetro = parseFloat(($('#valor_perimetro').val() || '0').replace(/\./g, '').replace(',', '.')) || 0;
        const valorRestricao = parseFloat(($('#valor_restricao').val() || '0').replace(/\./g, '').replace(',', '.')) || 0;
        const valorRepassadoMotorista = parseFloat(($('#valor_repassado_motorista').val() || '0').replace(/\./g, '').replace(',', '.')) || 0;

        const total = somaValoresAjudantes + valorMotorista + valorPerimetro + valorRestricao;
        const repasse = valorRepassadoMotorista + somaRepasseAjudantes;
        const resultado = total - repasse;

        $('#valor_total').val(total.toLocaleString('pt-BR', { minimumFractionDigits: 2 }));
        $('#valor_repasse_resultado').val(resultado.toLocaleString('pt-BR', { minimumFractionDigits: 2 }))
            .css({ color: resultado >= 0 ? 'green' : 'red', 'font-weight': 'bold' });
    }

    // Função auxiliar para formatar valores em moeda brasileira (se necessário)
    function valorTotalFormatado(valor) {
        return valor.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
    }

    function setCurrentHoraFinal() {
        // Obter a data/hora atual
        var now = new Date();

        // Formatar as horas e minutos com dois dígitos (ex: 07, 09)
        var hours = now.getHours().toString().padStart(2, '0');
        var minutes = now.getMinutes().toString().padStart(2, '0');

        // Montar a string no formato "HH:mm"
        var timeValue = hours + ":" + minutes;

        // Atualizar o valor do input
        $('#hora_final').val(timeValue);
    }

    function btnFinalizarOS() {
        const status = $('#status').val();

        if (status === "concluido") {
            validarHorarioRestricao(function (podeContinuar) {

                if (!podeContinuar) return;

                // Tudo certo: exibe o modal de confirmação
                $('#confirmModal').modal('show');

                // Remover eventos antigos para evitar múltiplos disparos
                $('#btnModalConfirm').off('click').on('click', function () {
                    $('#status').val("concluido").trigger('change');
                    $('#confirmModal').modal('hide');
                    FinalizacaoOS();
                });

                $('#btnModalCancel').off('click').on('click', function () {
                    $('#confirmModal').modal('hide');
                    console.log("Operação cancelada pelo usuário.");
                });
            });
        } else {
            AlterarStatusOS();
        }
    }

    function FinalizacaoOS(){
        $.ajax({
            url: "{{ route('ordemservicos.finalizar', ['ordemservico' => $ordemServico->id]) }}",
            method: "PATCH",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                window.location.href = "{{ route('ordemservicos.index') }}";
            },
            error: function(xhr) {
                console.error("Erro:", xhr.responseText);
                alert("Erro ao finalizar a OS. Por favor, tente novamente.");
            }
        });
    }

    function AlterarStatusOS() {
        const ordemServicoId = {{ $ordemServico->id ?? 'null' }};
        if (!ordemServicoId || ordemServicoId === null) {
            alert("ID da Ordem de Serviço não encontrado!");
            return;
        }

        // Obtenha o token CSRF do meta tag
        const token = $('meta[name="csrf-token"]').attr('content');

        // URL completa sem substituição - evita problemas
        const url = "{{ route('ordemservicos.alterarstatusordem', ['ordemservico' => $ordemServico->id ?? 0]) }}";

        // Dados a serem enviados
        const formData = new FormData();
        formData.append('_token', token);
        formData.append('status', $('#status').val());

        // Use o objeto XMLHttpRequest nativo em vez de jQuery
        const xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('X-CSRF-TOKEN', token);

        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                window.location.href = "{{ route('ordemservicos.index') }}";
            } else {
                console.error('Erro:', xhr.responseText);
                alert("Erro ao atualizar o status da OS.");
            }
        };

        xhr.onerror = function() {
            alert("Erro na conexão ao tentar atualizar status.");
        };

        xhr.send(formData);
    }

    var urlAjudantes = "{{ route('entregadores.entregadores') }}";

    function validarHorarioRestricao(callback) {
        const horaFinal = $('#hora_final').val();
        const valorRestricao = limparFormatoValor($('#valor_restricao').val());

        // Se não há hora, segue normalmente
        if (!horaFinal) {
            callback(true);
            return;
        }

        const [hora, minuto] = horaFinal.split(':').map(Number);
        const horaLimite = 10;

        const passouDoLimite = hora > horaLimite || (hora === horaLimite && minuto > 0);

        // Caso horário seja superior a 10h E taxa está zerada
        if (passouDoLimite && valorRestricao <= 0) {
            //const modalRestricao = new bootstrap.Modal(document.getElementById('modalTaxaRestricao'));

            $('#modalTaxaRestricao').modal('show');

            $('#aceitarTaxa').off('click').on('click', function () {
                $('#modalTaxaRestricao').modal('hide');

                // Rola até o campo e foca
                $('html, body').animate({
                    scrollTop: $('#valor_restricao').offset().top - 100
                }, 500, function () {
                    $('#valor_restricao').focus().select();
                });

                // ⚠️ NÃO chama callback — usuário precisará clicar novamente no botão depois
            });

            // Clicou em "Não, continuar mesmo assim"
            $('#recusarTaxa').off('click').on('click', function () {
                $('#modalTaxaRestricao').modal('hide');
                callback(true); // ✅ continua normalmente
            });

        } else {
            callback(true); // ✅ tudo certo, segue normalmente
        }
    }

    function toggleCamposFinalizacao(ativo)
    {
        if (ativo) {
            $('#campo_data_final, #campo_hora_final').css({
                visibility: 'visible',
                height: 'auto',
                overflow: 'visible'
            });
        } else {
            $('#campo_data_final, #campo_hora_final').css({
                visibility: 'hidden',
                height: '0',
                overflow: 'hidden'
            });

            $('#data_final').val('');
            $('#hora_final').val('');
            $('#tempo_total').val('');
        }
    }

    // Evento: Abrir modal de cliente avulso e limpar formulário
    $('#modalClienteAvulsoDestino').on('show.bs.modal', function () {
        $('#formClienteAvulsoDestino')[0].reset(); // limpa os campos do form
        limparDetalhesClientesAvulso(); // por garantia, também limpa manualmente
    });

    $('#btnSalvarClienteAvulsoDestino').on('click', function () {
        const form = $('#formClienteAvulsoDestino');
        const btnSalvar = $(this);

        // Validação dos campos obrigatórios
        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }

        const dados = {
            nome_avulso: $.trim(form.find('[name="nome_avulso"]').val()),
            telefone_avulso: $.trim(form.find('[name="telefone_avulso"]').val()),
            endereco_avulso: $.trim(form.find('[name="endereco_avulso"]').val()),
            numero_avulso: $.trim(form.find('[name="numero_avulso"]').val()),
            _token: "{{ csrf_token() }}"
        };

        // Desabilita o botão durante o processamento
        btnSalvar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Salvando...');

        $.post("/clientes/avulso", dados)
        .done(function (resposta) {
            console.log('Resposta do servidor:', resposta);

            if (resposta.exists) {
                if (confirm(`Já existe um cliente neste endereço:\n\n${resposta.cliente.nome} - ${resposta.cliente.telefone}\n\nDeseja usá-lo?`)) {
                    adicionarClienteDestino(resposta.cliente);
                    fecharModalClienteAvulso();
                } else {
                    alert("Cadastro cancelado. Edite os dados para cadastrar um novo.");
                }
            } else if (resposta.created) {
                adicionarClienteDestino(resposta.cliente);
                fecharModalClienteAvulso();

                // Exibe mensagem de sucesso
                if ($('#alert-container').length) {
                    $('#alert-container').html(
                        '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                        'Cliente cadastrado com sucesso!' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>'
                    );
                }
            }
        })
        .fail(function (xhr) {
            console.error('Erro ao salvar:', xhr);
            let mensagem = 'Erro ao salvar cliente avulso.';

            if (xhr.responseJSON && xhr.responseJSON.message) {
                mensagem = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                mensagem = xhr.responseText;
            }

            alert(mensagem);
        })
        .always(function() {
            // Reabilita o botão
            btnSalvar.prop('disabled', false).html('Salvar');
        });
    });

</script>


@endpush
