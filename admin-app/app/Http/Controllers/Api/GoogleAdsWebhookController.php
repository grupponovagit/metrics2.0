<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GoogleAdsWebhookController extends Controller
{
    /**
     * Riceve i dati da Google Ads Script e li salva in leads_costi_digital
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveLeadsCosti(Request $request)
    {
        try {
            // Log della richiesta ricevuta per debug
            Log::info('📥 Dati ricevuti da Google Ads Script', [
                'payload' => $request->all(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Validazione dati
            $validator = Validator::make($request->all(), [
                'id_account' => 'required|string|max:100',
                'data' => 'required|date_format:Y-m-d',
                'utm_campaign' => 'required|string|max:100',
                'importo_speso' => 'required|numeric|min:0',
                'clicks' => 'required|integer|min:0',
                'conversioni' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                Log::warning('⚠️ Validazione fallita', [
                    'errors' => $validator->errors()->toArray()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Dati non validi',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Controlla se esiste già un record con questi dati (per evitare duplicati)
            $exists = DB::table('leads_costi_digital')
                ->where('id_account', $validated['id_account'])
                ->where('data', $validated['data'])
                ->where('utm_campaign', $validated['utm_campaign'])
                ->exists();

            if ($exists) {
                // Aggiorna il record esistente
                DB::table('leads_costi_digital')
                    ->where('id_account', $validated['id_account'])
                    ->where('data', $validated['data'])
                    ->where('utm_campaign', $validated['utm_campaign'])
                    ->update([
                        'importo_speso' => $validated['importo_speso'],
                        'clicks' => $validated['clicks'],
                        'conversioni' => $validated['conversioni'],
                    ]);

                Log::info('✅ Record aggiornato con successo', [
                    'id_account' => $validated['id_account'],
                    'data' => $validated['data'],
                    'utm_campaign' => $validated['utm_campaign']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Dati aggiornati con successo',
                    'action' => 'updated'
                ], 200);
            }

            // Inserisci nuovo record
            $id = DB::table('leads_costi_digital')->insertGetId([
                'id_account' => $validated['id_account'],
                'data' => $validated['data'],
                'utm_campaign' => $validated['utm_campaign'],
                'importo_speso' => $validated['importo_speso'],
                'clicks' => $validated['clicks'],
                'conversioni' => $validated['conversioni'],
            ]);

            Log::info('✅ Nuovo record inserito con successo', [
                'id' => $id,
                'id_account' => $validated['id_account'],
                'data' => $validated['data'],
                'utm_campaign' => $validated['utm_campaign']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dati salvati con successo',
                'action' => 'created',
                'id' => $id
            ], 201);

        } catch (\Exception $e) {
            Log::error('❌ Errore nel salvataggio dati Google Ads', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel salvataggio dei dati',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint di test per verificare che l'API sia raggiungibile
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function test()
    {
        return response()->json([
            'success' => true,
            'message' => 'Endpoint Google Ads Webhook attivo',
            'timestamp' => now()->toDateTimeString()
        ]);
    }
}

