@extends('layouts.app')

@section('title', 'Aktivitas Log')

@section('content')
<div class="dashboard-grid" style="grid-template-columns: 1fr;">
    <div class="widget-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2 class="section-title" style="margin: 0;">Log Sistem & Riwayat Deteksi</h2>
            <div>
                <button class="btn-primary" onclick="exportToCSV()" style="width: auto; padding: 8px 16px; background: var(--bg-color); color: var(--primary-blue); border: 1px solid var(--primary-blue); margin-right: 8px;">
                    <i class="fas fa-file-export"></i> Ekspor CSV
                </button>

            </div>
        </div>

        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('webLogs')">Log Aktivitas Web</button>
            <button class="tab-btn" onclick="switchTab('sensorLogs')">Log Sensor</button>
            <button class="tab-btn" onclick="switchTab('imageLogs')">Log Gambar</button>
        </div>
        
        <div id="webLogs" class="tab-pane active" style="display: flex; flex-direction: column; gap: 12px;">
            <div style="display: flex; justify-content: flex-end;">
                <button class="btn-primary" onclick="clearWebLogs()" style="width: auto; padding: 6px 12px; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; font-size: 13px;">
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
                    <select id="filterNode" class="form-control" style="padding: 6px 12px; font-size: 13px; border-radius: 6px; width: auto;" onchange="if(window.renderSensorTable) window.renderSensorTable()">
                        <option value="all">Semua Node</option>
                        <option value="hulu">Node Hulu</option>
                        <option value="hilir">Node Hilir</option>
                    </select>
                    <select id="filterStatus" class="form-control" style="padding: 6px 12px; font-size: 13px; border-radius: 6px; width: auto;" onchange="if(window.renderSensorTable) window.renderSensorTable()">
                        <option value="all">Semua Status</option>
                        <option value="AMAN">Aman</option>
                        <option value="SIAGA">Siaga</option>
                        <option value="WASPADA">Waspada</option>
                    </select>
                </div>
                <button class="btn-primary" id="clearSensorLogsBtn" style="width: auto; padding: 6px 12px; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; font-size: 13px;">
                    <i class="fas fa-trash"></i> Bersihkan Semua
                </button>
            </div>
            <div id="sensorLogsContent" style="display: flex; flex-direction: column; gap: 12px; min-height: 200px; justify-content: center; align-items: center; color: var(--text-secondary);">
                <i class="fas fa-spinner fa-spin fa-2x" style="margin-bottom: 12px;"></i>
                <p>Memuat tabel data sensor...</p>
            </div>
        </div>

        <div id="imageLogs" class="tab-pane" style="display: none; flex-direction: column; gap: 12px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <p style="color: var(--text-secondary); font-size: 14px;">Riwayat tangkapan gambar dari sensor kamera node.</p>
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
    function switchTab(tabId) {
        // Remove active class from all buttons and panes
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('active');
            pane.style.display = 'none';
        });

        // Add active class to clicked button and target pane
        event.target.classList.add('active');
        const targetPane = document.getElementById(tabId);
        targetPane.classList.add('active');
        targetPane.style.display = 'flex';
    }

    function clearWebLogs() {
        if(confirm("Apakah Anda yakin ingin menghapus log aktivitas web?")) {
            document.getElementById('webLogsContent').innerHTML = `
                <div style="text-align: center; padding: 24px; color: var(--text-secondary);">
                    <p>Aktivitas log web telah dibersihkan.</p>
                </div>
            `;
            showToast('Log aktivitas web berhasil dibersihkan.', 'success');
        }
    }

    window.allSensorLogs = [];
    
    function exportToCSV() {
        if (!window.allSensorLogs || window.allSensorLogs.length === 0) {
            showToast('Tidak ada data sensor untuk diekspor!', 'danger');
            return;
        }

        let csvContent = "\uFEFF"; // Tambahkan UTF-8 BOM agar Excel membacanya dengan rapi
        csvContent += "ID;Waktu;Node;Ketinggian Air (cm);Arus Air (L/min);Curah Hujan (mm/jam);Kecepatan Angin (km/jam);Status\n";

        window.allSensorLogs.forEach(row => {
            csvContent += `${row.id};${row.waktu};${row.node};${row.ketinggian_air};${row.arus_air};${row.curah_hujan};${row.kecepatan_angin};${row.status}\n`;
        });

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement("a");
        const url = URL.createObjectURL(blob);
        link.setAttribute("href", url);
        link.setAttribute("download", "log_sensor_bpi_pamulang_" + new Date().getTime() + ".csv");
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        showToast('Berhasil mengunduh format Excel (CSV)!', 'success');
    }
