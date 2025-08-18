<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUserIdFromNotificationsTable extends Migration
{
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Rimuovi la constraint della chiave esterna prima di rimuovere la colonna
            $table->dropForeign(['user_id']);  // Drop the foreign key constraint
            $table->dropColumn('user_id');     // Drop the column
        });
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Se la migration dovesse essere annullata, aggiungi di nuovo user_id e la sua foreign key
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}

