<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhotoUrlAndProviderToUsersTable extends Migration
{
    /**
     * Esegui le modifiche alla tabella.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('photo_url')->nullable()->after('email'); // Colonna per l'URL della foto
            $table->string('provider')->nullable()->after('photo_url'); // Colonna per il provider (Google, Apple, ecc.)
        });
    }

    /**
     * Reverti le modifiche.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['photo_url', 'provider']);
        });
    }
}

