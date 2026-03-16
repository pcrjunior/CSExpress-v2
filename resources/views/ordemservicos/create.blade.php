@extends('layouts.app')

@section('title', 'Criar Ordem de Serviço')

@section('content')

<div class="container-fluid py-4">

    <!-- Card de título -->

    <div class="row mb-2">
        <div class="col-12">
            <div class="card shadow-xs">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0"><i class="fas fa-motorcycle me-2"></i>Ordem de Serviço</h5>
                </div>

            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-12">
            <form id="formOS">
                <!-- Seção: Informações Básicas -->
                <div class="card mb-2">
                    <div class="card-header bg-header-blue">
                        Informações Básicas
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <label for="empresa" class="form-label required">Empresa</label>
                                <select class="form-select" id="empresa" name="empresa_id" required>
                                    @forelse($empresas as $empresa)
                                        <option value="{{ $empresa->id }}" {{ $loop->first ? 'selected' : '' }}>
                                            {{ $empresa->nome }}
                                        </option>
                                    @empty
                                        <option value="">Nenhuma empresa encontrada</option>
                                    @endforelse
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="numero_os" class="form-label">Número OS</label>
                                <input type="text" class="form-control" id="numero_os" name="numero_os" value="{{ old('numero_os', $numero_os) }}" readonly>
                            </div>

                            <div class="col-md-3">
                                <label for="data_criacao" class="form-label">Data de Criação</label>
                                <input type="text" class="form-control" id="data_criacao" name="data_criacao" value="{{ old('data_criacao', $datacriacao) }}" readonly>
                            </div>

                            <div class="col-md-3">
                                <label for="contratante_tipo" class="form-label required">Cliente Contratante</label>
                                <select class="form-select" id="contratante_tipo" name="contratante_tipo" required>
                                    <option value="">Selecione</option>
                                    <option value="origem" {{ old('contratante_tipo') == 'origem' ? 'selected' : '' }}>Cliente de Origem</option>
                                    <option value="destino" {{ old('contratante_tipo') == 'destino' ? 'selected' : '' }}>Cliente de Destino</option>
                                </select>
                            </div>
                        </div>

                        <!-- Clientes Origem e Destino -->
                        <div class="row">
                            <!-- Cliente Origem -->
                            <div class="col-md-6">
                                <div class="card mb-3 shadow-sm">
                                    <div class="card-header bg-header-blue text-white">
                                        Cliente Origem
                                    </div>

                                    <div class="card-body">

                                        <!-- Busca Cliente -->
                                        <div class="mb-3">
                                            <div class="input-group">
                                                <input type="text"
                                                    class="form-control"
                                                    id="busca_cliente_origem"
                                                    name="busca_cliente_origem"
                                                    placeholder="Buscar por apelido do cliente">

                                                <button class="btn btn-outline-secondary"
                                                        type="button"
                                                        id="btn_buscar_origem">
                                                    <i class="fas fa-search"></i>
                                                </button>

                                                <button class="btn btn-outline-primary"
                                                        type="button"
                                                        id="btn_recarregar_clientes_origem">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Select Cliente -->
                                        <div class="mb-3">
                                            <label class="form-label">Cliente</label>
                                            <div class="input-group">
                                                <select class="form-select"
                                                        id="cliente_origem"
                                                        name="cliente_origem_id"
                                                        required>
                                                    <option value="">Selecione o cliente de origem</option>
                                                </select>

                                                <button type="button"
                                                        class="btn btn-outline-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalClienteAvulso_Origem"
                                                        title="Cadastrar cliente avulso">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Endereço -->
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="cep_origem" class="form-label">CEP</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="cep_origem"
                                                        name="cep_origem"
                                                        readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-7">
                                                <div class="mb-3">
                                                    <label for="endereco_origem" class="form-label">
                                                        Endereço
                                                    </label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="endereco_origem"
                                                        name="endereco_origem"
                                                        readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label for="numero_origem" class="form-label">Nº</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="numero_origem"
                                                        name="numero_origem"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Cidade -->
                                        <div class="mb-3">
                                            <label for="cidade_uf_origem" class="form-label">
                                                Cidade/UF
                                            </label>
                                            <input type="text"
                                                class="form-control"
                                                id="cidade_uf_origem"
                                                name="cidade_uf_origem"
                                                readonly>
                                        </div>


                                        <!-- Select Responsável -->
                                        <div class="mb-3">
                                            <label class="form-label">Responsável</label>
                                            <div class="input-group">
                                                <select name="responsavel_origem_id"
                                                        id="responsavel_origem_id"
                                                        class="form-select">
                                                    <option value="">Selecione o responsável</option>
                                                </select>

                                                <button type="button"
                                                        class="btn btn-outline-primary"
                                                        id="btnNovoResponsavelOrigem"
                                                        title="Cadastrar novo responsável">
                                                    <i class="fas fa-user-plus"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Telefone e Email na mesma linha -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="telefone_origem" class="form-label">
                                                        Telefone
                                                    </label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="telefone_origem"
                                                        name="telefone_origem"
                                                        readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="email_origem" class="form-label">
                                                        E-mail
                                                    </label>
                                                    <input type="email"
                                                        class="form-control"
                                                        id="email_origem"
                                                        name="email_origem"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card mb-3 shadow-sm">
                                    <div class="card-header bg-header-blue text-white">
                                        Cliente Destino
                                    </div>

                                    <div class="card-body">

                                        <!-- Busca Cliente -->
                                        <div class="mb-3">
                                            <div class="input-group">
                                                <input type="text"
                                                    class="form-control"
                                                    id="busca_cliente_destino"
                                                    name="busca_cliente_destino"
                                                    placeholder="Buscar por apelido do cliente">

                                                <button class="btn btn-outline-secondary"
                                                        type="button"
                                                        id="btn_buscar_destino">
                                                    <i class="fas fa-search"></i>
                                                </button>

                                                <button class="btn btn-outline-primary"
                                                        type="button"
                                                        id="btn_recarregar_clientes_destino">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Select Cliente -->
                                        <div class="mb-3">
                                            <label class="form-label">Cliente</label>
                                            <div class="input-group">
                                                <select class="form-select"
                                                        id="cliente_destino"
                                                        name="cliente_destino_id"
                                                        required>

                                                    <option value="">Selecione o cliente de destino</option>

                                                    @forelse($clientes as $cliente)
                                                        <option value="{{ $cliente->id }}"
                                                                data-apelido="{{ $cliente->apelido }}">
                                                            {{ $cliente->nome }} {{ $cliente->apelido ? "({$cliente->apelido})" : '' }}
                                                        </option>
                                                    @empty
                                                        <option value="">Nenhum cliente encontrado</option>
                                                    @endforelse

                                                </select>

                                                <button type="button"
                                                        class="btn btn-outline-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalClienteAvulso_Destino"
                                                        title="Cadastrar cliente avulso">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>


                                        <!-- Endereço -->
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="cep_destino" class="form-label">CEP</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="cep_destino"
                                                        name="cep_destino"
                                                        readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-7">
                                                <div class="mb-3">
                                                    <label for="endereco_destino" class="form-label">
                                                        Endereço
                                                    </label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="endereco_destino"
                                                        name="endereco_destino"
                                                        readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label for="numero_destino" class="form-label">Nº</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="numero_destino"
                                                        name="numero_destino"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Cidade -->
                                        <div class="mb-3">
                                            <label for="cidade_uf_destino" class="form-label">
                                                Cidade/UF
                                            </label>
                                            <input type="text"
                                                class="form-control"
                                                id="cidade_uf_destino"
                                                name="cidade_uf_destino"
                                                readonly>
                                        </div>

                                        <!-- Select Responsável -->
                                        <div class="mb-3">
                                            <label class="form-label">Responsável</label>
                                            <div class="input-group">
                                                <select name="responsavel_destino_id"
                                                        id="responsavel_destino_id"
                                                        class="form-select">
                                                    <option value="">Selecione o responsável</option>
                                                </select>

                                                <button type="button"
                                                        class="btn btn-outline-primary"
                                                        id="btnNovoResponsavelDestino"
                                                        title="Cadastrar novo responsável">
                                                    <i class="fas fa-user-plus"></i>
                                                </button>
                                            </div>
                                        </div>


                                        <!-- Telefone e Email na mesma linha -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="telefone_destino" class="form-label">
                                                        Telefone
                                                    </label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="telefone_destino"
                                                        name="telefone_destino"
                                                        readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="email_destino" class="form-label">
                                                        E-mail
                                                    </label>
                                                    <input type="email"
                                                        class="form-control"
                                                        id="email_destino"
                                                        name="email_destino"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Seção: Informações do Serviço -->
                <div class="card mb-2">
                    <div class="card-header bg-header-blue">
                        Informações do Serviço
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="data_servico" class="form-label required">Data do Serviço</label>
                                <input type="date" class="form-control" id="data_servico" name="data_servico" >
                            </div>
                            <div class="col-md-3">
                                <label for="hora_inicial" class="form-label">Hora Inicial</label>
                                <input type="time" class="form-control" id="hora_inicial" name="hora_inicial">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seção: Motorista e Veículo -->
                <div class="card mb-2">
                    <div class="card-header bg-header-blue">
                        Motorista e Veículo
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="motorista" class="form-label required">Motorista</label>
                                <select class="form-select select2" id="motorista" name="motorista_id" required>
                                    <option value="">Digite para buscar motorista</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="veiculo" class="form-label required">Veículo</label>
                                <select id="veiculos" name="veiculos[]" class="form-select">
                                    <option value="">Selecione um motorista para carregar os veículos</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seção: Entregadores/Ajudantes -->
                <div class="card mb-2">
                    <div class="card-header bg-header-blue d-flex justify-content-between align-items-center">
                        <span>Entregadores/Ajudantes</span>
                        <button type="button" class="btn btn-success btn-sm py-0 px-1" id="btnAddAjudante">
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
                                        <th>Valor</th>
                                        <th>Valor Repasse</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Os ajudantes serão adicionados aqui dinamicamente -->
                                    <tr class="sem-registros">
                                        <td colspan="5" class="text-center">Nenhum ajudante adicionado</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Seção: Valores do Serviço -->
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

                <!-- Seção: Observações -->
                <div class="card mb-2">
                    <div class="card-header bg-header-blue">
                        Observações
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <textarea class="form-control" id="observacoes" name="observacoes" rows="2" placeholder="Informe aqui observações relevantes sobre o serviço"></textarea>
                        </div>
                           <div class="d-flex justify-content-end gap-2 mb-4">
                                <button type="button" class="btn btn-secondary" id="btnCancelar">Cancelar</button>
                                <button type="submit" class="btn btn-primary" id="btnSalvar">Salvar Ordem de Serviço</button>
                            </div>
                    </div>
                </div>
            </form>
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


