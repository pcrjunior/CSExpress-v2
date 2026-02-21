<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 2); // PF ou PJ
            $table->string('documento', 18)->unique(); // CPF ou CNPJ
            $table->string('nome', 100); // Nome completo ou Razão Social
            $table->string('apelido', 50)->nullable();
            $table->string('cep', 10);
            $table->string('endereco', 100);
            $table->string('numero', 20);
            $table->string('complemento', 100)->nullable();
            $table->string('cidade', 50);
            $table->string('uf', 2);
            $table->string('responsavel', 100)->nullable();
            $table->string('telefone', 20);
            $table->string('email', 100);
            $table->timestamps();
            $table->softDeletes(); // Permite exclusão lógica
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
