<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendita extends Model
{
    use HasFactory;

    protected $table = 'vendite';

    protected $fillable = [
        'istanza',
        'id_sede',
        'id_vendita',
        'id_lista',
        'id_lead',
        'userlogin',
        'campagna_id',
        'codice_pratica',
        'chiave_fatturazione',
        'data_vendita',
        'data_inserimento',
        'esito_vendita',
        'esito_cliente',
    ];

    protected $casts = [
        'data_vendita' => 'date',
        'data_inserimento' => 'date',
    ];

    /**
     * Relazioni
     */
    public function sede()
    {
        return $this->belongsTo(Sede::class, 'id_sede', 'id_sede');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'id_lead', 'id_lead');
    }

    public function operatore()
    {
        return $this->belongsTo(Operatore::class, 'userlogin', 'userlogin');
    }

    public function campagna()
    {
        return $this->belongsTo(Campagna::class, 'campagna_id', 'id_campagna');
    }

    public function prodotti()
    {
        return $this->hasMany(Prodotto::class, 'id_vendita', 'id_vendita');
    }

    /**
     * Relazione soft verso configurazione prodotti basata su istanza
     */
    public function configurazioneProdotti()
    {
        return $this->hasMany(ConfigurazioneProdotto::class, 'istanza', 'istanza');
    }

    /**
     * Relazione soft verso configurazione opzioni prodotto basata su campagna_id
     */
    public function configurazioneOpzioni()
    {
        return $this->hasMany(ConfigurazioneOpzioneProdotto::class, 'campagna_id', 'campagna_id');
    }
}

