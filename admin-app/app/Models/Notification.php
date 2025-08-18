<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'is_read',
        'image_notification',
        'link_notification'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'notification_user')
            ->withPivot('is_read', 'read_at')
            ->withTimestamps();
    }
}
