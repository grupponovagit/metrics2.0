<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operatori', function (Blueprint $table) {
            $table->id();
            $table->string('userlogin', 191)->unique();
            $table->unsignedBigInteger('id_responsabile')->nullable();
            $table->string('id_sede', 191)->nullable();
            $table->string('nome', 191)->nullable();
            $table->string('cognome', 191)->nullable();
            $table->string('codice_fiscale', 191)->nullable();
            $table->date('data_assunzione')->nullable();
            $table->timestamps();

            $table->foreign('id_responsabile')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            $table->foreign('id_sede')
                  ->references('id_sede')
                  ->on('sedi')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operatori');
    }
};
