<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdottoOpzione extends Model
{
    use HasFactory;

    protected $table = 'prodotti_opzioni';

    protected $fillable = [
        'id_prodotto',
        'istanza',
        'id_vendita',
        'nome_opzione',
        'descrizione',
        'valore_opzione',
    ];

    /**
     * Relazioni
     */
    public function prodotto()
    {
        return $this->belongsTo(Prodotto::class, 'id_prodotto', 'id_prodotto');
    }

    /**
     * Relazione soft verso configurazione opzioni prodotto basata su nome_opzione
     */
    public function configurazioniOpzioni()
    {
        return $this->hasMany(ConfigurazioneOpzioneProdotto::class, 'nome_opzione', 'nome_opzione');
    }
}

