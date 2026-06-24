@extends('layouts.app')

@section('title', 'Kelola Sensor & Perangkat')

@section('content')
<style>
    .devices-page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 20px;
        flex-wrap: wrap;
        position: relative;
        z-index: 5;
    }

    .devices-page-header h2 {
        margin: 0;
        font-size: 22px;
        color: var(--text-primary);
    }

    .devices-page-header p {
        margin: 6px 0 0;
        font-size: 13px;
        color: var(--text-secondary);
    }

    .add-device-top-btn {
        background: var(--primary-blue, #2563eb);
        color: #ffffff;
        border: none;
        padding: 11px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.18);
        transition: all 0.2s ease;
        white-space: nowrap;
        position: relative;
        z-index: 10;
        pointer-events: auto;
    }

    .add-device-top-btn:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

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
        overflow: hidden;
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
        gap: 12px;
    }

    .node-title-area h3 {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 6px 0 2px 0;
    }

    .node-title-area p {
        margin: 0;
        font-size: 12px;
        color: var(--text-secondary);
    }

    .node-title-area span.badge,
    .badge {
        font-size: 11px;
        font-weight: 700;
        background: #f3f4f6;
        color: #4b5563;
        padding: 2px 8px;
        border-radius: 4px;
        font-family: monospace;
        display: inline-flex;
        align-items: center;
        width: fit-content;
    }

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
        gap: 16px;
    }

    .panel-header h2 {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 6px 0 0;
    }

    .sensor-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .sensor-table {
        width: 100%;
        min-width: 620px;
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

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 12px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .status-badge.online {
        background: #dcfce7;
        color: #15803d;
    }

    .status-badge.offline {
        background: #fee2e2;
        color: #b91c1c;
    }

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

    .actuator-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        background: #f8fafc;
        padding: 16px 20px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }

    .sirene-button {
        padding: 12px 28px;
        border-radius: 8px;
        border: 2px solid #cbd5e1;
        background: #94a3b8;
        color: white;
        font-weight: bold;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        min-width: 140px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .sirene-button:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-card {
        background: #ffffff;
        width: 100%;
        max-width: 560px;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.25);
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
        gap: 16px;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 800;
        color: var(--text-primary);
    }

    .modal-close {
        border: none;
        background: transparent;
        font-size: 28px;
        cursor: pointer;
        color: #64748b;
        line-height: 1;
    }

    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 14px;
    }

    .form-group label {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-secondary);
    }

    .form-control {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        padding: 10px 12px;
        font-size: 13px;
        outline: none;
        background: #ffffff;
        color: var(--text-primary);
    }

    .form-control:focus {
        border-color: var(--primary-blue, #2563eb);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .modal-note {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 12px;
        font-size: 12px;
        line-height: 1.5;
        margin-bottom: 16px;
    }

    .modal-save-btn {
        width: 100%;
        background: var(--primary-blue, #2563eb);
        color: white;
        border: none;
        padding: 12px 16px;
        border-radius: 10px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 4px;
    }

    .modal-save-btn:disabled {
        background: #94a3b8;
        cursor: not-allowed;
    }

    @media (max-width: 640px) {
        .devices-page-header {
            flex-direction: column;
            align-items: stretch;
        }

        .add-device-top-btn {
            width: 100%;
            justify-content: center;
        }

        .control-panel-card {
            padding: 18px;
        }

        .panel-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .actuator-box {
            flex-direction: column;
            align-items: stretch;
        }

        .sirene-button {
            width: 100%;
        }

        .form-grid-2 {
            grid-template-columns: 1fr;
        }

        .modal-card {
            padding: 18px;
            border-radius: 14px;
        }
    }
</style>

<div class="devices-container">
    <div class="devices-page-header">
        <div>
            <h2 class="section-title">Pusat Manajemen Sensor & Perangkat</h2>

        </div>

        <button type="button" class="add-device-top-btn" id="addDeviceBtn">
            <i class="fas fa-plus"></i>
            Tambah Perangkat
        </button>
    </div>

    <div class="admin-grid">
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <div class="panel-section-title">
                <i class="fas fa-microchip"></i> Daftar Node Terhubung
            </div>

            <div id="nodeList" style="display: flex; flex-direction: column; gap: 16px;">
                <div class="admin-node-card">
                    <div class="node-header-row">
                        <div class="node-title-area">
                            <span class="badge">LOADING</span>
                            <h3>Memuat data node...</h3>
                            <p>Mengambil data dari Firestore.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="control-panel-card" id="controlPanel">
            <div class="blank-state" id="panelBlankState">
                <i class="fas fa-sliders-h"></i>
                <h3>Panel Kontrol Belum Terpilih</h3>
                <p>
                    Klik salah satu kartu node di sebelah kiri untuk melihat rincian sensor,
                    status perangkat, dan kontrol sirine berdasarkan data Firestore.
                </p>
            </div>

            <div id="panelActiveState" style="display: none; flex-direction: column; flex: 1;">
                <div class="panel-header">
                    <div>
                        <span class="badge" id="panelNodeId" style="background: #e0f2fe; color: #0369a1;">
                            DEL-000
                        </span>
                        <h2 id="panelNodeTitle">Detail Perangkat</h2>
                        <p id="panelNodeLocation" style="margin: 4px 0 0; font-size: 12px; color: var(--text-secondary);">
                            -
                        </p>
                    </div>
                    <div class="status-badge offline" id="panelOverallBadge">OFFLINE</div>
                </div>

                <div class="panel-section-title">
                    <i class="fas fa-tasks"></i> Status & Detail Sensor Individu
                </div>

                <div class="sensor-table-wrapper">
                    <table class="sensor-table">
                        <thead>
                            <tr>
                                <th>Sensor</th>
                                <th>Nilai Real-time</th>
                                <th>Status Alat</th>
                            </tr>
                        </thead>
                        <tbody id="sensorTableBody"></tbody>
                    </table>
                </div>

                <div id="actuatorControlSection"
                    style="display: block; margin-top: 16px; border-top: 1px dashed var(--border-color); padding-top: 24px;">
                    <div class="panel-section-title">
                        <i class="fas fa-volume-up"></i> Kontrol Aktuator Sirine
                    </div>

                    <div class="actuator-box">
                        <div>
                            <h4 style="margin: 0 0 6px 0; font-size: 15px; color: var(--text-primary);">
                                Sirine Manual Darurat
                            </h4>
                            <p style="margin: 0; font-size: 12px; color: var(--text-secondary);">
                            </p>
                        </div>

                        <button id="sireneToggleButtonDevice" class="sirene-button">
                            MATI
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="addDeviceModal" class="modal-overlay" style="display: none;">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Tambah Perangkat Baru</h3>
            <button type="button" id="closeAddDeviceModal" class="modal-close">&times;</button>
        </div>

        <div class="modal-note">
            Penambahan perangkat tidak membuat collection baru. Web akan menambahkan satu dokumen awal ke
            <strong>monitoring/depok/log_data</strong> agar node baru ikut terbaca pada daftar node dan dropdown halaman lain.
        </div>

        <form id="addDeviceForm">
            <div class="form-grid-2">
                <div class="form-group">
                    <label for="newNodeId">Node ID</label>
                    <input type="number" id="newNodeId" class="form-control" placeholder="Contoh: 3" required>
                </div>

                <div class="form-group">
                    <label for="newPenempatan">Penempatan</label>
                    <select id="newPenempatan" class="form-control" required>
                        <option value="">Pilih penempatan</option>
                        <option value="hulu">Hulu</option>
                        <option value="hilir">Hilir</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="newNodeName">Nama Perangkat</label>
                <input type="text" id="newNodeName" class="form-control" placeholder="Contoh: Node Cinere" required>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="newSitus">Situs / Area</label>
                    <input type="text" id="newSitus" class="form-control" placeholder="Contoh: pnj" required>
                </div>

                <div class="form-group">
                    <label for="newCity">Kota</label>
                    <input type="text" id="newCity" class="form-control" value="depok" required>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="newLatitude">Latitude</label>
                    <input type="number" step="any" id="newLatitude" class="form-control" placeholder="Opsional">
                </div>

                <div class="form-group">
                    <label for="newLongitude">Longitude</label>
                    <input type="number" step="any" id="newLongitude" class="form-control" placeholder="Opsional">
                </div>
            </div>

            <button type="submit" class="modal-save-btn" id="saveDeviceBtn">
                <i class="fas fa-save"></i>
                Simpan Perangkat
            </button>
        </form>
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
        updateDoc,
        addDoc
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
    const db = getFirestore(app);
    const auth = getAuth(app);

    let currentFirebaseUser = null;
    let currentSireneState = false;
    let selectedNodeKey = null;
    let latestNodes = new Map();

    window.selectedNodeId = null;

    const nodeList = document.getElementById("nodeList");
    const sireneBtn = document.getElementById("sireneToggleButtonDevice");

    const addDeviceBtn = document.getElementById("addDeviceBtn");
    const addDeviceModal = document.getElementById("addDeviceModal");
    const closeAddDeviceModal = document.getElementById("closeAddDeviceModal");
    const addDeviceForm = document.getElementById("addDeviceForm");
    const saveDeviceBtn = document.getElementById("saveDeviceBtn");

    if (sireneBtn) {
        sireneBtn.disabled = true;
        sireneBtn.title = "Menunggu autentikasi Firebase...";
    }

    onAuthStateChanged(auth, (user) => {
        currentFirebaseUser = user;

        if (sireneBtn) {
            sireneBtn.disabled = !user;
            sireneBtn.title = user ? "Kontrol sirine manual" : "Login Firebase diperlukan untuk mengubah sirine";
        }
    });

    function formatNodeKey(data) {
        if (data.node_id !== undefined && data.node_id !== null && data.node_id !== "") {
            return `node_${data.node_id}`;
        }

        return String(data.penempatan || "unknown").toLowerCase();
    }

    function formatNodeBadge(data) {
        const rawNodeId = data.node_id ?? data.id ?? null;

        if (rawNodeId !== null && rawNodeId !== undefined && rawNodeId !== "") {
            const numeric = Number(rawNodeId);

            if (!Number.isNaN(numeric)) {
                return `DEL-${String(numeric).padStart(3, "0")}`;
            }

            return String(rawNodeId).toUpperCase();
        }

        const penempatan = String(data.penempatan || "node").toUpperCase();
        return penempatan;
    }

    function formatNodeName(data) {
        if (data.node_name) return data.node_name;
        if (data.nama_node) return data.nama_node;

        const penempatan = String(data.penempatan || "node").toLowerCase();

        if (penempatan === "hulu") return "Node Hulu";
        if (penempatan === "hilir") return "Node Hilir";

        return `Node ${penempatan.charAt(0).toUpperCase() + penempatan.slice(1)}`;
    }

    function formatLocation(data) {
        const situs = data.situs || data.area || "-";
        const city = data.city || "depok";
        const penempatan = data.penempatan || "-";

        return `${String(situs).toUpperCase()} • ${String(city).toUpperCase()} • ${String(penempatan).toUpperCase()}`;
    }

    function toDateFromFirestore(value) {
        if (!value) return null;

        if (value.toDate) {
            return value.toDate();
        }

        if (typeof value === "string") {
            const parsed = new Date(value.replace(" ", "T"));
            return Number.isNaN(parsed.getTime()) ? null : parsed;
        }

        if (value instanceof Date) return value;

        return null;
    }

    function isNodeActive(data) {
        const nodeStatus = String(data.node_status || "").toLowerCase();

        if (nodeStatus === "offline") {
            return false;
        }

        const time = toDateFromFirestore(data.time || data.created_at || data.updated_at);

        if (!time) {
            return nodeStatus === "online";
        }

        const timeoutMs = 5 * 60 * 1000;
        return (new Date() - time) <= timeoutMs;
    }

    function getMiniTagHtml(label, isAvailable) {
        return `
            <span class="sensor-mini-tag ${isAvailable ? "online" : "offline"}">
                <i class="fas ${isAvailable ? "fa-check-circle" : "fa-times-circle"}"></i>
                ${label}: ${isAvailable ? "Online" : "Off"}
            </span>
        `;
    }

    function renderNodeCards() {
        if (!nodeList) return;

        const nodes = Array.from(latestNodes.values()).sort((a, b) => {
            const aId = Number(a.data.node_id ?? 9999);
            const bId = Number(b.data.node_id ?? 9999);

            if (!Number.isNaN(aId) && !Number.isNaN(bId)) {
                return aId - bId;
            }

            return formatNodeName(a.data).localeCompare(formatNodeName(b.data));
        });

        if (nodes.length === 0) {
            nodeList.innerHTML = `
                <div class="admin-node-card">
                    <div class="node-header-row">
                        <div class="node-title-area">
                            <span class="badge">NO DATA</span>
                            <h3>Belum ada node</h3>
                            <p>Tambahkan perangkat atau tunggu data masuk ke log_data.</p>
                        </div>
                    </div>
                </div>
            `;
            return;
        }

        nodeList.innerHTML = nodes.map((node) => {
            const data = node.data;
            const nodeKey = node.key;
            const penempatanClass = String(data.penempatan || "").toLowerCase();
            const isActive = isNodeActive(data);
            const isSelected = selectedNodeKey === nodeKey;

            const hasWaterLevel = data.water_level !== undefined && data.water_level !== null;
            const hasWaterFlow = data.water_flow !== undefined && data.water_flow !== null;
            const hasRain = data.ombrometer !== undefined && data.ombrometer !== null;
            const hasWind = data.anemometer !== undefined && data.anemometer !== null;
            const hasSirene = data.sirine_status !== undefined && data.sirine_status !== null;

            return `
                <div class="admin-node-card ${penempatanClass} ${isSelected ? "active" : ""}"
                    data-node-key="${nodeKey}"
                    onclick="selectNode('${nodeKey}')">
                    <div class="node-header-row">
                        <div class="node-title-area">
                            <span class="badge">${formatNodeBadge(data)}</span>
                            <h3>${formatNodeName(data)}</h3>
                            <p>${formatLocation(data)}</p>
                        </div>
                        <div class="status-badge ${isActive ? "online" : "offline"}">
                            ${isActive ? "ONLINE" : "OFFLINE"}
                        </div>
                    </div>

                    <div class="mini-status-list">
                        ${getMiniTagHtml("Lvl", hasWaterLevel)}
                        ${getMiniTagHtml("Flow", hasWaterFlow)}
                        ${getMiniTagHtml("Rain", hasRain)}
                        ${getMiniTagHtml("Wind", hasWind)}
                        ${getMiniTagHtml("Siren", hasSirene)}
                    </div>
                </div>
            `;
        }).join("");
    }

    const logDataRef = collection(db, "monitoring", "depok", "log_data");
    const q = query(logDataRef, orderBy("time", "desc"), limit(200));

    onSnapshot(q, (snapshot) => {
        const nodes = new Map();

        snapshot.forEach((docSnap) => {
            const data = docSnap.data();
            const key = formatNodeKey(data);

            if (!nodes.has(key)) {
                nodes.set(key, {
                    key,
                    docId: docSnap.id,
                    data
                });
            }
        });

        latestNodes = nodes;

        if (selectedNodeKey && !latestNodes.has(selectedNodeKey)) {
            selectedNodeKey = null;
            window.selectedNodeId = null;
        }

        renderNodeCards();

        if (selectedNodeKey) {
            refreshPanelData();
        }
    }, (error) => {
        console.error("Gagal membaca log_data:", error);
        if (nodeList) {
            nodeList.innerHTML = `
                <div class="admin-node-card">
                    <div class="node-header-row">
                        <div class="node-title-area">
                            <span class="badge">ERROR</span>
                            <h3>Gagal memuat data</h3>
                            <p>Periksa koneksi Firestore dan rules.</p>
                        </div>
                    </div>
                </div>
            `;
        }
    });

    window.selectNode = function(nodeKey) {
        selectedNodeKey = nodeKey;
        window.selectedNodeId = nodeKey;

        document.querySelectorAll(".admin-node-card").forEach(card => {
            card.classList.remove("active");
        });

        const selectedCard = document.querySelector(`[data-node-key="${nodeKey}"]`);

        if (selectedCard) {
            selectedCard.classList.add("active");
        }

        const blankState = document.getElementById("panelBlankState");
        const activeState = document.getElementById("panelActiveState");

        if (blankState) blankState.style.display = "none";
        if (activeState) activeState.style.display = "flex";

        refreshPanelData();
    };

    function sensorStatus(value) {
        return value !== undefined && value !== null && value !== "";
    }

    function sensorRow(label, icon, value, isActive) {
        return `
            <tr>
                <td>
                    <div class="sensor-name-cell">
                        <i class="fas ${icon}"></i>
                        ${label}
                    </div>
                </td>
                <td style="font-family: monospace; font-size: 14px; font-weight: 600;">
                    ${value}
                </td>
                <td>
                    <span class="status-badge ${isActive ? "online" : "offline"}" style="font-size: 10px;">
                        ${isActive ? "AKTIF (MEMBACA)" : "NONAKTIF / BELUM ADA DATA"}
                    </span>
                </td>
            </tr>
        `;
    }

    function refreshPanelData() {
        if (!selectedNodeKey || !latestNodes.has(selectedNodeKey)) {
            return;
        }

        const node = latestNodes.get(selectedNodeKey);
        const logs = node.data;

        const panelNodeId = document.getElementById("panelNodeId");
        const panelNodeTitle = document.getElementById("panelNodeTitle");
        const panelNodeLocation = document.getElementById("panelNodeLocation");
        const panelBadge = document.getElementById("panelOverallBadge");
        const tableBody = document.getElementById("sensorTableBody");

        if (panelNodeId) panelNodeId.innerText = formatNodeBadge(logs);
        if (panelNodeTitle) panelNodeTitle.innerText = formatNodeName(logs);
        if (panelNodeLocation) panelNodeLocation.innerText = formatLocation(logs);

        const active = isNodeActive(logs);

        if (panelBadge) {
            panelBadge.className = `status-badge ${active ? "online" : "offline"}`;
            panelBadge.innerText = active ? "ONLINE" : "OFFLINE";
        }

        if (tableBody) {
            const sirineValue = Number(logs.sirine_status ?? 0);
            const isSirenTriggered = sirineValue === 1;

            tableBody.innerHTML = [
                sensorRow(
                    "Sensor Ketinggian Air (Water Level)",
                    "fa-water",
                    logs.water_level !== undefined && logs.water_level !== null ? `${logs.water_level} cm` : "--",
                    sensorStatus(logs.water_level)
                ),
                sensorRow(
                    "Sensor Debit Arus (Water Flow)",
                    "fa-fan",
                    logs.water_flow !== undefined && logs.water_flow !== null ? `${logs.water_flow} L/min` : "--",
                    sensorStatus(logs.water_flow)
                ),
                sensorRow(
                    "Sensor Curah Hujan (Ombrometer)",
                    "fa-cloud-showers-heavy",
                    logs.ombrometer !== undefined && logs.ombrometer !== null ? `${logs.ombrometer} mm/j` : "--",
                    sensorStatus(logs.ombrometer)
                ),
                sensorRow(
                    "Sensor Kecepatan Angin (Anemometer)",
                    "fa-wind",
                    logs.anemometer !== undefined && logs.anemometer !== null ? `${logs.anemometer} km/h` : "--",
                    sensorStatus(logs.anemometer)
                ),
                sensorRow(
                    "Sirine Alarm Peringatan (EWS)",
                    "fa-bullhorn",
                    isSirenTriggered ? "AKTIF (MENYALA)" : "AMAN (MATI)",
                    sensorStatus(logs.sirine_status)
                )
            ].join("");
        }

        updateSireneButtonFromLog();
    }

    function updateSireneButtonFromLog() {
        if (!sireneBtn || !selectedNodeKey || !latestNodes.has(selectedNodeKey)) {
            return;
        }

        const node = latestNodes.get(selectedNodeKey);
        const sirineValue = Number(node.data.sirine_status ?? 0);
        currentSireneState = sirineValue === 1;

        if (currentSireneState) {
            sireneBtn.style.background = "var(--danger-color, #ef4444)";
            sireneBtn.style.borderColor = "#fca5a5";
            sireneBtn.style.color = "white";
            sireneBtn.style.boxShadow = "0 0 15px rgba(239, 68, 68, 0.6)";
            sireneBtn.innerText = "MENYALA";
        } else {
            sireneBtn.style.background = "#94a3b8";
            sireneBtn.style.borderColor = "#cbd5e1";
            sireneBtn.style.color = "white";
            sireneBtn.style.boxShadow = "none";
            sireneBtn.innerText = "MATI";
        }
    }

    async function writeAuditLog(action, details = {}) {
        try {
            await addDoc(collection(db, "monitoring", "depok", "admin_audit_logs"), {
                action,
                admin_email: currentFirebaseUser?.email || "-",
                source: "web_admin",
                details,
                ip_address: "-",
                user_agent: navigator.userAgent,
                created_at: serverTimestamp()
            });
        } catch (error) {
            console.warn("Gagal menulis admin_audit_logs:", error);
        }
    }

    if (sireneBtn) {
        sireneBtn.addEventListener("click", async () => {
            if (!currentFirebaseUser) {
                alert("Akses ditolak. Silakan login ulang.");
                return;
            }

            if (!selectedNodeKey || !latestNodes.has(selectedNodeKey)) {
                alert("Pilih node terlebih dahulu.");
                return;
            }

            const node = latestNodes.get(selectedNodeKey);
            const newState = currentSireneState ? 0 : 1;

            sireneBtn.disabled = true;
            sireneBtn.innerText = "...";

            try {
                const latestLogRef = doc(db, "monitoring", "depok", "log_data", node.docId);

                await updateDoc(latestLogRef, {
                    sirine_status: newState,
                    sirine_updated_at: serverTimestamp(),
                    sirine_updated_by: currentFirebaseUser.email || currentFirebaseUser.uid,
                    sirine_source: "web_admin"
                });

                await writeAuditLog("update_sirine_status", {
                    node_id: node.data.node_id ?? null,
                    penempatan: node.data.penempatan ?? null,
                    doc_id: node.docId,
                    sirine_status: newState
                });

                node.data.sirine_status = newState;
                latestNodes.set(selectedNodeKey, node);
                currentSireneState = newState === 1;

                updateSireneButtonFromLog();
                refreshPanelData();
                renderNodeCards();
            } catch (err) {
                console.error("Gagal mengubah status sirine:", err);
                alert("Gagal mengubah status sirine manual. Cek Firestore Rules dan koneksi.");
            } finally {
                sireneBtn.disabled = !currentFirebaseUser;
            }
        });
    }

    function openAddDeviceModal() {
        if (addDeviceModal) {
            addDeviceModal.style.display = "flex";
        }
    }

    function closeDeviceModal() {
        if (addDeviceModal) {
            addDeviceModal.style.display = "none";
        }

        if (addDeviceForm) {
            addDeviceForm.reset();
        }

        const cityInput = document.getElementById("newCity");
        if (cityInput) {
            cityInput.value = "depok";
        }
    }

    if (addDeviceBtn) {
        addDeviceBtn.addEventListener("click", function() {
            openAddDeviceModal();
        });
    }

    if (closeAddDeviceModal) {
        closeAddDeviceModal.addEventListener("click", closeDeviceModal);
    }

    if (addDeviceModal) {
        addDeviceModal.addEventListener("click", (event) => {
            if (event.target === addDeviceModal) {
                closeDeviceModal();
            }
        });
    }

    if (addDeviceForm) {
        addDeviceForm.addEventListener("submit", async (event) => {
            event.preventDefault();

            if (!currentFirebaseUser) {
                alert("Akses ditolak. Silakan login ulang.");
                return;
            }

            const nodeId = Number(document.getElementById("newNodeId").value);
            const nodeName = document.getElementById("newNodeName").value.trim();
            const penempatan = document.getElementById("newPenempatan").value.trim().toLowerCase();
            const situs = document.getElementById("newSitus").value.trim().toLowerCase();
            const city = document.getElementById("newCity").value.trim().toLowerCase();
            const latitudeValue = document.getElementById("newLatitude").value;
            const longitudeValue = document.getElementById("newLongitude").value;

            if (!nodeId || !nodeName || !penempatan || !situs || !city) {
                alert("Field wajib belum lengkap.");
                return;
            }

            const duplicateNode = Array.from(latestNodes.values()).find((node) => {
                return Number(node.data.node_id) === nodeId;
            });

            if (duplicateNode) {
                alert("Node ID sudah ada di log_data. Gunakan Node ID lain.");
                return;
            }

            const initialPayload = {
                node_id: nodeId,
                node_name: nodeName,
                city: city,
                situs: situs,
                area: situs,
                penempatan: penempatan,

                water_level: 0,
                water_flow: 0,
                ombrometer: 0,
                anemometer: 0,

                water_status: "Aman",
                node_status: "offline",
                sirine_status: 0,

                espcam_img_url: "",
                sync_status: "sent",

                latitude: latitudeValue ? Number(latitudeValue) : null,
                longitude: longitudeValue ? Number(longitudeValue) : null,

                created_by: currentFirebaseUser.email || currentFirebaseUser.uid,
                created_from: "web_admin",
                time: new Date().toISOString(),
                created_at: serverTimestamp(),
                updated_at: serverTimestamp()
            };

            saveDeviceBtn.disabled = true;
            saveDeviceBtn.innerText = "Menyimpan...";

            try {
                const docRef = await addDoc(collection(db, "monitoring", "depok", "log_data"), initialPayload);

                await writeAuditLog("create_device", {
                    node_id: nodeId,
                    node_name: nodeName,
                    penempatan: penempatan,
                    situs: situs,
                    city: city,
                    log_data_doc_id: docRef.id
                });

                alert("Perangkat baru berhasil ditambahkan.");
                closeDeviceModal();
            } catch (error) {
                console.error("Gagal menambah perangkat:", error);
                alert("Gagal menambah perangkat baru. Cek Firestore Rules dan koneksi.");
            } finally {
                saveDeviceBtn.disabled = false;
                saveDeviceBtn.innerText = "Simpan Perangkat";
            }
        });
    }
</script>
@endsection