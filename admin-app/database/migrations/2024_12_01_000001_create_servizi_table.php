<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servizi', function (Blueprint $table) {
            $table->id();
            $table->string('id_servizio_mandato', 191)->unique();
            $table->integer('id_agenzia')->unique();
            $table->string('nome_agenzia', 100)->nullable();
            $table->text('ragione_sociale')->nullable();
            $table->string('p_iva', 100)->nullable();
            $table->string('tipo_servizio', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servizi');
    }
};
