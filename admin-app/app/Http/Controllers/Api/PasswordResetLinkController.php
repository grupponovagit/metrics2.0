<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User; // Assicurati di importare il model User

class PasswordResetLinkController extends Controller
{
    /**
     * Handle an incoming password reset link request.
     */
    public function store(Request $request)
    {
        // Valida la richiesta
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Cerca l'utente in base all'email
        $user = User::where('email', $request->input('email'))->first();

        // Se l'utente non esiste, restituisci un errore
        if (!$user) {
            return response()->json([
                'error' => 'Non esiste alcun account associato a questo indirizzo email.'
            ], 404);
        }

        // Se l'utente esiste e la sua password è NULL, significa che l'account è gestito tramite social login
        if (is_null($user->password)) {
            return response()->json([
                'error' => 'Questo account utilizza il social login e non ha una password da resettare.'
            ], 400);
        }

        // Tenta di inviare il link per il reset della password
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Restituisci una risposta JSON in base allo stato
        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)], 200);
        } else {
            return response()->json(['error' => __($status)], 400);
        }
    }
}
