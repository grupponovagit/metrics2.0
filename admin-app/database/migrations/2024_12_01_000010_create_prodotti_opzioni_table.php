<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prodotti_opzioni', function (Blueprint $table) {
            $table->id();
            $table->integer('id_prodotto');
            $table->string('istanza', 191);
            $table->integer('id_vendita');
            $table->string('nome_opzione', 191)->nullable();
            $table->text('descrizione')->nullable();
            $table->string('valore_opzione', 2)->nullable();
            $table->timestamps();

            $table->foreign('id_prodotto')
                  ->references('id_prodotto')
                  ->on('prodotti')
                  ->onDelete('cascade');

            $table->foreign('istanza')
                  ->references('istanza')
                  ->on('prodotti')
                  ->onDelete('cascade');

            $table->foreign('id_vendita')
                  ->references('id_vendita')
                  ->on('prodotti')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prodotti_opzioni');
    }
};
