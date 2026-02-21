<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressFieldsToEntregadoresTable extends Migration
{
    public function up()
    {
        Schema::table('entregadores', function (Blueprint $table) {
            // Adiciona os campos de endereço
            $table->string('cep')->nullable()->after('data_nascimento');
            // O campo "endereco" já deve existir se usamos anteriormente, mas se não, adicione-o:
            $table->string('numero')->nullable()->after('endereco');
            $table->string('complemento')->nullable()->after('numero');
            $table->string('bairro')->nullable()->after('complemento');
            $table->string('cidade')->nullable()->after('bairro');
            $table->string('estado', 2)->nullable()->after('cidade'); // Geralmente os estados são representados por 2 caracteres
        });
    }

    public function down()
    {
        Schema::table('entregadores', function (Blueprint $table) {
            $table->dropColumn(['cep', 'numero', 'complemento', 'bairro', 'cidade', 'estado']);
        });
    }
}
