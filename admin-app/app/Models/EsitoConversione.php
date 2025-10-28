<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EsitoConversione extends Model
{
    use HasFactory;

    protected $table = 'esiti_conversione';

    protected $fillable = [
        'commessa',
        'esito_originale',
        'esito_globale',
        'note',
    ];

    /**
     * Esiti globali disponibili
     */
    public const ESITI_GLOBALI = [
        'OK' => 'OK',
        'KO' => 'KO',
        'IN_ATTESA' => 'In Attesa',
        'BACKLOG' => 'BackLog',
        'BACKLOG_PARTNER' => 'BackLog Partner',
    ];

    /**
     * Ottieni tutti gli esiti raggruppati per commessa
     */
    public static function getEsitiGroupedByCommessa()
    {
        return self::orderBy('commessa')
            ->orderBy('esito_originale')
            ->get()
            ->groupBy('commessa');
    }

    /**
     * Ottieni tutte le commesse distinte
     */
    public static function getCommesse()
    {
        return self::select('commessa')
            ->distinct()
            ->orderBy('commessa')
            ->pluck('commessa');
    }

    /**
     * Converti un esito originale in esito globale
     * 
     * @param string $commessa
     * @param string $esitoOriginale
     * @return string|null
     */
    public static function convertiEsito($commessa, $esitoOriginale)
    {
        $conversione = self::where('commessa', $commessa)
            ->where('esito_originale', $esitoOriginale)
            ->first();

        return $conversione ? $conversione->esito_globale : null;
    }

    /**
     * Ottieni il conteggio esiti per commessa
     */
    public static function conteggioPerCommessa()
    {
        return self::selectRaw('commessa, COUNT(*) as totale')
            ->groupBy('commessa')
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
            'BACKLOG' => 'badge-info',
            'BACKLOG_PARTNER' => 'badge-secondary',
            default => 'badge-neutral',
        };
    }

    /**
     * Scope per filtrare per commessa
     */
    public function scopeCommessa($query, $commessa)
    {
        return $query->where('commessa', $commessa);
    }

    /**
     * Scope per filtrare per esito globale
     */
    public function scopeEsitoGlobale($query, $esitoGlobale)
    {
        return $query->where('esito_globale', $esitoGlobale);
    }
}

