<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToContasV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('contas_receber', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('contas_receber', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
