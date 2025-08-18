<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceNotificationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
            'device_type' => 'required|string',
        ]);

        try {
            $deviceNotification = DeviceNotification::where('fcm_token', $request->fcm_token)->first();
           

            if (!$deviceNotification) {
                $deviceNotification = DeviceNotification::create([
                    'user_id' => $request->user_id,
                    'device_type' => $request->device_type,
                    'fcm_token' => $request->fcm_token,
                ]);
            }

            return response()->json([
                'message' => 'Device notification saved successfully.',
                'data' => $deviceNotification,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'errore',
                'err' => $e
            ]);
        }
    }
}