<!-- Modal de Erro -->
<div class="modal fade" id="modalErro" tabindex="-1" aria-labelledby="modalErroLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-danger">
        <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="modalErroLabel">Erro ao salvar Ordem de Serviço</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body" id="modalErroBody">
            <!-- Mensagem de erro será injetada aqui -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        </div>
        </div>
    </div>
</div>

<!-- Modal Novo Cliente Avulso - Destino -->
<div class="modal fade" id="modalClienteAvulso_Destino" tabindex="-1" aria-labelledby="modalClienteAvulsoDestinoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalClienteAvulsoDestinoLabel">Cadastrar Cliente Avulso (Destino)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form id="formClienteAvulso_Destino">
                    <div class="mb-3">
                        <label class="form-label">Responsável (Nome)</label>
                        <input type="text" class="form-control" name="nome_avulso_destino" id="nome_avulso_destino" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" class="form-control" name="telefone_avulso_destino" id="telefone_avulso_destino" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Endereço</label>
                        <input type="text" class="form-control" name="endereco_avulso_destino" id="endereco_avulso_destino" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nº</label>
                        <input type="text" class="form-control" name="numero_avulso_destino" id="numero_avulso_destino" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary" id="btnSalvarClienteAvulso_Destino">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Novo Cliente Avulso - Origem -->
