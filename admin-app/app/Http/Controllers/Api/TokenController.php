<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;



class TokenController extends Controller
{
    public function checkToken(Request $request)
    {
        $accessToken = $request->bearerToken();

        if (!$accessToken) {
            return response()->json(['message' => 'Access token missing'], 401);
        }

        // Trova il token nella tabella `personal_access_tokens`
        $token = PersonalAccessToken::findToken($accessToken);

        if (!$token || ($token->expires_at && $token->expires_at->isPast())) {
            return response()->json(['message' => 'Invalid or expired access token'], 401);
        }

        return response()->json(['message' => 'Access token valid'], 200);
    }

    public function refreshToken(Request $request)
    {
        $refreshToken = $request->input('refresh_token');

        if (!$refreshToken) {
            return response()->json(['message' => 'Refresh token missing'], 400);
        }

        // Estrai la parte dopo il `|`
        if (strpos($refreshToken, '|') !== false) {
            [, $token] = explode('|', $refreshToken, 2); // Ottieni solo la seconda parte
        } else {
            $token = $refreshToken; // Nessuna barra |, usa tutto il token
        }

        $hashedToken = hash('sha256', $token);

        // Trova il token nella tabella `personal_access_tokens`
        $tokenRecord = PersonalAccessToken::where('token', $hashedToken)->first();

        if (!$tokenRecord || ($tokenRecord->expires_at && $tokenRecord->expires_at->isPast())) {
            return response()->json(['message' => 'Invalid or expired refresh token'], 401);
        }

        // Crea nuovi token
        $newAccessToken = $tokenRecord->tokenable->createToken('auth-token', ['*'], now()->addMinutes(30));
        $newRefreshToken = $tokenRecord->tokenable->createToken('refresh-token', ['refresh'], now()->addMonths(6));

        // Elimina il vecchio refresh token
        $tokenRecord->delete();

        return response()->json([
            'access_token' => $newAccessToken->plainTextToken,
            'refresh_token' => $newRefreshToken->plainTextToken,
        ]);
    }
}
