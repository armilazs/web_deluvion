@extends('layouts.app')

@section('title', 'Kualitas Jaringan (QoS)')

@section('content')
<style>
    .qos-page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .qos-page-title {
        margin: 0;
        font-size: 24px;
        color: var(--text-primary);
    }

    .qos-page-subtitle {
        margin: 6px 0 0;
        color: var(--text-secondary);
        font-size: 14px;
    }

    .qos-filter-box {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 10px 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
    }

    .qos-filter-box label {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-secondary);
        white-space: nowrap;
    }

    .qos-filter-box select {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 14px;
        outline: none;
        min-width: 190px;
        background: #ffffff;
        color: var(--text-primary);
    }

    .qos-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .qos-card {
        background: white;
        border-radius: 12px;
        padding: 24px 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .qos-icon {
        font-size: 24px;
        color: var(--primary-blue);
        margin-bottom: 16px;
        background: #eff6ff;
        width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .qos-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
        font-family: monospace;
    }

    .qos-label {
        font-size: 13px;
        color: var(--text-secondary);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .qos-status {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .status-good {
        background: var(--success-color);
        box-shadow: 0 0 8px var(--success-color);
    }

    .status-warn {
        background: #eab308;
        box-shadow: 0 0 8px #eab308;
    }

    .status-bad {
        background: var(--danger-color);
        box-shadow: 0 0 8px var(--danger-color);
    }

    .status-neutral {
        background: #94a3b8;
        box-shadow: 0 0 8px #94a3b8;
    }

    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border-color);
    }

    .qos-table-header {
        padding: 24px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .qos-export-btn {
        width: auto;
        padding: 8px 16px;
        background: var(--bg-color);
        color: var(--primary-blue);
        border: 1px solid var(--primary-blue);
    }

    .qos-table-wrapper {
        overflow-x: auto;
    }

    .qos-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 14px;
        min-width: 900px;
    }

    .qos-table thead tr {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    .qos-table th {
        padding: 12px 24px;
        font-weight: 600;
        color: #475569;
        white-space: nowrap;
    }

    .qos-table td {
        padding: 12px 24px;
        white-space: nowrap;
    }

    @media (max-width: 992px) {
        .qos-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .qos-page-header {
            flex-direction: column;
            align-items: stretch;
        }

        .qos-filter-box {
            width: 100%;
            flex-direction: column;
            align-items: stretch;
        }

        .qos-filter-box select {
            width: 100%;
            min-width: 0;
        }

        .qos-grid {
            grid-template-columns: 1fr;
        }

        .qos-card {
            padding: 20px 14px;
        }

        .qos-value {
            font-size: 28px;
        }

        .qos-table-header {
            padding: 18px;
            align-items: stretch;
        }

        .qos-export-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div>
    <div class="qos-page-header">
        <div>
            <h2 class="section-title qos-page-title">Quality of Services (QoS)</h2>
            <p class="qos-page-subtitle">
            </p>
        </div>

        <div class="qos-filter-box">
            <label for="nodeFilter">Pilih Node</label>
            <select id="nodeFilter">
                <option value="all">Semua Node</option>
            </select>
        </div>
    </div>

    <div class="qos-grid">
        <div class="qos-card">
            <div class="qos-status status-neutral" id="status-throughput"></div>
            <div class="qos-icon"><i class="fas fa-tachometer-alt"></i></div>
            <div class="qos-value">
                <span id="val-throughput">0</span>
                <span style="font-size: 14px; color: var(--text-secondary);">Kbps</span>
            </div>
            <div class="qos-label">Throughput</div>
        </div>

        <div class="qos-card">
            <div class="qos-status status-neutral" id="status-delay"></div>
            <div class="qos-icon"><i class="fas fa-stopwatch"></i></div>
            <div class="qos-value">
                <span id="val-delay">0</span>
                <span style="font-size: 14px; color: var(--text-secondary);">ms</span>
            </div>
            <div class="qos-label">Delay (Latency)</div>
        </div>

        <div class="qos-card">
            <div class="qos-status status-neutral" id="status-jitter"></div>
            <div class="qos-icon"><i class="fas fa-wave-square"></i></div>
            <div class="qos-value">
                <span id="val-jitter">0</span>
                <span style="font-size: 14px; color: var(--text-secondary);">ms</span>
            </div>
            <div class="qos-label">Jitter</div>
        </div>

        <div class="qos-card">
            <div class="qos-status status-neutral" id="status-loss"></div>
            <div class="qos-icon"><i class="fas fa-box-open"></i></div>
            <div class="qos-value">
                <span id="val-loss">0</span>
                <span style="font-size: 14px; color: var(--text-secondary);">%</span>
            </div>
            <div class="qos-label">Packet Loss</div>
        </div>
    </div>

    <div class="chart-container" style="padding: 0;">
        <div class="qos-table-header">
            <div>
                <h3 class="section-title" style="margin: 0;">Riwayat Kualitas Jaringan</h3>
                <p id="qosSummaryText" style="margin: 6px 0 0; color: var(--text-secondary); font-size: 13px;">
                    Memuat data QoS...
                </p>
            </div>

            <button
                class="btn-primary qos-export-btn"
                type="button"
                onclick="exportQosToCSV()">
                <i class="fas fa-file-export"></i> Ekspor CSV
            </button>
        </div>

        <div class="qos-table-wrapper">
            <table class="qos-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Node</th>
                        <th>Penempatan</th>
                        <th>Throughput</th>
                        <th>Delay</th>
                        <th>Jitter</th>
                        <th>Packet Loss</th>
                        <th>Received</th>
                        <th>Lost</th>
                    </tr>
                </thead>

                <tbody id="qosTableBody">
                    <tr>
                        <td colspan="9" style="padding: 24px; text-align: center; color: var(--text-secondary);">
                            Memuat data QoS dari Firestore...
                        </td>
                    </tr>
                </tbody>
            </table>
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

    let allQosData = [];
    let allNodeOptions = new Map();
    let selectedNode = "all";

    const tableBody = document.getElementById("qosTableBody");
    const nodeFilter = document.getElementById("nodeFilter");
    const qosSummaryText = document.getElementById("qosSummaryText");

    function setText(id, value) {
        const element = document.getElementById(id);

        if (element) {
            element.textContent = value;
        }
    }

    function updateStatusDot(elementId, status) {
        const element = document.getElementById(elementId);

        if (element) {
            element.className = `qos-status status-${status}`;
        }
    }

    function toNumber(value, fallback = 0) {
        const numberValue = Number(value);

        if (Number.isFinite(numberValue)) {
            return numberValue;
        }

        return fallback;
    }

    function formatNumber(value, digit = 2) {
        const numberValue = toNumber(value, 0);

        return numberValue.toLocaleString("id-ID", {
            minimumFractionDigits: 0,
            maximumFractionDigits: digit
        });
    }

    function normalizeNodeId(value) {
        if (value === undefined || value === null || value === "") {
            return "unknown";
        }

        return String(value).trim();
    }

    function normalizeText(value, fallback = "-") {
        if (value === undefined || value === null || value === "") {
            return fallback;
        }

        return String(value);
    }

    function formatNodeLabel(row) {
        const nodeId = normalizeNodeId(row.node_id);
        const nodeName = row.node_name ? String(row.node_name).trim() : "";
        const penempatan = row.penempatan ? String(row.penempatan).trim() : "";
        const situs = row.situs || row.area ? String(row.situs || row.area).trim() : "";

        if (nodeName) {
            return nodeName;
        }

        let label = `Node ${nodeId}`;

        if (penempatan) {
            label += ` - ${penempatan.charAt(0).toUpperCase() + penempatan.slice(1)}`;
        }

        if (situs) {
            label += ` (${situs.toUpperCase()})`;
        }

        return label;
    }

    function parseTimeValue(value) {
        if (!value) {
            return 0;
        }

        if (typeof value === "object" && typeof value.toDate === "function") {
            return value.toDate().getTime();
        }

        if (typeof value === "object" && typeof value.seconds === "number") {
            return value.seconds * 1000;
        }

        const parsed = new Date(value).getTime();

        if (Number.isFinite(parsed)) {
            return parsed;
        }

        return 0;
    }

    function formatTime(value) {
        if (!value) {
            return "-";
        }

        let dateObject = null;

        if (typeof value === "object" && typeof value.toDate === "function") {
            dateObject = value.toDate();
        } else if (typeof value === "object" && typeof value.seconds === "number") {
            dateObject = new Date(value.seconds * 1000);
        } else {
            const parsed = new Date(value);

            if (!Number.isNaN(parsed.getTime())) {
                dateObject = parsed;
            }
        }

        if (!dateObject) {
            return String(value);
        }

        return dateObject.toLocaleString("id-ID", {
            day: "2-digit",
            month: "short",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit"
        });
    }

    function getThroughputKbps(row) {
        const throughputBps = toNumber(row.throughput_bps, null);

        if (throughputBps !== null) {
            return throughputBps / 1000;
        }

        const throughputKbps = toNumber(row.throughput_kbps, null);

        if (throughputKbps !== null) {
            return throughputKbps;
        }

        return 0;
    }

    function getPacketLossPercent(row) {
        const explicitLoss = row.packet_loss_percent ?? row.packet_loss;

        if (explicitLoss !== undefined && explicitLoss !== null && explicitLoss !== "") {
            return toNumber(explicitLoss, 0);
        }

        const received = toNumber(row.packet_received, 0);
        const lost = toNumber(row.packet_lost, 0);
        const total = received + lost;

        if (total <= 0) {
            return 0;
        }

        return (lost / total) * 100;
    }

    function getDelayMs(row) {
        return toNumber(row.delay_ms, 0);
    }

    function getJitterMs(row) {
        return toNumber(row.jitter_ms, 0);
    }

    function getPacketReceived(row) {
        return toNumber(row.packet_received, 0);
    }

    function getPacketLost(row) {
        return toNumber(row.packet_lost, 0);
    }

    function getFilteredQosData() {
        if (selectedNode === "all") {
            return allQosData;
        }

        return allQosData.filter(row => normalizeNodeId(row.node_id) === selectedNode);
    }

    function setEmptyMetricCards() {
        setText("val-throughput", "0");
        setText("val-delay", "0");
        setText("val-jitter", "0");
        setText("val-loss", "0");

        updateStatusDot("status-throughput", "neutral");
        updateStatusDot("status-delay", "neutral");
        updateStatusDot("status-jitter", "neutral");
        updateStatusDot("status-loss", "neutral");
    }

    function updateMetricCards(filteredData) {
        if (!filteredData.length) {
            setEmptyMetricCards();
            return;
        }

        const latest = filteredData[0];

        const throughput = getThroughputKbps(latest);
        const delay = getDelayMs(latest);
        const jitter = getJitterMs(latest);
        const loss = getPacketLossPercent(latest);

        setText("val-throughput", formatNumber(throughput, 2));
        setText("val-delay", formatNumber(delay, 2));
        setText("val-jitter", formatNumber(jitter, 2));
        setText("val-loss", formatNumber(loss, 2));

        updateStatusDot("status-throughput", throughput >= 100 ? "good" : (throughput >= 50 ? "warn" : "bad"));
        updateStatusDot("status-delay", delay < 150 ? "good" : (delay < 300 ? "warn" : "bad"));
        updateStatusDot("status-jitter", jitter < 75 ? "good" : (jitter < 125 ? "warn" : "bad"));
        updateStatusDot("status-loss", loss <= 1 ? "good" : (loss <= 5 ? "warn" : "bad"));
    }

    function createCell(value, style = "") {
        const td = document.createElement("td");
        td.textContent = value;
        td.setAttribute("style", style);
        return td;
    }

    function renderTable() {
        if (!tableBody) {
            return;
        }

        const filteredData = getFilteredQosData();

        tableBody.innerHTML = "";

        if (filteredData.length === 0) {
            const tr = document.createElement("tr");
            const td = document.createElement("td");

            td.colSpan = 9;
            td.textContent = selectedNode === "all" ?
                "Belum ada data QoS di Firestore." :
                "Belum ada data QoS untuk node yang dipilih.";

            td.setAttribute("style", "padding: 24px; text-align: center; color: var(--text-secondary);");

            tr.appendChild(td);
            tableBody.appendChild(tr);

            updateMetricCards([]);
            updateSummaryText(filteredData);
            return;
        }

        filteredData.slice(0, 100).forEach((row, index) => {
            const tr = document.createElement("tr");
            const bg = index % 2 === 0 ? "#ffffff" : "#f8fafc";

            tr.setAttribute("style", `border-bottom: 1px solid #e2e8f0; background: ${bg};`);

            tr.appendChild(createCell(formatTime(row.time || row.created_at), "padding: 12px 24px;"));
            tr.appendChild(createCell(formatNodeLabel(row), "padding: 12px 24px; font-weight: 600;"));
            tr.appendChild(createCell(normalizeText(row.penempatan), "padding: 12px 24px; text-transform: capitalize;"));
            tr.appendChild(createCell(`${formatNumber(getThroughputKbps(row), 2)} Kbps`, "padding: 12px 24px; font-weight: 500;"));
            tr.appendChild(createCell(`${formatNumber(getDelayMs(row), 2)} ms`, "padding: 12px 24px;"));
            tr.appendChild(createCell(`${formatNumber(getJitterMs(row), 2)} ms`, "padding: 12px 24px;"));
            tr.appendChild(createCell(`${formatNumber(getPacketLossPercent(row), 2)} %`, "padding: 12px 24px;"));
            tr.appendChild(createCell(formatNumber(getPacketReceived(row), 0), "padding: 12px 24px;"));
            tr.appendChild(createCell(formatNumber(getPacketLost(row), 0), "padding: 12px 24px;"));

            tableBody.appendChild(tr);
        });

        updateMetricCards(filteredData);
        updateSummaryText(filteredData);
    }

    function updateSummaryText(filteredData) {
        if (!qosSummaryText) {
            return;
        }

        const nodeLabel = selectedNode === "all" ?
            "semua node" :
            (allNodeOptions.get(selectedNode)?.label || `Node ${selectedNode}`);

        qosSummaryText.textContent = `Menampilkan ${filteredData.length} data QoS untuk ${nodeLabel}.`;
    }

    function refreshNodeDropdown() {
        if (!nodeFilter) {
            return;
        }

        const currentValue = nodeFilter.value || selectedNode;

        nodeFilter.innerHTML = "";

        const allOption = document.createElement("option");
        allOption.value = "all";
        allOption.textContent = "Semua Node";
        nodeFilter.appendChild(allOption);

        const sortedNodes = Array.from(allNodeOptions.values()).sort((a, b) => {
            return String(a.label).localeCompare(String(b.label), "id");
        });

        sortedNodes.forEach(node => {
            const option = document.createElement("option");
            option.value = node.id;
            option.textContent = node.label;
            nodeFilter.appendChild(option);
        });

        if (Array.from(nodeFilter.options).some(option => option.value === currentValue)) {
            nodeFilter.value = currentValue;
            selectedNode = currentValue;
        } else {
            nodeFilter.value = "all";
            selectedNode = "all";
        }
    }

    function registerNodeOption(data) {
        const nodeId = normalizeNodeId(data.node_id);

        if (nodeId === "unknown") {
            return;
        }

        if (!allNodeOptions.has(nodeId)) {
            allNodeOptions.set(nodeId, {
                id: nodeId,
                label: formatNodeLabel(data)
            });
        }
    }

    function safeCsv(value) {
        const text = String(value ?? "").replaceAll('"', '""');

        if (/^[=+\-@]/.test(text)) {
            return `"'${text}"`;
        }

        return `"${text}"`;
    }

    window.exportQosToCSV = function() {
        const filteredData = getFilteredQosData();

        if (filteredData.length === 0) {
            alert("Belum ada data QoS untuk diekspor.");
            return;
        }

        let csvContent = "\uFEFF";
        csvContent += "Waktu;Node;Penempatan;Throughput Kbps;Delay ms;Jitter ms;Packet Loss %;Packet Received;Packet Lost\n";

        filteredData.forEach(row => {
            csvContent += [
                safeCsv(formatTime(row.time || row.created_at)),
                safeCsv(formatNodeLabel(row)),
                safeCsv(normalizeText(row.penempatan)),
                safeCsv(formatNumber(getThroughputKbps(row), 2)),
                safeCsv(formatNumber(getDelayMs(row), 2)),
                safeCsv(formatNumber(getJitterMs(row), 2)),
                safeCsv(formatNumber(getPacketLossPercent(row), 2)),
                safeCsv(formatNumber(getPacketReceived(row), 0)),
                safeCsv(formatNumber(getPacketLost(row), 0))
            ].join(";") + "\n";
        });

        const blob = new Blob([csvContent], {
            type: "text/csv;charset=utf-8;"
        });

        const link = document.createElement("a");
        const url = URL.createObjectURL(blob);
        const fileNode = selectedNode === "all" ? "semua_node" : `node_${selectedNode}`;

        link.setAttribute("href", url);
        link.setAttribute("download", `log_qos_${fileNode}_${new Date().getTime()}.csv`);
        link.style.visibility = "hidden";

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        URL.revokeObjectURL(url);
    };

    if (nodeFilter) {
        nodeFilter.addEventListener("change", function() {
            selectedNode = this.value;
            renderTable();
        });
    }

    function listenQosData() {
        const qosRef = collection(db, "monitoring", "depok", "qos_log");

        onSnapshot(qosRef, (snapshot) => {
            const rows = [];

            snapshot.forEach((docSnap) => {
                const data = {
                    id: docSnap.id,
                    ...docSnap.data()
                };

                rows.push(data);
                registerNodeOption(data);
            });

            rows.sort((a, b) => {
                const timeA = parseTimeValue(a.time || a.created_at);
                const timeB = parseTimeValue(b.time || b.created_at);

                return timeB - timeA;
            });

            allQosData = rows;

            refreshNodeDropdown();
            renderTable();
        }, (error) => {
            console.error("Gagal membaca log_qos:", error);

            if (tableBody) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="9" style="padding: 24px; text-align: center; color: var(--danger-color);">
                            Gagal membaca data QoS dari Firestore. Cek console browser.
                        </td>
                    </tr>
                `;
            }

            setEmptyMetricCards();
        });
    }

    function listenLogDataForNodeOptions() {
        const logDataQuery = query(
            collection(db, "monitoring", "depok", "log_data"),
            limit(300)
        );

        onSnapshot(logDataQuery, (snapshot) => {
            snapshot.forEach((docSnap) => {
                registerNodeOption(docSnap.data());
            });

            refreshNodeDropdown();
        }, (error) => {
            console.warn("Gagal membaca log_data untuk dropdown node:", error);
        });
    }

    onAuthStateChanged(auth, (user) => {
        if (!user) {
            window.location.href = @json(route('login'));
            return;
        }

        listenLogDataForNodeOptions();
        listenQosData();
    });
</script>
@endsection