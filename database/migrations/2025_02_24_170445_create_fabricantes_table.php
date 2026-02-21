<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFabricantesTable extends Migration
{
    public function up()
    {
        Schema::create('fabricantes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');                       // Nome do fabricante
            $table->string('apelido')->nullable();          // Apelido (opcional)
            $table->enum('tipo', ['Veiculos', 'Motos']);      // Tipo: Veículos ou Motos
            $table->boolean('active')->default(true);       // Status: ativo ou não
            $table->timestamps();                           // created_at e updated_at
            $table->softDeletes();                          // deleted_at para soft delete (opcional)
        });
    }

    public function down()
    {
        Schema::dropIfExists('fabricantes');
    }
}
