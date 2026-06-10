@extends('layouts.app')

@section('title', 'BPI PAMULANG')

@section('content')
<style>
    /* Medium Compact Layout */
    .dashboard-grid { gap: 16px; }
    .main-column { gap: 12px !important; }
    .side-column { gap: 16px !important; }
    
    /* Padding Kotak yang Proporsional */
    .dashboard-grid .stat-card { padding: 12px 16px; }
    .dashboard-grid .status-card { padding: 16px 20px; }
    .widget-card { padding: 16px 20px; margin-bottom: 0; }
    .camera-card { padding: 12px 16px !important; margin: 0 !important;}
    
    /* Ukuran Font Menengah */
    .stat-card .value { font-size: 28px !important; }
    .stat-card .label { font-size: 12px; margin-bottom: 6px; }
    .section-title { font-size: 16px; margin-bottom: 12px !important; margin-top: 8px !important; }
    .status-card h2 { font-size: 18px; margin-bottom: 4px; }
    .status-card p { font-size: 13px; margin-bottom: 0; }
    
    /* Tinggi Peta dan Grafik Menengah */
    .map-container { height: 180px !important; border-radius: 8px; }
    .chart-card { min-height: 200px !important; padding: 16px !important; }
    .dashboard-grid .stats-grid { gap: 12px !important; margin-bottom: 8px !important; }
    
