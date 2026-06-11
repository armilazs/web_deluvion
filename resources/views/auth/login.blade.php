<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | DL-VN Flood Monitoring</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">DL&middot;VN</div>
            <p class="login-subtitle">Sistem Monitoring dan Mitigasi Banjir</p>

            <form id="loginForm">
                <div
                    id="errorMessage"
                    style="display: none; background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; text-align: center;">
                </div>

                <div class="form-group">
                    <label for="email">Email Admin</label>
                    <input
                        type="email"
                        id="email"
                        class="form-control"
                        placeholder="admin@dlvn.com"
                        autocomplete="email"
                        required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>

                    <div style="position: relative;">
                        <input
                            type="password"
                            id="password"
                            class="form-control"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required
                            style="padding-right: 40px;">

                        <i
                            class="fas fa-eye"
                            id="togglePassword"
                            style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-secondary);">
                        </i>
                    </div>
                </div>

                <button type="submit" class="btn-primary" id="loginBtn">Masuk</button>
            </form>
        </div>
    </div>

    <script type="module">
        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js";

        import {
            getAuth,
            signInWithEmailAndPassword
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

        const loginForm = document.getElementById("loginForm");
        const loginBtn = document.getElementById("loginBtn");
        const errorMessage = document.getElementById("errorMessage");
        const togglePassword = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("password");

        togglePassword.addEventListener("click", function() {
            const isPassword = passwordInput.getAttribute("type") === "password";

            passwordInput.setAttribute("type", isPassword ? "text" : "password");

            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });

        function showError(message) {
            errorMessage.innerText = message;
            errorMessage.style.display = "block";
        }

        function setLoading(isLoading) {
            loginBtn.innerText = isLoading ? "Memverifikasi..." : "Masuk";
            loginBtn.disabled = isLoading;
        }

        loginForm.addEventListener("submit", async function(e) {
            e.preventDefault();

            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value;

            errorMessage.style.display = "none";
            setLoading(true);

            try {
                const userCredential = await signInWithEmailAndPassword(auth, email, password);

                const idToken = await userCredential.user.getIdToken();

                const response = await fetch("{{ route('firebase.login') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content")
                    },
                    body: JSON.stringify({
                        idToken: idToken
                    })
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(result.message || "Login gagal diproses oleh server.");
                }

                window.location.href = result.redirect;
            } catch (error) {
                const errorCode = error.code || "";
                const errorMsg = error.message || "Login gagal.";

                let msg = errorMsg;

                if (
                    errorCode === "auth/invalid-credential" ||
                    errorCode === "auth/wrong-password" ||
                    errorCode === "auth/user-not-found"
                ) {
                    msg = "Email atau password salah. Cek kembali email dan password Anda.";
                } else if (errorCode === "auth/too-many-requests") {
                    msg = "Terlalu banyak percobaan login. Coba lagi nanti.";
                } else if (errorCode === "auth/network-request-failed") {
                    msg = "Koneksi bermasalah. Periksa internet lalu coba lagi.";
                }

                showError(msg);
                setLoading(false);
            }
        });
    </script>
</body>

</html>