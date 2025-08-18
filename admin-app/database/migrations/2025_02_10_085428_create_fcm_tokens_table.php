<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relazione con users
            $table->string('fcm_token')->unique(); // Token univoco per dispositivo
            $table->string('device_type')->nullable(); // Facoltativo: iOS, Android, Web
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fcm_tokens');
    }
};
