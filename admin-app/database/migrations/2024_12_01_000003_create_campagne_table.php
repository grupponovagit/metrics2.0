<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campagne', function (Blueprint $table) {
            $table->string('id_campagna', 191)->primary();
            $table->string('id_servizio_mandato', 191);
            $table->string('nome_campagna', 100)->nullable();
            $table->string('macro_campagna', 191)->nullable()->unique();
            $table->string('canale', 50)->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->string('cliente_committente', 100)->nullable();
            $table->string('commessa', 100)->nullable();
            $table->timestamps();

            $table->foreign('id_servizio_mandato')
                  ->references('id_servizio_mandato')
                  ->on('servizi')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campagne');
    }
};
