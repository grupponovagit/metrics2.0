<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationControllerApi extends Controller
{
    /**
     * Marca come letta la notifica per l'utente specificato.
     *
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request)
    {
        $userId = $request->input('user_id');
        $notificationId = $request->input('notification_id');

        // Trova l'utente; se non esiste restituisce errore.
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Aggiorna il record pivot impostando is_read a true e registrando la data di lettura.
        $updated = $user->notifications()->updateExistingPivot($notificationId, [
            'is_read' => 1,
            'read_at' => now(),
        ]);

        if ($updated) {
            return response()->json(['message' => 'Notifica contrassegnata come letta']);
        } else {
            return response()->json(['message' => 'Operazione fallita'], 500);
        }
    }

    /**
     * Elimina la notifica per l'utente specificato (rimuove la relazione nella tabella pivot).
     *
     * La richiesta deve contenere nel body:
     * {
     *    "user_id": <id dell'utente>,
     *    "notification_id": <id della notifica>
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUserNotification(Request $request)
    {
        $userId = $request->input('user_id');
        $notificationId = $request->input('notification_id');

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->notifications()->detach($notificationId);
        return response()->json(['message' => 'Notifica eliminata']);
    }

    /**
     * Recupera le notifiche per un utente specifico.
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotificationsByUser($userId)
    {
        $user = User::findOrFail($userId);
        $notifications = $user->notifications()->get();
        return response()->json($notifications);
    }
    
    /**
     * Recupera le notifiche per l'utente (usando query param o altro).
     * In questo caso, ad esempio, si potrebbe passare l'user id come query string.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserNotifications(Request $request)
    {
        $userId = $request->query('user_id');
        if (!$userId) {
            return response()->json(['message' => 'User ID required'], 400);
        }
        $user = User::findOrFail($userId);
        $notifications = $user->notifications()->get();
        return response()->json($notifications);
    }
}