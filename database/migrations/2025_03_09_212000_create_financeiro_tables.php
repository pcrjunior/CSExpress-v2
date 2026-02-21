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
        Schema::create('contas_pagar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordem_servico_id')->constrained('ordem_servicos')->onDelete('cascade');
            $table->foreignId('entregador_id')->constrained('entregadores');
            $table->enum('tipo', ['motorista', 'ajudante']);
            $table->decimal('valor_total', 10, 2);
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            $table->enum('status_pagamento', ['pendente', 'pago'])->default('pendente');
            $table->text('observacao')->nullable();
            $table->timestamps();
        });

        Schema::create('contas_receber', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordem_servico_id')->constrained('ordem_servicos')->onDelete('cascade');
            $table->decimal('valor_total', 10, 2);
            $table->date('data_vencimento');
            $table->date('data_recebimento')->nullable();
            $table->enum('status_pagamento', ['pendente', 'recebido'])->default('pendente');
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contas_pagar');
        Schema::dropIfExists('contas_receber');
    }
};