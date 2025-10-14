<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operatore extends Model
{
    use HasFactory;

    protected $table = 'operatori';

    protected $fillable = [
        'userlogin',
        'id_responsabile',
        'id_sede',
        'nome',
        'cognome',
        'codice_fiscale',
        'data_assunzione',
    ];

    protected $casts = [
        'data_assunzione' => 'date',
    ];

    /**
     * Relazioni
     */
    public function responsabile()
    {
        return $this->belongsTo(User::class, 'id_responsabile');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'id_sede', 'id_sede');
    }

    public function oreLavorate()
    {
        return $this->hasMany(OreLavorate::class, 'userlogin', 'userlogin');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'userlogin', 'userlogin');
    }

    public function vendite()
    {
        return $this->hasMany(Vendita::class, 'userlogin', 'userlogin');
    }
}

