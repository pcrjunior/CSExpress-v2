<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToFiliaisTable extends Migration
{
    public function up()
    {
        Schema::table('filiais', function (Blueprint $table) {
            if (!Schema::hasColumn('filiais', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down()
    {
        Schema::table('filiais', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Remove a coluna deleted_at se necessário
        });
    }

}
