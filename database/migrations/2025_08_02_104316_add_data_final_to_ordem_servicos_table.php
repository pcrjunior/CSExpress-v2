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
        Schema::table('ordem_servicos', function (Blueprint $table) {
            $table->date('data_final')->nullable()->after('data_servico');
        });
    }

    public function down()
    {
        Schema::table('ordem_servicos', function (Blueprint $table) {
            $table->dropColumn('data_final');
        });
    }
};
