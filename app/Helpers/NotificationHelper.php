<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Google_Client;

/**
 * Send an FCM Notification.
 *
 * @param string $fcmToken Recipient's device FCM token.
 * @param string $title Notification title.
 * @param string $body Notification body message.
 * @param array $data Custom data for app-specific handling (optional).
 * @param string|null $imageUrl URL of the image to include in the notification (optional).
 * @return array Response from FCM API.
 */

function sendFCMNotification($fcmToken, $title, $body, $data = [], $imageUrl = null)
{
    try {
        $serviceAccountPath = storage_path('app/service-account.json');
        $client = new Google_Client();
        $client->setAuthConfig($serviceAccountPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];
        $fcmUrl = 'https://fcm.googleapis.com/v1/projects/weddingbanquetsfcm/messages:send';
        $stringifiedData = array_map('strval', $data);
        $message = [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $stringifiedData,
            ],
        ];
        if ($imageUrl) {
            $message['message']['notification']['image'] = $imageUrl;
        }
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->post($fcmUrl, $message);
        return $response->json();

        Log::info($response);
    } catch (\Exception $e) {
        Log::error("Error in FCM Notification: " . $e->getMessage());
        return ['error' => $e->getMessage()];
    }
}
