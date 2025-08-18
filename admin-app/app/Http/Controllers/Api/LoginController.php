<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Google\Client as GoogleClient;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

use PhpParser\Node\Stmt\TryCatch;

class LoginController
{
    /**
     * Check if the user's profile is complete.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkProfileComplete(Request $request)
    {
        // Verifica che l'utente sia autenticato
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = $request->user();
        $isComplete = $user->profile_complete; // Assumi che questa colonna esista nel tuo modello User

        return response()->json(['profile_complete' => $isComplete]);
    }
    public function googleLogin(Request $request)
    {
        $request->validate([
            'idToken' => 'required|string',
            'email' => 'nullable|email',
            'name' => 'nullable|string',
            'photo_url' => 'nullable|string'
        ]);

        try {
            $client = new GoogleClient();
            $client->setAuthConfig(base_path('client_credentials.json'));
            $payload = $client->verifyIdToken($request->idToken);

            if ($payload) {
                $user = User::where('email', $payload['email'])->first();

                if ($user) {
                    // Utente esistente: crea i token
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
                        'user' => $user,
                        'access_token' => $accessToken,
                        'refresh_token' => $refreshToken,
                        'is_authenticated' => true,
                    ]);
                } else {
                    // Creazione nuovo utente
                    $newUser = User::create([
                        'email' => $payload['email'],
                        'name' => $request->name ?? $payload['name'] ?? null,
                        'surname' => $request->surname ?? null,
                        'photo_url' => $request->photo_url ?? null,
                    ]);
                    $accessToken = $newUser->createToken(
                        'auth-token',
                        ['*'],
                        now()->addMinutes(30)
                    )->plainTextToken;

                    $refreshToken = $newUser->createToken(
                        'refresh-token',
                        ['refresh'],
                        now()->addMonths(6)
                    )->plainTextToken;

                    return response()->json([
                        'user' => $newUser,
                        'is_authenticated' => false,
                        'access_token' => $accessToken,
                        'refresh_token' => $refreshToken,
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'Autenticazione fallita'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Autenticazione fallita',
                'error' => $e->getMessage()
            ], 401);
        }
    }




    public function appleLogin(Request $request)
    {
        $response = Http::get('https://appleid.apple.com/auth/keys');
        $keys = JWK::parseKeySet($response->json());

        try {
            $idToken = $request->idToken;
            $tokenParts = explode('.', $idToken);
            $header = json_decode(JWT::urlsafeB64Decode($tokenParts[0]), true);

            if (!isset($header['kid'])) {
                throw new \Exception('No key ID in token header');
            }

            $payload = JWT::decode(
                $idToken,
                $keys
            );

            $verifica = $this->validatePayload($payload);
            if ($verifica['messaggio'] == 'authenticated') {
                $email = $payload->email ?? null;

                if (!$email) {
                    return response()->json([
                        'error' => 'Email non trovata nel token.',
                    ], 400);
                }

                // Cerca l'utente nel DB
                $existingUser = User::where('email', $email)->first();

                if ($existingUser) {
                    // Utente trovato, crea i token
                    $accessToken = $existingUser->createToken(
                        'auth-token',
                        ['*'],
                        now()->addMinutes(30)
                    )->plainTextToken;

                    $refreshToken = $existingUser->createToken(
                        'refresh-token',
                        ['refresh'],
                        now()->addMonths(6)
                    )->plainTextToken;

                    return response()->json([
                        'user' => $existingUser,
                        'access_token' => $accessToken,
                        'refresh_token' => $refreshToken,
                        'is_authenticated' => true,
                    ]);
                } else {
                    // Utente non trovato, crea un nuovo utente
                    $newUser = User::create([
                        'email' => $email,
                    ]);
                    // Utente trovato, crea i token
                    $accessToken = $newUser->createToken(
                        'auth-token',
                        ['*'],
                        now()->addMinutes(30)
                    )->plainTextToken;

                    $refreshToken = $newUser->createToken(
                        'refresh-token',
                        ['refresh'],
                        now()->addMonths(6)
                    )->plainTextToken;

                    return response()->json([
                        'user' => $newUser,
                        'is_authenticated' => false,
                        'access_token' => $accessToken,
                        'refresh_token' => $refreshToken,
                    ]);
                }
            } else {
                return response()->json($verifica);
            }
        } catch (\Exception $e) {
            return response()->json([
                "errore" => $e->getMessage()
            ]);
        }
    }



    private function validatePayload($payload)
    {
        $now = time();

        if ($payload->iss !== 'https://appleid.apple.com') {
            return ['messaggio' => 'Invalid issuer'];
        }

        if ($payload->aud !== 'it.novadirect.risparmiamiApp') {
            return ['messaggio' => 'Invalid audience'];
        }

        if ($payload->exp < $now) {
            return ['messaggio' => 'Token has expired'];
        }

        if (isset($payload->iat) && $payload->iat > $now) {
            return ['messaggio' => 'Token issued in future'];
        }

        return [
            'messaggio' => 'authenticated',
            'timestamp' => $now,
            'payload' => $payload,
            'email' => $payload->email ?? null,
        ];
    }


    // Metodo per effettuare il login con credenziali tramite Laravel
    public function login(Request $request)
    {
        // Prendi email e password dalla richiesta
        $credentials = $request->only('email', 'password');

        // Tentativo di login con le credenziali fornite
        if (!auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'Credenziali non valide',
            ], 401);
        }

        // Ottieni l'utente dal database
        $user = User::where('email', $request->email)->first();

        // 1. Crea un token con scadenza di 30 minuti
        $tokenWithExpiration = $user->createToken(
            'auth-token',
            ['*'],
            now()->addMinutes(30)
        )->plainTextToken;

        // 2. Crea un refresh token con scadenza lunga (6 mesi)
        $refreshToken = $user->createToken(
            'refresh-token',
            ['refresh'],
            now()->addMonths(6)
        )->plainTextToken;

        // Ritorna i dettagli dell'utente con i token generati
        return response()->json([
            'user' => $user,
            'access_token' => $tokenWithExpiration,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'is_authenticated' => true,
        ]);
    }

    // Metodo per revocare i token dell'utente
    public function revokeTokens(Request $request)
    {
        // 1. Revoca tutti i token dell'utente
        $request->user()->tokens()->delete();

        // 2. Revoca un token specifico, ad esempio 'auth-token'
        $request->user()->tokens()
            ->where('name', 'auth-token')
            ->delete();

        // 3. Revoca token con abilità specifiche
        $request->user()->tokens()
            ->where('abilities', 'LIKE', '%refresh%')
            ->delete();

        return response()->json(['message' => 'Token revocati']);
    }

    // Metodo per ottenere informazioni sul token corrente
    public function tokenInfo(Request $request)
    {
        // Ottieni il token corrente dell'utente
        $token = $request->user()->currentAccessToken();

        return response()->json([
            'token_name' => $token->name,
            'abilities' => $token->abilities,
            'created_at' => $token->created_at,
            'expires_at' => $token->expires_at
        ]);
    }

    // Metodo per verificare le abilità del token
    public function checkAbility(Request $request)
    {
        // Verifica se il token ha determinate abilità
        if ($request->user()->tokenCan('write')) {
            // L'utente ha l'abilità di scrittura
        }

        // Puoi anche usare il middleware per applicare il controllo alle rotte
        // Route::middleware(['ability:write'])->group(function () {
        //     // Rotte che richiedono l'abilità 'write'
        // });
    }
}
