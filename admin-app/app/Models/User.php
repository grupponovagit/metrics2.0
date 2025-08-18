<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\CustomResetPassword;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasRoles, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',          // Aggiunto
        'email',
        'password',
        'codice_fiscale',   // Aggiunto
        'data_nascita',     // Aggiunto
        'luogo_nascita',    // Aggiunto
        'phone',            // Aggiunto
        'cluster',
        'privacy',
        'marketing',
        'phone_verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'codice_fiscale',   // Aggiunto per nascondere il codice fiscale, se necessario
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'data_nascita' => 'date', // Aggiunto per il casting della data di nascita
    ];

    /**
     * Get the notifications for the user.
     */

    /**
     * Get the consents for the user.
     */
    public function consents()
    {
        return $this->hasOne(Consent::class);
    }

    /**
     * Get the contracts for the user.
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get the offers associated with the user's provider.
     */
    public function offers()
    {
        return $this->hasManyThrough(Offer::class, Provider::class, 'id', 'provider_id', 'id', 'id');
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'notification_user')
            ->withPivot('is_read', 'read_at')
            ->withTimestamps();
    }
}
