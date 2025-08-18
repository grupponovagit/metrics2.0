<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmsOtp extends Model
{
    use HasFactory;

    protected $table = 'sms_otps';

    protected $fillable = [
        'uuid', 
        'code',
        'destinatario',
        'user_id',
        'status_sms',
        'validita',
    ];
}