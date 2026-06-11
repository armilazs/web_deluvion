<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SensorLogController;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

/*
|--------------------------------------------------------------------------
| Firebase Authentication Session
|--------------------------------------------------------------------------
| Route ini menerima Firebase ID Token dari login.blade.php,
| lalu Laravel membuat session agar route bisa diamankan.
*/
Route::post('/firebase-login', function (Request $request) {
    $request->validate([
        'idToken' => ['required', 'string'],
    ]);

    try {
        $auth = app('firebase.auth');
        $verifiedIdToken = $auth->verifyIdToken($request->idToken);

        $uid = $verifiedIdToken->claims()->get('sub');
        $email = $verifiedIdToken->claims()->get('email');

        if (!$uid || !$email) {
            return response()->json([
                'success' => false,
                'message' => 'Token Firebase tidak lengkap.'
            ], 403);
        }

        session([
            'firebase_uid' => $uid,
            'firebase_email' => $email,
            'role' => 'admin',
        ]);

        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'redirect' => route('monitoring'),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Token Firebase tidak valid atau sudah kedaluwarsa.'
        ], 401);
    }
})->middleware('throttle:10,1')->name('firebase.login');

Route::post('/logout', function (Request $request) {
    $request->session()->flush();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

Route::middleware(['firebase.login'])->group(function () {
    Route::get('/monitoring', function () {
        return view('pages.monitoring');
    })->name('monitoring');

    Route::get('/location/{slug}', function ($slug) {
        $locationName = ucwords(str_replace('-', ' ', $slug));
        return view('pages.location_detail', compact('locationName', 'slug'));
    })->name('location.detail');

    Route::get('/devices', function () {
        return view('pages.devices');
    })->name('devices');

    Route::get('/qos', function () {
        return view('pages.qos');
    })->name('qos');

    Route::get('/logs', function () {
        return view('pages.logs');
    })->name('logs');
});

Route::middleware(['admin'])->group(function () {
    Route::get('/maintenance', [MaintenanceController::class, 'index'])
        ->name('maintenance');

    Route::post('/maintenance', [MaintenanceController::class, 'store'])
        ->middleware('throttle:20,1')
        ->name('maintenance.store');

    Route::post('/maintenance/{id}', [MaintenanceController::class, 'update'])
        ->middleware('throttle:20,1')
        ->name('maintenance.update');

    Route::delete('/maintenance/{id}', [MaintenanceController::class, 'destroy'])
        ->middleware('throttle:20,1')
        ->name('maintenance.destroy');

    Route::get('/settings', function () {
        return view('pages.settings');
    })->name('settings');

    Route::post('/settings/add-admin', [SettingsController::class, 'addAdmin'])
        ->middleware('throttle:5,1')
        ->name('settings.add_admin');

    Route::post('/clear-sensor-logs', [SensorLogController::class, 'clear'])
        ->middleware('throttle:3,1')
        ->name('logs.clear');
});