</style>
<div class="dashboard-grid">
    <!-- Main Column -->
    <div class="main-column">
        
        <!-- Status Cards (Sejajar) -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 8px;">
            <div class="status-card interactive-card">
                <div>
                    <h2>Setu Pamulang</h2>
                    <p>Hulu</p>
                    <div class="online-badge" id="huluOnlineBadge">🟢 ONLINE</div>
                </div>
                <div class="status-badge" id="huluStatus">AMAN</div>
            </div>

            <div class="status-card interactive-card">
                <div>
                    <h2>BPI PAMULANG</h2>
                    <p>Hilir</p>
                    <div class="online-badge" id="hilirOnlineBadge">🟢 ONLINE</div>
                </div>
                <div class="status-badge" id="hilirStatus">AMAN</div>
            </div>
        </div>

        <!-- Data Statistik Grouped -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <!-- Bagian Hulu -->
            <div style="background: rgba(255, 255, 255, 0.5); padding: 12px; border-radius: 12px; border: 1px dashed #cbd5e1;">
                <div class="section-title" style="margin-top: 0; margin-bottom: 12px; color: var(--success-color); font-size: 15px;">
                    <i class="fas fa-tint"></i> Sensor Node Hulu
                </div>
                <div class="stats-grid" style="grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="stat-card interactive-card">
                        <div class="label">Ketinggian (Cm)</div>
                        <div class="value" id="huluLevel">--</div>
                    </div>
                    <div class="stat-card interactive-card">
                        <div class="label">Arus (L/min)</div>
                        <div class="value red" id="huluFlow">--</div>
                    </div>
                    <div class="stat-card interactive-card">
                        <div class="label">Curah Hujan (mm/j)</div>
                        <div class="value" id="huluRain">--</div>
                    </div>
                    <div class="stat-card interactive-card">
                        <div class="label">Angin (km/j)</div>
                        <div class="value" id="huluWind">--</div>
                    </div>
                </div>
            </div>

            <!-- Bagian Hilir -->
            <div style="background: rgba(255, 255, 255, 0.5); padding: 12px; border-radius: 12px; border: 1px dashed #cbd5e1;">
                <div class="section-title" style="margin-top: 0; margin-bottom: 12px; color: var(--primary-blue); font-size: 15px;">
                    <i class="fas fa-water"></i> Sensor Node Hilir
                </div>
                <div class="stats-grid" style="grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="stat-card interactive-card">
                        <div class="label">Ketinggian (Cm)</div>
                        <div class="value" id="hilirLevel">--</div>
                    </div>
                    <div class="stat-card interactive-card">
                        <div class="label">Arus (L/min)</div>
                        <div class="value green" id="hilirFlow">--</div>
                    </div>
                    <div class="stat-card interactive-card">
                        <div class="label">Curah Hujan (mm/j)</div>
                        <div class="value" id="hilirRain">--</div>
                    </div>
                    <div class="stat-card interactive-card">
                        <div class="label">Angin (km/j)</div>
                        <div class="value" id="hilirWind">--</div>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 300px 1fr; gap: 16px;">
            <!-- Kiri: Webcam -->
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div class="interactive-card" style="margin: 0; flex: 1; position: relative; overflow: hidden; display: flex; flex-direction: column; padding: 0; border-radius: 12px; border: 1px solid rgba(229, 231, 235, 0.8);">
                    <div class="camera-header" style="position: absolute; top: 0; left: 0; right: 0; z-index: 10; background: rgba(0,0,0,0.6); padding: 8px 12px; border-radius: 8px 8px 0 0; color: white;">
                        <span>Webcam Hulu</span>
                        <span class="clockWidgetTime">--:--</span>
                    </div>
                    <div class="recording-dot" style="position: absolute; top: 12px; right: 12px; z-index: 10;"></div>
                    <div style="flex: 1; background: #000; display: flex; align-items: center; justify-content: center; min-height: 150px; border-radius: 8px;">
                        <img id="streamHulu" src="" alt="Live Feed Hulu" style="display: none; width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                        <div id="placeholderHulu" style="text-align: center; color: white;">
                            <i class="fas fa-video fa-2x" style="opacity: 0.5; margin-bottom: 8px;"></i>
                            <p style="font-size: 12px; margin: 0;">Menunggu stream...</p>
                        </div>
                    </div>
                </div>

                <div class="interactive-card" style="margin: 0; flex: 1; position: relative; overflow: hidden; display: flex; flex-direction: column; padding: 0; border-radius: 12px; border: 1px solid rgba(229, 231, 235, 0.8);">
                    <div class="camera-header" style="position: absolute; top: 0; left: 0; right: 0; z-index: 10; background: rgba(0,0,0,0.6); padding: 8px 12px; border-radius: 8px 8px 0 0; color: white;">
                        <span>Webcam Hilir</span>
                        <span class="clockWidgetTime">--:--</span>
                    </div>
                    <div class="recording-dot" style="position: absolute; top: 12px; right: 12px; z-index: 10;"></div>
                    <div style="flex: 1; background: #000; display: flex; align-items: center; justify-content: center; min-height: 150px; border-radius: 8px;">
                        <img id="streamHilir" src="" alt="Live Feed Hilir" style="display: none; width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                        <div id="placeholderHilir" style="text-align: center; color: white;">
                            <i class="fas fa-video fa-2x" style="opacity: 0.5; margin-bottom: 8px;"></i>
                            <p style="font-size: 12px; margin: 0;">Menunggu stream...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kanan: Prediksi Kenaikan Air Chart -->
            <div class="chart-card interactive-card" style="margin: 0; min-height: 200px;">
                <div class="section-title" style="margin-bottom: 0;">Prediksi Kenaikan Air (Hulu)</div>
                <div style="display: flex; gap: 16px; align-items: center;">
                <span id="lastUpdateText" style="font-size: 13px; color: var(--text-secondary); background: white; padding: 6px 12px; border-radius: 6px; border: 1px solid var(--border-color);"><i class="fas fa-clock"></i> Belum ada data</span>
                <div class="status-legend" style="margin: 0;">
                    <span><span style="display:inline-block; width:10px; height:10px; background:#22c55e; border:1px solid #16a34a;"></span> Aman (<100cm)</span>
                    <span><span style="display:inline-block; width:10px; height:10px; background:#eab308; border:1px solid #ca8a04;"></span> Siaga (100-150cm)</span>
                    <span><span style="display:inline-block; width:10px; height:10px; background:#ef4444; border:1px solid #dc2626;"></span> Waspada (>150cm)</span>
                </div>
            </div>
                <div style="position: relative; height: 150px; width: 100%;">
                    <canvas id="predictionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Side Column -->
    <div class="side-column">
        <!-- Lokasi Lain -->
        <div class="widget-card">
            <h3 class="section-title">Lokasi lain</h3>
            <div class="location-list">
                <div class="location-item interactive-card" onclick="window.location.href='/location/puri-cinere'">
                    <div class="location-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="location-info">
                        <h4>Puri Cinere Hijau</h4>
                        <p>Klik untuk melihat detail sensor</p>
                    </div>
                </div>
                <div class="location-item interactive-card" onclick="window.location.href='/location/permata-depok'">
                    <div class="location-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="location-info">
                        <h4>Permata Depok Sektor Nilam</h4>
                        <p>Klik untuk melihat detail sensor</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribusi Titik Node -->
        <div class="widget-card interactive-card">
            <h3 class="section-title">Distribusi Titik Node</h3>
            <div class="node-badges">
                <span class="node-badge hulu interactive-card" onclick="focusMap(-6.342, 106.738)">HULU</span>
                <span class="node-badge hilir interactive-card" onclick="focusMap(-6.350, 106.745)">HILIR</span>
            </div>
            <div id="map" class="map-container"></div>
        </div>



        <!-- Time Widget -->
        <div style="margin-top: auto; padding: 20px;">
            <p style="color: var(--text-secondary); font-size: 14px;">Hari ini</p>
            <div class="time-widget interactive-card" id="clockWidget">
                <span class="clockWidgetTime">--:--</span> <span style="font-size:16px;">GMT+7</span>
            </div>
        </div>
    </div>
