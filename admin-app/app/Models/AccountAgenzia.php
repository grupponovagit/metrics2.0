<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountAgenzia extends Model
{
    use HasFactory;

    protected $table = 'account_agenzia';

    protected $fillable = [
        'id_agenzia',
        'account_id',
        'fornitore',
        'ragione_sociale',
        'p_iva',
        'tipo_servizio',
    ];

    /**
     * Relazioni
     */
    public function servizio()
    {
        return $this->belongsTo(Servizio::class, 'id_agenzia', 'id_agenzia');
    }
}

