<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerfilToEntregadoresTable extends Migration
{
    public function up()
    {
        Schema::table('entregadores', function (Blueprint $table) {
            // Adiciona o campo 'perfil' com opções 'Motorista' ou 'Ajudante'
            // Se o seu banco de dados suportar ENUM, use-o; caso contrário, use string com validação
            $table->enum('perfil', ['Motorista', 'Ajudante'])->default('Motorista')->after('foto');
        });
    }

    public function down()
    {
        Schema::table('entregadores', function (Blueprint $table) {
            $table->dropColumn('perfil');
        });
    }
}

