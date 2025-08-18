<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FcmToken;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class FcmTokenController extends Controller
{
    public function saveFcmToken(Request $request)
    {
        try {
            // Validazione della richiesta
            $validatedData = $request->validate([
                'fcm_token' => 'required|string',
                'user_id' => 'required|exists:users,id',
                'device_type' => 'nullable|string',
            ]);

            // Verifica se esiste già un token con lo stesso valore per lo stesso user_id
            $existingToken = FcmToken::where('fcm_token', $validatedData['fcm_token'])
                ->where('user_id', $validatedData['user_id'])
                ->first();

            if ($existingToken) {
                // Se esiste già per questo utente, aggiorna il timestamp e restituisce il record esistente
                $existingToken->touch(); // Aggiorna il campo updated_at
                return response()->json([
                    'message' => 'FCM Token già registrato per questo utente, aggiornato timestamp!',
                    'token' => $existingToken
                ], 200);
            }

            // Se non esiste, crea un nuovo record
            $fcmToken = FcmToken::create([
                'user_id' => $validatedData['user_id'],
                'fcm_token' => $validatedData['fcm_token'],
                'device_type' => $validatedData['device_type'] ?? 'unknown',
            ]);

            return response()->json([
                'message' => 'FCM Token salvato con successo!',
                'token' => $fcmToken
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Errore di validazione',
                'errors' => $e->errors()
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Errore nel salvataggio del FCM Token',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Si è verificato un errore imprevisto',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}