<div class="modal fade" id="modalClienteAvulso_Origem" tabindex="-1" aria-labelledby="modalClienteAvulsoOrigemLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalClienteAvulsoOrigemLabel">Cadastrar Cliente Avulso (Origem)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form id="formClienteAvulso_Origem">
                <div class="mb-3">
                    <label class="form-label">Responsável (Nome)</label>
                    <input type="text" class="form-control" name="nome_avulso_origem" id="nome_avulso_origem" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Telefone</label>
                    <input type="text" class="form-control" name="telefone_avulso_origem" id="telefone_avulso_origem" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Endereço</label>
                    <input type="text" class="form-control" name="endereco_avulso_origem" id="endereco_avulso_origem" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nº</label>
                    <input type="text" class="form-control" name="numero_avulso_origem" id="numero_avulso_origem" required>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary" id="btnSalvarClienteAvulso_Origem">Salvar</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal de Confirmação de Cliente Existente -->
<div class="modal fade" id="modalConfirmarCliente" tabindex="-1" aria-labelledby="modalConfirmarClienteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalConfirmarClienteLabel">Cliente já cadastrado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body" id="confirmarClienteBody">
                <!-- Conteúdo dinâmico aqui -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não</button>
                <button type="button" class="btn btn-primary" id="btnConfirmarCliente">Sim</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal: Cliente Cadastrado -->
<div class="modal fade" id="modalClienteCadastrado" tabindex="-1" aria-labelledby="modalClienteCadastradoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalClienteCadastradoLabel">Cliente cadastrado com sucesso!</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body" id="clienteCadastradoBody">
                <!-- Conteúdo dinâmico via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Cadastro Responsavel -->
<div class="modal fade" id="modalNovoResponsavel"
                        tabindex="-1"
                        aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Novo Responsável</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="cliente_id_modal">

                <div class="mb-3">
                    <label>Nome</label>
                    <input type="text" id="novo_nome" name="novo_nome" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Telefone</label>
                    <input
                        type="text"
                        class="form-control @error('novo_telefone') is-invalid @enderror"
                        id="novo_telefone"
                        name="novo_telefone"
                        placeholder="(99) 99999-9999 ou (99) 9999-9999"
                        maxlength="15"
                        autocomplete="off">
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" id="novo_email" class="form-control">
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary" id="btnSalvarNovoResponsavel">Salvar</button>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<!-- Bootstrap e jQuery -->
<script src="{{ asset('js/jquery/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('js/jquery/mask/1.14.16/jquery/jquery.mask.min.js') }}"></script>
<script src="{{ asset('js/bootstrap/5.3.3/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/os_ajudante.js') }}"></script>
<script src="{{ asset('js/os_cliente.js') }}"></script>
<script src="{{ asset('js/os_veiculo.js') }}"></script>
<script src="{{ asset('js/select2/4.0.13/dist/js/select2.min.js') }}"></script>

<link href="{{ asset('css/select2/4.0.13/dist/css/select2.min.css') }}" rel="stylesheet">

