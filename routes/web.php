<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserAdminController;
use App\Http\Controllers\EntregadorController;
use App\Http\Controllers\VeiculoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\FilialController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrdemServicoController;
use App\Http\Controllers\CepController;
use App\Http\Controllers\FinanceiroController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ContaReceber;
use App\Http\Controllers\RelatorioController;
use App\Models\Entregador;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Arquivo: routes/web.php
| Descrição: Rotas principais da aplicação
| Versão: 1.0.0
| Data: 30/10/2025
|
*/

// ============================================================================
// ROTA RAIZ - Redireciona para Login
// ============================================================================
Route::get('/', function () {
    return redirect()->route('login');
});

// ============================================================================
// ROTAS AUTENTICADAS
// ============================================================================
Route::middleware(['auth'])->group(function () {

    // ========================================================================
    // DASHBOARD
    // ========================================================================
    Route::controller(DashboardController::class)->prefix('dashboard')->name('dashboard')->group(function () {
        Route::get('/', 'charts');
        Route::get('/charts', 'charts')->name('.charts');
    });

    // ========================================================================
    // PERFIL DO USUÁRIO
    // ========================================================================
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });

    // ========================================================================
    // VIEWS SIMPLES (sem controller específico)
    // ========================================================================
    Route::middleware('verified')->group(function () {
        Route::view('/relatorio', 'relatorio')->name('relatorio');
        Route::view('/consulta', 'consulta')->name('consulta');
        Route::view('/faturamento', 'faturamento')->name('faturamento');
        Route::view('/backup', 'backup')->name('backup');
    });

    // ========================================================================
    // ORDENS DE SERVIÇO
    // ========================================================================
    // Resource completo
    Route::resource('ordemservicos', OrdemServicoController::class);

    // Rota alternativa para index (compatibilidade)
    Route::get('/ordens', [OrdemServicoController::class, 'index'])->name('ordemservicos');

    // Finalizar OS
    Route::patch('/ordemservicos/{ordemservico}/finalizar', [OrdemServicoController::class, 'finalizacaoOS'])
        ->name('ordemservicos.finalizar');

    // Alterar status da OS
    Route::post('/ordemservicos/{ordemservico}/alterar-status', [OrdemServicoController::class, 'alterarStatusOrdem'])
        ->name('ordemservicos.alterarstatusordem');

    // ========================================================================
    // CLIENTES
    // ========================================================================
    // Rotas específicas ANTES do resource
    Route::get('/clientes/busca_por_apelido', [ClienteController::class, 'buscaPorApelido'])
        ->name('clientes.busca_por_apelido');
    Route::get('/clientes/listar_todos', [ClienteController::class, 'listarTodos'])
        ->name('clientes.listar_todos');
    Route::get('/clientes/{clienteId}/dados', [ClienteController::class, 'getDadosCliente'])
        ->name('clientes.dados');

    // Resource completo
    Route::resource('clientes', ClienteController::class);


    // Clientes Avulsos
    Route::post('/clientes/avulso', [ClienteController::class, 'storeAvulso'])
        ->name('clientes.avulso.store');


    // ========================================================================
    // ENTREGADORES E AJUDANTES (Fora do gerenciamento)
    // ========================================================================
    Route::controller(EntregadorController::class)->group(function () {
        // Lista de veículos do entregador
        Route::get('/entregadores/{id}/veiculos', 'veiculos')->name('entregadores.veiculos');

        // Lista de ajudantes
        Route::get('/ajudantes', 'ajudantes')->name('ajudantes.index');

        // Buscar motoristas
        Route::get('/entregadores/buscar', 'buscarMotoristas')->name('motoristas.buscar');

        // Lista geral de entregadores (USADA NO DASHBOARD)
        Route::get('/entregadores', 'entregadoresIndex')->name('entregadores.entregadores');
    });

    // ========================================================================
    // ÁREA DE GERENCIAMENTO
    // ========================================================================
    Route::prefix('gerenciamento')->group(function () {

        // --------------------------------------------------------------------
        // USUÁRIOS
        // --------------------------------------------------------------------
        Route::controller(UserAdminController::class)->group(function () {
            Route::patch('/usuarios/{id}/toggle', 'toggleActive')->name('usuarios.toggle');
        });
        Route::resource('usuarios', UserAdminController::class)->except(['show']);

        // --------------------------------------------------------------------
        // ENTREGADORES (CRUD Completo)
        // --------------------------------------------------------------------
        Route::controller(EntregadorController::class)->group(function () {
            Route::patch('/entregadores/{id}/toggle', 'toggleActive')->name('entregadores.toggle');
        });
        Route::resource('entregadores', EntregadorController::class)->except(['show']);

        // --------------------------------------------------------------------
        // VEÍCULOS
        // --------------------------------------------------------------------
        Route::resource('veiculos', VeiculoController::class)->except(['show']);

        // --------------------------------------------------------------------
        // EMPRESAS
        // --------------------------------------------------------------------
        Route::resource('empresas', EmpresaController::class)->except(['show']);

        // --------------------------------------------------------------------
        // FILIAIS
        // --------------------------------------------------------------------
        Route::resource('filiais', FilialController::class)
            ->except(['show'])
            ->parameters(['filiais' => 'filial']);
    });

    // ========================================================================
    // MÓDULO FINANCEIRO
    // ========================================================================
    Route::prefix('financeiro')->name('financeiro.')->middleware(['auth'])->group(function () {

        // Dashboard principal do financeiro
        Route::get('/', [FinanceiroController::class, 'index'])->name('index');

        // --------------------------------------------------------------------
        // CONTAS A PAGAR
        // --------------------------------------------------------------------
        Route::get('/contas-pagar', [FinanceiroController::class, 'contasPagar'])
            ->name('contas-pagar');
        Route::get('/contas-pagar/filtrar', [FinanceiroController::class, 'filtrarContasPagar'])
            ->name('filtrar-contas-pagar');
        Route::get('/contas-pagar/selecionar-os', [FinanceiroController::class, 'selecionarOSPagamento'])
            ->name('selecionar-os-pagamento');
        Route::post('/contas-pagar/gerar', [FinanceiroController::class, 'gerarPagamentos'])
            ->name('gerar-pagamentos');
        Route::patch('/contas-pagar/{id}/pagar', [FinanceiroController::class, 'realizarPagamento'])
            ->name('realizar-pagamento');

        // --------------------------------------------------------------------
        // CONTAS A RECEBER
        // --------------------------------------------------------------------
        Route::get('/contas-receber', [FinanceiroController::class, 'contasReceber'])
            ->name('contas-receber');
        Route::get('/contas-receber/filtrar', [FinanceiroController::class, 'filtrarContasReceber'])
            ->name('filtrar-contas-receber');
        Route::get('/contas-receber/selecionar-os', [FinanceiroController::class, 'selecionarOSRecebimento'])
            ->name('selecionar-os-recebimento');
        Route::post('/contas-receber/gerar', [FinanceiroController::class, 'gerarRecebimentos'])
            ->name('gerar-recebimentos');
        Route::patch('/contas-receber/{id}/receber', [FinanceiroController::class, 'registrarRecebimento'])
            ->name('registrar-recebimento');

        // --------------------------------------------------------------------
        // NOTA DE SERVIÇO (Visualização, Impressão e Envio)
        // --------------------------------------------------------------------
        Route::get('/contas-receber/{id}/nota-servico', [FinanceiroController::class, 'visualizarNotaServico'])
            ->name('visualizar-nota-servico');
        Route::get('/contas-receber/visualizar-nota-servico/{contaReceber}', [FinanceiroController::class, 'visualizarNotaServico'])
            ->name('visualizar-nota-servico-detalhada');
        Route::get('/contas-receber/imprimir-nota-servico/{contaReceber}', [FinanceiroController::class, 'imprimirNotaServico'])
            ->name('imprimir-nota-servico');
        Route::post('/contas-receber/{contaReceber}/enviar-nota-servico', [FinanceiroController::class, 'enviarNotaServico'])
            ->name('enviar-nota-servico');
    });

    // ========================================================================
    // MÓDULO RELATÓRIOS
    // ========================================================================
    Route::prefix('relatorios')->name('relatorios.')->group(function () {

        // --------------------------------------------------------------------
        // RELATÓRIO DE ORDENS DE SERVIÇO
        // --------------------------------------------------------------------
        Route::get('/ordens-servico', [RelatorioController::class, 'ordensServico'])
            ->name('ordens-servico');
        Route::get('/ordens-servico/exportar', [RelatorioController::class, 'exportarOSPDF'])
            ->name('ordens-servico.exportar');
        Route::get('/ordens-servico/exportar-excel', [RelatorioController::class, 'exportarOrdensServicoExcel'])
            ->name('ordens-servico.excel');

        // --------------------------------------------------------------------
        // RELATÓRIO DE ENTREGADORES
        // --------------------------------------------------------------------
        Route::get('/entregadores', [RelatorioController::class, 'relatorioEntregadores'])
            ->name('entregadores');
        Route::get('/entregadores/exportar', [RelatorioController::class, 'exportarEntregadoresPDF'])
            ->name('entregadores.exportar');
        Route::get('/entregadores/exportar-excel', [RelatorioController::class, 'exportarEntregadoresExcel'])
            ->name('entregadores.exportar.excel');

        // --------------------------------------------------------------------
        // RELATÓRIO DE MOTORISTAS
        // --------------------------------------------------------------------
        Route::get('/motoristas', [RelatorioController::class, 'relatorioMotoristas'])
            ->name('motoristas');
        Route::get('/motoristas/exportar', [RelatorioController::class, 'exportarMotoristasPDF'])
            ->name('motoristas.exportar');
        Route::get('/motoristas/exportar-excel', [RelatorioController::class, 'exportarMotoristasExcel'])
            ->name('motoristas.exportar.excel');

        // --------------------------------------------------------------------
        // RELATÓRIO DE CONTAS A PAGAR
        // --------------------------------------------------------------------
        Route::get('/contas-pagar', [RelatorioController::class, 'relatorioContasPagar'])
            ->name('contas-pagar');
        Route::get('/contas-pagar/exportar', [RelatorioController::class, 'exportarContasPagarPDF'])
            ->name('contas-pagar.exportar');
        Route::get('/contas-pagar/exportar-excel', [RelatorioController::class, 'exportarContasPagarExcel'])
            ->name('contas-pagar.excel');

        // --------------------------------------------------------------------
        // RELATÓRIO DE CONTAS A RECEBER
        // --------------------------------------------------------------------
        Route::get('/contas-receber', [RelatorioController::class, 'relatorioContasReceber'])
            ->name('contas-receber');
        Route::get('/contas-receber/exportar', [RelatorioController::class, 'exportarContasReceberPDF'])
            ->name('contas-receber.exportar');
        Route::get('/contas-receber/exportar-excel', [RelatorioController::class, 'exportarContasReceberExcel'])
            ->name('contas-receber.excel');

        // --------------------------------------------------------------------
        // RELATÓRIO DE CLIENTES ATENDIDOS
        // --------------------------------------------------------------------
        Route::get('/clientes-atendidos', [RelatorioController::class, 'relatorioClientesAtendidos'])
            ->name('clientes-atendidos');
        Route::get('/clientes-atendidos/exportar', [RelatorioController::class, 'exportarClientesAtendidosPDF'])
            ->name('clientes-atendidos.exportar');
        Route::get('/clientes-atendidos/excel', [RelatorioController::class, 'exportarClientesAtendidosExcel'])
            ->name('clientes-atendidos.excel');
    });

    // ========================================================================
    // ROTA DE TESTE PDF
    // ========================================================================
    Route::get('/teste-pdf/{id}', function ($id) {
        $contaReceber = ContaReceber::with([
            'ordemServico.clienteOrigem',
            'ordemServico.empresa',
            'ordemServico.motorista',
            'ordemServico.veiculo',
            'ordemServico.ajudantes'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.nota-servico', compact('contaReceber'));
        return $pdf->stream('nota-servico.pdf');
    });

    // ========================================================================
    // LOGOUT
    // ========================================================================
    Route::get('/logout', function () {
        Auth::logout();
        return redirect()->route('login');
    })->name('logout');
});

