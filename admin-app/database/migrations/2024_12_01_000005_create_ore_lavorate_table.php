<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ore_lavorate', function (Blueprint $table) {
            $table->id();
            $table->string('userlogin', 191);
            $table->string('id_servizio', 191);
            $table->string('id_campagna', 191)->nullable();
            $table->string('id_sede', 191)->nullable();
            $table->date('data')->nullable();
            $table->integer('ore_lavorate')->nullable()->default(0);
            
            $table->integer('BRIEFING_Pausa')->nullable()->default(0);
            $table->integer('626_Pausa')->nullable()->default(0);
            $table->integer('GENERICA_Pausa')->nullable()->default(0);
            $table->integer('Agenda')->nullable()->default(0);
            $table->integer('Ready')->nullable()->default(0);
            $table->integer('Assign')->nullable()->default(0);
            $table->integer('In_Call')->nullable()->default(0);
            $table->integer('Wac')->nullable()->default(0);
            
            $table->timestamps();

            $table->foreign('userlogin')
                  ->references('userlogin')
                  ->on('operatori')
                  ->onDelete('cascade');

            $table->foreign('id_servizio')
                  ->references('id_servizio_mandato')
                  ->on('servizi')
                  ->onDelete('cascade');

            $table->foreign('id_campagna')
                  ->references('id_campagna')
                  ->on('campagne')
                  ->onDelete('set null');

            $table->foreign('id_sede')
                  ->references('id_sede')
                  ->on('sedi')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ore_lavorate');
    }
};
