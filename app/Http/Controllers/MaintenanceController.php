<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaintenanceController extends Controller
{
    public function index()
    {
        $schedules = Maintenance::orderBy('date', 'desc')->get();

        return view('pages.maintenance', compact('schedules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:100',
            ],
            'date' => [
                'required',
                'date',
            ],
            'location' => [
                'required',
                'string',
                Rule::in([
                    'Hulu (Setu Pamulang)',
                    'Hilir (BPI Pamulang)',
                    'Lokasi Lainnya',
                ]),
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        Maintenance::create([
            'title' => $validated['title'],
            'date' => $validated['date'],
            'location' => $validated['location'],
            'status' => 'Terjadwal',
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $maintenance = Maintenance::findOrFail($id);

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:100',
            ],
            'date' => [
                'required',
                'date',
            ],
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
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        $maintenance->update([
            'title' => $validated['title'],
            'date' => $validated['date'],
            'location' => $validated['location'],
            'status' => $validated['status'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->delete();

        return redirect()
            ->back()
            ->with('success', 'Jadwal berhasil dihapus!');
    }
}
