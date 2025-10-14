<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $table = 'leads';

    protected $fillable = [
        'id_lead',
        'id_lista',
        'userlogin',
        'id_campagna',
        'id_servizio_mandato',
        'id_sede',
        'data_import',
        'tipo_lead',
        'consenso_trattamento',
        'stato_lead',
        'esito_finale',
        'note',
    ];

    protected $casts = [
        'data_import' => 'date',
        'consenso_trattamento' => 'boolean',
    ];

    /**
     * Relazioni
     */
    public function operatore()
    {
        return $this->belongsTo(Operatore::class, 'userlogin', 'userlogin');
    }

    public function campagna()
    {
        return $this->belongsTo(Campagna::class, 'id_campagna', 'id_campagna');
    }

    public function servizio()
    {
        return $this->belongsTo(Servizio::class, 'id_servizio_mandato', 'id_servizio_mandato');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'id_sede', 'id_sede');
    }

    public function vendite()
    {
        return $this->hasMany(Vendita::class, 'id_lead', 'id_lead');
    }
}

