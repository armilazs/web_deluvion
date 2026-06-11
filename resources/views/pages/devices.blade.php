@extends('layouts.app')

@section('title', 'Kelola Sensor & Perangkat')

@section('content')
<style>
    /* Styling Visual Premium - Admin Panel */
    .admin-grid {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 24px;
        align-items: start;
        margin-top: 16px;
    }

    @media (max-width: 992px) {
        .admin-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Node Cards */
    .admin-node-card {
        background: var(--card-bg, #ffffff);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 14px;
        padding: 20px;
        cursor: pointer;
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .admin-node-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 6px;
        height: 100%;
        border-top-left-radius: 14px;
        border-bottom-left-radius: 14px;
        background: #9ca3af;
        transition: background 0.3s;
    }

    .admin-node-card.hulu::before {
        background: var(--success-color, #22c55e);
    }

    .admin-node-card.hilir::before {
        background: var(--danger-color, #ef4444);
    }

    .admin-node-card.active {
        border-color: var(--primary-blue, #2563eb);
        box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.1), 0 4px 6px -2px rgba(37, 99, 235, 0.05);
    }

    .admin-node-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    }

    .node-header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .node-title-area h3 {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 2px 0;
    }

    .node-title-area span.badge {
        font-size: 11px;
        font-weight: 600;
        background: #f3f4f6;
        color: #4b5563;
        padding: 2px 8px;
        border-radius: 4px;
        font-family: monospace;
    }

    /* Status Mini Tags */
    .mini-status-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 6px;
        padding-top: 10px;
        border-top: 1px dashed var(--border-color, #e5e7eb);
    }

    .sensor-mini-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 6px;
        background: #f3f4f6;
        color: #4b5563;
    }

    .sensor-mini-tag.online {
        background: #dcfce7;
        color: #15803d;
    }

    .sensor-mini-tag.offline {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* Detail Card Panel */
    .control-panel-card {
        background: #ffffff;
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        min-height: 480px;
        display: flex;
        flex-direction: column;
        transition: all 0.3s;
    }

    .blank-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex: 1;
        color: var(--text-secondary);
        text-align: center;
        padding: 40px;
    }

    .blank-state i {
        font-size: 48px;
        color: #cbd5e1;
        margin-bottom: 16px;
    }

    .panel-header {
        border-bottom: 1px solid var(--border-color, #e5e7eb);
        padding-bottom: 16px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .panel-header h2 {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    /* Sensor Grid Table */
    .sensor-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 24px;
    }

    .sensor-table th {
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-secondary);
        padding: 10px 12px;
        border-bottom: 2px solid #f1f5f9;
    }

    .sensor-table td {
        padding: 14px 12px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        vertical-align: middle;
    }

    .sensor-name-cell {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .sensor-name-cell i {
        color: var(--primary-blue, #2563eb);
        font-size: 16px;
        width: 20px;
        text-align: center;
    }

    /* Live Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 12px;
        text-transform: uppercase;
    }

    .status-badge.online {
        background: #dcfce7;
        color: #15803d;
    }

    .status-badge.offline {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* Control Sections */
    .panel-section-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .panel-card-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 24px;
    }

    @media (max-width: 576px) {
        .panel-card-row {
            grid-template-columns: 1fr;
        }
    }

    .panel-sub-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
    }

    .sim-input-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
        gap: 10px;
        margin-bottom: 12px;
    }

    .sim-field {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .sim-field label {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-secondary);
    }

    .sim-field input {
        padding: 6px 10px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 12px;
        outline: none;
    }

    .sim-field input:disabled {
        background: #e2e8f0;
        color: #94a3b8;
        cursor: not-allowed;
    }

    .admin-action-btn {
        background: var(--primary-blue, #2563eb);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: background 0.2s;
        width: 100%;
    }

    .admin-action-btn:hover {
        background: #1d4ed8;
    }

    .admin-action-btn:disabled {
        background: #94a3b8;
        cursor: not-allowed;
    }
</style>

<div class="devices-container">
    <div style="margin-bottom: 20px;">
        <h2 class="section-title" style="margin: 0; font-size: 22px; color: var(--text-primary);">
            Pusat Manajemen Sensor & Perangkat
        </h2>
    </div>

    <div class="admin-grid">
        <!-- LEFT: Perangkat List -->
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <div class="panel-section-title">
                <i class="fas fa-microchip"></i> Daftar Node Terhubung
            </div>

            <!-- Hulu Card -->
            <div class="admin-node-card hulu" id="card-hulu" onclick="selectNode('hulu')">
                <div class="node-header-row">
                    <div class="node-title-area">
                        <span class="badge">DEL-001</span>
                        <h3 id="huluNodeTitle" style="margin-top: 6px;">Node Hulu</h3>
                        <p id="huluNodeLoc" style="margin: 0; font-size: 12px; color: var(--text-secondary);">
                            Setu Pamulang
                        </p>
                    </div>
                    <div class="status-badge online" id="huluOverallBadge">ONLINE</div>
                </div>

                <div class="mini-status-list">
                    <span class="sensor-mini-tag online" id="huluMiniLvl">
                        <i class="fas fa-water"></i> Lvl: Online
                    </span>
                    <span class="sensor-mini-tag online" id="huluMiniFlw">
                        <i class="fas fa-fan"></i> Flow: Online
                    </span>
                </div>
            </div>

            <!-- Hilir Card -->
            <div class="admin-node-card hilir" id="card-hilir" onclick="selectNode('hilir')">
                <div class="node-header-row">
                    <div class="node-title-area">
                        <span class="badge">DEL-002</span>
                        <h3 id="hilirNodeTitle" style="margin-top: 6px;">Node Hilir</h3>
                        <p id="hilirNodeLoc" style="margin: 0; font-size: 12px; color: var(--text-secondary);">
                            BPI Pamulang
                        </p>
                    </div>
                    <div class="status-badge offline" id="hilirOverallBadge">OFFLINE</div>
                </div>

                <div class="mini-status-list">
                    <span class="sensor-mini-tag offline" id="hilirMiniLvl">
                        <i class="fas fa-times-circle"></i> Lvl: Off
                    </span>
                    <span class="sensor-mini-tag offline" id="hilirMiniRain">
                        <i class="fas fa-times-circle"></i> Rain: Off
                    </span>
                    <span class="sensor-mini-tag offline" id="hilirMiniWind">
                        <i class="fas fa-times-circle"></i> Wind: Off
                    </span>
                    <span class="sensor-mini-tag offline" id="hilirMiniSir">
                        <i class="fas fa-times-circle"></i> Siren: Off
                    </span>
                </div>
            </div>
        </div>

        <!-- RIGHT: Detail Control Panel -->
        <div class="control-panel-card" id="controlPanel">
            <!-- Blank state placeholder -->
            <div class="blank-state" id="panelBlankState">
                <i class="fas fa-sliders-h"></i>
                <h3>Panel Kontrol Belum Terpilih</h3>
                <p>
                    Klik salah satu kartu node di sebelah kiri untuk mulai melihat rincian sensor,
                    mengubah status online/offline, dan melakukan simulasi pengiriman data.
                </p>
            </div>

            <!-- Active control interface -->
            <div id="panelActiveState" style="display: none; flex-direction: column; flex: 1;">
                <!-- Panel Header -->
                <div class="panel-header">
                    <div>
                        <span class="badge" id="panelNodeId"
                            style="font-family: monospace; font-size: 11px; background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 4px; font-weight: 700;">
                            DEL-000
                        </span>
                        <h2 id="panelNodeTitle" style="margin-top: 6px;">Detail Perangkat</h2>
                    </div>
                    <div class="status-badge online" id="panelOverallBadge">ONLINE</div>
                </div>

                <!-- Sensor Table -->
                <div class="panel-section-title">
                    <i class="fas fa-tasks"></i> Status & Detail Sensor Individu
                </div>

                <table class="sensor-table">
                    <thead>
                        <tr>
                            <th>Sensor</th>
                            <th>Nilai Real-time</th>
                            <th>Status Alat</th>
                        </tr>
                    </thead>
                    <tbody id="sensorTableBody">
                        <!-- Dynamic sensor rows -->
                    </tbody>
                </table>

                <!-- Kontrol Aktuator Khusus -->
                <div id="actuatorControlSection"
                    style="display: none; margin-top: 16px; border-top: 1px dashed var(--border-color); padding-top: 24px;">
                    <div class="panel-section-title">
                        <i class="fas fa-volume-up"></i> Kontrol Aktuator (Sirine Peringatan)
                    </div>

                    <div
                        style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 16px 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                        <div>
                            <h4 style="margin: 0 0 6px 0; font-size: 15px; color: var(--text-primary);">
                                Sirine Manual Darurat
                            </h4>
                            <p style="margin: 0; font-size: 12px; color: var(--text-secondary);">
                                Aktifkan peringatan sirine secara paksa mengabaikan status sensor ketinggian air.
                            </p>
                        </div>

                        <button id="sireneToggleButtonDevice"
                            style="padding: 12px 28px; border-radius: 8px; border: 2px solid #cbd5e1; background: #94a3b8; color: white; font-weight: bold; font-size: 14px; cursor: pointer; transition: all 0.2s; min-width: 140px; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">
                            MATI
                        </button>
                    </div>
                </div>
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
        onSnapshot,
        serverTimestamp,
        doc,
        setDoc
    } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore.js";

    // Firebase Credentials
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
    const auth = getAuth(app);

    let currentFirebaseUser = null;

    // Memory caches for configs and logs
    window.selectedNodeId = null;

    let configHulu = {
        nama_node: "Node Hulu",
        lokasi: "Setu Pamulang",
        threshold_siaga: 100,
        threshold_waspada: 150,
        status_water_level: true,
        status_water_flow: true
    };

    let configHilir = {
        nama_node: "BPI PAMULANG Hilir",
        lokasi: "Titik Hilir Saluran Air",
        threshold_siaga: 100,
        threshold_kritis: 150,
        status_water_level: false,
        status_ombrometer: false,
        status_anemometer: false,
        status_sirine: false
    };

    let cachedLogsHulu = {
        water_level: 60,
        water_flow: 12
    };

    let cachedLogsHilir = {
        water_level: 45,
        ombrometer: 15,
        anemometer: 8,
        sirine_status: false
    };

    const sireneBtn = document.getElementById('sireneToggleButtonDevice');

    if (sireneBtn) {
        sireneBtn.disabled = true;
        sireneBtn.title = "Menunggu autentikasi Firebase...";
    }

    onAuthStateChanged(auth, (user) => {
        currentFirebaseUser = user;

        if (sireneBtn) {
            if (user) {
                sireneBtn.disabled = false;
                sireneBtn.title = "Kontrol sirine manual";
            } else {
                sireneBtn.disabled = true;
                sireneBtn.title = "Login Firebase diperlukan untuk mengubah sirine";
            }
        }
    });

    // Setup onSnapshot Firestore list for configurations
    try {
        const configColRef = collection(db, 'monitoring', 'depok', 'device_config');

        onSnapshot(configColRef, (snapshot) => {
            snapshot.forEach((doc) => {
                const data = doc.data();

                if (doc.id === 'hulu') {
                    configHulu = {
                        ...configHulu,
                        ...data
                    };
                    updateNodeSummaryCard('hulu', configHulu);
                }

                // Hilir is temporarily locked to offline default until configured physically
            });

            if (window.selectedNodeId) {
                refreshPanelData();
            }
        });
    } catch (err) {
        console.error("Config fetch error: ", err);
    }

    // Setup onSnapshot Firestore list for logs
    const logDataRef = collection(db, 'monitoring', 'depok', 'log_data');
    const q = query(logDataRef, orderBy('time', 'desc'), limit(30));

    onSnapshot(q, (snapshot) => {
        if (snapshot.empty) {
            return;
        }

        let latestHulu = null;
        let latestHilir = null;

        snapshot.forEach((doc) => {
            const data = doc.data();

            if (data.penempatan === 'hulu' && !latestHulu) {
                latestHulu = data;
                cachedLogsHulu = {
                    ...cachedLogsHulu,
                    ...data
                };
            } else if (data.penempatan === 'hilir' && !latestHilir) {
                latestHilir = data;
                cachedLogsHilir = {
                    ...cachedLogsHilir,
                    ...data
                };
            }
        });

        updateOverallKonektivitas();

        if (window.selectedNodeId) {
            refreshPanelData();
        }
    });

    // Helper functions
    function updateNodeSummaryCard(node, config) {
        const titleEl = document.getElementById(node + 'NodeTitle');
        const locEl = document.getElementById(node + 'NodeLoc');

        if (titleEl) {
            titleEl.innerText = config.nama_node;
        }

        if (locEl) {
            locEl.innerText = config.lokasi;
        }

        if (node === 'hulu') {
            updateMiniTag('huluMiniLvl', config.status_water_level, 'Lvl');
            updateMiniTag('huluMiniFlw', config.status_water_flow, 'Flow');
        } else {
            updateMiniTag('hilirMiniLvl', config.status_water_level, 'Lvl');
            updateMiniTag('hilirMiniRain', config.status_ombrometer, 'Rain');
            updateMiniTag('hilirMiniWind', config.status_anemometer, 'Wind');
            updateMiniTag('hilirMiniSir', config.status_sirine, 'Siren');
        }

        updateOverallKonektivitas();
    }

    function updateMiniTag(elementId, isOnline, label) {
        const el = document.getElementById(elementId);

        if (!el) {
            return;
        }

        el.className = `sensor-mini-tag ${isOnline !== false ? 'online' : 'offline'}`;
        el.innerHTML = isOnline !== false ?
            `<i class="fas fa-check-circle"></i> ${label}: Online` :
            `<i class="fas fa-times-circle"></i> ${label}: Off`;
    }

    function updateOverallKonektivitas() {
        const now = new Date();
        const timeoutMs = 5 * 60 * 1000;

        function isLogActive(logData) {
            if (!logData || !logData.time) {
                return false;
            }

            let logTime = new Date();

            if (logData.time.toDate) {
                logTime = logData.time.toDate();
            } else if (typeof logData.time === 'string') {
                logTime = new Date(logData.time.replace(' ', 'T'));
            }

            return (now - logTime) <= timeoutMs;
        }

        const isHuluConfigOn = configHulu.status_water_level !== false || configHulu.status_water_flow !== false;
        const isHuluOnline = isHuluConfigOn && isLogActive(cachedLogsHulu);
        const huluBadge = document.getElementById('huluOverallBadge');

        if (huluBadge) {
            huluBadge.className = `status-badge ${isHuluOnline ? 'online' : 'offline'}`;
            huluBadge.innerText = isHuluOnline ? 'ONLINE' : 'OFFLINE';
        }

        const isHilirConfigOn =
            configHilir.status_water_level !== false ||
            configHilir.status_ombrometer !== false ||
            configHilir.status_anemometer !== false ||
            configHilir.status_sirine !== false;

        const isHilirOnline = isHilirConfigOn && isLogActive(cachedLogsHilir);
        const hilirBadge = document.getElementById('hilirOverallBadge');

        if (hilirBadge) {
            hilirBadge.className = `status-badge ${isHilirOnline ? 'online' : 'offline'}`;
            hilirBadge.innerText = isHilirOnline ? 'ONLINE' : 'OFFLINE';
        }
    }

    // Node selection
    window.selectNode = function(nodeId) {
        window.selectedNodeId = nodeId;

        document.querySelectorAll('.admin-node-card').forEach(card => card.classList.remove('active'));

        const selectedCard = document.getElementById('card-' + nodeId);

        if (selectedCard) {
            selectedCard.classList.add('active');
        }

        document.getElementById('panelBlankState').style.display = 'none';
        document.getElementById('panelActiveState').style.display = 'flex';

        refreshPanelData();
    };

    function refreshPanelData() {
        if (!window.selectedNodeId) {
            return;
        }

        const nodeId = window.selectedNodeId;
        const config = nodeId === 'hulu' ? configHulu : configHilir;
        const logs = nodeId === 'hulu' ? cachedLogsHulu : cachedLogsHilir;

        document.getElementById('panelNodeId').innerText = nodeId === 'hulu' ? 'DEL-001' : 'DEL-002';
        document.getElementById('panelNodeTitle').innerText = config.nama_node;

        const now = new Date();
        const timeoutMs = 5 * 60 * 1000;
        let logActive = false;

        if (logs && logs.time) {
            let logTime = new Date();

            if (logs.time.toDate) {
                logTime = logs.time.toDate();
            } else if (typeof logs.time === 'string') {
                logTime = new Date(logs.time.replace(' ', 'T'));
            }

            logActive = (now - logTime) <= timeoutMs;
        }

        const isConfigOn = nodeId === 'hulu' ?
            (configHulu.status_water_level !== false || configHulu.status_water_flow !== false) :
            (
                configHilir.status_water_level !== false ||
                configHilir.status_ombrometer !== false ||
                configHilir.status_anemometer !== false ||
                configHilir.status_sirine !== false
            );

        const isNodeOnline = isConfigOn && logActive;

        const panelBadge = document.getElementById('panelOverallBadge');
        panelBadge.className = `status-badge ${isNodeOnline ? 'online' : 'offline'}`;
        panelBadge.innerText = isNodeOnline ? 'ONLINE' : 'OFFLINE';

        let sensors = [];

        if (nodeId === 'hulu') {
            sensors = [{
                    key: 'water_level',
                    label: 'Sensor Ketinggian Air (Water Level)',
                    icon: 'fa-water',
                    value: logs.water_level !== undefined ? logs.water_level + ' cm' : '--',
                    status: config.status_water_level
                },
                {
                    key: 'water_flow',
                    label: 'Sensor Debit Arus (Water Flow)',
                    icon: 'fa-fan',
                    value: logs.water_flow !== undefined ? logs.water_flow + ' L/min' : '--',
                    status: config.status_water_flow
                }
            ];
        } else {
            const levelVal = logs.water_level !== undefined ? logs.water_level : 0;
            const isSirenTriggered = logs.sirine_status === true || (levelVal >= (config.threshold_waspada || 150));

            sensors = [{
                    key: 'water_level',
                    label: 'Sensor Ketinggian Air (Water Level)',
                    icon: 'fa-water',
                    value: logs.water_level !== undefined ? logs.water_level + ' cm' : '--',
                    status: config.status_water_level
                },
                {
                    key: 'ombrometer',
                    label: 'Sensor Curah Hujan (Ombrometer)',
                    icon: 'fa-cloud-showers-heavy',
                    value: logs.ombrometer !== undefined ? logs.ombrometer + ' mm/j' : '--',
                    status: config.status_ombrometer
                },
                {
                    key: 'anemometer',
                    label: 'Sensor Kecepatan Angin',
                    icon: 'fa-wind',
                    value: logs.anemometer !== undefined ? logs.anemometer + ' km/h' : '--',
                    status: config.status_anemometer
                },
                {
                    key: 'sirine',
                    label: 'Sirine Alarm Peringatan (EWS)',
                    icon: 'fa-bullhorn',
                    value: isSirenTriggered ? 'AKTIF (MELENGKING)' : 'AMAN (MATI)',
                    status: config.status_sirine
                }
            ];
        }

        const tableBody = document.getElementById('sensorTableBody');
        tableBody.innerHTML = '';

        sensors.forEach(s => {
            const tr = document.createElement('tr');

            tr.innerHTML = `
                <td>
                    <div class="sensor-name-cell">
                        <i class="fas ${s.icon}"></i>
                        ${s.label}
                    </div>
                </td>
                <td style="font-family: monospace; font-size: 14px; font-weight: 600;">
                    ${s.value}
                </td>
                <td>
                    <span class="status-badge ${s.status !== false ? 'online' : 'offline'}" style="font-size: 10px;">
                        ${s.status !== false ? 'AKTIF (MEMBACA)' : 'NONAKTIF'}
                    </span>
                </td>
            `;

            tableBody.appendChild(tr);
        });

        const actuatorSection = document.getElementById('actuatorControlSection');

        if (actuatorSection) {
            actuatorSection.style.display = nodeId === 'hilir' ? 'block' : 'none';
        }
    }

    // KONTROL SIRINE MANUAL REALTIME
    let currentSireneState = false;

    if (sireneBtn) {
        const sireneDocRef = doc(db, 'monitoring', 'depok', 'sirene', 'status');

        onSnapshot(sireneDocRef, (docSnap) => {
            if (docSnap.exists()) {
                currentSireneState = docSnap.data().is_active === true;
            } else {
                currentSireneState = false;
            }

            if (currentSireneState) {
                sireneBtn.style.background = 'var(--danger-color)';
                sireneBtn.style.borderColor = '#fca5a5';
                sireneBtn.style.color = 'white';
                sireneBtn.style.boxShadow = '0 0 15px rgba(239, 68, 68, 0.6)';
                sireneBtn.innerText = 'MENYALA';
            } else {
                sireneBtn.style.background = '#94a3b8';
                sireneBtn.style.borderColor = '#cbd5e1';
                sireneBtn.style.boxShadow = 'none';
                sireneBtn.innerText = 'MATI';
            }
        });

        sireneBtn.addEventListener('click', async () => {
            if (!currentFirebaseUser) {
                alert("Akses ditolak. Silakan login ulang.");
                return;
            }

            const newState = !currentSireneState;

            sireneBtn.disabled = true;
            sireneBtn.innerText = '...';

            try {
                await setDoc(sireneDocRef, {
                    is_active: newState,
                    updated_at: serverTimestamp(),
                    updated_by: currentFirebaseUser.email || currentFirebaseUser.uid,
                    source: 'web_admin'
                }, {
                    merge: true
                });

                currentSireneState = newState;
            } catch (err) {
                console.error("Gagal mengubah status sirine:", err);
                alert("Gagal mengubah status sirine manual.");
            } finally {
                sireneBtn.disabled = false;
            }
        });
    }
</script>
@endsection