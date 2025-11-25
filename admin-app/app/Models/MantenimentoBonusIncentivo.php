<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MantenimentoBonusIncentivo extends Model
{
    protected $table = 'mantenimenti_bonus_incentivi';
    
    protected $fillable = [
        'istanza',
        'commessa',
        'macro_campagna',
        'tipologia_ripartizione',
        'sedi_ripartizione',
        'liste_ripartizione',
        'extra_bonus',
        'valido_dal',
        'valido_al',
    ];
    
    protected $casts = [
        'extra_bonus' => 'decimal:2',
        'valido_dal' => 'date',
        'valido_al' => 'date',
    ];
    
    /**
     * Scope per filtrare per istanza
     */
    public function scopeByIstanza($query, $istanza)
    {
        return $query->where('istanza', $istanza);
    }
    
    /**
     * Scope per filtrare per commessa
     */
    public function scopeByCommessa($query, $commessa)
    {
        return $query->where('commessa', $commessa);
    }
    
    /**
     * Scope per filtrare per tipologia
     */
    public function scopeByTipologia($query, $tipologia)
    {
        return $query->where('tipologia_ripartizione', $tipologia);
    }
    
    /**
     * Scope per bonus validi da una certa data
     */
    public function scopeValidiDa($query, $data)
    {
        return $query->where('valido_dal', '<=', $data);
    }
    
    /**
     * Ottieni sedi come array
     */
    public function getSediArray()
    {
        if (empty($this->sedi_ripartizione)) {
            return [];
        }
        
        // Se è JSON
        $decoded = json_decode($this->sedi_ripartizione, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }
        
        // Altrimenti tratta come CSV
        return array_map('trim', explode(',', $this->sedi_ripartizione));
    }
    
    /**
     * Ottieni macro campagne come array
     */
    public function getMacroCampagneArray()
    {
        if (empty($this->macro_campagna)) {
            return [];
        }
        
        // Se è JSON
        $decoded = json_decode($this->macro_campagna, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }
        
        // Altrimenti tratta come stringa singola (per retrocompatibilità)
        return [$this->macro_campagna];
    }
    
    /**
     * Ottieni liste come array
     */
    public function getListeArray()
    {
        if (empty($this->liste_ripartizione)) {
            return [];
        }
        
        // Se è JSON
        $decoded = json_decode($this->liste_ripartizione, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }
        
        // Altrimenti tratta come CSV
        return array_map('trim', explode(',', $this->liste_ripartizione));
    }
}

