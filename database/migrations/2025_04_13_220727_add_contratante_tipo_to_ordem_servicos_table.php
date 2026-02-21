<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ordem_servicos', function (Blueprint $table) {
            $table->enum('contratante_tipo', ['origem', 'destino'])
                ->nullable()
                ->after('cliente_destino_id')
                ->comment('Indica se o cliente contratante é o de origem ou de destino');
        });
    }

    public function down(): void
    {
        Schema::table('ordem_servicos', function (Blueprint $table) {
            $table->dropColumn('contratante_tipo');
        });
    }
};

