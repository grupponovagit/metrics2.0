<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prodotto extends Model
{
    use HasFactory;

    protected $table = 'prodotti';

    protected $fillable = [
        'id_prodotto',
        'istanza',
        'id_vendita',
        'nome_prodotto',
        'tipologia_prodotto',
        'peso',
    ];

    /**
     * Relazioni
     */
    public function vendita()
    {
        return $this->belongsTo(Vendita::class, 'id_vendita', 'id_vendita');
    }

    public function opzioni()
    {
        return $this->hasMany(ProdottoOpzione::class, 'id_prodotto', 'id_prodotto');
    }

    /**
     * Relazione soft verso configurazione prodotti basata su nome_prodotto
     */
    public function configurazioni()
    {
        return $this->hasMany(ConfigurazioneProdotto::class, 'nome_prodotto', 'nome_prodotto');
    }

    /**
     * Relazione soft verso configurazione prodotti basata su id_prodotto
     */
    public function configurazioniById()
    {
        return $this->hasMany(ConfigurazioneProdotto::class, 'product_id', 'id_prodotto');
    }

    /**
     * Relazione soft verso configurazione opzioni prodotto basata su nome_prodotto
     */
    public function configurazioniOpzioni()
    {
        return $this->hasMany(ConfigurazioneOpzioneProdotto::class, 'nome_prodotto', 'nome_prodotto');
    }
}

