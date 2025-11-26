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
        'sede_id',
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
     * Ottieni liste come array (supporta CSV)
     */
    public function getListeArray()
    {
        if (empty($this->liste_ripartizione)) {
            return [];
        }
        
        // Tratta come CSV
        return array_map('trim', explode(',', $this->liste_ripartizione));
    }
}

