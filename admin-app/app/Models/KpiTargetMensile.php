<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model per KPI Target Mensile
 * Tabella: kpi_target_mensile
 * Significato: KPI di target o obiettivi mensili (dati di pianificazione)
 */
class KpiTargetMensile extends Model
{
    protected $table = 'kpi_target_mensile';
    
    protected $fillable = [
        'commessa',
        'sede_crm',
        'sede_estesa',
        'nome_kpi',
        'anno',
        'mese',
        'valore_kpi',
    ];
    
    public $timestamps = false;
    
    /**
     * Scope per filtrare per periodo (anno/mese)
     */
    public function scopeByPeriod($query, $anno, $mese)
    {
        return $query->where('anno', $anno)
                     ->where('mese', $mese);
    }
    
    /**
     * Scope per filtrare per commessa
     */
    public function scopeByCommessa($query, $commessa)
    {
        return $query->where('commessa', $commessa);
    }
    
    /**
     * Scope per filtrare per sede
     */
    public function scopeBySede($query, $sede)
    {
        return $query->where('sede_crm', $sede);
    }
}