<script>
    var urlClientesDados = "{{ route('clientes.dados', ['clienteId' => 'CLIENTE_ID_PLACEHOLDER']) }}";
    var urlAjudantes = "{{ route('ajudantes.index') }}";
    var urlVeiculos = "{{ route('entregadores.veiculos', ['id' => 'MOTORISTA_ID_PLACEHOLDER']) }}";

    $(document).ready(function() {
        const dataAtual = new Date();
        $('#hora_inicial').val(dataAtual.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' }));
        $('#data_criacao').val(dataAtual.toLocaleDateString('pt-BR'));
        $('#data_servico').val(dataAtual.toISOString().split('T')[0]);

        // Aplicar máscaras
        const camposComMascara = [
            '#valor_ajudantes', '#valor_motorista', '#valor_perimetro', '#valor_restricao', '#valor_total',
            '#valor_repassado_ajudantes', '#valor_repassado_motorista', '#valor_repasse_resultado',
            '#valor_repassado_aj', '#valor_ajudante'
        ];
        camposComMascara.forEach(function(campo) {
            $(campo).mask('#.###.##0,00', {reverse: true});
        });

        // Eventos para calculo
        $(camposComMascara.join(',')).on('blur change', calcularValorTotalServico);

        $('#responsavel_origem_id').change(function () {

            let selected = $(this).find(':selected');

            $('#telefone_origem').val(selected.data('telefone') || '');
            $('#email_origem').val(selected.data('email') || '');

        });

        $('#cliente_origem').change(function () {

            const clienteId = $(this).val();
            const select = $('#responsavel_origem_id');

            // Se não tiver cliente selecionado
            if (!clienteId) {

                limparDetalhesClientes('origem');

                select.empty()
                    .append('<option value="">Selecione o responsável</option>');

                $('#telefone_origem').val('');
                $('#email_origem').val('');

                return;
            }

            // Carrega detalhes do cliente (endereço etc)
            CarregarDetalhesClientes('origem', clienteId);

            // Limpa responsável e contatos
            select.empty()
                .append('<option value="">Selecione o responsável</option>');

            $('#telefone_origem').val('');
            $('#email_origem').val('');

            // Carrega responsáveis
            $.get(`/clientes/${clienteId}/responsaveis`, function (data) {

                data.forEach(function (resp) {
                    select.append(`
                        <option value="${resp.id}"
                            data-telefone="${resp.telefone || ''}"
                            data-email="${resp.email || ''}">
                            ${resp.nome}
                        </option>
                    `);
                });

            })
            .fail(function (xhr) {
                console.error(xhr.responseText);
                alert('Erro ao carregar responsáveis. Verifique o console.');
            });

        });

        $('#cliente_destino').change(function() {
            const clienteId = $(this).val();
            const select = $('#responsavel_destino_id');

            if (!clienteId) {
                limparDetalhesClientes('destino');
                select.empty().append('<option value="">Selecione o responsável</option>');
                $('#telefone_destino').val('');
                $('#email_destino').val('');
                return;
            }

            CarregarDetalhesClientes('destino', clienteId);

            select.empty().append('<option value="">Selecione o responsável</option>');
            $('#telefone_destino').val('');
            $('#email_destino').val('');

            $.get(`/clientes/${clienteId}/responsaveis`, function (data) {
                data.forEach(function (resp) {
                    select.append(`
                        <option value="${resp.id}"
                            data-telefone="${resp.telefone || ''}"
                            data-email="${resp.email || ''}">
                            ${resp.nome}
                        </option>
                    `);
                });
            })
            .fail(function (xhr) {
                console.error(xhr.responseText);
                alert('Erro ao carregar responsáveis. Verifique o console.');
            });
        });

        $('#motorista').on('change', function() {
            const motoristaId = $(this).val();
            if (motoristaId) carregarVeiculosMotorista(motoristaId);
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

            $('#modalAjudante').modal('show');

        });

        $('#btnConfirmarAjudante').on('click', function() {
            const ajudanteId = $('#ajudante').val();
            if (!ajudanteId) return alert('Por favor, selecione um ajudante.');

            const nome = $('#ajudante option:selected').text();
            const telefone = $('#ajudante option:selected').data('telefone');
            const valor = $('#valor_ajudante').val();
            const valor_repassado_aj = $('#valor_repassado_aj').val();

            adicionarAjudanteTabela(ajudanteId, nome, telefone, valor, valor_repassado_aj);
            $('#modalAjudante').modal('hide');
            atualizarObservacoesAuto();
        });

        $('#btn_buscar_origem').on('click', function() {
            const apelido = $('#busca_cliente_origem').val().trim();
            if (apelido) buscarClientesPorApelido(apelido, 'origem');
        });

        $('#btn_buscar_destino').on('click', function() {
            const apelido = $('#busca_cliente_destino').val().trim();
            if (apelido) buscarClientesPorApelido(apelido, 'destino');
        });

        $('#busca_cliente_origem').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#btn_buscar_origem').click();
            }
        });

        $('#busca_cliente_destino').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#btn_buscar_destino').click();
            }
        });

        $('#motorista').select2({
            theme: 'bootstrap-5',
            placeholder: "Digite o nome do motorista...",
            allowClear: true,
            minimumInputLength: 2,
            width: '100%',
            language: "pt-BR",
            ajax: {
                url: "{{ route('motoristas.buscar') }}",
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return { term: params.term };
                },
                processResults: function (data) {
                    return { results: data.results };
                },
                cache: true
            }
        });

        $('#btnSalvar').on('click', function(e){
            e.preventDefault();
            salvarOS();
        });

        $('#btnCancelar').on('click', function(e){
            e.preventDefault();
            CancelarOS();
        });

        $('#btn_recarregar_clientes_origem').on('click', function() {
            limparDetalhesClientes('origem');
            recarregarListaClientes('origem');
        });

        $('#btn_recarregar_clientes_destino').on('click', function() {
            limparDetalhesClientes('destino');
            recarregarListaClientes('destino');
        });

        $('#cliente_origem, #cliente_destino').on('change', atualizarObservacoesAuto);

        buscar_usuario_criado();


        // Previne múltiplos listeners
        $('#modalClienteAvulso_Origem, #modalClienteAvulso_Destino').off();

        // ============================================
        // MODAL ORIGEM - Eventos
        // ============================================

        $('#modalClienteAvulso_Origem').on('show.bs.modal', function () {
            console.log('Abrindo modal ORIGEM');
            $('#formClienteAvulso_Origem')[0].reset();
            limparDetalhesClientesAvulso();
        });

        $('#modalClienteAvulso_Origem').on('hidden.bs.modal', function () {
            console.log('Modal ORIGEM fechado');
            forcarLimpezaModal();
        });

        // Botão abrir modal ORIGEM
        $('button[data-bs-target="#modalClienteAvulso_Origem"]').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            abrirModalSeguro('modalClienteAvulso_Origem');
        });

        // Botão salvar ORIGEM
        $('#btnSalvarClienteAvulso_Origem').off('click').on('click', function () {
            salvarClienteAvulso('origem');
        });

        // ============================================
        // MODAL DESTINO - Eventos
        // ============================================

        $('#modalClienteAvulso_Destino').on('show.bs.modal', function () {
            console.log('Abrindo modal DESTINO');
            $('#formClienteAvulso_Destino')[0].reset();
            limparDetalhesClientesAvulso();
        });

        $('#modalClienteAvulso_Destino').on('hidden.bs.modal', function () {
            console.log('Modal DESTINO fechado');
            forcarLimpezaModal();
        });

        // Botão abrir modal DESTINO
        $('button[data-bs-target="#modalClienteAvulso_Destino"]').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            abrirModalSeguro('modalClienteAvulso_Destino');
        });

        // Botão salvar DESTINO
        $('#btnSalvarClienteAvulso_Destino').off('click').on('click', function () {
            salvarClienteAvulso('destino');
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('btnLimparCliente');
        if (btn) {
            btn.addEventListener('click', btnLimparCliente);
        }
    });

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


    function parseValor(valor) {
        if (!valor) return 0;
        return parseFloat(valor.toString().replace(/\./g, '').replace(',', '.')) || 0;
    }

    function calcularValorTotalServico() {
        const ajud = parseValor($('#valor_ajudantes').val());
        const mot = parseValor($('#valor_motorista').val());
        const peri = parseValor($('#valor_perimetro').val());
        const rest = parseValor($('#valor_restricao').val());
        const repMot = parseValor($('#valor_repassado_motorista').val());
        const repAjud = parseValor($('#valor_repassado_ajudantes').val());

        const total = ajud + mot + peri + rest;
        const repasse = repMot + repAjud;
        const resultado = total - repasse;

        $('#valor_total').val(total.toLocaleString('pt-BR', { minimumFractionDigits: 2 }));
        $('#valor_repasse_resultado').val(resultado.toLocaleString('pt-BR', { minimumFractionDigits: 2 }))
            .css({ color: resultado >= 0 ? 'green' : 'red', 'font-weight': 'bold' });
    }

    function salvarOS() {
        if (!validarFormulario()) return;

        const ajudantes = [];
        $('#tabelaAjudantes tbody tr').not('.sem-registros').each(function() {
            ajudantes.push({
                id: $(this).data('id'),
                valor: parseValor($(this).find('td:nth-child(3)').text()),
                valor_repassado_ajudante: parseValor($(this).find('td:nth-child(4)').text())
            });
        });

        const dadosOS = {
            empresa_id: $('#empresa').val(),
            cliente_origem_id: $('#cliente_origem').val(),
            cliente_destino_id: $('#cliente_destino').val(),
            motorista_id: $('#motorista').val(),
            veiculo_id: $('#veiculos').val(),
            ajudantes: ajudantes,
            data_servico: $('#data_servico').val(),
            hora_inicial: $('#hora_inicial').val(),
            hora_final: $('#hora_final').val(),
            tempo_total: $('#tempo_total').val(),
            valor_ajudantes: parseValor($('#valor_ajudantes').val()),
            valor_motorista: parseValor($('#valor_motorista').val()),
            valor_perimetro: parseValor($('#valor_perimetro').val()),
            valor_restricao: parseValor($('#valor_restricao').val()),
            valor_total: parseValor($('#valor_total').val()),
            observacoes: $('#observacoes').val(),
            created_by: $('#usuario_criador').val(),
            contratante_tipo: $('#contratante_tipo').val(),
            valor_repassado_motorista: parseValor($('#valor_repassado_motorista').val()),
            valor_repassado_ajudantes: parseValor($('#valor_repassado_ajudantes').val()),
            valor_repasse_resultado: parseValor($('#valor_repasse_resultado').val()),
            _token: "{{ csrf_token() }}"
        };

        $.post("{{ route('ordemservicos.store') }}", dadosOS)
            .done(function(response) {
                $('#alert-container').html(`<div class="alert alert-success alert-dismissible fade show" role="alert">OS salva com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`);
                CancelarOS();
            })
            .fail(function(xhr) {
                let mensagemErro = xhr.status === 422 ? '<ul>' + Object.values(xhr.responseJSON.errors).map(err => `<li>${err}</li>`).join('') + '</ul>' : xhr.responseText;
                $('#modalErroBody').html(mensagemErro);
                new bootstrap.Modal('#modalErro').show();
            });
    }

    function validarFormulario() {
        let isValido = true;
        let campos = ['empresa', 'contratante_tipo', 'cliente_origem', 'cliente_destino', 'data_servico', 'motorista', 'veiculos'];
        let faltantes = [];

        campos.forEach(function(campo) {
            const el = $('#' + campo);
            if (!el.val()) {
                el.addClass('is-invalid');
                faltantes.push(el.prev('label').text() || campo);
                isValido = false;
            } else {
                el.removeClass('is-invalid');
            }
        });

        if (!isValido) {
            alert('Por favor, preencha: ' + faltantes.join(', '));
            return false;
        }

        const total = parseValor($('#valor_total').val());
        if (total <= 0) {
            alert('O valor total do serviço deve ser maior que zero');
            $('#valor_total').addClass('is-invalid').focus();
            return false;
        }

        return true;
    }

    function buscar_usuario_criado() {
        const usuario = "{{ auth()->user()->name }}";
        if (!usuario) window.location.href = "{{ route('logout') }}";
        $('#usuario_criador').val(usuario);
    }

    function CancelarOS() {
        $('#formOS')[0].reset();
        limparDetalhesClientes('origem');
        limparDetalhesClientes('destino');
        recarregarListaClientes('origem');
        recarregarListaClientes('destino');
        $('#veiculos').html('<option value="">Selecione um motorista para carregar os veículos</option>');
        $('#tabelaAjudantes tbody').html(`<tr class="sem-registros"><td colspan="4" class="text-center">Nenhum ajudante adicionado</td></tr>`);
        $('#ajudante, #valor_ajudante').val('');
        $('#busca_cliente_origem').focus();
    }

    function buscarClientesPorApelido(apelido, tipoCliente) {
        $.get("/clientes/busca_por_apelido", { apelido: apelido }, function(response) {
            const selectId = tipoCliente === 'origem' ? 'cliente_origem' : 'cliente_destino';
            const select = $('#' + selectId);

            select.empty().append('<option value="">Selecione o cliente</option>');

            if (response.length) {
                response.forEach(cliente => {
                    select.append(
                        `<option value="${cliente.id}" data-apelido="${cliente.apelido}">
                            ${cliente.nome} ${cliente.apelido ? `(${cliente.apelido})` : ''}
                        </option>`
                    );
                });
            } else {
                select.append('<option value="">Nenhum cliente encontrado</option>');
            }
        }).fail(function(xhr) {
            alert("Erro ao buscar clientes: " + xhr.responseText);
        });
    }


    function recarregarListaClientes(tipo) {
        const selectId = tipo === 'origem' ? 'cliente_origem' : 'cliente_destino';
        const select = $('#' + selectId);

        select.html('<option value="">Carregando clientes...</option>');

        $.get("/clientes/listar_todos", function(response) {
            select.empty().append('<option value="">Selecione o cliente</option>');

            response.forEach(cliente => {
                select.append(
                    `<option value="${cliente.id}" data-apelido="${cliente.apelido}">
                        ${cliente.nome} ${cliente.apelido ? `(${cliente.apelido})` : ''}
                    </option>`
                );
            });
        }).fail(function(xhr) {
            select.html('<option value="">Erro ao carregar. Tente novamente.</option>');
        });
    }


    var urlAjudantes = "{{ route('entregadores.entregadores') }}";


    // Função para mostrar alertas de sucesso
    function mostrarAlertaSucesso(mensagem) {
        if ($('#alert-container').length) {
            $('#alert-container').html(
                '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                mensagem +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                '</div>'
            );

            // Auto-fechar após 3 segundos
            setTimeout(function() {
                $('#alert-container .alert').fadeOut();
            }, 3000);
        }
    }


    // ============================================
    // FUNÇÕES AUXILIARES
    // ============================================

    // Função centralizada para limpar backdrops
    function limparBackdropCompleto() {
        setTimeout(function() {
            // Remove TODOS os backdrops (inclusive múltiplos acumulados)
            $('.modal-backdrop').remove();

            // Restaura o body
            $('body').removeClass('modal-open');
            $('body').css({
                'overflow': '',
                'padding-right': ''
            });
        }, 150);
    }


    /**
     * Abre o modal de forma segura, garantindo limpeza prévia
     */
    function abrirModalSeguro(modalId) {
        console.log('Tentando abrir modal:', modalId);

        // 1. Força limpeza completa antes de abrir
        forcarLimpezaModal();

        // 2. Aguarda um momento para garantir limpeza
        setTimeout(function() {
            const modalElement = document.getElementById(modalId);

            if (!modalElement) {
                console.error('Modal não encontrado:', modalId);
                return;
            }

            // 3. Destroi instância existente se houver
            const instanciaExistente = bootstrap.Modal.getInstance(modalElement);
            if (instanciaExistente) {
                console.log('Destruindo instância existente');
                instanciaExistente.dispose();
            }

            // 4. Cria nova instância e abre
            console.log('Criando nova instância do modal');
            const novaInstancia = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false
            });

            novaInstancia.show();
            console.log('Modal aberto com sucesso');
        }, 100);
    }

    /**
     * Fecha o modal de forma segura
     */
    function fecharModalSeguro(modalId) {
        console.log('Fechando modal:', modalId);

        const modalElement = document.getElementById(modalId);
        if (!modalElement) return;

        const instancia = bootstrap.Modal.getInstance(modalElement);
        if (instancia) {
            instancia.hide();
        }

        // Aguarda o evento hidden.bs.modal fazer a limpeza
    }

    /**
     * Força limpeza completa de todos os resíduos de modais
     */
    function forcarLimpezaModal() {
        console.log('Forçando limpeza completa');

        // Remove TODOS os backdrops (pode haver múltiplos)
        $('.modal-backdrop').each(function() {
            $(this).remove();
        });

        // Restaura o body
        $('body').removeClass('modal-open');
        $('body').css({
            'overflow': '',
            'padding-right': '',
            'overflow-y': ''
        });

        // Remove atributos data-bs do body
        $('body').removeAttr('data-bs-padding-right');
        $('body').removeAttr('data-bs-overflow');

        // Garante que nenhum modal tenha classe 'show'
        $('.modal').removeClass('show').css('display', 'none');

        console.log('Limpeza concluída');
    }

    /**
     * Função unificada para salvar cliente avulso
     */
    function salvarClienteAvulso(tipo) {
        const modalId = `modalClienteAvulso_${tipo.charAt(0).toUpperCase() + tipo.slice(1)}`;
        const form = $(`#formClienteAvulso_${tipo.charAt(0).toUpperCase() + tipo.slice(1)}`);
        const btnSalvar = $(`#btnSalvarClienteAvulso_${tipo.charAt(0).toUpperCase() + tipo.slice(1)}`);

        // Validação
        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }

        // Coleta dados
        const dados = {
            nome_avulso: $.trim(form.find(`[name="nome_avulso_${tipo}"]`).val()),
            telefone_avulso: $.trim(form.find(`[name="telefone_avulso_${tipo}"]`).val()),
            endereco_avulso: $.trim(form.find(`[name="endereco_avulso_${tipo}"]`).val()),
            numero_avulso: $.trim(form.find(`[name="numero_avulso_${tipo}"]`).val()),
            _token: $('meta[name="csrf-token"]').attr('content') || "{{ csrf_token() }}"
        };

        console.log('Dados a enviar:', dados);

        // Desabilita botão
        btnSalvar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Salvando...');

        // Envia requisição
        $.ajax({
            url: "/clientes/avulso",
            type: "POST",
            data: dados,
            dataType: 'json',
            success: function(resposta) {

                if (resposta.exists && resposta.cliente) {
                    // Cria a mensagem para o modal
                    const mensagem = `
                        Já existe um cliente neste endereço:<br><br>
                        <strong>${resposta.cliente.nome}</strong><br>
                        ${resposta.cliente.endereco}, ${resposta.cliente.numero}<br><br>
                        Deseja usá-lo?
                    `;

                    // Insere a mensagem no corpo do modal
                    $('#confirmarClienteBody').html(mensagem);

                    // Exibe o modal
                    const modalConfirmar = new bootstrap.Modal(document.getElementById('modalConfirmarCliente'));
                    modalConfirmar.show();

                    // Remove qualquer evento anterior para evitar duplicações
                    $('#btnConfirmarCliente').off('click');

                    // Define a ação para o botão "Sim"
                    $('#btnConfirmarCliente').on('click', function () {
                        if (tipo === 'origem') {
                            adicionarCliente_Origem(resposta.cliente);
                        } else {
                            adicionarCliente_Destino(resposta.cliente);
                        }

                        modalConfirmar.hide(); // Fecha o modal
                        fecharModalSeguro(modalId);
                        //mostrarAlertaSucesso('Cliente existente selecionado com sucesso!');
                    });

                } else if (resposta.created && resposta.cliente) {
                    if (tipo === 'origem') {
                        adicionarCliente_Origem(resposta.cliente);
                    } else {
                        adicionarCliente_Destino(resposta.cliente);
                    }
                    fecharModalSeguro(modalId);

                    // Monta o HTML com os dados do cliente
                    const html = `
                        <p><strong>Nome:</strong> ${resposta.cliente.nome}</p>
                        <p><strong>Endereço:</strong> ${resposta.cliente.endereco}, ${resposta.cliente.numero}</p>
                        <p><strong>Telefone:</strong> ${resposta.cliente.telefone}</p>
                    `;

                    // Insere o HTML no corpo do modal
                    $('#clienteCadastradoBody').html(html);

                    // Mostra o modal
                    const modalClienteCadastrado = new bootstrap.Modal(document.getElementById('modalClienteCadastrado'));
                    modalClienteCadastrado.show();
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro ao salvar:', error, xhr);
                let mensagem = `Erro ao salvar cliente avulso (${tipo}).`;

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    mensagem = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    mensagem = xhr.responseText;
                }

                alert(mensagem);
            },
            complete: function() {
                // Reabilita botão
                btnSalvar.prop('disabled', false).html('Salvar');
            }
        });

    }

    /**
     * Mostra alerta de sucesso
     */
    function mostrarAlertaSucesso(mensagem) {
        console.log('Mostrando alerta:', mensagem);

        if ($('#alert-container').length) {
            $('#alert-container').html(
                `<div class="alert alert-success alert-dismissible fade show" role="alert">
                    ${mensagem}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`
            );

            setTimeout(function() {
                $('#alert-container .alert').fadeOut(function() {
                    $(this).remove();
                });
            }, 4000);
        } else {
            alert(mensagem);
        }
    }

    let tipoResponsavelAtual = null;

    $(document).on('click', '#btnNovoResponsavelOrigem', function () {

        const clienteId = $('#cliente_origem').val();

        if (!clienteId) {
            alert('Selecione o cliente antes de cadastrar um responsável.');
            return;
        }
        tipoResponsavelAtual = 'origem';

        $('#cliente_id_modal').val(clienteId);

        $('#novo_nome').val('');
        $('#novo_telefone').val('');
        $('#novo_email').val('');

        const modalElement = document.getElementById('modalNovoResponsavel');

        let modal = bootstrap.Modal.getInstance(modalElement);

        if (!modal) {
            modal = new bootstrap.Modal(modalElement);
        }

        modal.show();
    });

    var behavior = function (val) {
        return val.replace(/\D/g, '').length === 11
            ? '(00) 00000-0000'
                : '(00) 0000-00009';
        };

        var options = {
            onKeyPress: function(val, e, field, options) {
                field.mask(behavior.apply({}, arguments), options);
            }
    };


    $(document).on('click', '#btnNovoResponsavelDestino', function () {

        const clienteId = $('#cliente_destino').val();

        if (!clienteId) {
            alert('Selecione o cliente antes de cadastrar um responsável.');
            return;
        }
        tipoResponsavelAtual = 'destino';

        $('#cliente_id_modal').val(clienteId);

        $('#novo_nome').val('');
        $('#novo_telefone').val('');
        $('#novo_email').val('');

        const modalElement = document.getElementById('modalNovoResponsavel');

        let modal = bootstrap.Modal.getInstance(modalElement);

        if (!modal) {
            modal = new bootstrap.Modal(modalElement);
        }
        modal.show();
    });

    $(document).on('click', '#btnSalvarNovoResponsavel', function () {
        console.log('clicou salvar');

        const clienteId = $('#cliente_id_modal').val();

        console.log('cliente enviado para modal:', clienteId);

        $.ajax({
            url: `/clientes/${clienteId}/responsaveis`,
            method: 'POST',
            data: {
                nome: $('#novo_nome').val(),
                telefone: $('#novo_telefone').val(),
                email: $('#novo_email').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (resp) {

                const select = tipoResponsavelAtual === 'origem'
                    ? $('#responsavel_origem_id')
                    : $('#responsavel_destino_id');

                select.append(`
                    <option value="${resp.id}"
                        data-telefone="${resp.telefone || ''}"
                        data-email="${resp.email || ''}"
                        selected>
                        ${resp.nome}
                    </option>
                `);

                select.trigger('change');

                const modalId = 'modalNovoResponsavel';

                const modal = bootstrap.Modal.getInstance(
                    document.getElementById(modalId)
                );

                if (modal) modal.hide();
            },
            error: function (xhr) {
                console.log({
                    clienteId: clienteId,
                    nome: $('#novo_nome').val(),
                    telefone: $('#novo_telefone').val(),
                    email: $('#novo_email').val()
                });

                console.error(xhr.responseText);
                alert('Erro ao salvar responsável.');
            }
        });
    });


    $(document).on('shown.bs.modal', '#modalNovoResponsavel', function () {

        var behavior = function (val) {
            return val.replace(/\D/g, '').length === 11
                ? '(00) 00000-0000'
                : '(00) 0000-00009';
        };

        var options = {
            onKeyPress: function(val, e, field, options) {
                field.mask(behavior.apply({}, arguments), options);
            }
        };

        $('#novo_telefone').mask(behavior, options);

    });

 </script>

 @endpush
