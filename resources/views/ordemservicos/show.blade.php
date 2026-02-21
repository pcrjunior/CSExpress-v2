@section('title', 'Ordem de Serviço Filial')

<!-- Esta view é carregada via AJAX no modal de visualização da OS -->
<div class="container-fluid py-4">
    <!-- Cabeçalho com informações básicas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h7 class="card-subtitle mb-2 text-muted">Número da OS</h7>
                    <h5 class="card-title text-primary">{{ $ordemServico->numero_os }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h7 class="card-subtitle mb-2 text-muted">Data do Serviço</h7>
                    <h5 class="card-title">{{ $ordemServico->data_servico->format('d/m/Y') }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h7 class="card-subtitle mb-2 text-muted">Status</h7>
                    <h5 class="card-title">
                        @if($ordemServico->status == 'pendente')
                            <span class="badge bg-warning text-dark">Pendente</span>
                        @elseif($ordemServico->status == 'em_andamento')
                            <span class="badge bg-info text-dark">Em Andamento</span>
                        @elseif($ordemServico->status == 'concluido')
                            <span class="badge bg-success">Concluído</span>
                        @elseif($ordemServico->status == 'cancelado')
                            <span class="badge bg-danger">Cancelado</span>
                        @else
                            <span class="badge bg-secondary">Não Definido</span>
                        @endif
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Informações da empresa -->
    <!-- <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Empresa</h5>
        </div>
        <div class="card-body">
            <p class="mb-0"><strong>{{ $ordemServico->empresa->nome ?? 'N/A' }}</strong></p>
        </div>
    </div> -->


    <!-- Clientes -->
    <div class="row mb-4">
        <!-- Cliente Origem -->
        <div class="col-md-6">
            <div class="card h-100 {{ $ordemServico->contratante_tipo === 'origem' ? 'bg-success text-white' : '' }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Cliente Origem</h6>
                    @if($ordemServico->contratante_tipo === 'origem')
                        <span class="badge bg-light text-success">Contratante</span>
                    @endif
                </div>
                <div class="card-body">
                    <p class="fw-bold small mb-1">{{ $ordemServico->clienteOrigem->nome ?? 'N/A' }}</p>
                    <p class="fw-bold small mb-1">{{ $ordemServico->clienteOrigem->apelido ?? 'N/A' }}</p>
                    <p class="small mb-1"><strong>E-mail:</strong> {{ $ordemServico->clienteOrigem->email ?? 'N/A' }}</p>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="small mb-1"><strong>Responsável:</strong> {{ $ordemServico->clienteOrigem->responsavel ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="small mb-1"><strong>Telefone:</strong> {{ $ordemServico->clienteOrigem->telefone ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="small mb-1"><strong>Endereço:</strong> {{ $ordemServico->clienteOrigem->endereco ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-0"><strong>Cidade/UF:</strong> {{ $ordemServico->clienteOrigem->cidade ?? 'N/A' }}/{{ $ordemServico->clienteOrigem->uf ?? 'N/A' }}</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        <!-- Cliente Destino -->
        <div class="col-md-6">
            <div class="card h-100 {{ $ordemServico->contratante_tipo === 'destino' ? 'bg-success text-white' : '' }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fs-6">Cliente Destino</h6>
                    @if($ordemServico->contratante_tipo === 'destino')
                        <span class="badge bg-light text-success">Contratante</span>
                    @endif
                </div>
                <div class="card-body">
                    <p class="fw-bold small mb-1">{{ $ordemServico->clienteDestino->nome ?? 'N/A' }}</p>
                    <p class="fw-bold small mb-1">{{ $ordemServico->clienteDestino->apelido ?? 'N/A' }}</p>
                    <p class="small mb-1"><strong>E-mail:</strong> {{ $ordemServico->clienteDestino->email ?? 'N/A' }}</p>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="small mb-1"><strong>Responsável:</strong> {{ $ordemServico->clienteDestino->responsavel ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="small mb-1"><strong>Telefone:</strong> {{ $ordemServico->clienteDestino->telefone ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="small mb-1">
                                <strong>Endereço:</strong>
                                     {{ $ordemServico->clienteDestino->endereco ?? 'N/A' }},
                                     Nº {{ $ordemServico->clienteDestino->numero ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="small mb-1"><strong>Cidade/UF:</strong> {{ $ordemServico->clienteDestino->cidade ?? 'N/A' }}/{{ $ordemServico->clienteDestino->uf ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Motorista -->
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0 fs-6">Motorista</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th class="text-end">Valor Cobrado</th>
                            <th class="text-end">Valor Repasse</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $ordemServico->motorista-> nome ?? 'N/A' }}</td>
                            <td>{{ $ordemServico->motorista-> telefone ?? 'N/A' }}</td>
                            <td class="text-end">R$ {{ number_format($ordemServico->valor_motorista, 2, ',', '.') }}</td>
                            <td class="text-end">R$ {{ number_format($ordemServico->valor_repassado_motorista, 2, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Ajudantes -->
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0 fs-6">Ajudantes</h5>
        </div>
        <div class="card-body">
            @if($ordemServico->ajudantes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Telefone</th>
                                <th class="text-end">Valor Cobrado</th>
                                <th class="text-end">Valor Repasse</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ordemServico->ajudantes as $ajudante)
                                <tr>
                                    <td>{{ $ajudante->nome }}</td>
                                    <td>{{ $ajudante->telefone ?? 'N/A' }}</td>
                                    <td class="text-end">R$ {{ number_format($ajudante->pivot->valor, 2, ',', '.') }}</td>
                                    <td class="text-end">R$ {{ number_format($ajudante->pivot->valor_repassado_ajudante, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">Nenhum ajudante registrado para esta ordem de serviço.</p>
            @endif
        </div>
    </div>


    <!-- Valores Extras -->
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0 fs-6">Despesas Adicionais</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Valor Perimetro</td>
                            <td>R$ {{ number_format($ordemServico->valor_perimetro ?? 0, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Taxa de Restrição</td>
                            <td>R$ {{ number_format($ordemServico->valor_restricao ?? 0, 2, ',', '.') }}</td>
                        </tr>
                        <!-- <tr>
                            <td>Desconto</td>
                            <td>- R$ {{ number_format($ordemServico->desconto ?? 0, 2, ',', '.') }}</td>
                        </tr> -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>

        <!-- Valores Extras -->
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0 fs-6">Valores</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Valor Total Cobrado</td>
                            <td>R$ {{ number_format($ordemServico->valor_total ?? 0, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Valor Total Após Repasse</td>
                            <td>
                                R$ {{ number_format(($ordemServico->valor_repassado_motorista ?? 0) + ($ordemServico->valor_repassado_ajudantes ?? 0), 2, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td>Lucro / Prejuízo</td>
                            @php
                                $total = $ordemServico->valor_total ?? 0;
                                $repasse = ($ordemServico->valor_repassado_motorista ?? 0) + ($ordemServico->valor_repassado_ajudantes ?? 0);
                                $lucro = $total - $repasse;
                            @endphp
                            <td class="{{ $lucro >= 0 ? 'text-success fw-bold' : 'text-danger fw-bold' }}">
                                R$ {{ number_format($lucro, 2, ',', '.') }}
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

    </div>


    <!-- Horario e Veículo -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0 fs-6">Horários</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Hora Inicial:</strong>
                        {{ $ordemServico->hora_inicial ? \Carbon\Carbon::parse($ordemServico->hora_inicial)->format('H:i:s') : 'N/A' }}
                    </p>
                    <p class="mb-1"><strong>Hora Final:</strong>
                        {{ $ordemServico->hora_final ? \Carbon\Carbon::parse($ordemServico->hora_final)->format('H:i:s') : 'N/A' }}
                    </p>
                    <p class="mb-0"><strong>Tempo de serviço:</strong> {{ $ordemServico->tempo_total ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0 fs-6">Veículo</h5>
                </div>
                    <div class="card-body">
                    <p class="mb-1"><strong>Modelo:</strong>
                        {{ $ordemServico->veiculo->modelo ?? 'N/A' }}
                    </p>
                    <p class="mb-1"><strong>Categoria:</strong>
                        {{ $ordemServico->veiculo->categoria ?? 'N/A' }}
                    </p>
                    <p class="mb-0"><strong>Placa:</strong>
                        {{ $ordemServico->veiculo->placa ?? 'N/A' }}
                    </p>
                </div>


            </div>
        </div>
    </div>

    <!-- Horários e Tempo de Serviço -->
    <!-- <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Horários</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p class="mb-0"><strong>Hora Inicial:</strong> {{ $ordemServico->hora_inicial ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4">
                    <p class="mb-0"><strong>Hora Final:</strong> {{ $ordemServico->hora_final ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4">
                    <p class="mb-0"><strong>Tempo Total:</strong> {{ $ordemServico->tempo_total ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div> -->




    <!-- Observações -->
    @if($ordemServico->observacoes)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0 fs-6">Observações</h5>
        </div>
        <div class="card-body">
            <p class="mb-0">{{ $ordemServico->observacoes }}</p>
        </div>
    </div>
    @endif

    <!-- Histórico -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0 fs-6">Histórico</h5>
        </div>
        <div class="card-body">
            @if($ordemServico->historicos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Usuário</th>
                                <th>Status Anterior</th>
                                <th>Status Novo</th>
                                <th>Observação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ordemServico->historicos as $historico)
                                <tr>
                                    <td>{{ $historico->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $historico->usuario->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($historico->status_anterior)
                                            @if($historico->status_anterior == 'pendente')
                                                <span class="badge bg-warning text-dark">Pendente</span>
                                            @elseif($historico->status_anterior == 'em_andamento')
                                                <span class="badge bg-info text-dark">Em Andamento</span>
                                            @elseif($historico->status_anterior == 'concluido')
                                                <span class="badge bg-success">Concluído</span>
                                            @elseif($historico->status_anterior == 'cancelado')
                                                <span class="badge bg-danger">Cancelado</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($historico->status_novo == 'pendente')
                                            <span class="badge bg-warning text-dark">Pendente</span>
                                        @elseif($historico->status_novo == 'em_andamento')
                                            <span class="badge bg-info text-dark">Em Andamento</span>
                                        @elseif($historico->status_novo == 'concluido')
                                            <span class="badge bg-success">Concluído</span>
                                        @elseif($historico->status_novo == 'cancelado')
                                            <span class="badge bg-danger">Cancelado</span>
                                        @endif
                                    </td>
                                    <td>{{ $historico->observacao }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">Nenhum histórico disponível para esta ordem de serviço.</p>
            @endif
        </div>
    </div>

    <!-- Informações de Registro -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 fs-6">Informações de Registro</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p class="mb-0 fs-7"><strong>Criado por:</strong> {{ $ordemServico->usuario->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4">
                    <p class="mb-0 fs-7"><strong>Criado em:</strong> {{ $ordemServico->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-4">
                    <p class="mb-0 fs-7"><strong>Última atualização:</strong> {{ $ordemServico->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>


<!--     <div class="mb-3">
        <strong>Taxa de Restrição:</strong> R$ {{ number_format($ordemServico->taxa_restricao, 2, ',', '.') }}
    </div> -->
