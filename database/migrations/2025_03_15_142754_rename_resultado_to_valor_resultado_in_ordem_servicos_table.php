<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement("EXEC sp_rename 'ordem_servicos.resultado', 'valor_resultado', 'COLUMN'");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::statement("EXEC sp_rename 'ordem_servicos.valor_resultado', 'resultado', 'COLUMN'");
    }
};
