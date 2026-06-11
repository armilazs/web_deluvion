@extends('layouts.app')

@section('title', 'Jadwal Pemeliharaan')

@section('content')
<div class="dashboard-grid" style="grid-template-columns: 1fr;">
    <div class="widget-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2 class="section-title" style="margin: 0;">Daftar Jadwal</h2>

            <button class="btn-primary" type="button" style="width: auto; padding: 10px 20px;" onclick="openModal()">
                <i class="fas fa-plus"></i> Tambah Jadwal
            </button>
        </div>

        @if(session('success'))
        <div style="padding: 16px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 24px;">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div style="padding: 16px; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 24px;">
            <strong>Data belum valid:</strong>
            <ul style="margin: 8px 0 0 18px;">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                    <th style="padding: 12px; color: var(--text-secondary);">Tanggal</th>
                    <th style="padding: 12px; color: var(--text-secondary);">Kegiatan</th>
                    <th style="padding: 12px; color: var(--text-secondary);">Lokasi</th>
                    <th style="padding: 12px; color: var(--text-secondary);">Status</th>
                    <th style="padding: 12px; color: var(--text-secondary);">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($schedules as $schedule)
                <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.2s;"
                    onmouseover="this.style.background='#f9fafb'"
                    onmouseout="this.style.background='transparent'">

                    <td style="padding: 12px;">
                        {{ \Carbon\Carbon::parse($schedule->date)->format('d M Y') }}
                    </td>

                    <td style="padding: 12px; font-weight: 500;">
                        {{ $schedule->title }}
                    </td>

                    <td style="padding: 12px;">
                        {{ $schedule->location }}
                    </td>

                    <td style="padding: 12px;">
                        @php
                        $bg = '#fef08a';
                        $color = '#ca8a04';

                        if ($schedule->status === 'Selesai') {
                        $bg = '#dcfce7';
                        $color = '#166534';
                        } elseif ($schedule->status === 'Sedang Berjalan') {
                        $bg = '#e0f2fe';
                        $color = '#0369a1';
                        }
                        @endphp

                        <span class="status-badge"
                            style="padding: 4px 12px; font-size: 12px; background: {{ $bg }}; color: {{ $color }}; font-weight: 600; border-radius: 6px;">
                            {{ $schedule->status }}
                        </span>
                    </td>

                    <td style="padding: 12px; display: flex; gap: 12px;">
                        <button
                            type="button"
                            class="edit-maintenance-btn"
                            data-id="{{ $schedule->id }}"
                            data-title="{{ e($schedule->title) }}"
                            data-date="{{ e($schedule->date) }}"
                            data-location="{{ e($schedule->location) }}"
                            data-status="{{ e($schedule->status) }}"
                            data-description="{{ e($schedule->description ?? '') }}"
                            onclick="openEditModalFromButton(this)"
                            style="background: none; border: none; color: var(--primary-blue); cursor: pointer;">
                            <i class="fas fa-edit"></i> Edit
                        </button>

                        <form action="{{ route('maintenance.destroy', $schedule->id) }}"
                            method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                style="background: none; border: none; color: var(--danger-color); cursor: pointer;">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 24px; color: var(--text-secondary);">
                        Belum ada jadwal pemeliharaan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal-overlay" id="addModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="section-title" style="margin: 0;">Tambah Jadwal Baru</h3>

            <button class="close-modal" type="button" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('maintenance.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Nama Kegiatan</label>
                <input
                    type="text"
                    name="title"
                    class="form-control"
                    required
                    maxlength="100"
                    placeholder="Contoh: Pembersihan Sensor Hulu"
                    value="{{ old('title') }}">
            </div>

            <div class="form-group">
                <label>Tanggal</label>
                <input
                    type="date"
                    name="date"
                    class="form-control"
                    required
                    value="{{ old('date') }}">
            </div>

            <div class="form-group">
                <label>Lokasi / Node</label>
                <select name="location" class="form-control" required>
                    <option value="Hulu (Setu Pamulang)" {{ old('location') === 'Hulu (Setu Pamulang)' ? 'selected' : '' }}>
                        Hulu (Setu Pamulang)
                    </option>
                    <option value="Hilir (BPI Pamulang)" {{ old('location') === 'Hilir (BPI Pamulang)' ? 'selected' : '' }}>
                        Hilir (BPI Pamulang)
                    </option>
                    <option value="Lokasi Lainnya" {{ old('location') === 'Lokasi Lainnya' ? 'selected' : '' }}>
                        Lokasi Lainnya
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label>Keterangan Tambahan</label>
                <textarea
                    name="description"
                    class="form-control"
                    rows="3"
                    maxlength="500"
                    placeholder="Opsional">{{ old('description') }}</textarea>
            </div>

            <button type="submit" class="btn-primary">Simpan Jadwal</button>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal-overlay" id="editModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="section-title" style="margin: 0;">Edit Jadwal</h3>

            <button class="close-modal" type="button" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="editForm" method="POST">
            @csrf

            <div class="form-group">
                <label>Nama Kegiatan</label>
                <input
                    type="text"
                    name="title"
                    id="edit_title"
                    class="form-control"
                    required
                    maxlength="100">
            </div>

            <div class="form-group">
                <label>Tanggal</label>
                <input
                    type="date"
                    name="date"
                    id="edit_date"
                    class="form-control"
                    required>
            </div>

            <div class="form-group">
                <label>Lokasi / Node</label>
                <select name="location" id="edit_location" class="form-control" required>
                    <option value="Hulu (Setu Pamulang)">Hulu (Setu Pamulang)</option>
                    <option value="Hilir (BPI Pamulang)">Hilir (BPI Pamulang)</option>
                    <option value="Lokasi Lainnya">Lokasi Lainnya</option>
                </select>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" id="edit_status" class="form-control" required>
                    <option value="Terjadwal">Terjadwal</option>
                    <option value="Sedang Berjalan">Sedang Berjalan</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>

            <div class="form-group">
                <label>Keterangan Tambahan</label>
                <textarea
                    name="description"
                    id="edit_description"
                    class="form-control"
                    rows="3"
                    maxlength="500"></textarea>
            </div>

            <button type="submit" class="btn-primary">Update Jadwal</button>
        </form>
    </div>
</div>

<script>
    function openModal() {
        const modal = document.getElementById('addModal');

        if (modal) {
            modal.classList.add('active');
        }
    }

    function closeModal() {
        const modal = document.getElementById('addModal');

        if (modal) {
            modal.classList.remove('active');
        }
    }

    function openEditModalFromButton(button) {
        const id = button.dataset.id;
        const title = button.dataset.title || '';
        const date = button.dataset.date || '';
        const location = button.dataset.location || '';
        const status = button.dataset.status || 'Terjadwal';
        const description = button.dataset.description || '';

        const editForm = document.getElementById('editForm');
        const editTitle = document.getElementById('edit_title');
        const editDate = document.getElementById('edit_date');
        const editLocation = document.getElementById('edit_location');
        const editStatus = document.getElementById('edit_status');
        const editDescription = document.getElementById('edit_description');
        const editModal = document.getElementById('editModal');

        if (editForm) {
            editForm.action = "/maintenance/" + encodeURIComponent(id);
        }

        if (editTitle) {
            editTitle.value = title;
        }

        if (editDate) {
            editDate.value = date;
        }

        if (editLocation) {
            editLocation.value = location;
        }

        if (editStatus) {
            editStatus.value = status;
        }

        if (editDescription) {
            editDescription.value = description;
        }

        if (editModal) {
            editModal.classList.add('active');
        }
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');

        if (modal) {
            modal.classList.remove('active');
        }
    }
</script>
@endsection