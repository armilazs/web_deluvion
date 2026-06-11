@extends('layouts.app')

@section('title', 'Aktivitas Log')

@section('content')
<div class="dashboard-grid" style="grid-template-columns: 1fr;">
    <div class="widget-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2 class="section-title" style="margin: 0;">Log Sistem & Riwayat Deteksi</h2>

            <div>
                <button
                    class="btn-primary"
                    onclick="exportToCSV()"
                    style="width: auto; padding: 8px 16px; background: var(--bg-color); color: var(--primary-blue); border: 1px solid var(--primary-blue); margin-right: 8px;">
                    <i class="fas fa-file-export"></i> Ekspor CSV
                </button>
            </div>
        </div>

        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab(event, 'webLogs')">Log Aktivitas Web</button>
            <button class="tab-btn" onclick="switchTab(event, 'sensorLogs')">Log Sensor</button>
            <button class="tab-btn" onclick="switchTab(event, 'imageLogs')">Log Gambar</button>
        </div>

        <div id="webLogs" class="tab-pane active" style="display: flex; flex-direction: column; gap: 12px;">
            <div style="display: flex; justify-content: flex-end;">
                <button
                    class="btn-primary"
                    onclick="clearWebLogs()"
                    style="width: auto; padding: 6px 12px; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; font-size: 13px;">
                    <i class="fas fa-trash"></i> Bersihkan Log Web
                </button>
            </div>

            <div id="webLogsContent" style="display: flex; flex-direction: column; gap: 12px;">
                <div style="text-align: center; padding: 24px; color: var(--text-secondary);">
                    <p>Aktivitas log web masih kosong.</p>
                </div>
            </div>
        </div>

        <div id="sensorLogs" class="tab-pane" style="display: none; flex-direction: column; gap: 12px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <div style="display: flex; gap: 8px;">
                    <select
                        id="filterNode"
                        class="form-control"
                        style="padding: 6px 12px; font-size: 13px; border-radius: 6px; width: auto;"
                        onchange="if(window.renderSensorTable) window.renderSensorTable()">
                        <option value="all">Semua Node</option>
                        <option value="hulu">Node Hulu</option>
                        <option value="hilir">Node Hilir</option>
                    </select>

                    <select
                        id="filterStatus"
                        class="form-control"
                        style="padding: 6px 12px; font-size: 13px; border-radius: 6px; width: auto;"
                        onchange="if(window.renderSensorTable) window.renderSensorTable()">
                        <option value="all">Semua Status</option>
                        <option value="AMAN">Aman</option>
                        <option value="SIAGA">Siaga</option>
                        <option value="WASPADA">Waspada</option>
                    </select>
                </div>

                <button
                    class="btn-primary"
                    id="clearSensorLogsBtn"
                    style="width: auto; padding: 6px 12px; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; font-size: 13px;">
                    <i class="fas fa-trash"></i> Bersihkan Semua
                </button>
            </div>

            <div
                id="sensorLogsContent"
                style="display: flex; flex-direction: column; gap: 12px; min-height: 200px; justify-content: center; align-items: center; color: var(--text-secondary);">
                <i class="fas fa-spinner fa-spin fa-2x" style="margin-bottom: 12px;"></i>
                <p>Memuat tabel data sensor...</p>
            </div>
        </div>

        <div id="imageLogs" class="tab-pane" style="display: none; flex-direction: column; gap: 12px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <p style="color: var(--text-secondary); font-size: 14px;">
                    Riwayat tangkapan gambar dari sensor kamera node.
                </p>
            </div>

            <div id="imageLogsContent" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
                <div style="grid-column: 1 / -1; text-align: center; padding: 48px; color: var(--text-secondary);">
                    <i class="fas fa-spinner fa-spin fa-2x" style="margin-bottom: 12px;"></i>
                    <p>Memuat log gambar dari webcam...</p>
                </div>
            </div>
        </div>

        <div id="emptyLogState" style="display: none; text-align: center; padding: 48px; color: var(--text-secondary);">
            <i class="far fa-folder-open" style="font-size: 48px; margin-bottom: 16px; color: #cbd5e1;"></i>
            <p>Aktivitas log kosong. Belum ada rekaman sistem baru.</p>
        </div>
    </div>
</div>

