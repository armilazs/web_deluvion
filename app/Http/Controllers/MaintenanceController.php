<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MaintenanceController extends Controller
{
    private string $projectId = 'deluvion-23';

    private function collectionPath(): string
    {
        return 'monitoring/depok/maintenance_schedules';
    }

    private function auditCollectionPath(): string
    {
        return 'monitoring/depok/admin_audit_logs';
    }

    private function firestoreBaseUrl(): string
    {
        return "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
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

    private function parseFirestoreDocument(array $document): object
    {
        $fields = $document['fields'] ?? [];

        $get = function ($key, $default = '') use ($fields) {
            if (!isset($fields[$key])) {
                return $default;
            }

            $value = $fields[$key];

            return $value['stringValue']
                ?? $value['integerValue']
                ?? $value['doubleValue']
                ?? $value['booleanValue']
                ?? $default;
        };

        $name = $document['name'] ?? '';
        $id = basename($name);

        return (object) [
            'id' => $id,
            'title' => $get('title', '-'),
            'date' => $get('date', now()->toDateString()),
            'location' => $get('location', '-'),
            'status' => $get('status', 'Terjadwal'),
            'description' => $get('description', ''),
            'created_by' => $get('created_by', '-'),
            'updated_by' => $get('updated_by', '-'),
            'created_at' => $get('created_at', null),
            'updated_at' => $get('updated_at', null),
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
            Log::warning('Gagal menyimpan audit log admin', [
                'message' => $e->getMessage(),
                'action' => $action,
            ]);
        }
    }

    public function index()
    {
        try {
            $token = $this->getAccessToken();

            $url = $this->firestoreBaseUrl() . '/' . $this->collectionPath();

            $response = Http::withToken($token)->get($url);

            if (!$response->successful()) {
                throw new \Exception('Gagal membaca collection maintenance dari Firestore.');
            }

            $documents = $response->json('documents') ?? [];

            $schedules = [];

            foreach ($documents as $document) {
                $schedules[] = $this->parseFirestoreDocument($document);
            }

            usort($schedules, function ($a, $b) {
                return strcmp($b->date, $a->date);
            });

            return view('pages.maintenance', compact('schedules'));
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil jadwal maintenance dari Firestore REST', [
                'message' => $e->getMessage(),
            ]);

            $schedules = [];

            return view('pages.maintenance', compact('schedules'))
                ->withErrors([
                    'firestore' => 'Gagal memuat data jadwal pemeliharaan dari Firestore.',
                ]);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'date' => ['required', 'date'],
            'location' => [
                'required',
                'string',
                Rule::in([
                    'Hulu (Setu Pamulang)',
                    'Hilir (BPI Pamulang)',
                    'Lokasi Lainnya',
                ]),
            ],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $token = $this->getAccessToken();

            $adminEmail = $request->session()->get('firebase_email', 'unknown_admin');
            $now = now()->toDateTimeString();

            $payload = $this->firestoreFields([
                'title' => $validated['title'],
                'date' => Carbon::parse($validated['date'])->toDateString(),
                'location' => $validated['location'],
                'status' => 'Terjadwal',
                'description' => $validated['description'] ?? '',
                'created_by' => $adminEmail,
                'updated_by' => $adminEmail,
                'created_at' => $now,
                'updated_at' => $now,
                'source' => 'web_admin',
            ]);

            $url = $this->firestoreBaseUrl() . '/' . $this->collectionPath();

            $response = Http::withToken($token)->post($url, $payload);

            if (!$response->successful()) {
                throw new \Exception('Firestore REST create gagal.');
            }

            $createdDocument = $response->json();
            $documentName = $createdDocument['name'] ?? '';
            $documentId = basename($documentName);

            $this->auditLog($request, 'create_maintenance_schedule', [
                'document_id' => $documentId,
                'title' => $validated['title'],
                'date' => Carbon::parse($validated['date'])->toDateString(),
                'location' => $validated['location'],
            ]);

            return redirect()
                ->back()
                ->with('success', 'Jadwal berhasil ditambahkan ke Firestore!');
        } catch (\Throwable $e) {
            Log::error('Gagal menambahkan jadwal maintenance ke Firestore REST', [
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withErrors([
                    'firestore' => 'Gagal menambahkan jadwal ke Firestore.',
                ])
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'date' => ['required', 'date'],
            'location' => [
                'required',
                'string',
                Rule::in([
                    'Hulu (Setu Pamulang)',
                    'Hilir (BPI Pamulang)',
                    'Lokasi Lainnya',
                ]),
            ],
            'status' => [
                'required',
                'string',
                Rule::in([
                    'Terjadwal',
                    'Sedang Berjalan',
                    'Selesai',
                ]),
            ],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $token = $this->getAccessToken();

            $adminEmail = $request->session()->get('firebase_email', 'unknown_admin');
            $now = now()->toDateTimeString();

            $payload = $this->firestoreFields([
                'title' => $validated['title'],
                'date' => Carbon::parse($validated['date'])->toDateString(),
                'location' => $validated['location'],
                'status' => $validated['status'],
                'description' => $validated['description'] ?? '',
                'updated_by' => $adminEmail,
                'updated_at' => $now,
                'source' => 'web_admin',
            ]);

            $fieldPaths = [
                'updateMask.fieldPaths=title',
                'updateMask.fieldPaths=date',
                'updateMask.fieldPaths=location',
                'updateMask.fieldPaths=status',
                'updateMask.fieldPaths=description',
                'updateMask.fieldPaths=updated_by',
                'updateMask.fieldPaths=updated_at',
                'updateMask.fieldPaths=source',
            ];

            $url = $this->firestoreBaseUrl()
                . '/'
                . $this->collectionPath()
                . '/'
                . urlencode($id)
                . '?'
                . implode('&', $fieldPaths);

            $response = Http::withToken($token)->patch($url, $payload);

            if (!$response->successful()) {
                throw new \Exception('Firestore REST update gagal.');
            }

            $this->auditLog($request, 'update_maintenance_schedule', [
                'document_id' => $id,
                'title' => $validated['title'],
                'date' => Carbon::parse($validated['date'])->toDateString(),
                'location' => $validated['location'],
                'status' => $validated['status'],
            ]);

            return redirect()
                ->back()
                ->with('success', 'Jadwal berhasil diperbarui di Firestore!');
        } catch (\Throwable $e) {
            Log::error('Gagal memperbarui jadwal maintenance di Firestore REST', [
                'message' => $e->getMessage(),
                'id' => $id,
            ]);

            return redirect()
                ->back()
                ->withErrors([
                    'firestore' => 'Gagal memperbarui jadwal di Firestore.',
                ])
                ->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $token = $this->getAccessToken();

            $url = $this->firestoreBaseUrl()
                . '/'
                . $this->collectionPath()
                . '/'
                . urlencode($id);

            $response = Http::withToken($token)->delete($url);

            if (!$response->successful()) {
                throw new \Exception('Firestore REST delete gagal.');
            }

            $this->auditLog($request, 'delete_maintenance_schedule', [
                'document_id' => $id,
            ]);

            return redirect()
                ->back()
                ->with('success', 'Jadwal berhasil dihapus dari Firestore!');
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus jadwal maintenance dari Firestore REST', [
                'message' => $e->getMessage(),
                'id' => $id,
            ]);

            return redirect()
                ->back()
                ->withErrors([
                    'firestore' => 'Gagal menghapus jadwal dari Firestore.',
                ]);
        }
    }
}
