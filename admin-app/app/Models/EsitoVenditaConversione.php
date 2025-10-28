<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EsitoVenditaConversione extends Model
{
    use HasFactory;

    protected $table = 'esiti_vendita_conversione';

    protected $fillable = [
        'esito_originale',
        'esito_globale',
        'note',
    ];

    /**
     * Esiti globali disponibili per vendite
     */
    public const ESITI_GLOBALI = [
        'OK' => 'OK',
        'KO' => 'KO',
        'IN_ATTESA' => 'In Attesa',
        'ANNULLATI' => 'Annullati',
        'BACKLOG' => 'BackLog',
        'BACKLOG_PARTNER' => 'BackLog Partner',
        'IN_LAVORAZIONE' => 'In Lavorazione',
    ];

    /**
     * Converti un esito originale in esito globale (case-insensitive)
     * 
     * @param string $esitoOriginale
     * @return string|null
     */
    public static function convertiEsito($esitoOriginale)
    {
        if (!$esitoOriginale) {
            return null;
        }

        // Cerca esattamente come arriva
        $conversione = self::where('esito_originale', $esitoOriginale)->first();
        
        // Se non trova, prova case-insensitive
        if (!$conversione) {
            $conversione = self::whereRaw('LOWER(esito_originale) = LOWER(?)', [$esitoOriginale])->first();
        }

        return $conversione ? $conversione->esito_globale : null;
    }

    /**
     * Ottieni tutti gli esiti raggruppati per esito globale
     */
    public static function getEsitiGroupedByGlobale()
    {
        return self::orderBy('esito_globale')
            ->orderBy('esito_originale')
            ->get()
            ->groupBy('esito_globale');
    }

    /**
     * Ottieni il conteggio esiti per tipo globale
     */
    public static function conteggioPerTipo()
    {
        return self::selectRaw('esito_globale, COUNT(*) as totale')
            ->groupBy('esito_globale')
            ->get();
    }

    /**
     * Ottieni badge color per esito globale
     */
    public static function getBadgeClass($esitoGlobale)
    {
        return match($esitoGlobale) {
            'OK' => 'badge-success',
            'KO' => 'badge-error',
            'IN_ATTESA' => 'badge-warning',
            'ANNULLATI' => 'badge-neutral',
            'BACKLOG' => 'badge-info',
            'BACKLOG_PARTNER' => 'badge-secondary',
            'IN_LAVORAZIONE' => 'badge-primary',
            default => 'badge-ghost',
        };
    }

    /**
     * Scope per filtrare per esito globale
     */
    public function scopeEsitoGlobale($query, $esitoGlobale)
    {
        return $query->where('esito_globale', $esitoGlobale);
    }

    /**
     * Cerca esiti originali simili (case-insensitive, LIKE)
     */
    public static function cercaSimili($termine)
    {
        return self::whereRaw('LOWER(esito_originale) LIKE LOWER(?)', ['%' . $termine . '%'])
            ->orderBy('esito_originale')
            ->get();
    }
}

