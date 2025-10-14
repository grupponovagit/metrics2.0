<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servizio extends Model
{
    use HasFactory;

    protected $table = 'servizi';

    protected $fillable = [
        'id_servizio_mandato',
        'id_agenzia',
        'nome_agenzia',
        'ragione_sociale',
        'p_iva',
        'tipo_servizio',
    ];

    /**
     * Relazioni
     */
    public function campagne()
    {
        return $this->hasMany(Campagna::class, 'id_servizio_mandato', 'id_servizio_mandato');
    }

    public function oreLavorate()
    {
        return $this->hasMany(OreLavorate::class, 'id_servizio', 'id_servizio_mandato');
    }

    public function accountAgenzia()
    {
        return $this->hasOne(AccountAgenzia::class, 'id_agenzia', 'id_agenzia');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'id_servizio_mandato', 'id_servizio_mandato');
    }
}

