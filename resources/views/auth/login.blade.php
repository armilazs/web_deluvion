<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
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
                <div id="errorMessage" style="display: none; background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; text-align: center;"></div>
                <div class="form-group">
                    <label for="email">Email Admin</label>
                    <input type="email" id="email" class="form-control" placeholder="admin@dlvn.com" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div style="position: relative;">
                        <input type="password" id="password" class="form-control" placeholder="••••••••" required style="padding-right: 40px;">
                        <i class="fas fa-eye" id="togglePassword" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-secondary);"></i>
                    </div>
                </div>
                <button type="submit" class="btn-primary" id="loginBtn">Masuk</button>
            </form>
        </div>
    </div>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js";
        import { getAuth, signInWithEmailAndPassword } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-auth.js";

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

        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const errorMessage = document.getElementById('errorMessage');
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function (e) {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });

        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            loginBtn.innerText = "Memverifikasi...";
            loginBtn.disabled = true;
            errorMessage.style.display = "none";

            signInWithEmailAndPassword(auth, email, password)
                .then((userCredential) => {
                    // Login berhasil, redirect ke dashboard
                    window.location.href = "{{ route('monitoring') }}";
                })
                .catch((error) => {
                    const errorCode = error.code;
                    const errorMsg = error.message;
                    let msg = "Terjadi kesalahan: " + errorCode + " - " + errorMsg;
                    if (errorCode === 'auth/invalid-credential' || errorCode === 'auth/wrong-password' || errorCode === 'auth/user-not-found') {
                        msg = "Email atau password salah! (Cek kembali ketikan Anda)";
                    } else if (errorCode === 'auth/too-many-requests') {
                        msg = "Terlalu banyak percobaan. Coba lagi nanti.";
                    }
                    errorMessage.innerText = msg;
                    errorMessage.style.display = "block";
                    loginBtn.innerText = "Masuk";
                    loginBtn.disabled = false;
                });
        });
    </script>
</body>
</html>