// ============================================================================
// ROTA DE TESTE
// ============================================================================
Route::view('/teste-telefone', 'teste-telefone');

// ============================================================================
// ROTAS DE AUTENTICAÇÃO (Breeze/Fortify)
// ============================================================================
require base_path('routes/auth.php');

/*
|--------------------------------------------------------------------------
| RESUMO DE ROTAS PRINCIPAIS
|--------------------------------------------------------------------------
|
| DASHBOARD:
| - GET /dashboard → DashboardController@charts
| - GET /dashboard/charts → DashboardController@charts
|
| ORDENS DE SERVIÇO:
| - Resource: /ordemservicos (index, create, store, show, edit, update, destroy)
| - GET /ordens → OrdemServicoController@index (alternativa)
| - PATCH /ordemservicos/{id}/finalizar → finalizacaoOS
| - POST /ordemservicos/{id}/alterar-status → alterarStatusOrdem
|
| CLIENTES:
| - Resource: /clientes
| - GET /clientes/busca_por_apelido
| - GET /clientes/listar_todos
| - GET /clientes/{id}/dados
|
| ENTREGADORES (Listagem):
| - GET /entregadores → EntregadorController@entregadoresIndex (USADA NO DASHBOARD)
| - GET /entregadores/{id}/veiculos
| - GET /ajudantes
| - GET /entregadores/buscar
|
| GERENCIAMENTO:
| - /gerenciamento/usuarios (Resource)
| - /gerenciamento/entregadores (Resource - CRUD completo)
| - /gerenciamento/veiculos (Resource - USADA NO DASHBOARD)
| - /gerenciamento/empresas (Resource)
| - /gerenciamento/filiais (Resource)
|
| FINANCEIRO:
| - GET /financeiro → index
| - /financeiro/contas-pagar (múltiplas rotas)
| - /financeiro/contas-receber (múltiplas rotas)
|
| RELATÓRIOS:
| - /relatorios/ordens-servico
| - /relatorios/entregadores
| - /relatorios/motoristas
| - /relatorios/contas-pagar
| - /relatorios/contas-receber
| - /relatorios/clientes-atendidos
|
|--------------------------------------------------------------------------
| ROTAS USADAS NO DASHBOARD
|--------------------------------------------------------------------------
|
| 1. Ordens de Serviço:
|    - route('ordemservicos.index', ['status' => 'pendente'])
|    - route('ordemservicos.index', ['status' => 'concluido'])
|
| 2. Clientes:
|    - route('clientes.index')
|    - route('clientes.index', ['com_os_ativa' => '1'])
|
| 3. Entregadores:
|    - route('entregadores.entregadores')
|    - route('entregadores.entregadores', ['com_os_ativa' => '1'])
|
| 4. Veículos:
|    - route('veiculos.index')
|    - route('veiculos.index', ['em_uso' => '1'])
|
*/
