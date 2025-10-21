<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigurazioneOpzioneProdotto extends Model
{
    use HasFactory;

    protected $table = 'configurazione_opzioni_prodotto';

    protected $fillable = [
        'istanza',
        'nome_prodotto',
        'macro_campagna',
        'campagna_id',
        'nome_opzione',
        'prezzo',
        'valido_dal',
        'valido_al',
    ];

    protected $casts = [
        'prezzo' => 'decimal:2',
        'valido_dal' => 'date',
        'valido_al' => 'date',
    ];

    /**
     * Relazioni (tutte soft - senza FK nel database)
     */
    
    /**
     * Relazione verso Campagna basata su campagna_id
     * Nota: Relazione soft - punta a campagna_id
     */
    public function campagna()
    {
        return $this->belongsTo(Campagna::class, 'campagna_id', 'campagna_id');
    }

    /**
     * Relazione verso Campagna basata su macro_campagna
     * Nota: Relazione soft - macro_campagna non è una PK in campagne
     */
    public function campagnaMacro()
    {
        return $this->belongsTo(Campagna::class, 'macro_campagna', 'macro_campagna');
    }

    /**
     * Relazione verso Prodotto basata su nome_prodotto
     * Nota: Relazione soft - nome_prodotto non è una PK in prodotti
     */
    public function prodotto()
    {
        return $this->belongsTo(Prodotto::class, 'nome_prodotto', 'nome_prodotto');
    }

    /**
     * Relazione verso ProdottoOpzione basata su nome_opzione
     * Nota: Relazione soft - nome_opzione non è una PK in prodotti_opzioni
     */
    public function opzione()
    {
        return $this->belongsTo(ProdottoOpzione::class, 'nome_opzione', 'nome_opzione');
    }
}

