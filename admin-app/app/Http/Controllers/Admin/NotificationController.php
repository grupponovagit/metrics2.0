<?php

namespace App\Http\Controllers\Admin;

use App\Models\Notification;
use App\Models\FcmToken;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{

    public function sendNotification($id)
    {
        // Recupera la notifica da inviare
        $notification = Notification::findOrFail($id);

        // Recupera tutti i record FCM, così da avere anche l'user_id
        $fcmRecords = \App\Models\FcmToken::all();
        if ($fcmRecords->isEmpty()) {
            return redirect()->back()->with('error', 'Nessun utente ha un token FCM registrato.');
        }

        // Recupera i dati di configurazione di Firebase
        $projectId = config('services.fcm.project_id');
        $credentialsPath = base_path('client_credentials2.json');
        if (!file_exists($credentialsPath)) {
            return redirect()->back()->with('error', 'File client_credentials2.json non trovato.');
        }

        // Configura il Google Client per ottenere l'access token
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $credentialsPath);
        $client = new GoogleClient();
        $client->useApplicationDefaultCredentials();
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $tokenData = $client->getAccessToken();
        $access_token = $tokenData['access_token'];

        $headers = [
            "Authorization" => "Bearer $access_token",
            "Content-Type"  => "application/json",
        ];

        // Invia la notifica per ogni record FCM
        foreach ($fcmRecords as $record) {
            $data = [
                "message" => [
                    "token" => $record->fcm_token,
                    "notification" => [
                        "title" => $notification->title,
                        "body"  => $notification->message,
                    ],
                    // Puoi aggiungere ulteriori parametri (es. immagine o link) se necessario
                ]
            ];

            $response = Http::withHeaders($headers)->post(
                "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
                $data
            );

            if ($response->failed()) {
                Log::error("Errore nell'invio della notifica a token {$record->fcm_token}: " . json_encode($response->json()));
            }
        }

        // Associa la notifica agli utenti nella tabella pivot.
        // Raccogli tutti gli user_id univoci dai record FCM
        $userIds = $fcmRecords->pluck('user_id')->unique()->toArray();

        // Usa syncWithoutDetaching per evitare duplicati nella pivot
        foreach ($userIds as $userId) {
            $notification->users()->syncWithoutDetaching([$userId => ['is_read' => false]]);
        }

        // (Opzionale) Aggiorna la notifica per indicare che è stata inviata
        $notification->update(['sent' => true]);

        return redirect()->back()->with('success', 'Notifica inviata a tutti gli utenti e salvata nella tabella pivot.');
    }

    public function sendFcmNotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title'   => 'required|string',
            'body'    => 'required|string'
        ]);

        $userId = $request->user_id;

        // Recupera il token FCM dalla tabella fcm_tokens
        $fcmRecord = FcmToken::where('user_id', $userId)->first();
        if (!$fcmRecord) {
            return response()->json(['message' => 'User does not have a device token'], 400);
        }
        $fcm = $fcmRecord->fcm_token;

        $title = $request->title;
        $body  = $request->body;
        $projectId = config('services.fcm.project_id');

        $credentialsPath = base_path('client_credentials2.json');
        if (!file_exists($credentialsPath)) {
            return response()->json(['error' => 'File client_credentials2.json non trovato'], 500);
        }

        // Configurazione del Google Client
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $credentialsPath);
        $client = new GoogleClient();
        $client->useApplicationDefaultCredentials();
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();
        $access_token = $token['access_token'];

        $headers = [
            "Authorization" => "Bearer $access_token",
            'Content-Type'  => 'application/json'
        ];

        $data = [
            "message" => [
                "token" => $fcm,
                "notification" => [
                    "title" => $title,
                    "body"  => $body,
                ],
            ]
        ];

        // Invio della notifica tramite Firebase
        $response = Http::withHeaders($headers)->post(
            "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
            $data
        );

        if ($response->failed()) {
            return response()->json([
                'message' => 'Errore nell’invio della notifica',
                'status_code' => $response->status(),
                'error' => $response->json()
            ], 500);
        }

        // Salva la notifica nella tabella 'notifications'
        $notification = Notification::create([
            'title'   => $title,
            'message' => $body,
            // Aggiungi eventuali altri campi se necessari
        ]);

        // Associa la notifica all'utente tramite la tabella pivot
        $notification->users()->attach($userId, ['is_read' => false]);

        return response()->json([
            'message' => 'Notification has been sent and saved.',
            'response' => $response->json()
        ]);
    }

    // Metodo per recuperare le notifiche per l'utente autenticato
    public function getUserNotifications(Request $request)
    {
        $user = $request->user(); // Assicurati che l'utente sia autenticato
        $notifications = $user->notifications()->get();
        return response()->json($notifications);
    }

    public function getNotificationsByUser($userId)
    {
        // Recupera l'utente oppure fallisce se non esiste
        $user = User::findOrFail($userId);

        // Recupera le notifiche associate tramite la relazione definita nel modello User
        $notifications = $user->notifications()->get();

        return response()->json($notifications);
    }

    public function index()
    {
        // Ottieni tutte le notifiche
        $notifications = Notification::all();
        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        return view('admin.notifications.create');
    }

    public function store(Request $request)
    {
        // Convalida i dati in entrata
        $request->validate([
            'title' => 'required|string|max:255', // Aggiungi la validazione per il titolo
            'message' => 'required|string',
            'image_notification' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'link_notification' => 'nullable|url',
        ]);

        // Gestisci il caricamento dell'immagine
        if ($request->hasFile('image_notification')) {
            $imagePath = $request->file('image_notification')->store('notifications', 'public');
        }

        // Crea la notifica
        Notification::create([
            'title' => $request->title, // Aggiungi il campo title
            'message' => $request->message,
            'image_notification' => isset($imagePath) ? $imagePath : null,
            'link_notification' => $request->link_notification,
            'is_read' => 0, // Assicurati che sia di default "non letto"
        ]);

        return redirect()->route('admin.notifications.index')->with('success', 'Notification created successfully.');
    }

    public function edit(Notification $notification)
    {
        // Mostra la form di modifica della notifica
        return view('admin.notifications.edit', compact('notification'));
    }

    public function update(Request $request, Notification $notification)
    {
        // Convalida i dati in entrata
        $request->validate([
            'title' => 'required|string|max:255', // Aggiungi la validazione per il titolo
            'message' => 'required|string',
            'image_notification' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'link_notification' => 'nullable|url',
        ]);

        // Gestisci il caricamento dell'immagine (se presente)
        if ($request->hasFile('image_notification')) {
            $imagePath = $request->file('image_notification')->store('notifications', 'public');
            $notification->image_notification = $imagePath;
        }

        // Aggiorna la notifica
        $notification->update([
            'title' => $request->title, // Aggiungi il campo title
            'message' => $request->message,
            'image_notification' => isset($imagePath) ? $imagePath : $notification->image_notification,
            'link_notification' => $request->link_notification,
        ]);

        return redirect()->route('admin.notifications.index')->with('success', 'Notification updated successfully.');
    }

    public function destroy(Notification $notification)
    {
        // Elimina la notifica
        $notification->delete();
        return redirect()->route('admin.notifications.index')->with('success', 'Notification deleted successfully.');
    }
}
