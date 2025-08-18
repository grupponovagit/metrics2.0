<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFcmTokensUniqueIndex extends Migration
{
    public function up()
    {
        Schema::table('fcm_tokens', function (Blueprint $table) {
            // Rimuove il vincolo unico esistente sul campo fcm_token
            $table->dropUnique('fcm_tokens_fcm_token_unique');
            // Aggiunge un vincolo unico composito su user_id e fcm_token
            $table->unique(['user_id', 'fcm_token'], 'fcm_tokens_user_id_fcm_token_unique');
        });
    }

    public function down()
    {
        Schema::table('fcm_tokens', function (Blueprint $table) {
            $table->dropUnique('fcm_tokens_user_id_fcm_token_unique');
            // Ripristina il vincolo unico originale sul campo fcm_token
            $table->unique('fcm_token');
        });
    }
}