<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('responsavel2', 100)->nullable()->after('responsavel');
            $table->string('telefone2', 20)->nullable()->after('telefone');
            $table->string('email2', 100)->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['responsavel2', 'telefone2', 'email2']);
        });
    }
};