</div>



<script type="module">
    // MENGGUNAKAN CLOUD FIRESTORE
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js";
    import { getFirestore, collection, query, orderBy, limit, onSnapshot, addDoc, serverTimestamp, doc, setDoc } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore.js";

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

    // Inisialisasi Chart.js
    const ctx = document.getElementById('predictionChart');
    let chart;
    
    if (ctx) {
        chart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'],
                datasets: [{
                    label: 'Tinggi Air Hulu (cm)',
                    data: [40, 50, 45, 60, 55, 63],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, max: 200, grid: { borderDash: [2, 4], color: '#e5e7eb' } },
                    x: { grid: { display: false } }
                },
                plugins: { legend: { display: false } }
            }
        });
    }

    // Ambil elemen DOM
    const huluLevel = document.getElementById('huluLevel');
    const hilirLevel = document.getElementById('hilirLevel');
    const huluFlow = document.getElementById('huluFlow');
    const hilirFlow = document.getElementById('hilirFlow');
    const huluStatus = document.getElementById('huluStatus');
    const hilirStatus = document.getElementById('hilirStatus');

    // Memory cache for device configs
    const configHulu = {
        threshold_siaga: 100,
        threshold_waspada: 150,
    };
    const configHilir = {
        threshold_siaga: 100,
        threshold_waspada: 150,
    };

    let latestHuluLog = null;
    let latestHilirLog = null;

    // Listen for configuration updates
    try {
        const configColRef = collection(db, 'monitoring', 'depok', 'device_config');
        onSnapshot(configColRef, (snapshot) => {
            snapshot.forEach((doc) => {
                const data = doc.data();
                if (doc.id === 'hulu') {
                    configHulu = { ...configHulu, ...data };
                } else if (doc.id === 'hilir') {
                    configHilir = { ...configHilir, ...data };
                }
            });
            // Update UI immediately when configuration changes
            refreshUI();
        });
    } catch(err) {
        console.error("Config fetch error on homepage: ", err);
    }

    // Refresh UI function
    function refreshUI() {
        const now = new Date();
        const timeoutMs = 5 * 60 * 1000; // 5 menit

        let isHuluOnline = false;
        if (latestHuluLog) {
            isHuluOnline = true;
            let logTimeHulu = new Date();
            if (latestHuluLog.time) {
                if (latestHuluLog.time.toDate) {
                    logTimeHulu = latestHuluLog.time.toDate();
                } else if (typeof latestHuluLog.time === 'string') {
                    logTimeHulu = new Date(latestHuluLog.time.replace(' ', 'T'));
                }
            }
            if (now - logTimeHulu > timeoutMs) isHuluOnline = false;
        }

        if (huluLevel) {
            huluLevel.innerText = (latestHuluLog && latestHuluLog.water_level !== undefined) ? latestHuluLog.water_level + ' cm' : '--';
            huluLevel.style.color = isHuluOnline ? '' : '#ef4444';
        }
        if (huluFlow) {
            huluFlow.innerText = (latestHuluLog && latestHuluLog.water_flow !== undefined) ? latestHuluLog.water_flow + ' L/min' : '--';
            huluFlow.style.color = isHuluOnline ? '' : '#ef4444';
        }
        
        const huluRain = document.getElementById('huluRain');
        if (huluRain) {
            huluRain.innerText = (latestHuluLog && latestHuluLog.ombrometer !== undefined) ? latestHuluLog.ombrometer : '--';
            huluRain.style.color = isHuluOnline ? '' : '#ef4444';
        }
        
        const huluWind = document.getElementById('huluWind');
        if (huluWind) {
            huluWind.innerText = (latestHuluLog && latestHuluLog.anemometer !== undefined) ? latestHuluLog.anemometer : '--';
            huluWind.style.color = isHuluOnline ? '' : '#ef4444';
        }

        const huluOnlineBadge = document.getElementById('huluOnlineBadge');
        if (huluOnlineBadge) {
            huluOnlineBadge.innerHTML = isHuluOnline ? '🟢 ONLINE' : '🔴 OFFLINE';
            huluOnlineBadge.style.backgroundColor = isHuluOnline ? 'rgba(255, 255, 255, 0.2)' : 'var(--danger-color)';
        }

        if (huluStatus) {
            if (!isHuluOnline || !latestHuluLog) {
                huluStatus.innerText = !latestHuluLog ? "NO DATA" : "OFFLINE";
                huluStatus.style.backgroundColor = "#94a3b8"; // Grey
            } else {
                const lvl = latestHuluLog.water_level !== undefined ? latestHuluLog.water_level : 0;
                if (lvl >= configHulu.threshold_waspada) {
                    huluStatus.innerText = "WASPADA";
                    huluStatus.style.backgroundColor = "var(--danger-color)";
                } else if (lvl >= configHulu.threshold_siaga) {
                    huluStatus.innerText = "SIAGA";
                    huluStatus.style.backgroundColor = "#eab308";
                } else {
                    huluStatus.innerText = "AMAN";
                    huluStatus.style.backgroundColor = "var(--success-color)";
                }
            }
        }

        const streamHulu = document.getElementById('streamHulu');
        const placeholderHulu = document.getElementById('placeholderHulu');
        if (streamHulu && placeholderHulu) {
            if (isHuluOnline && latestHuluLog && latestHuluLog.espcam_img_url) {
                let camUrl = latestHuluLog.espcam_img_url;
                if (streamHulu.src !== camUrl) streamHulu.src = camUrl;
                streamHulu.style.display = 'block';
                placeholderHulu.style.display = 'none';
            } else {
                streamHulu.src = "";
                streamHulu.style.display = 'none';
                placeholderHulu.style.display = 'block';
            }
        }


        // Update Hilir Card UI
        let isHilirOnline = false;
        if (latestHilirLog) {
            isHilirOnline = true;
            let logTimeHilir = new Date();
            if (latestHilirLog.time) {
                if (latestHilirLog.time.toDate) {
                    logTimeHilir = latestHilirLog.time.toDate();
                } else if (typeof latestHilirLog.time === 'string') {
                    logTimeHilir = new Date(latestHilirLog.time.replace(' ', 'T'));
                }
            }
            if (now - logTimeHilir > timeoutMs) isHilirOnline = false;
        }
        
        const hilirLevel = document.getElementById('hilirLevel');
        const hilirFlow = document.getElementById('hilirFlow');
        const hilirRain = document.getElementById('hilirRain');
        const hilirWind = document.getElementById('hilirWind');
        
        if (hilirLevel) {
            hilirLevel.innerText = (latestHilirLog && latestHilirLog.water_level !== undefined) ? latestHilirLog.water_level + ' cm' : '--';
            hilirLevel.style.color = isHilirOnline ? '' : '#ef4444';
        }
        if (hilirFlow) {
            hilirFlow.innerText = (latestHilirLog && latestHilirLog.water_flow !== undefined) ? latestHilirLog.water_flow + ' L/min' : '--';
            hilirFlow.style.color = isHilirOnline ? '' : '#ef4444';
        }
        if (hilirRain) {
            hilirRain.innerText = (latestHilirLog && latestHilirLog.ombrometer !== undefined) ? latestHilirLog.ombrometer : '--';
            hilirRain.style.color = isHilirOnline ? '' : '#ef4444';
        }
        if (hilirWind) {
            hilirWind.innerText = (latestHilirLog && latestHilirLog.anemometer !== undefined) ? latestHilirLog.anemometer : '--';
            hilirWind.style.color = isHilirOnline ? '' : '#ef4444';
        }

        const hilirOnlineBadge = document.getElementById('hilirOnlineBadge');
        if (hilirOnlineBadge) {
            hilirOnlineBadge.innerHTML = isHilirOnline ? '🟢 ONLINE' : '🔴 OFFLINE';
            hilirOnlineBadge.style.backgroundColor = isHilirOnline ? 'rgba(255, 255, 255, 0.2)' : 'var(--danger-color)';
        }

        if (hilirStatus) {
            if (!isHilirOnline || !latestHilirLog) {
                hilirStatus.innerText = !latestHilirLog ? "NO DATA" : "OFFLINE";
                hilirStatus.style.backgroundColor = "#94a3b8"; // Grey
            } else {
                const lvl = latestHilirLog.water_level !== undefined ? latestHilirLog.water_level : 0;
                if (lvl >= configHilir.threshold_waspada) {
                    hilirStatus.innerText = "WASPADA";
                    hilirStatus.style.backgroundColor = "var(--danger-color)";
                } else if (lvl >= configHilir.threshold_siaga) {
                    hilirStatus.innerText = "SIAGA";
                    hilirStatus.style.backgroundColor = "#eab308";
                } else {
                    hilirStatus.innerText = "AMAN";
                    hilirStatus.style.backgroundColor = "var(--success-color)";
                }
            }
        }

        const streamHilir = document.getElementById('streamHilir');
        const placeholderHilir = document.getElementById('placeholderHilir');
        if (streamHilir && placeholderHilir) {
            if (isHilirOnline && latestHilirLog && latestHilirLog.espcam_img_url) {
                let camUrl = latestHilirLog.espcam_img_url;
                if (streamHilir.src !== camUrl) streamHilir.src = camUrl;
                streamHilir.style.display = 'block';
                placeholderHilir.style.display = 'none';
            } else {
                streamHilir.src = "";
                streamHilir.style.display = 'none';
                placeholderHilir.style.display = 'block';
            }
        }

        // Update Last Update Text
        const lastUpdateEl = document.getElementById('lastUpdateText');
        if (lastUpdateEl) {
            const now = new Date();
            lastUpdateEl.innerHTML = `<i class="fas fa-clock"></i> Terakhir Diperbarui: ${now.toLocaleTimeString('id-ID')}`;
        }

        // Prediction Chart Update
        if (chart && latestHuluLog && latestHuluLog.water_level !== undefined) {
            try {
                let dateObj = new Date();
                if (latestHuluLog.time) {
                    if (latestHuluLog.time.toDate) {
                        dateObj = latestHuluLog.time.toDate();
                    } else if (typeof latestHuluLog.time === 'string') {
                        dateObj = new Date(latestHuluLog.time.replace(' ', 'T'));
                    }
                }
                const timeString = `${String(dateObj.getHours()).padStart(2, '0')}:${String(dateObj.getMinutes()).padStart(2, '0')}`;
                
                const lastLabel = chart.data.labels[chart.data.labels.length - 1];
                if (lastLabel !== timeString) {
                    chart.data.labels.push(timeString);
                    chart.data.datasets[0].data.push(latestHuluLog.water_level);
                    
                    if (chart.data.labels.length > 15) {
                        chart.data.labels.shift();
                        chart.data.datasets[0].data.shift();
                    }
                    chart.update('none');
                }
            } catch (err) {
                console.error("Chart update error", err);
            }
        }
    }

    // FIRESTORE LOGS LISTENER
    try {
        const logDataRef = collection(db, 'monitoring', 'depok', 'log_data');
        const q = query(logDataRef, orderBy('time', 'desc'), limit(50));

        onSnapshot(q, (snapshot) => {
            if (snapshot.empty) {
                latestHuluLog = null;
                latestHilirLog = null;
                refreshUI();
                return;
            }

            let foundHulu = false;
            let foundHilir = false;

            snapshot.forEach((doc) => {
                const data = doc.data();

                // HIDE DUMMY DATA DIRECTLY FROM WEB (As requested)
                if (data.time && typeof data.time === 'string') {
                    if (data.time.startsWith('2026-06-09 07:') || data.time.startsWith('2026-06-09 08:') || data.time.startsWith('2026-06-09 09:')) {
                        return; // Skip this dummy data
                    }
                }
                
                if (data.penempatan === 'hulu' && !foundHulu) {
                    foundHulu = true;
                    latestHuluLog = data;
                }
                
                if (data.penempatan === 'hilir' && !foundHilir) {
                    foundHilir = true;
                    latestHilirLog = data;
                }
            });

            // Update UI with newly received logs
            refreshUI();

        }, (error) => {
            if (huluStatus) huluStatus.innerText = "ERROR DB";
            if (hilirStatus) hilirStatus.innerText = "ERROR DB";
            console.error("Firestore Error: " + error.message);
        });




    } catch (e) {
        if (huluStatus) huluStatus.innerText = "JS CRASH";
        console.error("System Error: " + e.message);
    }
