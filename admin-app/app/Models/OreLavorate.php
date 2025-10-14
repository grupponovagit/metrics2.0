<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OreLavorate extends Model
{
    use HasFactory;

    protected $table = 'ore_lavorate';

    protected $fillable = [
        'userlogin',
        'id_servizio',
        'id_campagna',
        'id_sede',
        'data',
        'ore_lavorate',
        'BRIEFING_Pausa',
        '626_Pausa',
        'GENERICA_Pausa',
        'Agenda',
        'Ready',
        'Assign',
        'In_Call',
        'Wac',
    ];

    protected $casts = [
        'data' => 'date',
        'ore_lavorate' => 'integer',
        'BRIEFING_Pausa' => 'integer',
        '626_Pausa' => 'integer',
        'GENERICA_Pausa' => 'integer',
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
        return $this->belongsTo(Campagna::class, 'id_campagna', 'id_campagna');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'id_sede', 'id_sede');
    }
}

