<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class RegistrationController extends Controller
{
    // Metodo per registrare un nuovo utente
    public function register(Request $request)
    {
        // Validazione dei dati in input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[0-9]/', // Almeno un numero
                'regex:/[\W]/', // Almeno un carattere speciale
                'confirmed'
            ],
        ], [
            'password.regex' => 'La password deve contenere almeno 8 caratteri, un numero e un carattere speciale.',
            'password.confirmed' => 'Le password non coincidono.',
            'email.unique' => 'L\'email inserita è già in uso.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Creazione dell'utente
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $accessToken = $user->createToken(
            'auth-token',
            ['*'],
            now()->addMinutes(30)
        )->plainTextToken;

        $refreshToken = $user->createToken(
            'refresh-token',
            ['refresh'],
            now()->addMonths(6)
        )->plainTextToken;

        return response()->json([
            'message' => 'Registrazione completata con successo.',
            'user' => $user,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ], 201);
    }

    // Metodo per completare il profilo utente
    public function completeProfile(Request $request)
    {
        // Validazione dei campi obbligatori
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'codice_fiscale' => 'required|string|max:16|unique:users,codice_fiscale',
            'privacy' => 'required|integer|in:0,1',
            'cessione_dati' => 'required|integer|in:0,1',
            'marketing' => 'nullable|integer|in:0,1',
            'profile_complete' => 'required|boolean',
        ], [
            'codice_fiscale.unique' => 'Il codice fiscale inserito è già in uso.',
            'codice_fiscale.max' => 'Il codice fiscale non può superare i 16 caratteri.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Trova l'utente in base all'ID passato
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'message' => 'Utente non trovato.',
            ], 404);
        }

        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->codice_fiscale = $request->codice_fiscale;
        $user->privacy = $request->privacy;
        $user->cessione_dati = $request->cessione_dati;
        $user->marketing = $request->marketing;
        $user->profile_complete = $request->profile_complete;
        $user->save();

        return response()->json([
            'message' => 'Profilo completato con successo.',
            'user' => $user,
        ], 200);
    }

    // Metodo per verificare se il telefono è stato validato
    public function checkPhoneVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::find($request->user_id);

        return response()->json([
            'phone_verified' => (bool) $user->phone_verified,
        ], 200);
    }
}
