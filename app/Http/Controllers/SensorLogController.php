<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SensorLogController extends Controller
{
    private string $projectId = 'deluvion-23';

    private function firestoreBaseUrl(): string
    {
        return "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
    }

    private function logCollectionPath(): string
    {
        return 'monitoring/depok/log_data';
    }

    private function auditCollectionPath(): string
    {
        return 'monitoring/depok/admin_audit_logs';
    }

    private function getAccessToken(): string
    {
        $credentialsPath = env('FIREBASE_CREDENTIALS');

        if (!$credentialsPath) {
            throw new \Exception('FIREBASE_CREDENTIALS belum diatur.');
        }

        if (!Str::contains($credentialsPath, ':') && !Str::startsWith($credentialsPath, '/')) {
            $credentialsPath = base_path($credentialsPath);
        }

        if (!file_exists($credentialsPath)) {
            throw new \Exception('File Firebase service account tidak ditemukan.');
        }

        $serviceAccount = json_decode(file_get_contents($credentialsPath), true);

        if (!$serviceAccount || empty($serviceAccount['client_email']) || empty($serviceAccount['private_key'])) {
            throw new \Exception('Format service account tidak valid.');
        }

        $now = time();

        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT',
        ];

        $claim = [
            'iss' => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/datastore',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ];

        $base64UrlHeader = $this->base64UrlEncode(json_encode($header));
        $base64UrlClaim = $this->base64UrlEncode(json_encode($claim));

        $signatureInput = $base64UrlHeader . '.' . $base64UrlClaim;

        openssl_sign(
            $signatureInput,
            $signature,
            $serviceAccount['private_key'],
            OPENSSL_ALGO_SHA256
        );

        $jwt = $signatureInput . '.' . $this->base64UrlEncode($signature);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Gagal mengambil access token Firebase.');
        }

        $data = $response->json();

        if (empty($data['access_token'])) {
            throw new \Exception('Access token Firebase kosong.');
        }

        return $data['access_token'];
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function firestoreFields(array $data): array
    {
        $fields = [];

        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $fields[$key] = ['booleanValue' => $value];
            } elseif (is_int($value)) {
                $fields[$key] = ['integerValue' => $value];
            } elseif (is_float($value)) {
                $fields[$key] = ['doubleValue' => $value];
            } else {
                $fields[$key] = ['stringValue' => (string) $value];
            }
        }

        return [
            'fields' => $fields,
        ];
    }

    private function auditLog(Request $request, string $action, array $details = []): void
    {
        try {
            $token = $this->getAccessToken();

            $adminEmail = $request->session()->get('firebase_email', 'unknown_admin');

            $payload = $this->firestoreFields([
                'admin_email' => $adminEmail,
                'action' => $action,
                'details' => json_encode($details),
                'ip_address' => $request->ip(),
                'user_agent' => substr($request->userAgent() ?? 'unknown', 0, 255),
                'created_at' => now()->toDateTimeString(),
                'source' => 'web_admin',
            ]);

            $url = $this->firestoreBaseUrl() . '/' . $this->auditCollectionPath();

            Http::withToken($token)->post($url, $payload);
        } catch (\Throwable $e) {
            Log::warning('Gagal menyimpan audit log hapus sensor', [
                'message' => $e->getMessage(),
                'action' => $action,
            ]);
        }
    }

    public function clear(Request $request)
    {
        try {
            $token = $this->getAccessToken();

            $listUrl = $this->firestoreBaseUrl() . '/' . $this->logCollectionPath();

            $listResponse = Http::withToken($token)->get($listUrl);

            if (!$listResponse->successful()) {
                throw new \Exception('Gagal membaca log_data dari Firestore REST.');
            }

            $documents = $listResponse->json('documents') ?? [];

            $deleted = 0;

            foreach ($documents as $document) {
                $documentName = $document['name'] ?? null;

                if (!$documentName) {
                    continue;
                }

                $deleteResponse = Http::withToken($token)->delete(
                    'https://firestore.googleapis.com/v1/' . $documentName
                );

                if ($deleteResponse->successful()) {
                    $deleted++;
                }
            }

            $this->auditLog($request, 'clear_sensor_logs', [
                'deleted_count' => $deleted,
            ]);

            return response()->json([
                'success' => true,
                'deleted' => $deleted,
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus log sensor dari Firestore REST', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Gagal menghapus log sensor.',
            ], 500);
        }
    }
}
