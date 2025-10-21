<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campagna extends Model
{
    use HasFactory;

    protected $table = 'campagne';
    protected $primaryKey = 'campagna_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'campagna_id',
        'istanza',
        'id_servizio_mandato',
        'nome_campagna',
        'macro_campagna',
        'canale',
        'budget',
        'cliente_committente',
        'commessa',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
    ];

    /**
     * Relazioni
     */
    public function servizio()
    {
        return $this->belongsTo(Servizio::class, 'id_servizio_mandato', 'id_servizio_mandato');
    }

    public function oreLavorate()
    {
        return $this->hasMany(OreLavorate::class, 'id_campagna', 'campagna_id');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'id_campagna', 'campagna_id');
    }

    public function vendite()
    {
        return $this->hasMany(Vendita::class, 'campagna_id', 'campagna_id');
    }

    /**
     * Relazione soft verso configurazione prodotti basata su macro_campagna
     */
    public function configurazioneProdotti()
    {
        return $this->hasMany(ConfigurazioneProdotto::class, 'macro_campagna', 'macro_campagna');
    }

    /**
     * Relazione soft verso configurazione opzioni basata su campagna_id
     */
    public function configurazioneOpzioni()
    {
        return $this->hasMany(ConfigurazioneOpzioneProdotto::class, 'campagna_id', 'campagna_id');
    }

    /**
     * Relazione soft verso configurazione opzioni basata su macro_campagna
     */
    public function configurazioneOpzioniMacro()
    {
        return $this->hasMany(ConfigurazioneOpzioneProdotto::class, 'macro_campagna', 'macro_campagna');
    }
}

