<?php

use Illuminate\Support\Facades\Http;

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
    // Firebase Server Key (replace with your actual server key)
    $serverKey = 'AAAA9pCpvEQ:APA91bFjajQJpXdBw1fW_sLPD0KxQlUFA1zTvbPWYwpR5jkY1oLQe_aAe-t4fDjQclOG7pCkeVTb2ITPjWbgIs5eQHS-0aiQxqtJ0P2AIaLAV11dQwU-hJOqbwWVPFwDXLq1JKGpcpAN';

    // Firebase FCM endpoint
    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    // Build the notification payload
    $notificationPayload = [
        'title' => $title,       // Title of the notification
        'body' => $body,         // Body message
        'sound' => 'default',    // Default notification sound
    ];

    // Add the image URL if provided
    if ($imageUrl) {
        $notificationPayload['image'] = $imageUrl;
    }

    // Complete payload with notification and optional custom data
    $payload = [
        'to' => $fcmToken,                 // Target device token
        'notification' => $notificationPayload, // Notification details
        'data' => $data,                   // Custom data for app-specific processing
    ];

    // Send the notification request to FCM
    $response = Http::withHeaders([
        'Authorization' => 'key=' . $serverKey, // Add server key to authorization header
        'Content-Type' => 'application/json',  // JSON content type
    ])->post($fcmUrl, $payload);

    // Return the FCM response
    return $response->json();
}
