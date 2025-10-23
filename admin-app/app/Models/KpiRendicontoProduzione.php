<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model per KPI Rendiconto Produzione
 * Tabella: kpi_rendiconto_produzione
 * Significato: KPI effettivi o consuntivo produzione (dati di esecuzione)
 */
class KpiRendicontoProduzione extends Model
{
    protected $table = 'kpi_rendiconto_produzione';
    
    protected $fillable = [
        'commessa',
        'istanza',
        'servizio_mandato',
        'macrocampagna',
        'nome_kpi',
        'valore_kpi',
        'descrizione',
        'note_del_mese_di_ottobre',
    ];
    
    public $timestamps = false;
    
    /**
     * Scope per filtrare per commessa
     */
    public function scopeByCommessa($query, $commessa)
    {
        return $query->where('commessa', $commessa);
    }
    
    /**
     * Scope per filtrare per servizio
     */
    public function scopeByServizio($query, $servizio)
    {
        return $query->where('servizio_mandato', $servizio);
    }
}
