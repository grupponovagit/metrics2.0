<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\DeviceNotificationController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\TokenController;
use App\Http\Controllers\Api\FcmTokenController;
use App\Http\Controllers\Api\OTPController;
use App\Http\Controllers\Api\NotificationControllerApi;
use App\Http\Controllers\Admin\NotificationController;



use Illuminate\Http\Request;
use App\Http\Controllers\API\PasswordResetLinkController;


Route::get('/', function () {
    return view('welcome');
});



Route::post('/send-notification', [NotificationController::class, 'sendFcmNotification']);

Route::post('/password/email', [PasswordResetLinkController::class, 'store']);

Route::post('login', [LoginController::class, 'login']);

Route::post('register', [RegistrationController::class, 'register']);

Route::post('complete-profile', [RegistrationController::class, 'completeProfile']);

Route::post('googleLogin', [LoginController::class, 'googleLogin']);

Route::post('appleLogin', [LoginController::class, 'appleLogin']);

Route::post('/device-notifications', [DeviceNotificationController::class, 'store']);

Route::post('/save-fcm-token', [FcmTokenController::class, 'saveFcmToken']);

Route::post('newuser', [UserController::class, 'storeAPI']);

Route::post('/check-token', [TokenController::class, 'checkToken']);


Route::post('/refresh-token', [TokenController::class, 'refreshToken']);
Route::middleware('auth:sanctum')->get('/check-profile-complete', [LoginController::class, 'checkProfileComplete']);

Route::middleware('auth:sanctum')->get('/user-details', [UserController::class, 'userDetails']);

Route::middleware('auth:sanctum')->post('/user-logout', [UserController::class, 'logOutUser']);

Route::get('/check-phone-verification', [RegistrationController::class, 'checkPhoneVerification']);

Route::post('/send-otp', [OTPController::class, 'sendOTP']);
Route::post('/verify-otp', [OTPController::class, 'verifyOTP']);

Route::patch('/notifications/patch/read', [NotificationControllerApi::class, 'markAsRead']);

// Elimina la notifica per l'utente (i dati user_id e notification_id sono nel body)
Route::delete('/notifications/delete', [NotificationControllerApi::class, 'deleteUserNotification']);

Route::get('/notifications', [NotificationControllerApi::class, 'getUserNotifications']);

Route::get('/notifications/{userId}', [NotificationControllerApi::class, 'getNotificationsByUser']);