</script>

<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js";
    import { getFirestore, collection, query, orderBy, limit, onSnapshot, getDocs, writeBatch, doc, deleteDoc } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore.js";

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
    const db = getFirestore(app);

    const sensorLogsContainer = document.getElementById('sensorLogsContent');

    try {
        const logDataRef = collection(db, 'monitoring', 'depok', 'log_data');
        const q = query(logDataRef, orderBy('time', 'desc'));

        window.currentPage = 1;
        const rowsPerPage = 50;

        window.renderSensorTable = function() {
            const filterNode = document.getElementById('filterNode') ? document.getElementById('filterNode').value : 'all';
            const filterStatus = document.getElementById('filterStatus') ? document.getElementById('filterStatus').value : 'all';

            let filteredLogs = window.allSensorLogs || [];

            if (filterNode !== 'all') {
                filteredLogs = filteredLogs.filter(log => log.nodeCode === filterNode);
            }
            if (filterStatus !== 'all') {
                filteredLogs = filteredLogs.filter(log => log.status === filterStatus);
            }

            if (filteredLogs.length === 0) {
                sensorLogsContainer.innerHTML = `
                    <div style="text-align: center; padding: 24px; color: var(--text-secondary);">
                        <i class="fas fa-filter" style="font-size: 24px; margin-bottom: 8px;"></i>
                        <p>Tidak ada data yang sesuai dengan filter pencarian.</p>
                    </div>`;
                return;
            }

            // Client-side pagination logic
            const totalPages = Math.ceil(filteredLogs.length / rowsPerPage);
            if (window.currentPage > totalPages) window.currentPage = totalPages;
            if (window.currentPage < 1) window.currentPage = 1;

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
                            <th style="padding: 12px 16px; font-weight: 600; color: #475569; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            paginatedLogs.forEach((log) => {
                let statusIcon = log.status === 'AMAN' ? '<i class="fas fa-check-circle"></i>' : 
                                 (log.status === 'SIAGA' ? '<i class="fas fa-exclamation-circle"></i>' : '<i class="fas fa-exclamation-triangle"></i>');
                
                htmlContent += `
                    <tr style="border-bottom: 1px solid #e2e8f0; background: ${log.statusBg}; transition: background 0.2s;">
                        <td style="padding: 12px 16px; white-space: nowrap;">${log.dateStr}</td>
                        <td style="padding: 12px 16px; font-weight: 500;">${log.node}</td>
                        <td style="padding: 12px 16px;">${log.ketinggian_air}</td>
                        <td style="padding: 12px 16px;">${log.arus_air}</td>
                        <td style="padding: 12px 16px;">${log.curah_hujan}</td>
                        <td style="padding: 12px 16px;">${log.kecepatan_angin}</td>
                        <td style="padding: 12px 16px;">
                            <span style="display: inline-flex; align-items: center; gap: 6px; background: ${log.status === 'AMAN' ? '#dcfce7' : (log.status === 'SIAGA' ? '#fef08a' : '#fee2e2')}; color: ${log.status === 'AMAN' ? '#166534' : (log.status === 'SIAGA' ? '#854d0e' : '#991b1b')}; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600;">
                                ${statusIcon}
                                ${log.status}
                            </span>
                        </td>
                        <td style="padding: 12px 16px; text-align: center;">
                            <button class="btn-primary" style="padding: 4px 8px; font-size: 12px; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; cursor: pointer;" onclick="deleteLogEntry('${log.id}')">Hapus</button>
                        </td>
                    </tr>
                `;
            });

            htmlContent += `</tbody></table></div>`;

            // Pagination Controls
            htmlContent += `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px; padding: 8px 0;">
                    <div style="font-size: 13px; color: var(--text-secondary);">
                        Menampilkan ${startIndex + 1} - ${Math.min(endIndex, filteredLogs.length)} dari total ${filteredLogs.length} data
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <button onclick="window.changePage(-1)" ${window.currentPage === 1 ? 'disabled' : ''} style="padding: 6px 12px; border: 1px solid #cbd5e1; background: ${window.currentPage === 1 ? '#f1f5f9' : 'white'}; border-radius: 6px; cursor: ${window.currentPage === 1 ? 'not-allowed' : 'pointer'};">Sebelumnya</button>
                        <span style="padding: 6px 12px; font-weight: 600;">Halaman ${window.currentPage} dari ${totalPages}</span>
                        <button onclick="window.changePage(1)" ${window.currentPage === totalPages ? 'disabled' : ''} style="padding: 6px 12px; border: 1px solid #cbd5e1; background: ${window.currentPage === totalPages ? '#f1f5f9' : 'white'}; border-radius: 6px; cursor: ${window.currentPage === totalPages ? 'not-allowed' : 'pointer'};">Selanjutnya</button>
                    </div>
                </div>
            `;

            sensorLogsContainer.innerHTML = htmlContent;
        };

        window.renderImageGallery = function() {
            const container = document.getElementById('imageLogsContent');
            if (window.imageLogsData.length === 0) {
                container.innerHTML = `
                    <div style="grid-column: 1 / -1; text-align: center; padding: 48px; color: var(--text-secondary);">
                        <i class="far fa-images" style="font-size: 48px; margin-bottom: 16px; color: #cbd5e1;"></i>
                        <p>Belum ada riwayat tangkapan gambar dari webcam.</p>
                    </div>`;
                return;
            }
            
            let htmlStr = '';
            window.imageLogsData.forEach(img => {
                let badgeColor = img.status === "AMAN" ? "var(--success-color)" : (img.status === "WASPADA" ? "var(--danger-color)" : "#eab308");
                htmlStr += `
                    <div class="widget-card interactive-card" style="padding: 12px; display: flex; flex-direction: column; gap: 8px;">
                        <img src="${img.url}" style="width: 100%; height: 180px; object-fit: cover; border-radius: 6px; background: #0f172a;" onerror="this.src='https://via.placeholder.com/300x180?text=Kamera+Offline'">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                            <span style="font-weight: 600; font-size: 14px; color: var(--primary-blue);">${img.node}</span>
                            <span style="font-size: 11px; padding: 3px 8px; border-radius: 4px; background: ${badgeColor}; color: white; font-weight: bold;">${img.status}</span>
                        </div>
                        <span style="font-size: 12px; color: var(--text-secondary);"><i class="far fa-clock"></i> ${img.time}</span>
                    </div>
                `;
            });
            container.innerHTML = htmlStr;
        };

        window.changePage = function(direction) {
            window.currentPage += direction;
            window.renderSensorTable();
        };

        // Global function to delete a specific log entry
        window.deleteLogEntry = async function(docId) {
            if(confirm("Apakah Anda yakin ingin menghapus data log ini?")) {
                try {
                    await deleteDoc(doc(db, 'monitoring', 'depok', 'log_data', docId));
                    showToast('Data log berhasil dihapus.', 'success');
                } catch(error) {
                    console.error("Gagal menghapus data:", error);
                    showToast('Gagal menghapus data. Periksa koneksi.', 'danger');
                }
            }
        };

        onSnapshot(q, (snapshot) => {
            if (snapshot.empty) {
                sensorLogsContainer.innerHTML = `
                    <div style="text-align: center; padding: 24px; color: var(--text-secondary);">
                        <i class="fas fa-exclamation-circle" style="font-size: 24px; margin-bottom: 8px;"></i>
                        <p>Tidak ada data sensor ditemukan di database.</p>
                    </div>`;
                return;
            }

            window.allSensorLogs = []; // Reset global data untuk export & filter
            window.imageLogsData = []; // Data untuk gambar

            snapshot.forEach((doc) => {
                const data = doc.data();

                // HIDE DUMMY DATA DIRECTLY FROM WEB (As requested)
                if (data.time && typeof data.time === 'string') {
                    if (data.time.startsWith('2026-06-09 07:') || data.time.startsWith('2026-06-09 08:') || data.time.startsWith('2026-06-09 09:')) {
                        return; // Skip rendering this dummy data
                    }
                }

                const nodeName = data.penempatan === 'hulu' ? 'Node Hulu' : 'Node Hilir';
                const nodeCode = data.penempatan === 'hulu' ? 'hulu' : 'hilir';
                
                let dateStr = 'Waktu tidak valid';
                let isoDate = '';
                if (data.time) {
                    const date = (data.time.toDate) ? data.time.toDate() : new Date();
                    dateStr = date.toLocaleString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', day: '2-digit', month: 'short', year: 'numeric' });
                    isoDate = date.toISOString();
                }

                let statusText = "AMAN";
                let statusColor = "var(--success-color)";
                let statusBg = "transparent";

                if (data.water_level > 150) {
                    statusText = "WASPADA";
                    statusColor = "var(--danger-color)";
                    statusBg = "#fef2f2";
                } else if (data.water_level > 100) {
                    statusText = "SIAGA";
                    statusColor = "#ca8a04";
                    statusBg = "#fefce8";
                }

                const wLevel = data.water_level !== undefined ? data.water_level : 0;
                const wFlow = data.water_flow !== undefined ? data.water_flow : 0;
                const rain = data.ombrometer !== undefined ? data.ombrometer : 0;
                const wind = data.anemometer !== undefined ? data.anemometer : 0;

                window.allSensorLogs.push({
                    id: doc.id,
                    waktu: isoDate,
                    dateStr: dateStr,
                    node: nodeName,
                    nodeCode: nodeCode,
                    ketinggian_air: wLevel,
                    arus_air: wFlow,
                    curah_hujan: rain,
                    kecepatan_angin: wind,
                    status: statusText,
                    statusColor: statusColor,
                    statusBg: statusBg
                });

                // Cek gambar
                if (data.espcam_img_url && data.espcam_img_url.trim() !== "") {
                    window.imageLogsData.push({
                        id: doc.id,
                        url: data.espcam_img_url,
                        time: dateStr,
                        node: nodeName,
                        status: statusText
                    });
                }
            });

            // Initial render
            window.renderSensorTable();
            window.renderImageGallery();

        }, (error) => {
            console.error("Firestore Error: " + error.message);
            document.getElementById('sensorLogsContent').innerHTML = `
                <div style="text-align: center; padding: 24px; color: var(--danger-color);">
                    <i class="fas fa-exclamation-triangle" style="font-size: 24px; margin-bottom: 8px;"></i>
                    <p>Error mengambil data sensor: ${error.message}</p>
                </div>`;
        });

        window.deleteSingleLog = async function(docId) {
            if (confirm("Apakah Anda yakin ingin menghapus baris log ini?")) {
                try {
                    await deleteDoc(doc(db, 'monitoring', 'depok', 'log_data', docId));
                    showToast('Data log berhasil dihapus!', 'success');
                } catch (error) {
                    console.error("Error deleting log:", error);
                    showToast('Gagal menghapus log.', 'danger');
                }
            }
        };
    } catch (e) {
        console.error("Logs error: ", e);
    }

    // Real deletion for Sensor Logs
    const clearSensorLogsBtn = document.getElementById('clearSensorLogsBtn');
    if (clearSensorLogsBtn) {
        clearSensorLogsBtn.addEventListener('click', async () => {
            if(confirm("PERINGATAN: Apakah Anda yakin ingin menghapus SELURUH data log sensor dari database Firestore? Tindakan ini tidak dapat dibatalkan!")) {
                clearSensorLogsBtn.innerText = "Menghapus...";
                clearSensorLogsBtn.disabled = true;
                try {
                    const response = await fetch('{{ route("logs.clear") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        if (result.deleted > 0) {
                            showToast(`Data log sensor berhasil dihapus! (${result.deleted} data)`, 'success');
                        } else {
                            showToast('Tidak ada data sensor untuk dihapus.', 'info');
                        }
                    } else {
                        throw new Error(result.error || "Gagal menghapus.");
                    }
                } catch(error) {
                    console.error("Gagal menghapus log:", error);
                    showToast('Gagal menghapus log sensor. Pastikan koneksi stabil.', 'danger');
                } finally {
                    clearSensorLogsBtn.innerHTML = '<i class="fas fa-trash"></i> Bersihkan Log Sensor';
                    clearSensorLogsBtn.disabled = false;
                }
            }
        });
    }
</script>
@endsection
