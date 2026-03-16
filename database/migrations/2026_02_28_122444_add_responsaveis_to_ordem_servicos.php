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

            $table->unsignedBigInteger('responsavel_origem_id')->nullable();
            $table->unsignedBigInteger('responsavel_destino_id')->nullable();

            $table->foreign('responsavel_origem_id')
                ->references('id')
                ->on('cliente_responsaveis')
                ->onDelete('no action');

            $table->foreign('responsavel_destino_id')
                ->references('id')
                ->on('cliente_responsaveis')
                ->onDelete('no action');
        });
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
