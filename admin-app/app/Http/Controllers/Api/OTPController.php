<?php

namespace App\Http\Controllers\api;

use App\Models\SmsOtp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class OTPController
{
    // Durata dell'OTP in minuti
    protected $otpValidityMinutes = 5;
    // Intervallo minimo tra invii (rate limiting) in secondi
    protected $otpRateLimitSeconds = 120;

    /**
     * Invia un OTP tramite Smoos e salva il record nella tabella sms_otps
     */
    public function sendOTP(Request $request)
    {
        $request->validate([
            'destinatario' => 'required|string',
            'user_id'      => 'required|integer',
        ]);

        $destinatario = $request->destinatario;
        $user_id = $request->user_id;


        // Controllo di unicità: se esiste un utente con lo stesso numero (escludendo l'utente corrente),
        // restituisci un errore
        $existingUser = \App\Models\User::where('phone', $destinatario)
            ->where('id', '!=', $user_id)
            ->first();

        if ($existingUser) {
            return response()->json([
                'message' => 'Il numero di telefono è già associato ad un altro utente.'
            ], 422);
        }

        // Rate limiting: controlla se per questo destinatario è già stato inviato un OTP recentemente
        $recentOtp = \App\Models\SmsOtp::where('destinatario', $destinatario)
            ->where('created_at', '>=', \Carbon\Carbon::now()->subSeconds($this->otpRateLimitSeconds))
            ->first();

        if ($recentOtp) {
            return response()->json([
                'message' => 'OTP già inviato di recente. Attendere prima di richiederne uno nuovo.'
            ], 429);
        }

        // Genera un codice OTP a 6 cifre
        $otpCode = rand(100000, 999999);
        // Prepara il testo SMS
        $testoSMS = "Ciao, Il codice per l'app Risparmiami è: " . $otpCode . " inseriscilo per verificare l'account";

        // Crea il record nella tabella sms_otps (stato iniziale "pending")
        $smsOtp = SmsOtp::create([
            'code'         => $otpCode,
            'destinatario' => $destinatario,
            'user_id'      => $user_id,
            'status_sms'   => 'pending',
            'validita'     => 1,
        ]);

        // Invia l'SMS tramite l'API Smoos
        $client = new Client();
        try {
            $response = $client->post('https://platform.smooos.com/api/v2/0d694237-3ecf-486e-a4d1-28e53fbf7cab/sms/transactional/', [
                'headers' => [
                    'Authorization' => 'Bearer 1917c86b1c56170d699c681c53342683a4d8a24854cf8da911d2a6a5',
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ],
                'json' => [
                    "default" => [
                        "sender_id" => 7562,
                        "cds_id"    => "307",
                        "text"      => $testoSMS,
                    ],
                    "specific" => [
                        [
                            "phone_number" => $destinatario
                        ]
                    ]
                ]
            ]);

            $responseData = json_decode($response->getBody(), true);
            // Il payload restituito è un array; prendiamo il primo elemento
            if (isset($responseData[0]['uuid'])) {
                $smsUuid = $responseData[0]['uuid'];
                // Aggiorna il record con il valore restituito e cambia lo stato a "approved"
                $smsOtp->uuid = $smsUuid;
                $smsOtp->status_sms = 'approved';
                $smsOtp->save();
            } else {
                $smsOtp->status_sms = 'failed';
                $smsOtp->save();
                return response()->json(['message' => 'Invio OTP fallito.'], 500);
            }
        } catch (\Exception $e) {
            $smsOtp->status_sms = 'failed';
            $smsOtp->save();
            return response()->json(['message' => 'Errore durante l\'invio dell\'SMS: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'OTP inviato con successo.'], 200);
    }

    /**
     * Verifica l'OTP inserito dall'utente e, se valido, aggiorna phone_verified dell'utente
     */
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'destinatario' => 'required|string',
            'code'         => 'required|string',
            'user_id'      => 'required|integer',
        ]);

        $destinatario = $request->destinatario;
        $code = $request->code;
        $user_id = $request->user_id;

        // Recupera l'ultimo record OTP per questo destinatario e utente
        $smsOtp = SmsOtp::where('destinatario', $destinatario)
            ->where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$smsOtp) {
            return response()->json(['message' => 'OTP non trovato.'], 404);
        }

        // Verifica se l'OTP è scaduto (durata 5 minuti)
        $expirationTime = Carbon::parse($smsOtp->created_at)->addMinutes($this->otpValidityMinutes);
        if (Carbon::now()->greaterThan($expirationTime)) {
            return response()->json(['message' => 'OTP scaduto.'], 400);
        }

        // Controlla se il codice corrisponde
        if ($smsOtp->code != $code) {
            return response()->json(['message' => 'OTP non valido.'], 400);
        }

        // OTP verificato: segna il record come usato e aggiorna il campo phone_verified dell'utente
        $smsOtp->validita = 0;
        $smsOtp->save();

        $user = User::find($user_id);
        if ($user) {
            $user->phone_verified = true;
            $user->phone = $destinatario;
            $user->save();
        }

        return response()->json(['message' => 'OTP verificato con successo.'], 200);
    }
}
