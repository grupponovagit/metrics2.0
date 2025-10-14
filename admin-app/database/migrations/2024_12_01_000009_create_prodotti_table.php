<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prodotti', function (Blueprint $table) {
            $table->id();
            $table->integer('id_prodotto')->unique();
            $table->string('istanza', 191);
            $table->integer('id_vendita');
            $table->string('nome_prodotto', 191)->nullable()->unique();
            $table->string('tipologia_prodotto', 191)->nullable();
            $table->integer('peso')->nullable();
            $table->timestamps();

            $table->foreign('istanza')
                  ->references('istanza')
                  ->on('vendite')
                  ->onDelete('cascade');

            $table->foreign('id_vendita')
                  ->references('id_vendita')
                  ->on('vendite')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prodotti');
    }
};
