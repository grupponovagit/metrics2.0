<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configurazione_prodotti', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id')->nullable();
            $table->string('istanza', 191)->nullable();
            $table->string('nome_prodotto', 191)->nullable();
            $table->string('macro_prodotto', 191)->nullable();
            $table->string('macro_campagna', 191)->nullable();
            $table->decimal('prezzo', 12, 2)->nullable();
            $table->date('valido_dal')->nullable();
            $table->date('valido_al')->nullable();
            $table->timestamps();

            $table->foreign('nome_prodotto')
                  ->references('nome_prodotto')
                  ->on('prodotti')
                  ->onDelete('set null');

            $table->foreign('macro_campagna')
                  ->references('macro_campagna')
                  ->on('campagne')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configurazione_prodotti');
    }
};
