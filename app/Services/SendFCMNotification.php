<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Google_Client;

class SendFCMNotification
{
    public static function to($fcmToken, $title, $body, $data = [], $imageUrl = null)
    {
        try {
            Log::info('START');
            $serviceAccountPath = storage_path('app/service-account.json');
            $client = new Google_Client();
            $client->setAuthConfig($serviceAccountPath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

            $fcmUrl = 'https://fcm.googleapis.com/v1/projects/weddingbanquetsfcm/messages:send';
            $data = is_array($data) && count($data) ? array_map('strval', $data) : new \stdClass();

            $message = [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $data, // Now always a valid map or empty object
                ],
            ];

            if ($imageUrl) {
                $message['message']['notification']['image'] = $imageUrl;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($fcmUrl, $message);

            Log::info('FCM Response:', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error("Error in FCM Notification: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
