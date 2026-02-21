<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Criar tabela de ordens de serviço
        Schema::create('ordem_servicos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_os')->unique();
            
            // Relações
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('cliente_origem_id')->constrained('clientes');
            $table->foreignId('cliente_destino_id')->constrained('clientes');
            $table->foreignId('motorista_id')->references('id')->on('entregadores')->where('perfil', 'motorista');
            $table->foreignId('veiculo_id')->constrained('veiculos');
            $table->foreignId('user_id')->constrained('users')->comment('Usuário que criou a OS');
            
            // Datas e horários
            $table->date('data_servico');
            $table->time('hora_inicial')->nullable();
            $table->time('hora_final')->nullable();
            $table->string('tempo_total')->nullable();
            
            // Valores
            $table->decimal('valor_motorista', 10, 2)->default(0);
            $table->decimal('valor_ajudantes', 10, 2)->default(0);
            $table->decimal('valor_total', 10, 2)->default(0);
            
            // Informações adicionais
            $table->text('observacoes')->nullable();
            $table->string('status', 20)->default('pendente'); // SQL Server não suporta ENUM, usando string
            
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Tabela pivot para ajudantes
        Schema::create('ordem_servico_ajudante', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordem_servico_id')->constrained('ordem_servicos')->onDelete('cascade');
            $table->foreignId('ajudante_id')->references('id')->on('entregadores')->where('perfil', 'ajudante');
            $table->decimal('valor', 10, 2)->default(0)->comment('Valor individual deste ajudante');
            $table->timestamps();
        });
        
        // Tabela de histórico de status
        Schema::create('ordem_servico_historicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordem_servico_id')->constrained('ordem_servicos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->string('status_anterior', 20)->nullable(); // SQL Server não suporta ENUM
            $table->string('status_novo', 20); // SQL Server não suporta ENUM
            $table->text('observacao')->nullable();
            $table->timestamps();
        });

        // Criar uma restrição CHECK para os valores válidos de status
        DB::statement("ALTER TABLE ordem_servicos ADD CONSTRAINT CK_ordem_servicos_status CHECK (status IN ('pendente', 'em_andamento', 'concluido', 'cancelado'))");
        DB::statement("ALTER TABLE ordem_servico_historicos ADD CONSTRAINT CK_ordem_servico_historicos_status_anterior CHECK (status_anterior IS NULL OR status_anterior IN ('pendente', 'em_andamento', 'concluido', 'cancelado'))");
        DB::statement("ALTER TABLE ordem_servico_historicos ADD CONSTRAINT CK_ordem_servico_historicos_status_novo CHECK (status_novo IN ('pendente', 'em_andamento', 'concluido', 'cancelado'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover restrições CHECK
        DB::statement("ALTER TABLE ordem_servicos DROP CONSTRAINT CK_ordem_servicos_status");
        DB::statement("ALTER TABLE ordem_servico_historicos DROP CONSTRAINT CK_ordem_servico_historicos_status_anterior");
        DB::statement("ALTER TABLE ordem_servico_historicos DROP CONSTRAINT CK_ordem_servico_historicos_status_novo");

        Schema::dropIfExists('ordem_servico_historicos');
        Schema::dropIfExists('ordem_servico_ajudante');
        Schema::dropIfExists('ordem_servicos');
    }
};