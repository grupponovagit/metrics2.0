<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('id_lead', 191)->unique();
            $table->integer('id_lista')->nullable();
            $table->string('userlogin', 191)->nullable();
            $table->string('id_campagna', 191)->nullable();
            $table->string('id_servizio_mandato', 191)->nullable();
            $table->string('id_sede', 191)->nullable();
            $table->date('data_import')->nullable();
            $table->string('tipo_lead', 191)->nullable();
            $table->boolean('consenso_trattamento')->nullable();
            $table->string('stato_lead', 50)->nullable();
            $table->string('esito_finale', 50)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('userlogin')
                  ->references('userlogin')
                  ->on('operatori')
                  ->onDelete('set null');

            $table->foreign('id_campagna')
                  ->references('id_campagna')
                  ->on('campagne')
                  ->onDelete('set null');

            $table->foreign('id_servizio_mandato')
                  ->references('id_servizio_mandato')
                  ->on('servizi')
                  ->onDelete('set null');

            $table->foreign('id_sede')
                  ->references('id_sede')
                  ->on('sedi')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
