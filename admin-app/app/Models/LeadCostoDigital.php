<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LeadCostoDigital extends Model
{
    protected $table = 'leads_costi_digital';

    protected $fillable = [
        'id_account',
        'data',
        'utm_campaign',
        'importo_speso',
        'clicks',
        'conversioni',
    ];

    protected $casts = [
        'data' => 'date',
        'importo_speso' => 'decimal:2',
        'clicks' => 'integer',
        'conversioni' => 'decimal:2',
    ];

    /**
     * Relazione con configurazione campagna
     */
    public function configurazioneCampagna()
    {
        return $this->belongsTo(ConfigurazioneCampagnaDigital::class, 'id_account', 'account_id')
                    ->where('utm_campaign', $this->utm_campaign);
    }

    /**
     * Relazione con account agenzia
     */
    public function accountAgenzia()
    {
        return $this->belongsTo(AccountAgenzia::class, 'id_account', 'account_id');
    }

    /**
     * Scope per data di oggi
     */
    public function scopeToday($query)
    {
        return $query->whereDate('data', Carbon::today());
    }

    /**
     * Scope per range di date
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('data', [$startDate, $endDate]);
    }
}

