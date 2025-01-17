<?php
namespace App\Services;
use App\Models\User;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\Guzzle6HttpHandler;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class FirebaseNotificationService
{
    /**
     * Generate access token for Firebase Cloud Messaging.
     *
     * @return string|null
     */
    private function generateAccessToken()
    {
        // Check if the token exists in cache
        if (Cache::has('firebase_access_token')) {
            return Cache::get('firebase_access_token');
        }
        try {
            // Path to the service_account.json file
            $credentialsFilePath = storage_path('app/private/service_account.json');
            // Create credentials object
            $credentials = new ServiceAccountCredentials(
                ['https://www.googleapis.com/auth/firebase.messaging'],
                $credentialsFilePath,
            );
            // Fetch the token
            $httpHandler = new Guzzle6HttpHandler(
                new Client(['verify' => false]) // Disable SSL verification
            );
            $token = $credentials->fetchAuthToken($httpHandler);
            $accessToken = $token['access_token'];
            // Cache the token for 55 minutes
            Cache::put('firebase_access_token', $accessToken, now()->addMinutes(55));
            return $accessToken;
        } catch (\Exception $e) {
            Log::error($e->getTraceAsString());
            Log::error('Error generating access token: ' . $e->getMessage());
            return null;
        }
    }
    /**
     * Send push notifications via Firebase Cloud Messaging.
     *
     * @param $to
     * @param string $title
     * @param string $body
     */
    public function sendPushNotificationSync(User $to, $title, $body)
    {
        // Generate access token for Firebase
        $access_token = $this->generateAccessToken();
        Log::info($access_token);
        // Retrieve the user's device details
        $tokens = $to->fcmTokens->pluck('fcm_token');
        // Define the FCM endpoint
        $fcmEndpoint = config('firebase.fcm_endpoint');
        foreach ($tokens as $token) {
            try {
                // Prepare the message payload (title and body only)
                $message = [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $title,
                            'body'  => $body
                        ]
                    ]
                ];
                // Send the notification via HTTP POST request
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $access_token,
                    'Content-Type' => 'application/json',
                ])->withOptions(['verify' => false])->post($fcmEndpoint, $message);
                // Log the result of the notification
                if ($response->status() == 200) {
                    Log::info('Notification sent successfully: ' . $response->body());
                } else {
                    Log::error('Error sending FCM notification: ' . $response->body());
                }
            } catch (\Exception $e) {
                Log::error('Error sending FCM notification: ' . $e->getMessage());
            }
        }
    }
}