<script>
    window.allSensorLogs = [];
    window.currentPage = 1;

    function switchTab(event, tabId) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('active');
            pane.style.display = 'none';
        });

        event.target.classList.add('active');

        const targetPane = document.getElementById(tabId);
        if (targetPane) {
            targetPane.classList.add('active');
            targetPane.style.display = 'flex';
        }
    }

    function clearWebLogs() {
        if (confirm("Apakah Anda yakin ingin menghapus log aktivitas web?")) {
            const webLogsContent = document.getElementById('webLogsContent');

            if (webLogsContent) {
                webLogsContent.innerHTML = `
                    <div style="text-align: center; padding: 24px; color: var(--text-secondary);">
                        <p>Aktivitas log web telah dibersihkan.</p>
                    </div>
                `;
            }

            showToast('Log aktivitas web berhasil dibersihkan.', 'success');
        }
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function safeCsv(value) {
        const text = String(value ?? '').replaceAll('"', '""');

        if (/^[=+\-@]/.test(text)) {
            return `"'${text}"`;
        }

        return `"${text}"`;
    }

    function exportToCSV() {
        if (!window.allSensorLogs || window.allSensorLogs.length === 0) {
            showToast('Tidak ada data sensor untuk diekspor!', 'danger');
            return;
        }

        let csvContent = "\uFEFF";
        csvContent += "ID;Waktu;Node;Ketinggian Air (cm);Arus Air (L/min);Curah Hujan (mm/jam);Kecepatan Angin (km/jam);Status\n";

        window.allSensorLogs.forEach(row => {
            csvContent += [
                safeCsv(row.id),
                safeCsv(row.waktu),
                safeCsv(row.node),
                safeCsv(row.ketinggian_air),
                safeCsv(row.arus_air),
                safeCsv(row.curah_hujan),
                safeCsv(row.kecepatan_angin),
                safeCsv(row.status)
            ].join(';') + "\n";
        });

        const blob = new Blob([csvContent], {
            type: 'text/csv;charset=utf-8;'
        });

        const link = document.createElement("a");
        const url = URL.createObjectURL(blob);

        link.setAttribute("href", url);
        link.setAttribute("download", "log_sensor_deluvion_" + new Date().getTime() + ".csv");
        link.style.visibility = 'hidden';

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        URL.revokeObjectURL(url);

        showToast('Berhasil mengunduh format CSV.', 'success');
    }
</script>

<script type="module">
    import {
        initializeApp
    } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js";

    import {
        getAuth,
        onAuthStateChanged
    } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-auth.js";

    import {
        getFirestore,
        collection,
        query,
        orderBy,
        onSnapshot
    } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore.js";

    const firebaseConfig = {
        apiKey: "AIzaSyB27xUygjk082h56nsqaa1r4Nm5tQBiY9g",
        authDomain: "deluvion-23.firebaseapp.com",
        projectId: "deluvion-23",
        storageBucket: "deluvion-23.firebasestorage.app",
        messagingSenderId: "603292812342",
        appId: "1:603292812342:web:cb7afaf76ca5710b7e4497",
        measurementId: "G-2J5Z645QL2"
    };

    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);
    const db = getFirestore(app);

    const sensorLogsContainer = document.getElementById('sensorLogsContent');
    const imageLogsContainer = document.getElementById('imageLogsContent');
    const clearSensorLogsBtn = document.getElementById('clearSensorLogsBtn');

    const rowsPerPage = 50;
    let unsubscribeSensorLogs = null;

    function getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    }

    function formatTimestamp(value) {
        try {
            if (value && typeof value.toDate === 'function') {
                const dateObj = value.toDate();
                return dateObj.toLocaleString('id-ID', {
                    dateStyle: 'medium',
                    timeStyle: 'short'
                });
            }

            if (typeof value === 'string') {
                const dateObj = new Date(value.replace(' ', 'T'));

                if (!Number.isNaN(dateObj.getTime())) {
                    return dateObj.toLocaleString('id-ID', {
                        dateStyle: 'medium',
                        timeStyle: 'short'
                    });
                }

                return value;
            }

            return '-';
        } catch (error) {
            return '-';
        }
    }

    function getStatusByWaterLevel(value) {
        const level = Number(value);

        if (Number.isNaN(level)) {
            return 'AMAN';
        }

        if (level > 150) {
            return 'WASPADA';
        }

        if (level > 100) {
            return 'SIAGA';
        }

        return 'AMAN';
    }

    function getStatusStyle(status) {
        if (status === 'AMAN') {
            return {
                bg: '#dcfce7',
                color: '#166534',
                rowBg: '#ffffff',
                icon: '<i class="fas fa-check-circle"></i>'
            };
        }

        if (status === 'SIAGA') {
            return {
                bg: '#fef08a',
                color: '#854d0e',
                rowBg: '#fffbeb',
                icon: '<i class="fas fa-exclamation-circle"></i>'
            };
        }

        return {
            bg: '#fee2e2',
            color: '#991b1b',
            rowBg: '#fff1f2',
            icon: '<i class="fas fa-exclamation-triangle"></i>'
        };
    }

    window.renderSensorTable = function() {
        const filterNode = document.getElementById('filterNode') ?
            document.getElementById('filterNode').value :
            'all';

        const filterStatus = document.getElementById('filterStatus') ?
            document.getElementById('filterStatus').value :
            'all';

        let filteredLogs = window.allSensorLogs || [];

        if (filterNode !== 'all') {
            filteredLogs = filteredLogs.filter(log => log.nodeCode === filterNode);
        }

        if (filterStatus !== 'all') {
            filteredLogs = filteredLogs.filter(log => log.status === filterStatus);
        }

        if (!sensorLogsContainer) {
            return;
        }

        if (filteredLogs.length === 0) {
            sensorLogsContainer.innerHTML = `
                <div style="text-align: center; padding: 24px; color: var(--text-secondary);">
                    <i class="fas fa-filter" style="font-size: 24px; margin-bottom: 8px;"></i>
                    <p>Tidak ada data yang sesuai dengan filter pencarian.</p>
                </div>
            `;
            return;
        }

        const totalPages = Math.ceil(filteredLogs.length / rowsPerPage);

        if (window.currentPage > totalPages) {
            window.currentPage = totalPages;
        }

        if (window.currentPage < 1) {
            window.currentPage = 1;
        }

        const startIndex = (window.currentPage - 1) * rowsPerPage;
        const endIndex = startIndex + rowsPerPage;
        const paginatedLogs = filteredLogs.slice(startIndex, endIndex);

        let htmlContent = `
            <div style="overflow-x: auto; border-radius: 8px; border: 1px solid #e2e8f0; width: 100%;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                            <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Waktu</th>
                            <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Node</th>
                            <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Air (cm)</th>
                            <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Arus (L/min)</th>
                            <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Hujan (mm/j)</th>
                            <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Angin (km/j)</th>
                            <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        paginatedLogs.forEach((log) => {
            const statusStyle = getStatusStyle(log.status);

            htmlContent += `
                <tr style="border-bottom: 1px solid #e2e8f0; background: ${statusStyle.rowBg}; transition: background 0.2s;">
                    <td style="padding: 12px 16px; white-space: nowrap;">${escapeHtml(log.dateStr)}</td>
                    <td style="padding: 12px 16px; font-weight: 500;">${escapeHtml(log.node)}</td>
                    <td style="padding: 12px 16px;">${escapeHtml(log.ketinggian_air)}</td>
                    <td style="padding: 12px 16px;">${escapeHtml(log.arus_air)}</td>
                    <td style="padding: 12px 16px;">${escapeHtml(log.curah_hujan)}</td>
                    <td style="padding: 12px 16px;">${escapeHtml(log.kecepatan_angin)}</td>
                    <td style="padding: 12px 16px;">
                        <span style="display: inline-flex; align-items: center; gap: 6px; background: ${statusStyle.bg}; color: ${statusStyle.color}; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600;">
                            ${statusStyle.icon}
                            ${escapeHtml(log.status)}
                        </span>
                    </td>
                </tr>
            `;
        });

        htmlContent += `
                    </tbody>
                </table>
            </div>
        `;

        htmlContent += `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px;">
                <button
                    class="btn-primary"
                    style="width: auto; padding: 6px 12px; font-size: 13px;"
                    ${window.currentPage <= 1 ? 'disabled' : ''}
                    onclick="window.currentPage--; window.renderSensorTable();">
                    Sebelumnya
                </button>

                <span style="font-size: 13px; color: var(--text-secondary);">
                    Halaman ${window.currentPage} dari ${totalPages}
                </span>

                <button
                    class="btn-primary"
                    style="width: auto; padding: 6px 12px; font-size: 13px;"
                    ${window.currentPage >= totalPages ? 'disabled' : ''}
                    onclick="window.currentPage++; window.renderSensorTable();">
                    Berikutnya
                </button>
            </div>
        `;

        sensorLogsContainer.innerHTML = htmlContent;
    };

    function renderImageLogsFromSensorData(logs) {
        if (!imageLogsContainer) {
            return;
        }

        const imageLogs = logs.filter(log => log.imageUrl);

        if (imageLogs.length === 0) {
            imageLogsContainer.innerHTML = `
                <div style="grid-column: 1 / -1; text-align: center; padding: 48px; color: var(--text-secondary);">
                    <i class="far fa-images" style="font-size: 36px; margin-bottom: 12px;"></i>
                    <p>Belum ada log gambar yang tersedia.</p>
                </div>
            `;
            return;
        }

        let htmlContent = '';

        imageLogs.slice(0, 30).forEach(log => {
            htmlContent += `
                <div class="interactive-card" style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background: #fff;">
                    <img
                        src="${escapeHtml(log.imageUrl)}"
                        alt="Gambar sensor ${escapeHtml(log.node)}"
                        style="width: 100%; height: 180px; object-fit: cover;"
                        onerror="this.style.display='none';">

                    <div style="padding: 12px;">
                        <div style="font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">
                            ${escapeHtml(log.node)}
                        </div>

                        <div style="font-size: 12px; color: var(--text-secondary);">
                            ${escapeHtml(log.dateStr)}
                        </div>
                    </div>
                </div>
            `;
        });

        imageLogsContainer.innerHTML = htmlContent;
    }

    function buildLogObject(docSnap) {
        const data = docSnap.data();

        const nodeCode = data.penempatan === 'hulu' ? 'hulu' : 'hilir';
        const nodeName = nodeCode === 'hulu' ? 'Node Hulu' : 'Node Hilir';
        const status = data.status || getStatusByWaterLevel(data.water_level);
        const dateStr = formatTimestamp(data.time);

        return {
            id: docSnap.id,
            waktu: dateStr,
            dateStr: dateStr,
            node: nodeName,
            nodeCode: nodeCode,
            ketinggian_air: data.water_level ?? '-',
            arus_air: data.water_flow ?? '-',
            curah_hujan: data.ombrometer ?? '-',
            kecepatan_angin: data.anemometer ?? '-',
            status: status,
            imageUrl: data.espcam_img_url || data.image_url || ''
        };
    }

    function startSensorLogListener() {
        if (!sensorLogsContainer) {
            return;
        }

        const logDataRef = collection(db, 'monitoring', 'depok', 'log_data');
        const q = query(logDataRef, orderBy('time', 'desc'));

        unsubscribeSensorLogs = onSnapshot(q, (snapshot) => {
            if (snapshot.empty) {
                window.allSensorLogs = [];

                sensorLogsContainer.innerHTML = `
                    <div style="text-align: center; padding: 24px; color: var(--text-secondary);">
                        <p>Belum ada data log sensor.</p>
                    </div>
                `;

                renderImageLogsFromSensorData([]);
                return;
            }

            const logs = [];

            snapshot.forEach((docSnap) => {
                logs.push(buildLogObject(docSnap));
            });

            window.allSensorLogs = logs;
            window.currentPage = 1;

            window.renderSensorTable();
            renderImageLogsFromSensorData(logs);
        }, (error) => {
            console.error("Firestore log listener error:", error);

            sensorLogsContainer.innerHTML = `
                <div style="text-align: center; padding: 24px; color: #b91c1c;">
                    <p>Gagal memuat log sensor.</p>
                </div>
            `;
        });
    }

    if (clearSensorLogsBtn) {
        clearSensorLogsBtn.addEventListener('click', async () => {
            const confirmDelete = confirm(
                "Apakah Anda yakin ingin menghapus semua log sensor? Aksi ini hanya boleh dilakukan oleh admin."
            );

            if (!confirmDelete) {
                return;
            }

            clearSensorLogsBtn.disabled = true;
            clearSensorLogsBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';

            try {
                const response = await fetch("{{ route('logs.clear') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": getCsrfToken(),
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({})
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(result.error || "Gagal menghapus log sensor.");
                }

                showToast(`Berhasil menghapus ${result.deleted} log sensor.`, 'success');
            } catch (error) {
                console.error("Clear logs error:", error);
                showToast("Gagal menghapus log sensor.", 'danger');
            } finally {
                clearSensorLogsBtn.disabled = false;
                clearSensorLogsBtn.innerHTML = '<i class="fas fa-trash"></i> Bersihkan Semua';
            }
        });
    }

    onAuthStateChanged(auth, (user) => {
        if (!user) {
            window.location.href = @json(route('login'));
            return;
        }

        startSensorLogListener();
    });

    window.addEventListener('beforeunload', () => {
        if (typeof unsubscribeSensorLogs === 'function') {
            unsubscribeSensorLogs();
        }
    });
</script>
@endsection