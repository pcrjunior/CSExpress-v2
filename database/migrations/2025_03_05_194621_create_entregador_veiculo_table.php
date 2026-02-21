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
        Schema::create('entregador_veiculo', function (Blueprint $table) {
            $table->id();
            // Use o nome correto da tabela aqui
            $table->unsignedBigInteger('entregador_id');
            $table->unsignedBigInteger('veiculo_id');
            $table->timestamps();
            
            // Garantir que não haja duplicatas
            $table->unique(['entregador_id', 'veiculo_id']);
            
            // Adicione as chaves estrangeiras com o nome correto das tabelas
            $table->foreign('entregador_id')
                  ->references('id')
                  ->on('entregadores')  // Nome correto da tabela
                  ->onDelete('cascade');
                  
            $table->foreign('veiculo_id')
                  ->references('id')
                  ->on('veiculos')  // Nome correto da tabela de veículos
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entregador_veiculo');
    }
};