<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsOtpsTable extends Migration
{
    public function up()
    {
        Schema::create('sms_otps', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->nullable();
            $table->string('code')->nullable();
            $table->string('destinatario');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('status_sms')->nullable();
            // Se "validita" indica la data/ora di scadenza, possiamo usare un timestamp
            $table->boolean('validita')->nullable();
            $table->timestamps();

            // Relazione opzionale: se il campo user_id fa riferimento a users.id
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sms_otps');
    }
}