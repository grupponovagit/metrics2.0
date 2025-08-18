<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Rimuovi i campi
            $table->dropColumn(['data_nascita', 'luogo_nascita']);
            
            // Aggiungi il campo cessione_dati
            $table->boolean('cessione_dati')->default(false); // Imposta il valore predefinito a false
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // In caso di rollback, riaggiungi i campi
            $table->date('data_nascita')->nullable();
            $table->string('luogo_nascita')->nullable();
            
            // Rimuovi il campo cessione_dati
            $table->dropColumn('cessione_dati');
        });
    }
};
