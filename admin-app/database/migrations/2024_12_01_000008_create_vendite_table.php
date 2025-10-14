<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendite', function (Blueprint $table) {
            $table->id();
            $table->string('istanza', 191)->unique();
            $table->string('id_sede', 191)->nullable();
            $table->integer('id_vendita')->unique();
            $table->integer('id_lista')->nullable();
            $table->string('id_lead', 191)->nullable();
            $table->string('userlogin', 191)->nullable();
            $table->string('campagna_id', 191)->nullable()->unique();
            $table->string('codice_pratica', 191)->nullable();
            $table->string('chiave_fatturazione', 191)->nullable();
            $table->date('data_vendita')->nullable();
            $table->date('data_inserimento')->nullable();
            $table->string('esito_vendita', 191)->nullable();
            $table->string('esito_cliente', 191)->nullable();
            $table->timestamps();

            $table->foreign('id_sede')
                  ->references('id_sede')
                  ->on('sedi')
                  ->onDelete('set null');

            $table->foreign('id_lead')
                  ->references('id_lead')
                  ->on('leads')
                  ->onDelete('set null');

            $table->foreign('userlogin')
                  ->references('userlogin')
                  ->on('operatori')
                  ->onDelete('set null');

            $table->foreign('campagna_id')
                  ->references('id_campagna')
                  ->on('campagne')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendite');
    }
};
