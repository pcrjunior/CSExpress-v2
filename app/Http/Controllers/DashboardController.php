<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OrdemServico;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function charts(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        $endDate = Carbon::parse($request->input('end_date', now()));
        $startDate = Carbon::parse($request->input('start_date', now()->subDays(30)));

        $totalColaboradores = DB::table('entregadores')
            ->whereNull('deleted_at')
            ->where('Perfil', 'motorista')
            ->count();

        $entregadoresStatus = DB::table('entregadores')
            ->selectRaw("active, COUNT(*) AS total")
            ->whereNull('deleted_at')
            ->where('Perfil', 'motorista')
            ->when($request->start_date, fn($q) => $q->where('created_at', '>=', $startDate))
            ->when($request->end_date, fn($q) => $q->where('created_at', '<=', $endDate->endOfDay()))
            ->groupBy('active')
            ->get();

        $entregadoresStatusLabels = $entregadoresStatus->map(fn($row) => $row->active ? 'Ativos' : 'Inativos')->toArray();
        $entregadoresStatusData = $entregadoresStatus->pluck('total')->toArray();

        $entregasPorStatus = OrdemServico::selectRaw('status, COUNT(*) as total')
            ->whereBetween('data_servico', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('status')
            ->get();

        // 🔹 Entregadores com OS em aberto (não concluídas)
        $entregadoresComOSAberta  = DB::table('ordem_servicos as os')
            ->join('entregadores as e', 'os.motorista_id', '=', 'e.id')
            ->whereNotIn('os.status', ['concluída', 'concluido', 'finalizada'])
            ->whereNull('e.deleted_at')
            ->distinct('e.id')
            ->count('e.id');

        $totalClientes = DB::table('clientes')
            ->whereNull('deleted_at')
            ->count();

        $clientesComOSAberta = DB::table('clientes as c')
            ->join('ordem_servicos as os', 'os.cliente_origem_id', '=', 'c.id')
            ->whereNull('c.deleted_at')
            ->whereNull('os.deleted_at')
            ->whereNotIn('os.status', ['concluída', 'concluido', 'finalizada'])
            ->distinct('c.id')
            ->count('c.id');

        $totalOS = DB::table('ordem_servicos')
            ->whereNull('deleted_at')
            ->whereIn(DB::raw('LOWER(status)'), ['concluido', 'concluída', 'finalizada'])
            ->count();

        $totalOSAberta  = DB::table('ordem_servicos')
            ->whereNull('deleted_at')
            ->whereNotIn(DB::raw('LOWER(status)'), ['concluido', 'concluída', 'finalizada'])
            ->count();

        $totalVeiculos = DB::table('veiculos')
            ->whereNull('deleted_at')
            ->count();

        $veiculosComOSAberta = DB::table('veiculos as v')
            ->join('ordem_servicos as os', 'os.veiculo_id', '=', 'v.id')
            ->whereNull('v.deleted_at')
            ->whereNull('os.deleted_at')
            ->whereNotIn('os.status', ['concluída', 'concluido', 'finalizada'])
            ->distinct('v.id')
            ->count('v.id');

        $entregasStatusLabels = $entregasPorStatus->pluck('status')->toArray();
        $entregasStatusData = $entregasPorStatus->pluck('total')->toArray();

        $osCriadasPorStatus = DB::table('ordem_servicos')
            ->select('status', DB::raw('COUNT(*) as total'))
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        // Prepara arrays para o gráfico de pizza
        $osCriadasStatusLabels = $osCriadasPorStatus->pluck('status')->toArray();
        $osCriadasStatusData = $osCriadasPorStatus->pluck('total')->toArray();

        $entregasPorDia = OrdemServico::selectRaw('CAST(data_servico AS DATE) as dia, COUNT(*) as total')
            ->whereNull('deleted_at')
            ->whereBetween('data_servico', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy(DB::raw('CAST(data_servico AS DATE)'))
            ->orderBy(DB::raw('CAST(data_servico AS DATE)'))
            ->get();

        $entregasPorDiaLabels = $entregasPorDia->pluck('dia')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))->toArray();
        $entregasPorDiaData = $entregasPorDia->pluck('total')->toArray();

        $topColaboradores = DB::table('ordem_servicos as os')
            ->join('entregadores as e', 'os.motorista_id', '=', 'e.id')
            ->select('e.nome', DB::raw('COUNT(*) as total'))
            ->whereNull('os.deleted_at')
            ->whereNull('e.deleted_at')
            ->whereBetween('os.data_servico', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('e.nome')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $topColaboradoresLabels = $topColaboradores->pluck('nome')->toArray();
        $topColaboradoresData = $topColaboradores->pluck('total')->toArray();

        $topClientesContratantes = DB::table('ordem_servicos as os')
            ->selectRaw("
                CASE
                    WHEN os.contratante_tipo = 'origem' THEN os.cliente_origem_id
                    WHEN os.contratante_tipo = 'destino' THEN os.cliente_destino_id
                END AS cliente_id,
                COUNT(*) as total_contratacoes
            ")
            ->whereNull('os.deleted_at')
            ->whereBetween('os.data_servico', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupByRaw("
                CASE
                    WHEN os.contratante_tipo = 'origem' THEN os.cliente_origem_id
                    WHEN os.contratante_tipo = 'destino' THEN os.cliente_destino_id
                END
            ")
            ->orderByDesc('total_contratacoes')
            ->take(10)
            ->get();

        // Obtem os nomes dos clientes com apelido (origem/destino)
        $clienteIds = $topClientesContratantes->pluck('cliente_id')->toArray();

        $clientes = DB::table('clientes')
            ->whereIn('id', $clienteIds)
            ->get()
            ->mapWithKeys(function ($cliente) {
                $nomeCompleto = trim($cliente->apelido . ' || ' . ($cliente->nome ?? ''));
                return [$cliente->id => $nomeCompleto];
        });

        // Monta os dados para o gráfico
        $topClientesLabels = $topClientesContratantes->map(function ($item) use ($clientes) {
            return $clientes[$item->cliente_id] ?? 'Desconhecido';
        })->toArray();

        $topClientesData = $topClientesContratantes->pluck('total_contratacoes')->toArray();

        // ✅ NOVO GRÁFICO 1: Usuário x Quantidade de OS
        $osUsuarios = DB::table('ordem_servicos as os')
            ->join('users as u', 'os.user_id', '=', 'u.id') // 👈 CORRIGIDO: user_id
            ->select('u.name', DB::raw('COUNT(*) as total'))
            ->whereNull('os.deleted_at')
            ->whereBetween('os.data_servico', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('u.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $usuarioOsLabels = $osUsuarios->pluck('name')->toArray();
        $usuarioOsData = $osUsuarios->pluck('total')->toArray();

        // ✅ NOVO GRÁFICO 2: Dia da semana com mais corridas
        $osPorDiaSemana = DB::table('ordem_servicos')
            ->selectRaw('DATEPART(WEEKDAY, data_servico) as dia_semana, COUNT(*) as total') // 👈 CORRIGIDO para SQL Server
            ->whereNull('deleted_at')
            ->whereBetween('data_servico', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy(DB::raw('DATEPART(WEEKDAY, data_servico)'))
            ->orderBy(DB::raw('DATEPART(WEEKDAY, data_servico)'))
            ->get();

        $diasSemanaMap = [
            1 => 'Domingo',
            2 => 'Segunda',
            3 => 'Terça',
            4 => 'Quarta',
            5 => 'Quinta',
            6 => 'Sexta',
            7 => 'Sábado'
        ];

        $diaSemanaLabels = [];
        $diaSemanaData = [];
        foreach ($osPorDiaSemana as $item) {
            $diaSemanaLabels[] = $diasSemanaMap[$item->dia_semana] ?? 'Indefinido';
            $diaSemanaData[] = $item->total;
        }

        // ✅ NOVO GRÁFICO 3: Top 10 Veículos Mais Utilizados (por modelo)
        $topVeiculos = DB::table('ordem_servicos as os')
            ->join('veiculos as v', 'os.veiculo_id', '=', 'v.id')
            ->select('v.modelo', DB::raw('COUNT(*) as total'))
            ->whereNull('os.deleted_at')
            ->whereNull('v.deleted_at')
            ->whereNotNull('os.veiculo_id')
            ->whereBetween('os.data_servico', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('v.modelo')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $topVeiculosLabels = $topVeiculos->pluck('modelo')->toArray();
        $topVeiculosData = $topVeiculos->pluck('total')->toArray();

        return view('dashboard.charts', [
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'totalColaboradores' => $totalColaboradores,
            'entregadoresStatusLabels' => $entregadoresStatusLabels,
            'entregadoresStatusData' => $entregadoresStatusData,
            'entregasStatusLabels' => $entregasStatusLabels,
            'entregasStatusData' => $entregasStatusData,
            'entregadoresComOSAberta' => $entregadoresComOSAberta,
            'totalClientes' => $totalClientes,
            'clientesComOSAberta' => $clientesComOSAberta,
            'totalVeiculos' => $totalVeiculos,
            'veiculosComOSAberta' => $veiculosComOSAberta,
            'totalOS' => $totalOS,
            'totalOSAberta' => $totalOSAberta,
            'osCriadasStatusLabels' => $osCriadasStatusLabels,
            'osCriadasStatusData' => $osCriadasStatusData,
            'entregasPorDiaLabels' => $entregasPorDiaLabels,
            'entregasPorDiaData' => $entregasPorDiaData,
            'topColaboradoresLabels' => $topColaboradoresLabels,
            'topColaboradoresData' => $topColaboradoresData,
            'topClientesLabels' => $topClientesLabels,
            'topClientesData' => $topClientesData,
            // ✅ Novos gráficos
            'usuarioOsLabels' => $usuarioOsLabels,
            'usuarioOsData' => $usuarioOsData,
            'diaSemanaLabels' => $diaSemanaLabels,
            'diaSemanaData' => $diaSemanaData,
            'topVeiculosLabels' => $topVeiculosLabels,
            'topVeiculosData' => $topVeiculosData,
        ]);
    }
}