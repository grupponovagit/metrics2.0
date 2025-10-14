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


class User extends Authenticatable
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
        'phone',            // Aggiunto
        'role',
        'codice_fiscale',   // Codice fiscale
        'reparto',          // Reparto di appartenenza
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
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
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
     * Get the operatori where this user is responsabile.
     */
    public function operatori()
    {
        return $this->hasMany(Operatore::class, 'id_responsabile');
    }





    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }
}
