@extends('layouts.app')

@section('title', 'BPI PAMULANG')

@section('content')
<style>
    /* Medium Compact Layout */
    .dashboard-grid {
        gap: 16px;
    }

    .main-column {
        gap: 12px !important;
    }

    .side-column {
        gap: 16px !important;
    }

    /* Padding Kotak yang Proporsional */
    .dashboard-grid .stat-card {
        padding: 12px 16px;
    }

    .dashboard-grid .status-card {
        padding: 16px 20px;
    }

    .widget-card {
        padding: 16px 20px;
        margin-bottom: 0;
    }

    .camera-card {
        padding: 12px 16px !important;
        margin: 0 !important;
    }

    /* Ukuran Font Menengah */
    .stat-card .value {
        font-size: 28px !important;
    }

    .stat-card .label {
        font-size: 12px;
        margin-bottom: 6px;
    }

    .section-title {
        font-size: 16px;
        margin-bottom: 12px !important;
        margin-top: 8px !important;
    }

    .status-card h2 {
        font-size: 18px;
        margin-bottom: 4px;
    }

    .status-card p {
        font-size: 13px;
        margin-bottom: 0;
    }

    /* Tinggi Peta dan Grafik Menengah */
    .map-container {
        height: 180px !important;
        border-radius: 8px;
    }

    .chart-card {
        min-height: 200px !important;
        padding: 16px !important;
    }

    .dashboard-grid .stats-grid {
        gap: 12px !important;
        margin-bottom: 8px !important;
    }
</style>

<div class="dashboard-grid">
    <!-- Main Column -->
    <div class="main-column">

        <!-- Status Cards -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 8px;">
            <div class="status-card interactive-card">
                <div>
                    <h2>Setu Pamulang</h2>
                    <p>Hulu</p>
                    <div class="online-badge" id="huluOnlineBadge">🟡 MEMUAT</div>
                </div>
                <div class="status-badge" id="huluStatus">MENUNGGU</div>
            </div>

            <div class="status-card interactive-card">
                <div>
                    <h2>BPI PAMULANG</h2>
                    <p>Hilir</p>
                    <div class="online-badge" id="hilirOnlineBadge">🟡 MEMUAT</div>
                </div>
                <div class="status-badge" id="hilirStatus">MENUNGGU</div>
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
                    <span id="lastUpdateText" style="font-size: 13px; color: var(--text-secondary); background: white; padding: 6px 12px; border-radius: 6px; border: 1px solid var(--border-color);">
                        <i class="fas fa-clock"></i> Belum ada data
                    </span>

                    <div class="status-legend" style="margin: 0;">
                        <span><span style="display:inline-block; width:10px; height:10px; background:#22c55e; border:1px solid #16a34a;"></span> Aman </span>
                        <span><span style="display:inline-block; width:10px; height:10px; background:#eab308; border:1px solid #ca8a04;"></span> Siaga </span>
                        <span><span style="display:inline-block; width:10px; height:10px; background:#ef4444; border:1px solid #dc2626;"></span> Waspada </span>
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
                <span class="clockWidgetTime">--:--</span>
                <span style="font-size:16px;">GMT+7</span>
            </div>
        </div>
    </div>
