@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
@php
$adminEmail = session('firebase_email', 'admin@dlvn.com');
$adminName = explode('@', $adminEmail)[0] ?? 'Admin';
@endphp

<div class="dashboard-grid" style="grid-template-columns: 1fr;">
    <div class="widget-card" style="max-width: 800px;">

        <!-- Tabs Navigation -->
        <div class="tabs">
            <button type="button" class="tab-btn active" onclick="switchTab(event, 'profil')">
                Profil Admin
            </button>

            <button type="button" class="tab-btn" onclick="switchTab(event, 'manajemen')">
                Manajemen Admin
            </button>

            <button type="button" class="tab-btn" onclick="switchTab(event, 'parameter')">
                Parameter Sensor
            </button>

            <button type="button" class="tab-btn" onclick="switchTab(event, 'notifikasi')">
                Sistem Notifikasi
            </button>
        </div>

        @if(session('success'))
        <div style="padding: 12px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 16px; font-size: 14px;">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div style="padding: 12px; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 16px; font-size: 14px;">
            <strong>Data belum valid:</strong>

            <ul style="margin: 8px 0 0 18px;">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Tab: Profil -->
        <div id="tab-profil" class="tab-pane active">
            <h3 class="section-title">Informasi Akun Anda</h3>

            <form onsubmit="event.preventDefault(); showToast('Profil akun hanya dapat diubah melalui Firebase Authentication.', 'info');">
                <div class="form-group">
                    <label>Nama Admin</label>
                    <input
                        type="text"
                        class="form-control"
                        value="{{ $adminName }}"
                        readonly>
                </div>

                <div class="form-group">
                    <label>Email Admin</label>
                    <input
                        type="email"
                        class="form-control"
                        value="{{ $adminEmail }}"
                        readonly>
                </div>

                <div class="form-group">
                    <label>Status Akses</label>
                    <input
                        type="text"
                        class="form-control"
                        value="Admin Terverifikasi Firebase"
                        readonly>
                </div>

                <button type="submit" class="btn-primary" style="width: auto; padding: 10px 24px;">
                    <i class="fas fa-info-circle"></i> Info Profil
                </button>
            </form>
        </div>

        <!-- Tab: Manajemen Admin Baru -->
        <div id="tab-manajemen" class="tab-pane">
            <h3 class="section-title">Tambah Admin Baru</h3>

            <p style="color: var(--text-secondary); margin-bottom: 24px; font-size: 14px;">
                Daftarkan akun admin baru untuk memberikan akses ke dashboard pemantauan.
                Seluruh akun yang dibuat di Firebase Authentication dianggap sebagai admin sistem.
            </p>

            <form action="{{ route('settings.add_admin') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Nama Lengkap Admin Baru</label>
                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        placeholder="Masukkan nama lengkap"
                        required
                        maxlength="100"
                        value="{{ old('name') }}">
                </div>

                <div class="form-group">
                    <label>Email Akses</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="email@dlvn.com"
                        required
                        maxlength="150"
                        value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <label>Password Sementara</label>
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Minimal 8 karakter"
                        required
                        minlength="8"
                        maxlength="100"
                        autocomplete="new-password">
                </div>

                <button type="submit" class="btn-primary" style="width: auto; padding: 10px 24px;">
                    <i class="fas fa-user-plus"></i> Daftarkan Admin
                </button>
            </form>
        </div>

        <!-- Tab: Parameter Sensor -->
        <div id="tab-parameter" class="tab-pane">
            <h3 class="section-title">Ambang Batas Peringatan (Thresholds)</h3>

            <p style="color: var(--text-secondary); margin-bottom: 24px; font-size: 14px;">
                Atur batas nilai sensor untuk memicu peringatan otomatis.
                Pada tahap ini perubahan parameter masih berupa simulasi tampilan dan belum disimpan ke database.
            </p>

            <form onsubmit="event.preventDefault(); showToast('Parameter sensor berhasil diperbarui pada tampilan lokal.', 'success');">
                <div class="dashboard-grid" style="gap: 16px; margin-bottom: 24px;">
                    <div class="form-group" style="margin: 0;">
                        <label>Batas Waspada Ketinggian (Cm)</label>
                        <input
                            type="number"
                            class="form-control"
                            value="100"
                            min="0"
                            max="500"
                            required>
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label>Batas Kritis Ketinggian (Cm)</label>
                        <input
                            type="number"
                            class="form-control"
                            value="150"
                            min="0"
                            max="500"
                            required>
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label>Batas Arus Air (L/min)</label>
                        <input
                            type="number"
                            class="form-control"
                            value="20"
                            min="0"
                            max="1000"
                            required>
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label>Curah Hujan Tinggi (mm/jam)</label>
                        <input
                            type="number"
                            class="form-control"
                            value="50"
                            min="0"
                            max="500"
                            required>
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width: auto; padding: 10px 24px;">
                    Simpan Parameter
                </button>
            </form>
        </div>

        <!-- Tab: Notifikasi -->
        <div id="tab-notifikasi" class="tab-pane">
            <h3 class="section-title">Pengaturan Integrasi Peringatan</h3>

            <p style="color: var(--text-secondary); margin-bottom: 24px; font-size: 14px;">
                Pengaturan ini digunakan sebagai simulasi tampilan. Kontrol sirine yang benar-benar menulis ke Firestore berada pada halaman perangkat dan tetap membutuhkan akun admin.
            </p>

            <div style="display: flex; flex-direction: column; gap: 24px; margin-bottom: 32px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h4 style="margin-bottom: 4px;">Sirine Peringatan Dini (EWS)</h4>
                        <p style="font-size: 12px; color: var(--text-secondary);">
                            Aktifkan sirine di Node Hilir secara otomatis jika status Waspada.
                        </p>
                    </div>

                    <label class="toggle-switch">
                        <input
                            type="checkbox"
                            checked
                            onchange="showToast('Status Sirine Otomatis diperbarui pada tampilan lokal.', 'info')">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function switchTab(event, tabId) {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('active');
        });

        if (event && event.currentTarget) {
            event.currentTarget.classList.add('active');
        }

        const targetTab = document.getElementById('tab-' + tabId);

        if (targetTab) {
            targetTab.classList.add('active');
        }
    }
</script>
@endsection