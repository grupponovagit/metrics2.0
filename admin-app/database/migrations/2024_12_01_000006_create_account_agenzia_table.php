<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_agenzia', function (Blueprint $table) {
            $table->id();
            $table->integer('id_agenzia');
            $table->integer('account_id')->nullable();
            $table->string('fornitore', 100)->nullable();
            $table->text('ragione_sociale')->nullable();
            $table->string('p_iva', 100)->nullable();
            $table->string('tipo_servizio', 50)->nullable();
            $table->timestamps();

            $table->foreign('id_agenzia')
                  ->references('id_agenzia')
                  ->on('servizi')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_agenzia');
    }
};
