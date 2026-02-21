<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordem_servicos', function (Blueprint $table) {
            // Renomear colunas
            $table->renameColumn('valor_adicionais', 'valor_perimetro');
            $table->renameColumn('valor_descontos', 'valor_restricao');

            // Remover colunas
            $table->dropColumn([
                'valor_servico',
                'valor_resultado',
                'taxa_restricao',
                'valor_cobrado_motorista',
                'valor_cobrado_ajudantes'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('ordem_servicos', function (Blueprint $table) {
            // Voltar nomes antigos
            $table->renameColumn('valor_perimetro', 'valor_adicionais');
            $table->renameColumn('valor_restricao', 'valor_descontos');

            // Restaurar colunas removidas
            $table->decimal('valor_servico', 10, 2)->default(0);
            $table->decimal('valor_resultado', 10, 2)->default(0);
            $table->decimal('taxa_restricao', 10, 2)->default(0);
            $table->decimal('valor_cobrado_motorista', 10, 2)->default(0);
            $table->decimal('valor_cobrado_ajudantes', 10, 2)->default(0);
        });
    }
};
