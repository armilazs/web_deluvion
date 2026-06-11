<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function addAdmin(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:150',
            ],
            'password' => [
                'required',
                'string',
                Password::min(8),
            ],
        ]);

        try {
            $auth = app('firebase.auth');

            /*
             * Karena sistem login memakai Firebase Authentication,
             * admin baru harus dibuat di Firebase Auth, bukan hanya di database Laravel lokal.
             */
            $auth->createUser([
                'displayName' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'emailVerified' => false,
                'disabled' => false,
            ]);

            return redirect()
                ->back()
                ->with('success', 'Admin baru berhasil didaftarkan di Firebase Authentication.');
        } catch (\Kreait\Firebase\Exception\Auth\EmailExists $e) {
            return redirect()
                ->back()
                ->withErrors([
                    'email' => 'Email tersebut sudah terdaftar sebagai admin.',
                ])
                ->withInput();
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->withErrors([
                    'firebase' => 'Gagal menambahkan admin baru. Periksa konfigurasi Firebase service account.',
                ])
                ->withInput();
        }
    }
}
