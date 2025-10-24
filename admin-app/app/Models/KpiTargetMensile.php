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
        'kpi_variato',
        'data_validita_inizio',
        'data_validita_fine',
    ];
    
    protected $casts = [
        'valore_kpi' => 'decimal:2',
        'kpi_variato' => 'decimal:2',
        'data_validita_inizio' => 'date',
        'data_validita_fine' => 'date',
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
    
    /**
     * Calcola il valore KPI valido per una data specifica
     */
    public function getValorePerData($data)
    {
        $dataCarbon = \Carbon\Carbon::parse($data);
        
        // Se c'è un valore variato e la data è >= data_validita_inizio
        if ($this->kpi_variato && $this->data_validita_inizio) {
            $dataInizio = \Carbon\Carbon::parse($this->data_validita_inizio);
            
            // Controlla se la data ricade nel periodo di validità del valore variato
            if ($dataCarbon->gte($dataInizio)) {
                // Se c'è una data_fine, controlla che la data sia <= data_fine
                if ($this->data_validita_fine) {
                    $dataFine = \Carbon\Carbon::parse($this->data_validita_fine);
                    if ($dataCarbon->lte($dataFine)) {
                        return $this->kpi_variato;
                    }
                } else {
                    // Nessuna data_fine, usa il valore variato
                    return $this->kpi_variato;
                }
            }
        }
        
        // Altrimenti ritorna il valore iniziale
        return $this->valore_kpi;
    }
    
    /**
     * Calcola la media ponderata del mese
     */
    public function getMediaPonderata()
    {
        if (!$this->kpi_variato || !$this->data_validita_inizio) {
            return $this->valore_kpi;
        }
        
        $dataInizioMese = \Carbon\Carbon::create($this->anno, $this->mese, 1);
        $dataFineMese = $dataInizioMese->copy()->endOfMonth();
        $totaleGiorni = $dataFineMese->day;
        
        $dataCambio = \Carbon\Carbon::parse($this->data_validita_inizio);
        $dataFineVariato = $this->data_validita_fine 
            ? \Carbon\Carbon::parse($this->data_validita_fine) 
            : $dataFineMese;
        
        // Giorni con valore iniziale (dal 1 al giorno prima del cambio)
        $giorniIniziale = max(0, $dataCambio->day - 1);
        
        // Giorni con valore variato (dal cambio alla fine)
        $giorniVariato = min($dataFineVariato->day, $totaleGiorni) - $dataCambio->day + 1;
        
        // Calcolo media ponderata
        $mediaPonderata = (
            ($this->valore_kpi * $giorniIniziale) + 
            ($this->kpi_variato * $giorniVariato)
        ) / $totaleGiorni;
        
        return round($mediaPonderata, 2);
    }
    
    /**
     * Verifica se il KPI ha una variazione
     */
    public function hasVariazione()
    {
        return !is_null($this->kpi_variato) && !is_null($this->data_validita_inizio);
    }
}