</div>

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
        limit,
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

    const huluLevel = document.getElementById("huluLevel");
    const hilirLevel = document.getElementById("hilirLevel");
    const huluFlow = document.getElementById("huluFlow");
    const hilirFlow = document.getElementById("hilirFlow");
    const huluStatus = document.getElementById("huluStatus");
    const hilirStatus = document.getElementById("hilirStatus");

    let latestHuluLog = null;
    let latestHilirLog = null;
    let unsubscribeConfig = null;
    let unsubscribeLogs = null;

    let configHulu = {
        threshold_siaga: 100,
        threshold_waspada: 150,
    };

    let configHilir = {
        threshold_siaga: 100,
        threshold_waspada: 150,
    };

    const ctx = document.getElementById("predictionChart");
    let chart = null;

    if (ctx) {
        chart = new Chart(ctx.getContext("2d"), {
            type: "line",
            data: {
                labels: ["00:00", "04:00", "08:00", "12:00", "16:00", "20:00"],
                datasets: [{
                    label: "Tinggi Air Hulu (cm)",
                    data: [40, 50, 45, 60, 55, 63],
                    borderColor: "#2563eb",
                    backgroundColor: "rgba(37, 99, 235, 0.1)",
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: "#2563eb",
                    pointBorderColor: "#fff",
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 200,
                        grid: {
                            borderDash: [2, 4],
                            color: "#e5e7eb"
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    function parseLogTime(value) {
        try {
            if (value && typeof value.toDate === "function") {
                return value.toDate();
            }

            if (typeof value === "string") {
                return new Date(value.replace(" ", "T"));
            }

            return null;
        } catch (error) {
            return null;
        }
    }

    function isLogOnline(logData) {
        if (!logData || !logData.time) {
            return false;
        }

        const logTime = parseLogTime(logData.time);

        if (!logTime || Number.isNaN(logTime.getTime())) {
            return false;
        }

        const now = new Date();
        const timeoutMs = 5 * 60 * 1000;

        return (now - logTime) <= timeoutMs;
    }

    function normalizeWaterStatus(status) {
        if (!status) {
            return "NO DATA";
        }

        const value = String(status).trim().toLowerCase();

        if (value === "aman") {
            return "AMAN";
        }

        if (value === "siaga") {
            return "SIAGA";
        }

        if (value === "waspada") {
            return "WASPADA";
        }

        if (value === "darurat") {
            return "DARURAT";
        }

        return String(status).trim().toUpperCase();
    }

    function getWaterStatusColor(status) {
        const value = normalizeWaterStatus(status);

        if (value === "AMAN") {
            return "var(--success-color)";
        }

        if (value === "SIAGA") {
            return "#eab308";
        }

        if (value === "WASPADA") {
            return "#f97316";
        }

        if (value === "DARURAT") {
            return "var(--danger-color)";
        }

        return "#94a3b8";
    }

    function updateStatusBadge(element, isOnline, logData, config) {
        if (!element) {
            return;
        }

        if (!logData) {
            element.innerText = "NO DATA";
            element.style.backgroundColor = "#94a3b8";
            return;
        }

        if (!isOnline) {
            element.innerText = "OFFLINE";
            element.style.backgroundColor = "#94a3b8";
            return;
        }

        const waterStatus = normalizeWaterStatus(logData.water_status);

        element.innerText = waterStatus;
        element.style.backgroundColor = getWaterStatusColor(waterStatus);
    }

    function updateOnlineBadge(elementId, isOnline) {
        const badge = document.getElementById(elementId);

        if (!badge) {
            return;
        }

        badge.innerText = isOnline ? "🟢 ONLINE" : "🔴 OFFLINE";
        badge.style.backgroundColor = isOnline ? "rgba(255, 255, 255, 0.2)" : "var(--danger-color)";
    }

    function setValue(element, value, unit, isOnline) {
        if (!element) {
            return;
        }

        element.innerText = value !== undefined && value !== null ? `${value}${unit}` : "--";
        element.style.color = isOnline ? "" : "#ef4444";
    }

    function updateCamera(node, isOnline, logData) {
        const stream = document.getElementById(node === "hulu" ? "streamHulu" : "streamHilir");
        const placeholder = document.getElementById(node === "hulu" ? "placeholderHulu" : "placeholderHilir");

        if (!stream || !placeholder) {
            return;
        }

        const cameraUrl = logData && logData.espcam_img_url ? logData.espcam_img_url : "";

        if (isOnline && cameraUrl) {
            if (stream.src !== cameraUrl) {
                stream.src = cameraUrl;
            }

            stream.style.display = "block";
            placeholder.style.display = "none";
        } else {
            stream.src = "";
            stream.style.display = "none";
            placeholder.style.display = "block";
        }
    }

    function updateLastUpdateText() {
        const lastUpdateEl = document.getElementById("lastUpdateText");

        if (!lastUpdateEl) {
            return;
        }

        const now = new Date();
        lastUpdateEl.innerHTML = `<i class="fas fa-clock"></i> Terakhir Diperbarui: ${now.toLocaleTimeString("id-ID")}`;
    }

    function updatePredictionChart() {
        if (!chart || !latestHuluLog || latestHuluLog.water_level === undefined) {
            return;
        }

        const dateObj = parseLogTime(latestHuluLog.time) || new Date();
        const timeString = `${String(dateObj.getHours()).padStart(2, "0")}:${String(dateObj.getMinutes()).padStart(2, "0")}`;

        const lastLabel = chart.data.labels[chart.data.labels.length - 1];

        if (lastLabel !== timeString) {
            chart.data.labels.push(timeString);
            chart.data.datasets[0].data.push(Number(latestHuluLog.water_level));

            if (chart.data.labels.length > 15) {
                chart.data.labels.shift();
                chart.data.datasets[0].data.shift();
            }

            chart.update("none");
        }
    }

    function refreshUI() {
        const isHuluOnline = isLogOnline(latestHuluLog);
        const isHilirOnline = isLogOnline(latestHilirLog);

        setValue(huluLevel, latestHuluLog?.water_level, " cm", isHuluOnline);
        setValue(huluFlow, latestHuluLog?.water_flow, " L/min", isHuluOnline);
        setValue(document.getElementById("huluRain"), latestHuluLog?.ombrometer, "", isHuluOnline);
        setValue(document.getElementById("huluWind"), latestHuluLog?.anemometer, "", isHuluOnline);

        setValue(hilirLevel, latestHilirLog?.water_level, " cm", isHilirOnline);
        setValue(hilirFlow, latestHilirLog?.water_flow, " L/min", isHilirOnline);
        setValue(document.getElementById("hilirRain"), latestHilirLog?.ombrometer, "", isHilirOnline);
        setValue(document.getElementById("hilirWind"), latestHilirLog?.anemometer, "", isHilirOnline);

        updateOnlineBadge("huluOnlineBadge", isHuluOnline);
        updateOnlineBadge("hilirOnlineBadge", isHilirOnline);

        updateStatusBadge(huluStatus, isHuluOnline, latestHuluLog, configHulu);
        updateStatusBadge(hilirStatus, isHilirOnline, latestHilirLog, configHilir);

        updateCamera("hulu", isHuluOnline, latestHuluLog);
        updateCamera("hilir", isHilirOnline, latestHilirLog);

        updateLastUpdateText();
        updatePredictionChart();
    }

    function shouldSkipDummyData(data) {
        if (!data.time || typeof data.time !== "string") {
            return false;
        }

        return (
            data.time.startsWith("2026-06-09 07:") ||
            data.time.startsWith("2026-06-09 08:") ||
            data.time.startsWith("2026-06-09 09:")
        );
    }

    function startFirestoreListeners() {
        try {
            const configColRef = collection(db, "monitoring", "depok", "device_config");

            unsubscribeConfig = onSnapshot(configColRef, (snapshot) => {
                snapshot.forEach((docSnap) => {
                    const data = docSnap.data();

                    if (docSnap.id === "hulu") {
                        configHulu = {
                            ...configHulu,
                            ...data
                        };
                    } else if (docSnap.id === "hilir") {
                        configHilir = {
                            ...configHilir,
                            ...data
                        };
                    }
                });

                refreshUI();
            }, (error) => {
                console.error("Config listener error:", error);
            });
        } catch (error) {
            console.error("Config setup error:", error);
        }

        try {
            const logDataRef = collection(db, "monitoring", "depok", "log_data");
            const q = query(logDataRef, orderBy("time", "desc"), limit(50));

            unsubscribeLogs = onSnapshot(q, (snapshot) => {
                if (snapshot.empty) {
                    latestHuluLog = null;
                    latestHilirLog = null;
                    refreshUI();
                    return;
                }

                let foundHulu = false;
                let foundHilir = false;

                snapshot.forEach((docSnap) => {
                    const data = docSnap.data();

                    if (shouldSkipDummyData(data)) {
                        return;
                    }

                    if (data.penempatan === "hulu" && !foundHulu) {
                        foundHulu = true;
                        latestHuluLog = data;
                    }

                    if (data.penempatan === "hilir" && !foundHilir) {
                        foundHilir = true;
                        latestHilirLog = data;
                    }
                });

                refreshUI();
            }, (error) => {
                console.error("Firestore log listener error:", error);

                if (huluStatus) {
                    huluStatus.innerText = "ERROR DB";
                    huluStatus.style.backgroundColor = "#94a3b8";
                }

                if (hilirStatus) {
                    hilirStatus.innerText = "ERROR DB";
                    hilirStatus.style.backgroundColor = "#94a3b8";
                }
            });
        } catch (error) {
            console.error("Log setup error:", error);

            if (huluStatus) {
                huluStatus.innerText = "JS ERROR";
            }

            if (hilirStatus) {
                hilirStatus.innerText = "JS ERROR";
            }
        }
    }

    onAuthStateChanged(auth, (user) => {
        if (!user) {
            window.location.href = @json(route('login'));
            return;
        }

        startFirestoreListeners();
    });

    window.addEventListener("beforeunload", () => {
        if (typeof unsubscribeConfig === "function") {
            unsubscribeConfig();
        }

        if (typeof unsubscribeLogs === "function") {
            unsubscribeLogs();
        }
    });
</script>

<script>
    let leafletMap;

    try {
        leafletMap = L.map("map").setView([-6.342, 106.738], 13);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "© OpenStreetMap"
        }).addTo(leafletMap);

        const huluMarker = L.circleMarker([-6.342, 106.738], {
            color: "#22c55e",
            radius: 8,
            fillOpacity: 0.8
        }).addTo(leafletMap).bindPopup("<b>Node Hulu</b>");

        const hilirMarker = L.circleMarker([-6.350, 106.745], {
            color: "#ef4444",
            radius: 8,
            fillOpacity: 0.8
        }).addTo(leafletMap).bindPopup("<b>Node Hilir</b>");

        setInterval(() => {
            huluMarker.setRadius(huluMarker.options.radius === 8 ? 11 : 8);
            hilirMarker.setRadius(hilirMarker.options.radius === 8 ? 11 : 8);
        }, 1000);
    } catch (error) {
        console.error("Gagal memuat Map:", error);
    }

    function focusMap(lat, lng) {
        if (leafletMap) {
            leafletMap.flyTo([lat, lng], 16, {
                duration: 1.5
            });
        }
    }

    function updateTime() {
        const now = new Date();
        const timeStr = `${String(now.getHours()).padStart(2, "0")}:${String(now.getMinutes()).padStart(2, "0")}`;

        document.querySelectorAll(".clockWidgetTime").forEach(el => {
            el.innerText = timeStr;
        });
    }

    setInterval(updateTime, 1000);
    updateTime();
</script>
@endsection