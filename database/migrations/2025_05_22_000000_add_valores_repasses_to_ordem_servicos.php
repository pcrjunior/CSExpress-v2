<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ordem_servicos', function (Blueprint $table) {
            $table->decimal('valor_cobrado_motorista', 10, 2)->default(0);
            $table->decimal('valor_repassado_motorista', 10, 2)->default(0);
            $table->decimal('valor_cobrado_ajudantes', 10, 2)->default(0);
            $table->decimal('valor_repassado_ajudantes', 10, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('ordem_servicos', function (Blueprint $table) {
            $table->dropColumn([
                'valor_cobrado_motorista',
                'valor_repassado_motorista',
                'valor_cobrado_ajudantes',
                'valor_repassado_ajudantes'
            ]);
        });
    }
};
