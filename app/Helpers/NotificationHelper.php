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
        // Path to your service account JSON file
        $serviceAccountPath = storage_path('app/service-account.json');

        // Initialize Google Client
        $client = new Google_Client();
        $client->setAuthConfig($serviceAccountPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        // Get an access token
        $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

        // Log the access token for debugging
        Log::info("Generated Access Token: " . $accessToken);

        // Firebase FCM endpoint
        $fcmUrl = 'https://fcm.googleapis.com/v1/projects/weddingbanquetsfcm/messages:send';

        // Convert all data values to strings
        $stringifiedData = array_map('strval', $data);

        // Build the notification payload
        $message = [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $stringifiedData, // Use the stringified data here
            ],
        ];

        if ($imageUrl) {
            $message['message']['notification']['image'] = $imageUrl;
        }

        // Send the request
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->post($fcmUrl, $message);

        // Log the response for debugging
        Log::info('FCM Request Payload: ', $message);
        Log::info('FCM Response: ', $response->json());

        // Return the FCM response
        return $response->json();
    } catch (\Exception $e) {
        // Log any exceptions for debugging
        Log::error("Error in FCM Notification: " . $e->getMessage());
        return ['error' => $e->getMessage()];
    }
}
