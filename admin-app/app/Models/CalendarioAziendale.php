<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CalendarioAziendale extends Model
{
    protected $table = 'calendario_aziendale';
    
    protected $fillable = [
        'data',
        'anno',
        'mese',
        'giorno',
        'giorno_settimana',
        'tipo_giorno',
        'peso_giornata',
        'descrizione',
        'mandato',
        'is_ricorrente',
    ];
    
    protected $casts = [
        'data' => 'date',
        'peso_giornata' => 'decimal:2',
        'is_ricorrente' => 'boolean',
    ];
    
    /**
     * Calcola i giorni lavorativi totali in un mese
     */
    public static function giorniLavorativiMese($anno, $mese)
    {
        return self::where('anno', $anno)
            ->where('mese', $mese)
            ->sum('peso_giornata');
    }
    
    /**
     * Calcola i giorni lavorativi rimanenti nel mese corrente
     * ESCLUSO oggi
     */
    public static function giorniLavorativiRimanenti($anno = null, $mese = null)
    {
        $anno = $anno ?? date('Y');
        $mese = $mese ?? date('m');
        $oggi = date('Y-m-d');
        
        return self::where('anno', $anno)
            ->where('mese', $mese)
            ->where('data', '>', $oggi)
            ->sum('peso_giornata');
    }
    
    /**
     * Calcola i giorni lavorativi già trascorsi nel mese corrente
     * INCLUSO oggi
     */
    public static function giorniLavorativiTrascorsi($anno = null, $mese = null)
    {
        $anno = $anno ?? date('Y');
        $mese = $mese ?? date('m');
        $oggi = date('Y-m-d');
        
        return self::where('anno', $anno)
            ->where('mese', $mese)
            ->where('data', '<=', $oggi)
            ->sum('peso_giornata');
    }
    
    /**
     * Ottieni tutti i giorni festivi di un anno
     */
    public static function festivitaAnno($anno)
    {
        return self::where('anno', $anno)
            ->where('tipo_giorno', 'festivo')
            ->orderBy('data')
            ->get();
    }
    
    /**
     * Verifica se una data è lavorativa
     */
    public static function isGiornoLavorativo($data)
    {
        $giorno = self::where('data', $data)->first();
        
        if (!$giorno) {
            return false;
        }
        
        return $giorno->peso_giornata > 0;
    }
    
    /**
     * Ottieni il peso di una giornata (0.00, 0.50, 1.00)
     */
    public static function pesoGiornata($data)
    {
        $giorno = self::where('data', $data)->first();
        
        return $giorno ? $giorno->peso_giornata : 0;
    }
    
    /**
     * Statistiche mese
     */
    public static function statisticheMese($anno, $mese)
    {
        $risultato = self::where('anno', $anno)
            ->where('mese', $mese)
            ->select(
                DB::raw('COUNT(*) as totale_giorni'),
                DB::raw('SUM(peso_giornata) as giorni_lavorativi'),
                DB::raw('SUM(CASE WHEN tipo_giorno = "lavorativo" THEN 1 ELSE 0 END) as giorni_pieni'),
                DB::raw('SUM(CASE WHEN tipo_giorno = "sabato" THEN 1 ELSE 0 END) as sabati'),
                DB::raw('SUM(CASE WHEN tipo_giorno IN ("festivo", "domenica") THEN 1 ELSE 0 END) as giorni_non_lavorativi'),
                DB::raw('SUM(CASE WHEN tipo_giorno = "eccezione" THEN 1 ELSE 0 END) as eccezioni')
            )
            ->first();
        
        return $risultato;
    }
    
    /**
     * Calcola percentuale mese trascorsa (basata su giorni lavorativi)
     */
    public static function percentualeMeseTrascorsa($anno = null, $mese = null)
    {
        $anno = $anno ?? date('Y');
        $mese = $mese ?? date('m');
        
        $totali = self::giorniLavorativiMese($anno, $mese);
        $trascorsi = self::giorniLavorativiTrascorsi($anno, $mese);
        
        if ($totali == 0) {
            return 0;
        }
        
        return round(($trascorsi / $totali) * 100, 2);
    }
    
    /**
     * Ottieni tutti i giorni di un anno con dettagli
     */
    public static function giorniAnno($anno)
    {
        return self::where('anno', $anno)
            ->whereNull('mandato') // Solo giorni generali, non eccezioni specifiche
            ->orderBy('data')
            ->get();
    }
    
    /**
     * Ottieni eccezioni per un mandato specifico
     */
    public static function eccezioniMandato($mandato, $anno = null)
    {
        $query = self::where('mandato', $mandato)
            ->where('tipo_giorno', 'eccezione');
        
        if ($anno) {
            $query->where('anno', $anno);
        }
        
        return $query->orderBy('data')->get();
    }
    
    /**
     * Verifica se un giorno è lavorativo per un mandato specifico
     */
    public static function isGiornoLavorativoPerMandato($data, $mandato)
    {
        // Verifica eccezione specifica per mandato
        $eccezione = self::where('data', $data)
            ->where('mandato', $mandato)
            ->first();
        
        if ($eccezione) {
            return $eccezione->peso_giornata > 0;
        }
        
        // Altrimenti usa regola generale
        return self::isGiornoLavorativo($data);
    }
    
    /**
     * Ottieni tutti i mandati/fornitori con eccezioni
     */
    public static function getMandatiConEccezioni()
    {
        return self::whereNotNull('mandato')
            ->distinct()
            ->pluck('mandato')
            ->sort()
            ->values();
    }
}


