<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OreLavorate extends Model
{
    use HasFactory;

    protected $table = 'ore_lavorate';

    protected $fillable = [
        'istanza',
        'userlogin',
        'id_servizio',
        'id_campagna',
        'id_sede',
        'data',
        'tempo_lavorato',
        'Pausa_BRIEFING',
        'Pausa_626',
        'Pausa_GENERICA',
        'Agenda',
        'Ready',
        'Assign',
        'In_Call',
        'Wac',
    ];

    protected $casts = [
        'data' => 'date',
        'tempo_lavorato' => 'integer',
        'Pausa_BRIEFING' => 'integer',
        'Pausa_626' => 'integer',
        'Pausa_GENERICA' => 'integer',
        'Agenda' => 'integer',
        'Ready' => 'integer',
        'Assign' => 'integer',
        'In_Call' => 'integer',
        'Wac' => 'integer',
    ];

    /**
     * Relazioni
     */
    public function operatore()
    {
        return $this->belongsTo(Operatore::class, 'userlogin', 'userlogin');
    }

    public function servizio()
    {
        return $this->belongsTo(Servizio::class, 'id_servizio', 'id_servizio_mandato');
    }

    public function campagna()
    {
        return $this->belongsTo(Campagna::class, 'id_campagna', 'campagna_id');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'id_sede', 'id_sede');
    }
}

