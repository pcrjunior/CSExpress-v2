<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntregadoresTable extends Migration
{
    public function up()
    {
        Schema::create('entregadores', function (Blueprint $table) {
            $table->id();
            // Informações pessoais
            $table->string('nome');
            $table->string('cpf')->unique();
            $table->date('data_nascimento')->nullable();
            $table->string('endereco')->nullable();
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();
            // CNH
            $table->string('cnh_numero')->nullable();
            $table->date('cnh_validade')->nullable();
            $table->string('cnh_categoria')->nullable();
            // Status do entregador (habilitado/desabilitado)
            $table->boolean('active')->default(false);
            // Upload da foto
            $table->string('foto')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Se desejar manter o histórico de exclusões
        });
    }

    public function down()
    {
        Schema::dropIfExists('entregadores');
    }
}
