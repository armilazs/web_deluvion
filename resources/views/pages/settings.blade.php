@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="dashboard-grid" style="grid-template-columns: 1fr;">
    <div class="widget-card" style="max-width: 800px;">
        
        <!-- Tabs Navigation -->
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('profil')">Profil Admin</button>
            <button class="tab-btn" onclick="switchTab('manajemen')">Manajemen Admin</button>
            <button class="tab-btn" onclick="switchTab('parameter')">Parameter Sensor</button>
            <button class="tab-btn" onclick="switchTab('notifikasi')">Sistem Notifikasi</button>
        </div>

        <!-- Tab: Profil -->
        <div id="tab-profil" class="tab-pane active">
            <h3 class="section-title">Informasi Akun Anda</h3>
            <form onsubmit="event.preventDefault(); showToast('Profil berhasil diperbarui!', 'success');">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" class="form-control" value="rrrr" required />
                </div>
                <div class="form-group">
                    <label>Email Admin</label>
                    <input type="email" class="form-control" value="admin@dlvn.com" required />
                </div>
                <div class="form-group">
                    <label>Password Baru (Opsional)</label>
                    <input type="password" class="form-control" placeholder="Biarkan kosong jika tidak ingin mengubah" />
                </div>
                <button type="submit" class="btn-primary" style="width: auto; padding: 10px 24px;">Simpan Profil</button>
            </form>
        </div>

        <!-- Tab: Manajemen Admin Baru -->
        <div id="tab-manajemen" class="tab-pane">
            <h3 class="section-title">Tambah Admin Baru</h3>
            <p style="color: var(--text-secondary); margin-bottom: 24px; font-size: 14px;">Daftarkan akun admin baru untuk memberikan akses ke dashboard pemantauan.</p>
            
            <form action="{{ route('settings.add_admin') }}" method="POST">
                @csrf
                @if(session('success'))
                    <div style="padding: 12px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 16px; font-size: 14px;">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="form-group">
                    <label>Nama Lengkap Admin Baru</label>
                    <input type="text" name="name" class="form-control" placeholder="Masukkan nama lengkap" required />
                </div>
                <div class="form-group">
                    <label>Email Akses</label>
                    <input type="email" name="email" class="form-control" placeholder="email@dlvn.com" required />
                </div>
                <div class="form-group">
                    <label>Password Sementara</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required minlength="8" />
                </div>
                <button type="submit" class="btn-primary" style="width: auto; padding: 10px 24px;"><i class="fas fa-user-plus"></i> Daftarkan Admin</button>
            </form>
        </div>

        <!-- Tab: Parameter Sensor -->
        <div id="tab-parameter" class="tab-pane">
            <h3 class="section-title">Ambang Batas Peringatan (Thresholds)</h3>
            <p style="color: var(--text-secondary); margin-bottom: 24px; font-size: 14px;">Atur batas nilai sensor untuk memicu peringatan otomatis.</p>
            
            <form onsubmit="event.preventDefault(); showToast('Parameter sensor berhasil diperbarui!', 'success');">
                <div class="dashboard-grid" style="gap: 16px; margin-bottom: 24px;">
                    <div class="form-group" style="margin: 0;">
                        <label>Batas Waspada Ketinggian (Cm)</label>
                        <input type="number" class="form-control" value="100" required />
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label>Batas Kritis Ketinggian (Cm)</label>
                        <input type="number" class="form-control" value="150" required />
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label>Batas Arus Air (L/min)</label>
                        <input type="number" class="form-control" value="20" required />
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label>Curah Hujan Tinggi (mm/jam)</label>
                        <input type="number" class="form-control" value="50" required />
                    </div>
                </div>
                <button type="submit" class="btn-primary" style="width: auto; padding: 10px 24px;">Simpan Parameter</button>
            </form>
        </div>

        <!-- Tab: Notifikasi -->
        <div id="tab-notifikasi" class="tab-pane">
            <h3 class="section-title">Pengaturan Integrasi Peringatan</h3>
            
            <div style="display: flex; flex-direction: column; gap: 24px; margin-bottom: 32px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h4 style="margin-bottom: 4px;">Sirine Peringatan Dini (EWS)</h4>
                        <p style="font-size: 12px; color: var(--text-secondary);">Aktifkan sirine di Node Hilir secara otomatis jika status Waspada.</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" checked onchange="showToast('Status Sirine Otomatis diperbarui', 'info')">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
        
        event.currentTarget.classList.add('active');
        document.getElementById('tab-' + tabId).classList.add('active');
    }
</script>
@endsection
