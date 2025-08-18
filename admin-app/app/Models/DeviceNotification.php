<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class DeviceNotification extends Model
{
   

    protected $fillable = [
        'user_id',
        'device_type',
        'fcm_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