</script>

<script>
    // Leaflet Map Initialization
    let leafletMap;
    try {
        leafletMap = L.map('map').setView([-6.342, 106.738], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
            attribution: '© OpenStreetMap' 
        }).addTo(leafletMap);

        const huluMarker = L.circleMarker([-6.342, 106.738], { color: '#22c55e', radius: 8, fillOpacity: 0.8 }).addTo(leafletMap).bindPopup('<b>Node Hulu</b>');
        const hilirMarker = L.circleMarker([-6.350, 106.745], { color: '#ef4444', radius: 8, fillOpacity: 0.8 }).addTo(leafletMap).bindPopup('<b>Node Hilir</b>');
        
        setInterval(() => {
            huluMarker.setRadius(huluMarker.options.radius === 8 ? 11 : 8);
            hilirMarker.setRadius(hilirMarker.options.radius === 8 ? 11 : 8);
        }, 1000);
    } catch (e) {
        console.error("Gagal memuat Map:", e);
    }

    // Interactive functions
    function focusMap(lat, lng) {
        if(leafletMap) {
            leafletMap.flyTo([lat, lng], 16, { duration: 1.5 });
        }
    }

    function openCameraModal(node) {
        document.getElementById('cameraTitle').innerText = `Live Feed Webcam ${node.toUpperCase()}`;
        
        const streamImg = document.getElementById('cameraStream');
        const placeholder = document.getElementById('cameraPlaceholder');
        const statusText = document.getElementById('cameraStatusText');
        
        // Ambil URL kamera dari log terbaru Firebase
        const logData = node === 'hulu' ? latestHuluLog : latestHilirLog;
        const camUrl = logData && logData.espcam_img_url ? logData.espcam_img_url : "";
        
        if (camUrl) {
            streamImg.src = camUrl;
            streamImg.style.display = 'block';
            placeholder.style.display = 'none';
        } else {
            streamImg.src = "";
            streamImg.style.display = 'none';
            placeholder.style.display = 'block';
            statusText.innerText = "Kamera offline atau Stream URL tidak tersedia dari Node.";
        }
        
        document.getElementById('cameraModal').classList.add('active');
    }
    function closeCameraModal() {
        document.getElementById('cameraModal').classList.remove('active');
        document.getElementById('cameraStream').src = ""; // Stop stream pulling when closed
    }

    // Clock
    function updateTime() {
        const now = new Date();
        const timeStr = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
        document.querySelectorAll('.clockWidgetTime').forEach(el => el.innerText = timeStr);
    }
    setInterval(updateTime, 1000);
    updateTime();
</script>
@endsection