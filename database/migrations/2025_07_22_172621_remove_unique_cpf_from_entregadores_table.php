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
        Schema::table('entregadores', function (Blueprint $table) {
            $table->dropUnique('entregadores_cpf_unique');
        });
    }

    public function down()
    {
        Schema::table('entregadores', function (Blueprint $table) {
            $table->unique('cpf');
        });
    }
};
