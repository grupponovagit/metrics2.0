<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sedi', function (Blueprint $table) {
            $table->id();
            $table->string('id_sede', 191)->unique();
            $table->string('nome_sede', 191)->nullable();
            $table->string('comune', 191)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sedi');
    }
};
