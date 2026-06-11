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

                <h2 class="section-title" style="margin: 0;">
                    <i class="fas fa-map-marker-alt" style="color: var(--primary-blue);"></i>
                    {{ $locationName }}
                </h2>
            </div>

            <div class="online-badge" id="locationConnectionBadge">🟡 MEMUAT</div>
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
        where
    } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore.js";

    const firebaseConfig = {
        apiKey: "AIzaSyB27xUygjk082h56nsqaa1r4Nm5tQBiY9g",
        authDomain: "deluvion-23.firebaseapp.com",
        projectId: "deluvion-23",
        storageBucket: "deluvion-23.firebasestorage.app",
        messagingSenderId: "603292812342",
        appId: "1:603292812342:web:cb7afaf76ca5710b7e4497"
    };

    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);
    const db = getFirestore(app);

    const locLevel = document.getElementById("locLevel");
    const locFlow = document.getElementById("locFlow");
    const locRain = document.getElementById("locRain");
    const locWind = document.getElementById("locWind");
    const locStatus = document.getElementById("locStatus");
    const connectionBadge = document.getElementById("locationConnectionBadge");

    const locationSlug = @json($slug);

    const ctx = document.getElementById("locChart");
    let chart = null;
    let unsubscribeLogs = null;

    if (ctx) {
        chart = new Chart(ctx.getContext("2d"), {
            type: "line",
            data: {
                labels: [],
                datasets: [{
                    label: "Tinggi Air (cm)",
                    data: [],
                    borderColor: "#2563eb",
                    backgroundColor: "rgba(37, 99, 235, 0.1)",
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    function setConnectionStatus(status) {
        if (!connectionBadge) {
            return;
        }

        if (status === "online") {
            connectionBadge.innerText = "🟢 ONLINE";
        } else if (status === "offline") {
            connectionBadge.innerText = "🔴 OFFLINE";
        } else {
            connectionBadge.innerText = "🟡 MEMUAT";
        }
    }

    function setStatusBadge(waterLevel) {
        if (!locStatus) {
            return;
        }

        const level = Number(waterLevel);

        if (Number.isNaN(level)) {
            locStatus.innerText = "TIDAK ADA DATA";
            locStatus.style.backgroundColor = "#94a3b8";
            return;
        }

        if (level > 150) {
            locStatus.innerText = "WASPADA";
            locStatus.style.backgroundColor = "var(--danger-color)";
        } else if (level > 100) {
            locStatus.innerText = "SIAGA";
            locStatus.style.backgroundColor = "#eab308";
        } else {
            locStatus.innerText = "AMAN";
            locStatus.style.backgroundColor = "var(--success-color)";
        }
    }

    function formatTime(value) {
        try {
            let dateObj = null;

            if (value && typeof value.toDate === "function") {
                dateObj = value.toDate();
            } else if (typeof value === "string") {
                dateObj = new Date(value.replace(" ", "T"));
            } else {
                return "--:--";
            }

            if (Number.isNaN(dateObj.getTime())) {
                return "--:--";
            }

            return `${String(dateObj.getHours()).padStart(2, "0")}:${String(dateObj.getMinutes()).padStart(2, "0")}`;
        } catch (error) {
            return "--:--";
        }
    }

    function updateLatestData(data) {
        if (locLevel) {
            locLevel.innerText = data.water_level !== undefined ? data.water_level : "--";
        }

        if (locFlow) {
            locFlow.innerText = data.water_flow !== undefined ? data.water_flow : "--";
        }

        if (locRain) {
            locRain.innerText = data.ombrometer !== undefined ? data.ombrometer : "--";
        }

        if (locWind) {
            locWind.innerText = data.anemometer !== undefined ? data.anemometer : "--";
        }

        setStatusBadge(data.water_level);
    }

    function startLocationListener() {
        const logDataRef = collection(db, "monitoring", "depok", "log_data");

        /*
            Jika field situs di Firestore isinya sama dengan slug lokasi,
            query ini akan membaca data sesuai lokasi.
            Kalau field situs belum konsisten, fallback di catch akan membaca data terbaru umum.
        */
        let q = query(
            logDataRef,
            where("situs", "==", locationSlug),
            orderBy("time", "desc"),
            limit(15)
        );

        unsubscribeLogs = onSnapshot(q, (snapshot) => {
            if (snapshot.empty) {
                setConnectionStatus("offline");
                setStatusBadge(null);

                if (chart) {
                    chart.data.labels = [];
                    chart.data.datasets[0].data = [];
                    chart.update("none");
                }

                return;
            }

            setConnectionStatus("online");

            let isFirst = true;
            const chartLabels = [];
            const chartData = [];

            snapshot.forEach((docSnap) => {
                const data = docSnap.data();

                if (isFirst) {
                    updateLatestData(data);
                    isFirst = false;
                }

                if (data.water_level !== undefined && data.time) {
                    chartLabels.unshift(formatTime(data.time));
                    chartData.unshift(Number(data.water_level));
                }
            });

            if (chart && chartLabels.length > 0) {
                chart.data.labels = chartLabels;
                chart.data.datasets[0].data = chartData;
                chart.update("none");
            }
        }, (error) => {
            console.error("Firestore listener error:", error);
            setConnectionStatus("offline");
            setStatusBadge(null);
        });
    }

    onAuthStateChanged(auth, (user) => {
        if (!user) {
            setConnectionStatus("offline");
            window.location.href = @json(route('login'));
            return;
        }

        setConnectionStatus("loading");
        startLocationListener();
    });

    window.addEventListener("beforeunload", () => {
        if (typeof unsubscribeLogs === "function") {
            unsubscribeLogs();
        }
    });
</script>
@endsection