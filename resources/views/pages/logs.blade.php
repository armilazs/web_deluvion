@extends('layouts.app')

@section('title', 'Aktivitas Log')

@section('content')
<style>
    .logs-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
        border: 1px solid #e2e8f0;
    }

    .logs-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 22px;
        flex-wrap: wrap;
    }

    .logs-tabs {
        display: flex;
        gap: 28px;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 20px;
        overflow-x: auto;
    }

    .logs-tab-btn {
        border: none;
        background: transparent;
        padding: 12px 0;
        color: #64748b;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        border-bottom: 2px solid transparent;
    }

    .logs-tab-btn.active {
        color: var(--primary-blue);
        border-bottom-color: var(--primary-blue);
    }

    .logs-pane {
        display: none;
    }

    .logs-pane.active {
        display: block;
    }

    .logs-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .logs-description {
        color: var(--text-secondary);
        font-size: 14px;
        margin: 0;
    }

    .logs-select {
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #ffffff;
        color: var(--text-primary);
        font-size: 14px;
        min-width: 180px;
        outline: none;
    }

    .logs-table-wrapper {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }

    .logs-table {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
        font-size: 14px;
    }

    .logs-table thead {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .logs-table th,
    .logs-table td {
        padding: 12px 14px;
        text-align: left;
        white-space: nowrap;
        border-bottom: 1px solid #e2e8f0;
    }

    .logs-table th {
        color: #475569;
        font-size: 13px;
        font-weight: 700;
    }

    .logs-table td {
        color: #1e293b;
    }

    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .activity-card {
        display: grid;
        grid-template-columns: 46px 1fr;
        gap: 12px;
        padding: 14px;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
    }

    .activity-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: #eff6ff;
        color: var(--primary-blue);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .activity-title {
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
        word-break: break-word;
    }

    .activity-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        color: #64748b;
        font-size: 12px;
        margin-bottom: 8px;
    }

    .activity-detail {
        background: #f8fafc;
        border-radius: 10px;
        padding: 10px;
        color: #475569;
        font-size: 13px;
        word-break: break-word;
        white-space: pre-wrap;
    }

    .image-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 16px;
    }

    .image-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        background: #ffffff;
    }

    .image-card img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        display: block;
        background: #f1f5f9;
    }

    .image-card-body {
        padding: 12px;
    }

    .image-title {
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .image-meta {
        color: #64748b;
        font-size: 13px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
    }

    .empty-box {
        width: 100%;
        padding: 44px 20px;
        text-align: center;
        color: #64748b;
    }

    .export-btn {
        width: auto;
        padding: 8px 16px;
        background: var(--bg-color);
        color: var(--primary-blue);
        border: 1px solid var(--primary-blue);
    }

    @media (max-width: 768px) {
        .logs-card {
            padding: 18px;
        }

        .logs-header {
            align-items: stretch;
        }

        .export-btn {
            width: 100%;
            justify-content: center;
        }

        .logs-toolbar {
            align-items: stretch;
        }

        .logs-select {
            width: 100%;
        }

        .activity-card {
            grid-template-columns: 1fr;
        }

        .image-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="logs-card">
    <div class="logs-header">
        <h2 class="section-title" style="margin: 0;">Log Sistem & Riwayat Deteksi</h2>

        <button type="button" class="btn-primary export-btn" onclick="exportCurrentLogCSV()">
            <i class="fas fa-file-export"></i> Ekspor CSV
        </button>
    </div>

    <div class="logs-tabs">
        <button type="button" class="logs-tab-btn active" data-tab="webLogs">Log Aktivitas Web</button>
        <button type="button" class="logs-tab-btn" data-tab="sensorLogs">Log Sensor</button>
        <button type="button" class="logs-tab-btn" data-tab="imageLogs">Log Gambar</button>
    </div>

    <div id="webLogs" class="logs-pane active">
        <div class="logs-toolbar">
            <p class="logs-description">

            </p>

            <select id="webSourceFilter" class="logs-select">
                <option value="all">Semua Source</option>
            </select>
        </div>

        <div id="webLogsContainer" class="activity-list">
            <div class="empty-box">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Memuat log aktivitas web...</p>
            </div>
        </div>
    </div>

    <div id="sensorLogs" class="logs-pane">
        <div class="logs-toolbar">
            <p class="logs-description">

            </p>

            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <select id="sensorNodeFilter" class="logs-select">
                    <option value="all">Semua Node</option>
                </select>

                <select id="sensorStatusFilter" class="logs-select">
                    <option value="all">Semua Status</option>
                    <option value="AMAN">Aman</option>
                    <option value="SIAGA">Siaga</option>
                    <option value="WASPADA">Waspada</option>
                    <option value="DARURAT">Darurat</option>
                </select>
            </div>
        </div>

        <div id="sensorLogsContainer">
            <div class="empty-box">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Memuat log sensor...</p>
            </div>
        </div>
    </div>

    <div id="imageLogs" class="logs-pane">
        <div class="logs-toolbar">
            <p class="logs-description">
                Riwayat tangkapan gambar dari sensor kamera node.
            </p>

            <select id="imageNodeFilter" class="logs-select">
                <option value="all">Semua Node</option>
            </select>
        </div>

        <div id="imageLogsContainer" class="image-grid">
            <div class="empty-box" style="grid-column: 1 / -1;">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Memuat log gambar...</p>
            </div>
        </div>
    </div>
</div>

<script>
    window.activeLogTab = "webLogs";

    document.querySelectorAll(".logs-tab-btn").forEach((button) => {
        button.addEventListener("click", function() {
            const targetTab = this.dataset.tab;

            window.activeLogTab = targetTab;

            document.querySelectorAll(".logs-tab-btn").forEach((btn) => {
                btn.classList.remove("active");
            });

            document.querySelectorAll(".logs-pane").forEach((pane) => {
                pane.classList.remove("active");
            });

            this.classList.add("active");

            const pane = document.getElementById(targetTab);
            if (pane) {
                pane.classList.add("active");
            }
        });
    });

    function escapeHtml(value) {
        return String(value ?? "")
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");
    }

    function safeCsv(value) {
        const text = String(value ?? "").replaceAll('"', '""');

        if (/^[=+\-@]/.test(text)) {
            return `"'${text}"`;
        }

        return `"${text}"`;
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

    const webLogsContainer = document.getElementById("webLogsContainer");
    const sensorLogsContainer = document.getElementById("sensorLogsContainer");
    const imageLogsContainer = document.getElementById("imageLogsContainer");

    const webSourceFilter = document.getElementById("webSourceFilter");
    const sensorNodeFilter = document.getElementById("sensorNodeFilter");
    const sensorStatusFilter = document.getElementById("sensorStatusFilter");
    const imageNodeFilter = document.getElementById("imageNodeFilter");

    let webLogs = [];
    let sensorLogs = [];
    let nodeOptions = new Map();
    let sourceOptions = new Set();

    function parseTime(value) {
        if (!value) return 0;

        if (typeof value === "object" && typeof value.toDate === "function") {
            return value.toDate().getTime();
        }

        if (typeof value === "object" && typeof value.seconds === "number") {
            return value.seconds * 1000;
        }

        const dateText = String(value).replace(" ", "T");
        const parsed = new Date(dateText).getTime();

        return Number.isFinite(parsed) ? parsed : 0;
    }

    function formatTime(value) {
        if (!value) return "-";

        let dateObject = null;

        if (typeof value === "object" && typeof value.toDate === "function") {
            dateObject = value.toDate();
        } else if (typeof value === "object" && typeof value.seconds === "number") {
            dateObject = new Date(value.seconds * 1000);
        } else {
            const parsed = new Date(String(value).replace(" ", "T"));

            if (!Number.isNaN(parsed.getTime())) {
                dateObject = parsed;
            }
        }

        if (!dateObject) return String(value);

        return dateObject.toLocaleString("id-ID", {
            day: "2-digit",
            month: "short",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit"
        });
    }

    function normalizeText(value, fallback = "-") {
        if (value === undefined || value === null || value === "") {
            return fallback;
        }

        return String(value);
    }

    function normalizeNodeId(value) {
        if (value === undefined || value === null || value === "") {
            return "unknown";
        }

        return String(value).trim();
    }

    function normalizeStatus(value) {
        const status = String(value ?? "").trim().toUpperCase();

        if (status === "AMAN") return "AMAN";
        if (status === "SIAGA") return "SIAGA";
        if (status === "WASPADA") return "WASPADA";
        if (status === "DARURAT") return "DARURAT";

        return status || "AMAN";
    }

    function statusFromWaterLevel(level) {
        const value = Number(level);

        if (!Number.isFinite(value)) return "AMAN";
        if (value > 150) return "WASPADA";
        if (value > 100) return "SIAGA";

        return "AMAN";
    }

    function getStatusStyle(status) {
        const value = normalizeStatus(status);

        if (value === "AMAN") {
            return {
                bg: "#dcfce7",
                color: "#166534",
                icon: "fa-check-circle"
            };
        }

        if (value === "SIAGA") {
            return {
                bg: "#fef9c3",
                color: "#854d0e",
                icon: "fa-exclamation-circle"
            };
        }

        if (value === "WASPADA") {
            return {
                bg: "#ffedd5",
                color: "#9a3412",
                icon: "fa-triangle-exclamation"
            };
        }

        return {
            bg: "#fee2e2",
            color: "#991b1b",
            icon: "fa-triangle-exclamation"
        };
    }

    function getNodeLabel(data) {
        const nodeId = normalizeNodeId(data.node_id);
        const nodeName = String(data.node_name ?? "").trim();
        const penempatan = String(data.penempatan ?? "").trim().toLowerCase();
        const situs = String(data.situs ?? data.area ?? "").trim().toUpperCase();

        if (nodeName) return nodeName;

        let label = `Node ${nodeId}`;

        if (penempatan) {
            label += ` - ${penempatan.charAt(0).toUpperCase()}${penempatan.slice(1)}`;
        }

        if (situs) {
            label += ` (${situs})`;
        }

        return label;
    }

    function registerNode(data) {
        const nodeId = normalizeNodeId(data.node_id);

        if (nodeId === "unknown") return;

        if (!nodeOptions.has(nodeId)) {
            nodeOptions.set(nodeId, {
                id: nodeId,
                label: getNodeLabel(data)
            });
        }
    }

    function refreshNodeDropdowns() {
        const currentSensor = sensorNodeFilter ? sensorNodeFilter.value : "all";
        const currentImage = imageNodeFilter ? imageNodeFilter.value : "all";

        const nodes = Array.from(nodeOptions.values()).sort((a, b) => {
            return a.label.localeCompare(b.label, "id");
        });

        [sensorNodeFilter, imageNodeFilter].forEach((select) => {
            if (!select) return;

            select.innerHTML = `<option value="all">Semua Node</option>`;

            nodes.forEach((node) => {
                const option = document.createElement("option");
                option.value = node.id;
                option.textContent = node.label;
                select.appendChild(option);
            });
        });

        if (sensorNodeFilter && Array.from(sensorNodeFilter.options).some(opt => opt.value === currentSensor)) {
            sensorNodeFilter.value = currentSensor;
        }

        if (imageNodeFilter && Array.from(imageNodeFilter.options).some(opt => opt.value === currentImage)) {
            imageNodeFilter.value = currentImage;
        }
    }

    function refreshSourceDropdown() {
        if (!webSourceFilter) return;

        const currentSource = webSourceFilter.value || "all";

        webSourceFilter.innerHTML = `<option value="all">Semua Source</option>`;

        Array.from(sourceOptions)
            .sort((a, b) => a.localeCompare(b, "id"))
            .forEach((source) => {
                const option = document.createElement("option");
                option.value = source;
                option.textContent = source;
                webSourceFilter.appendChild(option);
            });

        if (Array.from(webSourceFilter.options).some(opt => opt.value === currentSource)) {
            webSourceFilter.value = currentSource;
        }
    }

    function buildSensorLog(docSnap) {
        const data = docSnap.data();
        registerNode(data);

        const status = normalizeStatus(
            data.water_status || data.status || statusFromWaterLevel(data.water_level)
        );

        return {
            id: docSnap.id,
            timeRaw: data.time || data.created_at,
            timeMs: parseTime(data.time || data.created_at),
            waktu: formatTime(data.time || data.created_at),
            node_id: normalizeNodeId(data.node_id),
            node: getNodeLabel(data),
            penempatan: normalizeText(data.penempatan),
            situs: normalizeText(data.situs || data.area),
            water_level: data.water_level ?? "-",
            water_flow: data.water_flow ?? "-",
            ombrometer: data.ombrometer ?? "-",
            anemometer: data.anemometer ?? "-",
            water_status: status,
            image_url: data.espcam_img_url || data.image_url || ""
        };
    }

    function buildWebLog(docSnap) {
        const data = docSnap.data();

        const source = normalizeText(data.source, "web_admin");
        sourceOptions.add(source);

        let details = data.details ?? "-";

        if (typeof details === "object") {
            try {
                details = JSON.stringify(details);
            } catch (error) {
                details = String(details);
            }
        }

        return {
            id: docSnap.id,
            action: normalizeText(data.action),
            admin_email: normalizeText(data.admin_email),
            created_at: data.created_at,
            timeMs: parseTime(data.created_at),
            waktu: formatTime(data.created_at),
            details: normalizeText(details),
            ip_address: normalizeText(data.ip_address),
            source: source,
            user_agent: normalizeText(data.user_agent)
        };
    }

    function renderWebLogs() {
        if (!webLogsContainer) return;

        const selectedSource = webSourceFilter ? webSourceFilter.value : "all";

        let rows = webLogs;

        if (selectedSource !== "all") {
            rows = rows.filter(row => row.source === selectedSource);
        }

        if (rows.length === 0) {
            webLogsContainer.innerHTML = `
                <div class="empty-box">
                    <i class="far fa-folder-open" style="font-size: 34px; margin-bottom: 12px;"></i>
                    <p>Aktivitas log web masih kosong.</p>
                </div>
            `;
            return;
        }

        let html = "";

        rows.slice(0, 100).forEach((row) => {
            html += `
                <div class="activity-card">
                    <div class="activity-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>

                    <div>
                        <div class="activity-title">${escapeHtml(row.action)}</div>

                        <div class="activity-meta">
                            <span><i class="far fa-clock"></i> ${escapeHtml(row.waktu)}</span>
                            <span><i class="far fa-user"></i> ${escapeHtml(row.admin_email)}</span>
                            <span><i class="fas fa-globe"></i> ${escapeHtml(row.source)}</span>
                            <span><i class="fas fa-network-wired"></i> ${escapeHtml(row.ip_address)}</span>
                        </div>

                        <div class="activity-detail">
                            ${escapeHtml(row.details)}
                        </div>

                        <div style="font-size: 11px; color: #64748b; margin-top: 8px; word-break: break-word;">
                            ${escapeHtml(row.user_agent)}
                        </div>
                    </div>
                </div>
            `;
        });

        webLogsContainer.innerHTML = html;
    }

    function renderSensorLogs() {
        if (!sensorLogsContainer) return;

        const selectedNode = sensorNodeFilter ? sensorNodeFilter.value : "all";
        const selectedStatus = sensorStatusFilter ? sensorStatusFilter.value : "all";

        let rows = sensorLogs;

        if (selectedNode !== "all") {
            rows = rows.filter(row => row.node_id === selectedNode);
        }

        if (selectedStatus !== "all") {
            rows = rows.filter(row => row.water_status === selectedStatus);
        }

        if (rows.length === 0) {
            sensorLogsContainer.innerHTML = `
                <div class="empty-box">
                    <i class="fas fa-filter" style="font-size: 28px; margin-bottom: 12px;"></i>
                    <p>Tidak ada log sensor sesuai filter.</p>
                </div>
            `;
            return;
        }

        let html = `
            <div class="logs-table-wrapper">
                <table class="logs-table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Node</th>
                            <th>Penempatan</th>
                            <th>Ketinggian</th>
                            <th>Arus</th>
                            <th>Curah Hujan</th>
                            <th>Angin</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        rows.slice(0, 100).forEach((row) => {
            const style = getStatusStyle(row.water_status);

            html += `
                <tr>
                    <td>${escapeHtml(row.waktu)}</td>
                    <td><strong>${escapeHtml(row.node)}</strong></td>
                    <td style="text-transform: capitalize;">${escapeHtml(row.penempatan)}</td>
                    <td>${escapeHtml(row.water_level)} cm</td>
                    <td>${escapeHtml(row.water_flow)} L/min</td>
                    <td>${escapeHtml(row.ombrometer)} mm/j</td>
                    <td>${escapeHtml(row.anemometer)} km/j</td>
                    <td>
                        <span class="status-badge" style="background: ${style.bg}; color: ${style.color};">
                            <i class="fas ${style.icon}"></i>
                            ${escapeHtml(row.water_status)}
                        </span>
                    </td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                </table>
            </div>
        `;

        sensorLogsContainer.innerHTML = html;
    }

    function renderImageLogs() {
        if (!imageLogsContainer) return;

        const selectedNode = imageNodeFilter ? imageNodeFilter.value : "all";

        let rows = sensorLogs.filter(row => row.image_url);

        if (selectedNode !== "all") {
            rows = rows.filter(row => row.node_id === selectedNode);
        }

        if (rows.length === 0) {
            imageLogsContainer.innerHTML = `
                <div class="empty-box" style="grid-column: 1 / -1;">
                    <i class="far fa-images" style="font-size: 34px; margin-bottom: 12px;"></i>
                    <p>Belum ada log gambar untuk filter ini.</p>
                </div>
            `;
            return;
        }

        let html = "";

        rows.slice(0, 80).forEach((row) => {
            html += `
                <div class="image-card">
                    <img src="${escapeHtml(row.image_url)}" alt="${escapeHtml(row.node)}" loading="lazy">

                    <div class="image-card-body">
                        <div class="image-title">${escapeHtml(row.node)}</div>
                        <div class="image-meta" style="text-transform: capitalize;">
                            ${escapeHtml(row.penempatan)}
                        </div>
                        <div class="image-meta">
                            ${escapeHtml(row.waktu)}
                        </div>
                    </div>
                </div>
            `;
        });

        imageLogsContainer.innerHTML = html;
    }

    function renderAll() {
        refreshNodeDropdowns();
        refreshSourceDropdown();
        renderWebLogs();
        renderSensorLogs();
        renderImageLogs();
    }

    function listenLogData() {
        const logDataRef = collection(db, "monitoring", "depok", "log_data");

        onSnapshot(logDataRef, (snapshot) => {
            const rows = [];

            nodeOptions = new Map();

            snapshot.forEach((docSnap) => {
                rows.push(buildSensorLog(docSnap));
            });

            rows.sort((a, b) => b.timeMs - a.timeMs);

            sensorLogs = rows;

            renderAll();
        }, (error) => {
            console.error("Gagal membaca log_data:", error);

            sensorLogsContainer.innerHTML = `
                <div class="empty-box" style="color: #b91c1c;">
                    <p>Gagal membaca log_data dari Firestore.</p>
                    <p>Cek console browser.</p>
                </div>
            `;
        });
    }

    function listenAdminAuditLogs() {
        const auditRef = collection(db, "monitoring", "depok", "admin_audit_logs");

        onSnapshot(auditRef, (snapshot) => {
            const rows = [];

            sourceOptions = new Set();

            snapshot.forEach((docSnap) => {
                rows.push(buildWebLog(docSnap));
            });

            rows.sort((a, b) => b.timeMs - a.timeMs);

            webLogs = rows;

            renderAll();
        }, (error) => {
            console.error("Gagal membaca admin_audit_logs:", error);

            webLogsContainer.innerHTML = `
                <div class="empty-box" style="color: #b91c1c;">
                    <p>Gagal membaca admin_audit_logs dari Firestore.</p>
                    <p>Cek console browser.</p>
                </div>
            `;
        });
    }

    if (webSourceFilter) {
        webSourceFilter.addEventListener("change", renderWebLogs);
    }

    if (sensorNodeFilter) {
        sensorNodeFilter.addEventListener("change", renderSensorLogs);
    }

    if (sensorStatusFilter) {
        sensorStatusFilter.addEventListener("change", renderSensorLogs);
    }

    if (imageNodeFilter) {
        imageNodeFilter.addEventListener("change", renderImageLogs);
    }

    window.exportCurrentLogCSV = function() {
        let rows = [];
        let filename = "log_deluvion.csv";
        let header = "";

        if (window.activeLogTab === "webLogs") {
            rows = webLogs;
            filename = `log_aktivitas_web_${Date.now()}.csv`;
            header = "Waktu;Action;Admin Email;Source;IP Address;Details;User Agent\n";

            let csv = "\uFEFF" + header;

            rows.forEach((row) => {
                csv += [
                    safeCsv(row.waktu),
                    safeCsv(row.action),
                    safeCsv(row.admin_email),
                    safeCsv(row.source),
                    safeCsv(row.ip_address),
                    safeCsv(row.details),
                    safeCsv(row.user_agent)
                ].join(";") + "\n";
            });

            downloadCSV(csv, filename);
            return;
        }

        if (window.activeLogTab === "sensorLogs") {
            rows = sensorLogs;
            filename = `log_sensor_${Date.now()}.csv`;
            header = "Waktu;Node;Node ID;Penempatan;Ketinggian;Arus;Curah Hujan;Angin;Status\n";

            let csv = "\uFEFF" + header;

            rows.forEach((row) => {
                csv += [
                    safeCsv(row.waktu),
                    safeCsv(row.node),
                    safeCsv(row.node_id),
                    safeCsv(row.penempatan),
                    safeCsv(row.water_level),
                    safeCsv(row.water_flow),
                    safeCsv(row.ombrometer),
                    safeCsv(row.anemometer),
                    safeCsv(row.water_status)
                ].join(";") + "\n";
            });

            downloadCSV(csv, filename);
            return;
        }

        if (window.activeLogTab === "imageLogs") {
            rows = sensorLogs.filter(row => row.image_url);
            filename = `log_gambar_${Date.now()}.csv`;
            header = "Waktu;Node;Node ID;Penempatan;Image URL\n";

            let csv = "\uFEFF" + header;

            rows.forEach((row) => {
                csv += [
                    safeCsv(row.waktu),
                    safeCsv(row.node),
                    safeCsv(row.node_id),
                    safeCsv(row.penempatan),
                    safeCsv(row.image_url)
                ].join(";") + "\n";
            });

            downloadCSV(csv, filename);
        }
    };

    function downloadCSV(csv, filename) {
        const blob = new Blob([csv], {
            type: "text/csv;charset=utf-8;"
        });

        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");

        link.href = url;
        link.download = filename;
        link.style.display = "none";

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        URL.revokeObjectURL(url);
    }

    onAuthStateChanged(auth, (user) => {
        if (!user) {
            window.location.href = @json(route('login'));
            return;
        }

        listenLogData();
        listenAdminAuditLogs();
    });
</script>
@endsection