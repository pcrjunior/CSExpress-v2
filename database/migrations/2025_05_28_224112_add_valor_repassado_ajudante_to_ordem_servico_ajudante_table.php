<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ordem_servico_ajudante', function (Blueprint $table) {
            $table->decimal('valor_repassado_ajudante', 10, 2)->default(0)->after('valor'); // ou ajuste conforme sua estrutura
        });
    }

    public function down(): void
    {
        Schema::table('ordem_servico_ajudante', function (Blueprint $table) {
            $table->dropColumn('valor_repassado_ajudante');
        });
    }
};
