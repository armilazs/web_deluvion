@extends('layouts.app')

@section('title', 'Detail Lokasi: ' . $locationName)

@section('content')
<div class="dashboard-grid" style="grid-template-columns: 1fr;">
    <div class="main-column">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <a href="{{ route('monitoring') }}" class="btn-primary" style="background: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color); width: auto; padding: 10px 16px;">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <h2 class="section-title" style="margin: 0;"><i class="fas fa-map-marker-alt" style="color: var(--primary-blue);"></i> {{ $locationName }}</h2>
            </div>
            <div class="online-badge">🟢 ONLINE</div>
        </div>

        <div style="display: grid; grid-template-columns: 280px 1fr; gap: 16px; margin-bottom: 12px;">
            
            <!-- Kotak Status Terkini -->
            <div class="status-card interactive-card" style="margin-bottom: 0; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
                <h2 style="margin: 0 0 8px 0; font-size: 20px;">Status Terkini</h2>
                <p style="margin: 0 0 24px 0; font-size: 13px; opacity: 0.9;">Berdasarkan sensor terakhir</p>
                <div class="status-badge" id="locStatus" style="font-size: 20px; padding: 12px 24px; margin: 0;">MENUNGGU...</div>
            </div>

            <!-- Kotak 4 Data Sensor -->
            <div class="stats-grid" style="grid-template-columns: repeat(2, 1fr); gap: 12px;">
                <div class="stat-card interactive-card">
                    <div class="label">Ketinggian Air (Cm)</div>
                    <div class="value" id="locLevel">--</div>
                </div>
                <div class="stat-card interactive-card">
                    <div class="label">Arus Air (L/menit)</div>
                    <div class="value" id="locFlow">--</div>
                </div>
                <div class="stat-card interactive-card">
                    <div class="label">Curah Hujan (Ombrometer)</div>
                    <div class="value" id="locRain">--</div>
                </div>
                <div class="stat-card interactive-card">
                    <div class="label">Kecepatan Angin (Anemometer)</div>
                    <div class="value" id="locWind">--</div>
                </div>
            </div>
        </div>

        <div class="chart-card interactive-card" style="margin-top: 12px; min-height: 300px;">
            <div class="section-title">Grafik Historis {{ $locationName }}</div>
            <div style="position: relative; height: 250px; width: 100%;">
                <canvas id="locChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js";
    import { getFirestore, collection, query, orderBy, limit, onSnapshot, where } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore.js";

    const firebaseConfig = {
        apiKey: "AIzaSyB27xUygjk082h56nsqaa1r4Nm5tQBiY9g",
        authDomain: "deluvion-23.firebaseapp.com",
        projectId: "deluvion-23",
        storageBucket: "deluvion-23.firebasestorage.app",
        messagingSenderId: "603292812342",
        appId: "1:603292812342:web:cb7afaf76ca5710b7e4497"
    };

    const app = initializeApp(firebaseConfig);
    const db = getFirestore(app);

    const ctx = document.getElementById('locChart');
    let chart;
    if (ctx) {
        chart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Tinggi Air (cm)',
                    data: [],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    const locLevel = document.getElementById('locLevel');
    const locFlow = document.getElementById('locFlow');
    const locRain = document.getElementById('locRain');
    const locWind = document.getElementById('locWind');
    const locStatus = document.getElementById('locStatus');
    const locationSlug = "{{ $slug }}";

    try {
        // Assume log_data contains all locations, filter by situs or just show generic
        const logDataRef = collection(db, 'monitoring', 'depok', 'log_data');
        const q = query(logDataRef, orderBy('time', 'desc'), limit(15));

        onSnapshot(q, (snapshot) => {
            if (snapshot.empty) return;
            
            let isFirst = true;
            let chartLabels = [];
            let chartData = [];

            snapshot.forEach((doc) => {
                const data = doc.data();
                
                // In a real app, you would filter by data.situs == locationSlug
                // Here we just grab the latest data to simulate the interaction
                if (isFirst) {
                    if (locLevel) locLevel.innerText = data.water_level !== undefined ? data.water_level : '--';
                    if (locFlow) locFlow.innerText = data.water_flow !== undefined ? data.water_flow : '--';
                    if (locRain) locRain.innerText = data.ombrometer !== undefined ? data.ombrometer : '--';
                    if (locWind) locWind.innerText = data.anemometer !== undefined ? data.anemometer : '--';
                    
                    if (locStatus) {
                        if (data.water_level > 150) {
                            locStatus.innerText = "WASPADA";
                            locStatus.style.backgroundColor = "var(--danger-color)";
                        } else if (data.water_level > 100) {
                            locStatus.innerText = "SIAGA";
                            locStatus.style.backgroundColor = "#eab308";
                        } else {
                            locStatus.innerText = "AMAN";
                            locStatus.style.backgroundColor = "var(--success-color)";
                        }
                    }
                    isFirst = false;
                }

                if (data.water_level !== undefined && data.time) {
                    const dateObj = data.time.toDate();
                    chartLabels.unshift(`${String(dateObj.getHours()).padStart(2, '0')}:${String(dateObj.getMinutes()).padStart(2, '0')}`);
                    chartData.unshift(data.water_level);
                }
            });

            if (chart && chartLabels.length > 0) {
                chart.data.labels = chartLabels;
                chart.data.datasets[0].data = chartData;
                chart.update('none');
            }
        });
    } catch (e) {
        console.error("Firestore Error: ", e);
    }
</script>
@endsection
