<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresasTable extends Migration
{
    public function up()
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('cnpj')->unique();       // Campo único para identificar a empresa
            $table->string('nome');                   // Campo obrigatório
            $table->string('apelido')->nullable();
            $table->string('email')->nullable();
            $table->string('nome_contato')->nullable();
            $table->string('telefone')->nullable();
            $table->string('logomarca')->nullable();  // Armazenará o caminho da imagem
            $table->timestamps();
            $table->softDeletes();                    // Para manter histórico de exclusões
        });
    }

    public function down()
    {
        Schema::dropIfExists('empresas');
    }
}

