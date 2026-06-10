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
    .status-good { background: var(--success-color); box-shadow: 0 0 8px var(--success-color); }
    .status-warn { background: #eab308; box-shadow: 0 0 8px #eab308; }
    .status-bad { background: var(--danger-color); box-shadow: 0 0 8px var(--danger-color); }

    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border-color);
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
            <div class="qos-value"><span id="val-throughput">0</span> <span style="font-size: 14px; color: var(--text-secondary);">Kbps</span></div>
            <div class="qos-label">Throughput</div>
        </div>
        
        <div class="qos-card">
            <div class="qos-status status-good" id="status-delay"></div>
            <div class="qos-icon"><i class="fas fa-stopwatch"></i></div>
            <div class="qos-value"><span id="val-delay">0</span> <span style="font-size: 14px; color: var(--text-secondary);">ms</span></div>
            <div class="qos-label">Delay (Latency)</div>
        </div>

        <div class="qos-card">
            <div class="qos-status status-good" id="status-jitter"></div>
            <div class="qos-icon"><i class="fas fa-wave-square"></i></div>
            <div class="qos-value"><span id="val-jitter">0</span> <span style="font-size: 14px; color: var(--text-secondary);">ms</span></div>
            <div class="qos-label">Jitter</div>
        </div>

        <div class="qos-card">
            <div class="qos-status status-good" id="status-loss"></div>
            <div class="qos-icon"><i class="fas fa-box-open"></i></div>
            <div class="qos-value"><span id="val-loss">0</span> <span style="font-size: 14px; color: var(--text-secondary);">%</span></div>
            <div class="qos-label">Packet Loss</div>
        </div>
    </div>

    <!-- Tabel Riwayat -->
    <div class="chart-container" style="padding: 0;">
        <div style="padding: 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 class="section-title" style="margin: 0;">Riwayat Kualitas Jaringan</h3>
            <button class="btn-primary" onclick="exportQosToCSV()" style="width: auto; padding: 8px 16px; background: var(--bg-color); color: var(--primary-blue); border: 1px solid var(--primary-blue);">
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
                    <!-- Data will be populated by JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let tableData = [];
        const tableBody = document.getElementById('qosTableBody');

        // Simulasi QoS murni di Javascript (Tanpa Pull Database)
        function simulateQoS() {
            // Throughput: 120 - 280 Kbps
            const throughput = Math.floor(Math.random() * (280 - 120 + 1)) + 120;
            
            // Delay: 20 - 60 ms
            let delay = Math.floor(Math.random() * (60 - 20 + 1)) + 20;
            if (Math.random() > 0.95) delay += Math.floor(Math.random() * 100); // Rare spike

            // Jitter: 2 - 12 ms
            const jitter = Math.floor(Math.random() * (12 - 2 + 1)) + 2;

            // Packet Loss: 0% - 1.5%
            let loss = 0;
            if (Math.random() > 0.85) loss = parseFloat((Math.random() * 1.5).toFixed(1));

            // Update UI Angka
            document.getElementById('val-throughput').innerText = throughput;
            document.getElementById('val-delay').innerText = delay;
            document.getElementById('val-jitter').innerText = jitter;
            document.getElementById('val-loss').innerText = loss;

            // Update Status Lampu
            updateStatusDot('status-throughput', throughput > 150 ? 'good' : 'warn');
            updateStatusDot('status-delay', delay < 50 ? 'good' : (delay < 100 ? 'warn' : 'bad'));
            updateStatusDot('status-jitter', jitter < 10 ? 'good' : 'warn');
            updateStatusDot('status-loss', loss === 0 ? 'good' : (loss < 1 ? 'warn' : 'bad'));

            // Update Table
            const dateTimeStr = new Date().toLocaleString('id-ID', {
                day: '2-digit', month: 'short', year: 'numeric',
                hour: '2-digit', minute: '2-digit', second: '2-digit'
            });
            
            tableData.unshift({
                time: dateTimeStr,
                throughput: throughput + " Kbps",
                delay: delay + " ms",
                jitter: jitter + " ms",
                loss: loss + " %"
            });

            if (tableData.length > 50) { // Keep up to 50 logs for exporting
                tableData.pop();
            }

            renderTable();
        }

        function renderTable() {
            let htmlStr = '';
            tableData.forEach((row, index) => {
                const bg = index % 2 === 0 ? 'background: #ffffff;' : 'background: #f8fafc;';
                htmlStr += `
                    <tr style="border-bottom: 1px solid #e2e8f0; ${bg}">
                        <td style="padding: 12px 24px;">${row.time}</td>
                        <td style="padding: 12px 24px; font-weight: 500;">${row.throughput}</td>
                        <td style="padding: 12px 24px;">${row.delay}</td>
                        <td style="padding: 12px 24px;">${row.jitter}</td>
                        <td style="padding: 12px 24px;">${row.loss}</td>
                    </tr>
                `;
            });
            tableBody.innerHTML = htmlStr;
        }

        function updateStatusDot(elementId, status) {
            const el = document.getElementById(elementId);
            if(el) el.className = `qos-status status-${status}`;
        }

        window.exportQosToCSV = function() {
            if (tableData.length === 0) {
                alert("Belum ada data riwayat jaringan.");
                return;
            }

            let csvContent = "Waktu,Throughput,Delay,Jitter,Packet Loss\n";
            
            tableData.forEach(row => {
                csvContent += `"${row.time}","${row.throughput}","${row.delay}","${row.jitter}","${row.loss}"\n`;
            });

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", "log_qos_" + new Date().getTime() + ".csv");
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };

        // Jalankan simulasi setiap 2.5 detik
        setInterval(simulateQoS, 2500);
        simulateQoS();
    });
</script>
@endsection
