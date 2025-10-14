<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigurazioneProdotto extends Model
{
    use HasFactory;

    protected $table = 'configurazione_prodotti';

    protected $fillable = [
        'product_id',
        'istanza',
        'nome_prodotto',
        'macro_prodotto',
        'macro_campagna',
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
     * Relazioni (soft relations - senza FK nel database)
     * Queste relazioni permettono di fare query ma non sono enforced dal DB
     */
    
    /**
     * Relazione verso Prodotto basata su nome_prodotto
     * Nota: Relazione soft - nome_prodotto non è una PK in prodotti
     */
    public function prodotto()
    {
        return $this->belongsTo(Prodotto::class, 'nome_prodotto', 'nome_prodotto');
    }

    /**
     * Relazione verso Campagna basata su macro_campagna
     * Nota: Relazione soft - macro_campagna non è una PK in campagne
     */
    public function campagna()
    {
        return $this->belongsTo(Campagna::class, 'macro_campagna', 'macro_campagna');
    }

    /**
     * Relazione verso Vendita basata su istanza
     * Nota: Relazione soft - istanza non è unique in vendite
     */
    public function vendita()
    {
        return $this->belongsTo(Vendita::class, 'istanza', 'istanza');
    }

    /**
     * Relazione verso Prodotto specifico basata su product_id (id_prodotto)
     */
    public function prodottoById()
    {
        return $this->belongsTo(Prodotto::class, 'product_id', 'id_prodotto');
    }
}

