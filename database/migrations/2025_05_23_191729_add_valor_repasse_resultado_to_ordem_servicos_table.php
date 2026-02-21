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
            $table->decimal('valor_repasse_resultado', 10, 2)->default(0)->after('valor_resultado');
        });
    }

    public function down()
    {
        Schema::table('ordem_servicos', function (Blueprint $table) {
            $table->dropColumn('valor_repasse_resultado');
        });
    }
};
