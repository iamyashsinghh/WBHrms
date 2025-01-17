<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Google\Client;

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
    // Path to your service account JSON file
    $serviceAccountPath = storage_path('app/service-account.json');

    // Initialize Google Client
    $client = new Client();
    $client->setAuthConfig($serviceAccountPath);
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

    // Get an access token
    $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

    // Firebase FCM endpoint (replace 'your-project-id' with your actual project ID)
    $fcmUrl = 'https://fcm.googleapis.com/v1/projects/your-project-id/messages:send';

    // Build the message payload
    $message = [
        'message' => [
            'token' => $fcmToken, // Target device FCM token
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'data' => $data, // Optional custom data for app-specific processing
        ],
    ];

    // Include an image in the notification if provided
    if ($imageUrl) {
        $message['message']['notification']['image'] = $imageUrl;
    }

    // Send the notification request to FCM
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $accessToken,
        'Content-Type' => 'application/json',
    ])->post($fcmUrl, $message);

    Log::info($response);
    // Return the FCM response
    return $response->json();
}
