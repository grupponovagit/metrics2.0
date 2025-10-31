<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProspettoMensile extends Model
{
    protected $table = 'prospetto_mensiles';
    
    protected $fillable = [
        'nome',
        'mese',
        'anno',
        'giorni_lavorativi',
        'descrizione',
        'dati_accounts',
        'attivo',
    ];
    
    protected $casts = [
        'dati_accounts' => 'array',
        'attivo' => 'boolean',
        'mese' => 'integer',
        'anno' => 'integer',
        'giorni_lavorativi' => 'integer',
    ];
    
    /**
     * Scope per prospetti attivi
     */
    public function scopeAttivi($query)
    {
        return $query->where('attivo', true);
    }
    
    /**
     * Scope per filtrare per anno
     */
    public function scopeAnno($query, $anno)
    {
        return $query->where('anno', $anno);
    }
    
    /**
     * Ottieni il prospetto di un mese specifico
     */
    public static function getByMeseAnno($mese, $anno)
    {
        return self::where('mese', $mese)
            ->where('anno', $anno)
            ->first();
    }
    
    /**
     * Calcola il budget mensile totale
     * Formula: Budget giornaliero finale × giorni lavorativi
     * Esclude la settimana 0 (partenza) e usa l'ultima settimana effettiva
     */
    public function getBudgetMensileAttribute()
    {
        if (!$this->dati_accounts || !isset($this->dati_accounts['accounts'])) {
            return 0;
        }
        
        $totalDailyBudget = 0;
        foreach ($this->dati_accounts['accounts'] as $account) {
            if (isset($account['weeks']) && count($account['weeks']) > 0) {
                // Filtra solo le settimane >= 1 (esclude week 0 = partenza)
                $effectiveWeeks = array_filter($account['weeks'], function($week) {
                    return isset($week['week']) && $week['week'] >= 1;
                });
                
                if (count($effectiveWeeks) > 0) {
                    // Prendi l'ultima settimana effettiva
                    $lastWeek = end($effectiveWeeks);
                    $totalDailyBudget += $lastWeek['budget'] ?? 0;
                } else {
                    // Se non ci sono settimane effettive, usa comunque l'ultima disponibile
                    $lastWeek = end($account['weeks']);
                    $totalDailyBudget += $lastWeek['budget'] ?? 0;
                }
            }
        }
        
        // Usa i giorni lavorativi del prospetto (default 24 se non specificato)
        $giorniLavorativi = $this->giorni_lavorativi ?? 24;
        return $totalDailyBudget * $giorniLavorativi;
    }
}
