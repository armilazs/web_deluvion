@extends('layouts.app')

@section('title', 'Kualitas Jaringan (QoS)')

@section('content')
<style>
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

    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border-color);
    }

    @media (max-width: 992px) {
        .qos-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .qos-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div>
    <div style="margin-bottom: 24px;">
        <h2 class="section-title" style="margin: 0; font-size: 24px;">Quality of Services (QoS)</h2>
    </div>

    <!-- Metrik Grid -->
    <div class="qos-grid">
        <div class="qos-card">
            <div class="qos-status status-good" id="status-throughput"></div>
            <div class="qos-icon"><i class="fas fa-tachometer-alt"></i></div>
            <div class="qos-value">
                <span id="val-throughput">0</span>
                <span style="font-size: 14px; color: var(--text-secondary);">Kbps</span>
            </div>
            <div class="qos-label">Throughput</div>
        </div>

        <div class="qos-card">
            <div class="qos-status status-good" id="status-delay"></div>
            <div class="qos-icon"><i class="fas fa-stopwatch"></i></div>
            <div class="qos-value">
                <span id="val-delay">0</span>
                <span style="font-size: 14px; color: var(--text-secondary);">ms</span>
            </div>
            <div class="qos-label">Delay (Latency)</div>
        </div>

        <div class="qos-card">
            <div class="qos-status status-good" id="status-jitter"></div>
            <div class="qos-icon"><i class="fas fa-wave-square"></i></div>
            <div class="qos-value">
                <span id="val-jitter">0</span>
                <span style="font-size: 14px; color: var(--text-secondary);">ms</span>
            </div>
            <div class="qos-label">Jitter</div>
        </div>

        <div class="qos-card">
            <div class="qos-status status-good" id="status-loss"></div>
            <div class="qos-icon"><i class="fas fa-box-open"></i></div>
            <div class="qos-value">
                <span id="val-loss">0</span>
                <span style="font-size: 14px; color: var(--text-secondary);">%</span>
            </div>
            <div class="qos-label">Packet Loss</div>
        </div>
    </div>

    <!-- Tabel Riwayat -->
    <div class="chart-container" style="padding: 0;">
        <div style="padding: 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 class="section-title" style="margin: 0;">Riwayat Kualitas Jaringan</h3>

            <button
                class="btn-primary"
                type="button"
                onclick="exportQosToCSV()"
                style="width: auto; padding: 8px 16px; background: var(--bg-color); color: var(--primary-blue); border: 1px solid var(--primary-blue);">
                <i class="fas fa-file-export"></i> Ekspor CSV
            </button>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 12px 24px; font-weight: 600; color: #475569;">Waktu</th>
                        <th style="padding: 12px 24px; font-weight: 600; color: #475569;">Throughput</th>
                        <th style="padding: 12px 24px; font-weight: 600; color: #475569;">Delay</th>
                        <th style="padding: 12px 24px; font-weight: 600; color: #475569;">Jitter</th>
                        <th style="padding: 12px 24px; font-weight: 600; color: #475569;">Packet Loss</th>
                    </tr>
                </thead>

                <tbody id="qosTableBody">
                    <tr>
                        <td colspan="5" style="padding: 24px; text-align: center; color: var(--text-secondary);">
                            Memuat simulasi QoS...
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

    let qosInterval = null;
    let tableData = [];

    const tableBody = document.getElementById("qosTableBody");

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

        tableBody.innerHTML = "";

        if (tableData.length === 0) {
            const tr = document.createElement("tr");
            const td = document.createElement("td");

            td.colSpan = 5;
            td.textContent = "Belum ada data QoS.";
            td.setAttribute("style", "padding: 24px; text-align: center; color: var(--text-secondary);");

            tr.appendChild(td);
            tableBody.appendChild(tr);
            return;
        }

        tableData.forEach((row, index) => {
            const tr = document.createElement("tr");
            const bg = index % 2 === 0 ? "#ffffff" : "#f8fafc";

            tr.setAttribute("style", `border-bottom: 1px solid #e2e8f0; background: ${bg};`);

            tr.appendChild(createCell(row.time, "padding: 12px 24px;"));
            tr.appendChild(createCell(row.throughput, "padding: 12px 24px; font-weight: 500;"));
            tr.appendChild(createCell(row.delay, "padding: 12px 24px;"));
            tr.appendChild(createCell(row.jitter, "padding: 12px 24px;"));
            tr.appendChild(createCell(row.loss, "padding: 12px 24px;"));

            tableBody.appendChild(tr);
        });
    }

    function simulateQoS() {
        const throughput = Math.floor(Math.random() * (280 - 120 + 1)) + 120;

        let delay = Math.floor(Math.random() * (60 - 20 + 1)) + 20;

        if (Math.random() > 0.95) {
            delay += Math.floor(Math.random() * 100);
        }

        const jitter = Math.floor(Math.random() * (12 - 2 + 1)) + 2;

        let loss = 0;

        if (Math.random() > 0.85) {
            loss = parseFloat((Math.random() * 1.5).toFixed(1));
        }

        setText("val-throughput", throughput);
        setText("val-delay", delay);
        setText("val-jitter", jitter);
        setText("val-loss", loss);

        updateStatusDot("status-throughput", throughput > 150 ? "good" : "warn");
        updateStatusDot("status-delay", delay < 50 ? "good" : (delay < 100 ? "warn" : "bad"));
        updateStatusDot("status-jitter", jitter < 10 ? "good" : "warn");
        updateStatusDot("status-loss", loss === 0 ? "good" : (loss < 1 ? "warn" : "bad"));

        const dateTimeStr = new Date().toLocaleString("id-ID", {
            day: "2-digit",
            month: "short",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit"
        });

        tableData.unshift({
            time: dateTimeStr,
            throughput: throughput + " Kbps",
            delay: delay + " ms",
            jitter: jitter + " ms",
            loss: loss + " %"
        });

        if (tableData.length > 50) {
            tableData.pop();
        }

        renderTable();
    }

    function safeCsv(value) {
        const text = String(value ?? "").replaceAll('"', '""');

        if (/^[=+\-@]/.test(text)) {
            return `"'${text}"`;
        }

        return `"${text}"`;
    }

    window.exportQosToCSV = function() {
        if (tableData.length === 0) {
            alert("Belum ada data riwayat jaringan.");
            return;
        }

        let csvContent = "\uFEFF";
        csvContent += "Waktu;Throughput;Delay;Jitter;Packet Loss\n";

        tableData.forEach(row => {
            csvContent += [
                safeCsv(row.time),
                safeCsv(row.throughput),
                safeCsv(row.delay),
                safeCsv(row.jitter),
                safeCsv(row.loss)
            ].join(";") + "\n";
        });

        const blob = new Blob([csvContent], {
            type: "text/csv;charset=utf-8;"
        });

        const link = document.createElement("a");
        const url = URL.createObjectURL(blob);

        link.setAttribute("href", url);
        link.setAttribute("download", "log_qos_" + new Date().getTime() + ".csv");
        link.style.visibility = "hidden";

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        URL.revokeObjectURL(url);
    };

    function startQosSimulation() {
        if (qosInterval) {
            clearInterval(qosInterval);
        }

        simulateQoS();
        qosInterval = setInterval(simulateQoS, 2500);
    }

    onAuthStateChanged(auth, (user) => {
        if (!user) {
            window.location.href = @json(route('login'));
            return;
        }

        startQosSimulation();
    });

    window.addEventListener("beforeunload", () => {
        if (qosInterval) {
            clearInterval(qosInterval);
        }
    });
</script>
@endsection