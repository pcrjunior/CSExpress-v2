@extends('layouts.clear')

@section('title', 'Nota de Serviço')

@section('content')
<div class="container py-4" style="max-width: 800px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; padding: 20px; background-color: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.05);">

    {{-- Botões --}}
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ url('financeiro/contas-receber') }}" class="btn btn-outline-secondary btn-sm me-2">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
        <form action="{{ route('financeiro.enviar-nota-servico', $contaReceber->id) }}" method="POST" onsubmit="return confirmarEnvio(this);">

            @csrf
            <button type="submit" class="btn btn-primary btn-sm btn-enviar-ns">
                <span class="btn-text">
                    <i class="fas fa-envelope me-1"></i> Enviar por E-mail
                </span>
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            </button>
        </form>
    </div>

    {{-- Cabeçalho Empresa --}}
    <div class="d-flex mb-3" style="gap: 10px;">
        <div class="border rounded p-3 flex-fill text-center">
            @if(optional(optional($contaReceber->ordemServico)->empresa)->logomarca)
                <img src="{{ asset('storage/' . $contaReceber->ordemServico->empresa->logomarca) }}" alt="Logomarca da Empresa" class="img-fluid" style="max-height: 60px;">
            @else
                <p class="text-muted mb-0">Logomarca não disponível</p>
            @endif
        </div>
        <div class="border rounded p-3 flex-fill">
            <p class="mb-1"><strong>Empresa:</strong> {{ optional(optional($contaReceber->ordemServico)->empresa)->nome ?? '-' }}</p>
            <p class="mb-1"><strong>Email:</strong> {{ optional(optional($contaReceber->ordemServico)->empresa)->email ?? '-' }}</p>
            <p class="mb-1"><strong>Telefone:</strong> {{ optional(optional($contaReceber->ordemServico)->empresa)->telefone ?? '-' }}</p>
        </div>
    </div>

    {{-- Título --}}
    <h2 class="text-center fw-bold mb-2" style="font-size: 1.1rem;">Minuta de Serviço</h2>
    <p class="text-center fw-semibold mb-4">Conhecimento Municipal de Serviços Prestados</p>

    {{-- Identificação dos Clientes --}}
    <h6 class="fw-bold border-bottom pb-1 mb-2">Cliente</h6>

    @php

        use App\Models\OrdemServicoHistorico;

        $ordemServico = $contaReceber->ordemServico;
        $contratanteTipo = $ordemServico->contratante_tipo ?? 'origem';
        $clienteOrigem = $ordemServico->clienteOrigem;
        $clienteDestino = $ordemServico->clienteDestino;
        $clientePagante = $contratanteTipo === 'destino' ? $clienteDestino : $clienteOrigem;
        $clienteNaoPagante = $contratanteTipo === 'destino' ? $clienteOrigem : $clienteDestino;
        
        
        $dataInicio  = OrdemServicoHistorico::ultimaDataPorStatus($ordemServico->id, 'em_andamento');
        $dataTermino = OrdemServicoHistorico::ultimaDataPorStatus($ordemServico->id, 'concluido');
    @endphp

    <div class="table-responsive">
        <table class="table table-bordered small">
            <tr>
                <th style="width: 120px;">Cliente</th>
                <td>{{ $clientePagante->nome ?? '-' }} - {{ $clientePagante->apelido ?? '-' }}</td>
            </tr>
            <!-- <tr>
                <th style="width: 120px;">Apelido</th>
                <td>{{ $clientePagante->apelido ?? '-' }}</td>
            </tr> -->
            <tr>
                <th>Endereço</th>
                <td>{{ $clientePagante->endereco ?? '-' }}, {{ $clientePagante->numero ?? '-' }}, {{ $clientePagante->cidade ?? '-' }} - {{ $clientePagante->uf ?? '-' }}</td>
            </tr>
            <tr>
                <th>Contato</th>
                <td>{{ $clientePagante->responsavel ?? '-' }}
                    - {{ $clientePagante->telefone ?? '-' }}
                    - {{ $clientePagante->email ?? '-' }}
                </td>
            </tr>
        </table>
    </div>

    {{-- Descrição do Serviço --}}
    <h6 class="fw-bold border-bottom pb-1 mb-2">Descrição do Serviço</h6>
    <div class="table-responsive">
        <table class="table table-bordered small">
            <tr>
                <th style="width: 120px;">Cliente</th>
                <td>{{ $clienteNaoPagante->nome ?? '-' }} - {{ $clienteNaoPagante->apelido ?? '-' }}</td>
            </tr>
            <!-- <tr>
                <th style="width: 120px;">Apelido</th>
                <td>{{ $clienteNaoPagante->apelido ?? '-' }}</td>
            </tr> -->
            <tr>
                <th>Endereço</th>
                <td>{{ $clienteNaoPagante->endereco ?? '-' }}, {{ $clienteNaoPagante->numero ?? '-' }}, {{ $clienteNaoPagante->cidade ?? '-' }} - {{ $clienteNaoPagante->uf ?? '-' }}</td>
            </tr>
            <tr>
                <th>Contato</th>
                <td>{{ $clienteNaoPagante->responsavel ?? '-' }} -
                    {{ $clienteNaoPagante->telefone ?? '-' }} -
                    {{ $clienteNaoPagante->email ?? '-' }}
                </td>
            </tr>
        </table>
    </div>

    {{-- Detalhes do Serviço --}}
    <h6 class="fw-bold border-bottom pb-1 mb-2">Detalhes do Serviço</h6>
    <div class="table-responsive">
        <table class="table table-bordered small">
            <tr>
                <th>OS Número</th>
                <th>Data do Serviço</th>
                <th>Inicio / Termino (hr)</th>
                <th>Motorista</th>
                <th>Veiculo</th>
            </tr>
            <tr>
                <td width="12%">{{ $ordemServico->numero_os ?? '-' }}</td>
                <td width="15%">{{
                    optional($ordemServico->data_servico)
                    ? \Carbon\Carbon::parse($ordemServico->data_servico)->format('d/m/Y')
                    : '-'
                    }}
                </td>
                <td width="18%">
                    {{ $dataInicio  
                        ? $dataInicio->format('H:i')  
                        : '-' 
                    }}
                    /
                    {{ $dataTermino 
                        ? $dataTermino->format('H:i') 
                        : '-' 
                    }}
                </td>
                <td width="25%">{{ optional($ordemServico->motorista)->nome ?? '-' }}</td>
                <td width="30%">{{ optional($ordemServico->veiculo)->descricao_completa ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- Valores --}}
    <h6 class="fw-bold border-bottom pb-1 mb-2">Valores</h6>
    <div class="table-responsive">
        <table class="table table-bordered small">
            <tr><th>Serviço</th><td class="text-end">R$ {{ number_format($ordemServico->valor_motorista ?? 0, 2, ',', '.') }}</td></tr>
            <tr><th>Ajudantes</th><td class="text-end">R$ {{ number_format($ordemServico->valor_ajudantes ?? 0, 2, ',', '.') }}</td></tr>
            <tr><th>Restrição</th><td class="text-end">R$ {{ number_format($ordemServico->valor_restricao ?? 0, 2, ',', '.') }}</td></tr>
            <tr><th>Perímetro</th><td class="text-end">R$ {{ number_format($ordemServico->valor_perimetro ?? 0, 2, ',', '.') }}</td></tr>
            <tr><th>Valor Total</th><td class="text-end fw-bold text-success">R$ {{ number_format($ordemServico->valor_total ?? 0, 2, ',', '.') }}</td></tr>
        </table>
    </div>

    {{-- Observações --}}
    <h6 class="fw-bold border-bottom pb-1 mb-2">Observações</h6>
    <p class="small">{!! nl2br(e($ordemServico->observacoes ?? '---')) !!}</p>

    {{-- Notas --}}
    <div class="small">
        <strong>Notas:</strong>
        <ul class="mb-0">
            <li>Não trabalhamos com seguro.</li>
            <li>Serviço fora do perímetro/hora extra/sábado/domingos: acréscimo de 25% ou 100%.</li>
            <li>Entregas só com mercadorias acompanhadas de NF.</li>
            <li>Não nos responsabilizamos por objetos/danos sem seguro.</li>
            <li>Reclamações só via gerência ou anotadas em OS.</li>
        </ul>
    </div>

    @php
        setlocale(LC_TIME, 'Portuguese_Brazil.1252');
        \Carbon\Carbon::setLocale('pt_BR');
    @endphp


    {{-- Assinatura --}}
    <div class="text-center mt-4">
        <p>São Paulo, {{ \Carbon\Carbon::parse($contaReceber->created_at)->translatedFormat('d \d\e F \d\e Y') }}</p>

        <hr style="width: 250px; margin: 8px auto;">
        <strong>Assinatura do Cliente</strong>
    </div>

</div>

{{-- Toast Enviando --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
    <div id="toastEnviando" class="toast align-items-center text-bg-warning border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                Enviando Nota de Serviço...
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmarEnvio(form) {
        const button = form.querySelector('button');
        const textSpan = button.querySelector('.btn-text');
        const spinner = button.querySelector('.spinner-border');

        textSpan.classList.add('d-none');
        spinner.classList.remove('d-none');
        button.disabled = true;

        const toast = new bootstrap.Toast(document.getElementById('toastEnviando'));
        toast.show();

        return true;
    }
</script>
@endpush
