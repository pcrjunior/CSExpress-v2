<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Minuta de Serviço</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 10px;
        }

        .empresa-cabecalho {
            display: table;
            width: 100%;
            margin-bottom: 8px;
            table-layout: fixed;
        }

        .logo-card, .info-card {
            display: table-cell;
            vertical-align: top;
            padding: 15px;
            border: 0.1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
        }

        .logo-card {
            width: 46%;
            text-align: center;
        }

        .info-card {
            width: 54%;
            text-align: left;
        }

        .logo {
            max-width: 60%;
            max-height: 60px;
            display: block;
            margin: 0 auto;
        }

        .info-card p {
            margin: 4px 0;
        }

        .info-card strong {
            display: inline-block;
            width: 100px;
        }

        h2 {
            font-size: 16px;
            margin-bottom: 5px;
            text-align: center;
        }

        p.subtitle {
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 13px;
            text-align: center;
        }

        .section-title {
            font-weight: bold;
            font-size: 13px;
            margin-top: 20px;
            margin-bottom: 8px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            vertical-align: top;
            font-size: 10px;
        }

        th {
            background-color: #f9f9f9;
            font-weight: bold;
            width: 20%;
            font-size: 11px;
        }

        .text-end {
            text-align: right;
        }

        .assinatura {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
        }

        .assinatura hr {
            border: 1px solid #000;
            width: 250px;
            margin: 8px auto;
        }

        .observacoes {
            font-size: 9px;
            margin-top: 8px;
        }

        .observacoes li {
            margin-bottom: 3px;
        }
    </style>
</head>
<body>

    {{-- Cabeçalho Empresa --}}
    <div class="empresa-cabecalho">
        <div class="logo-card">
            @if(optional(optional($contaReceber->ordemServico)->empresa)->logomarca)
                <img src="{{ public_path('storage/' . $contaReceber->ordemServico->empresa->logomarca) }}" class="logo" alt="Logomarca">
            @else
                <p>Logomarca não disponível</p>
            @endif
        </div>

        <div class="info-card">
            <p><strong>Empresa:</strong> {{ optional(optional($contaReceber->ordemServico)->empresa)->apelido ?? '-' }}</p>
            <p><strong>Email:</strong> {{ optional(optional($contaReceber->ordemServico)->empresa)->email ?? '-' }}</p>
            <p><strong>Telefone:</strong> {{ optional(optional($contaReceber->ordemServico)->empresa)->telefone ?? '-' }}</p>
        </div>
    </div>

    <h2>Minuta de Serviço</h2>
    <p class="subtitle">Conhecimento Municipal de Serviços Prestados</p>

    {{-- Identificação dos Clientes --}}
    @php

        use App\Models\OrdemServicoHistorico;
        
        $contratanteTipo = $contaReceber->ordemServico->contratante_tipo ?? 'origem';
        $clienteOrigem  = $contaReceber->ordemServico->clienteOrigem;
        $clienteDestino = $contaReceber->ordemServico->clienteDestino;
        $clientePagante = $contratanteTipo === 'destino' ? $clienteDestino : $clienteOrigem;
        $clienteNaoPagante = $contratanteTipo === 'destino' ? $clienteOrigem : $clienteDestino;

        $dataInicio  = OrdemServicoHistorico::ultimaDataPorStatus($ordemServico->id, 'em_andamento');
        $dataTermino = OrdemServicoHistorico::ultimaDataPorStatus($ordemServico->id, 'concluido');
    @endphp

    <div class="section-title">Cliente</div>
    <table>
        <tr>
            <th>Cliente</th>
            <td>{{ $clientePagante->nome ?? '-' }} - {{ $clientePagante->apelido ?? '-' }}</td>
        </tr>
        <tr>
            <th>Endereço</th>
            <td>
                {{ $clientePagante->endereco ?? '-' }},
                {{ $clientePagante->numero ?? '-' }},
                {{ $clientePagante->cidade ?? '-' }} - {{ $clientePagante->uf ?? '-' }}
            </td>
        </tr>
        <tr>
            <th>Contato</th>
            <td>{{ $clientePagante->responsavel ?? '-' }}
                - {{ $clientePagante->telefone ?? '-' }}
                - {{ $clientePagante->email ?? '-' }}
            </td>
        </tr>
    </table>

    <div class="section-title">Descrição do Serviço</div>
    <table>
        <tr>
            <th>Cliente</th>
            <td>{{ $clienteNaoPagante->nome ?? '-' }} - {{ $clienteNaoPagante->apelido ?? '-' }}</td></td>
        </tr>
        <tr>
            <th>Endereço</th>
            <td>
                {{ $clienteNaoPagante->endereco ?? '-' }},
                {{ $clienteNaoPagante->numero ?? '-' }},
                {{ $clienteNaoPagante->cidade ?? '-' }} - {{ $clienteNaoPagante->uf ?? '-' }}
            </td>
        </tr>
        <tr>
            <th>Contato</th>
            <td>{{ $clienteNaoPagante->responsavel ?? '-' }}
                - {{ $clienteNaoPagante->telefone ?? '-' }}
                - {{ $clienteNaoPagante->email ?? '-' }}</td>
        </tr>
    </table>

    <div class="section-title">Detalhes do Serviço</div>
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

    <div class="section-title">Valores</div>
    <table>
        <tr>
            <th>Serviço</th>
            <td class="text-end">R$ {{ number_format($contaReceber->ordemServico->valor_motorista ?? 0, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Ajudantes</th>
            <td class="text-end">R$ {{ number_format($contaReceber->ordemServico->valor_ajudantes ?? 0, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Restrição</th>
            <td class="text-end">R$ {{ number_format($contaReceber->ordemServico->valor_restricao ?? 0, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Perímetro</th>
            <td class="text-end">R$ {{ number_format($contaReceber->ordemServico->valor_perimetro ?? 0, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Valor Total</th>
            <td class="text-end"><strong>R$ {{ number_format($contaReceber->ordemServico->valor_total ?? 0, 2, ',', '.') }}</strong></td>
        </tr>
    </table>

    <div class="section-title">Observações</div>
    <p>{!! nl2br(e($contaReceber->ordemServico->observacoes ?? '---')) !!}</p>

    <div class="observacoes">
        <strong>Notas:</strong>
        <ul>
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

    <div class="assinatura">
        <p>São Paulo, {{ \Carbon\Carbon::parse($contaReceber->created_at)->translatedFormat('d \d\e F \d\e Y') }}</p>
        <hr>
        <strong>Assinatura do Cliente</strong>
    </div>

</body>
</html>
