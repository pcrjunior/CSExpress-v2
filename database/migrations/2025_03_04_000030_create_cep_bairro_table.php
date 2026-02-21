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
        Schema::create('dbo.CEP_Bairro', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('Zona', 255)->nullable();
            $table->text('Bairro')->nullable();
            $table->string('CEP_Inicial', 10)->nullable();
            $table->string('CEP_Final', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('dbo.CEP_Bairro');
    }
};
