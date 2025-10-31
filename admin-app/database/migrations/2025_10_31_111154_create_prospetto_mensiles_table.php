<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prospetto_mensiles', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique(); // es: "Novembre 2024"
            $table->integer('mese'); // 1-12
            $table->integer('anno'); // 2024, 2025, etc.
            $table->text('descrizione')->nullable();
            $table->json('dati_accounts'); // Struttura JSON con tutti gli account e settimane
            $table->boolean('attivo')->default(true);
            $table->timestamps();
            
            $table->index(['mese', 'anno']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospetto_mensiles');
    }
};
