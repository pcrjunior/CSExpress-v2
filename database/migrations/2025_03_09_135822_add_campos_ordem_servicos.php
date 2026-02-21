<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ordem_servicos', function (Blueprint $table) {
            $table->decimal('valor_adicionais', 10, 2)->nullable();
            $table->decimal('valor_descontos', 10, 2)->nullable();
            $table->string('created_by')->nullable(); // ou integer, se referenciar o usuário
        });;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordem_servicos', function (Blueprint $table) {
            //
        });
    }
};
