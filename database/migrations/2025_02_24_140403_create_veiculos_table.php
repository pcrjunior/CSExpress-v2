<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVeiculosTable extends Migration
{
    public function up()
    {
        Schema::create('veiculos', function (Blueprint $table) {
            $table->id();
            $table->string('placa')->unique(); // placa com máscara
            $table->string('fabricante'); // vindo de combolist
            $table->string('modelo'); // vindo de combolist
            $table->year('ano_fabricacao'); // ano de fabricação
            $table->year('ano_modelo'); // ano modelo
            $table->string('categoria'); // combolist
            $table->string('cor'); // cor do veículo
            $table->boolean('rodizio')->default(false); // rodízio sim ou não
            $table->string('dia_rodizio')->nullable(); // dia do rodízio, preenchido automaticamente se rodizio sim
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('veiculos');
    }
}
