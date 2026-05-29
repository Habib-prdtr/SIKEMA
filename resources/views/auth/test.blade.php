<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKEMA - Auth Test & Theme Playground</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --primary-light: #d1fae5;
            
            /* Theme Variables (Light Default) */
            --bg-color: #f0fdf4;
            --bg-pattern: radial-gradient(circle at center, rgba(16, 185, 129, 0.04) 2px, transparent 2px);
            --card-bg: rgba(255, 255, 255, 0.82);
            --card-border: rgba(255, 255, 255, 0.7);
            --card-shadow: rgba(0, 0, 0, 0.04);
            --text-color: #1e293b;
            --text-muted: #64748b;
            --text-title: #0f172a;
            --input-bg: rgba(255, 255, 255, 0.9);
            --input-border: #e2e8f0;
            --diag-item-bg: rgba(248, 250, 252, 0.6);
            --console-bg: #0f172a;
            --console-border: #1e293b;
        }

        [data-theme="dark"] {
            --bg-color: #060d0a;
            --bg-pattern: radial-gradient(circle at center, rgba(16, 185, 129, 0.08) 2px, transparent 2px);
            --card-bg: rgba(15, 23, 42, 0.75);
            --card-border: rgba(30, 41, 59, 0.6);
            --card-shadow: rgba(0, 0, 0, 0.4);
            --text-color: #cbd5e1;
            --text-muted: #94a3b8;
            --text-title: #f8fafc;
            --input-bg: rgba(30, 41, 59, 0.7);
            --input-border: #334155;
            --diag-item-bg: rgba(30, 41, 59, 0.4);
            --console-bg: #020617;
            --console-border: #1e293b;
        }

        * {
            font-family: 'Poppins', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease, transform 0.25s ease, box-shadow 0.25s ease;
        }

        body {
            background-color: var(--bg-color);
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(16, 185, 129, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(5, 150, 105, 0.05) 0%, transparent 40%),
                var(--bg-pattern);
            background-size: 100% 100%, 100% 100%, 60px 60px;
            min-height: 100vh;
            color: var(--text-color);
            padding: 40px 20px;
        }

        .test-container {
            max-width: 1100px;
            margin: 0 auto;
        }

        /* Glassmorphism Header */
        .glass-header {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 24px 35px;
            box-shadow: 0 10px 30px var(--card-shadow);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .logo-container {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.25);
            color: white;
            font-size: 26px;
        }

        .brand-title {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: var(--text-title);
            letter-spacing: -0.5px;
        }

        .brand-subtitle {
            margin: 0;
            font-size: 13px;
            color: var(--primary-dark);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Glassmorphism Cards */
        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px var(--card-shadow);
            height: 100%;
        }

        .glass-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.3);
        }

        .card-title-custom {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-title);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title-custom i {
            color: var(--primary);
            font-size: 20px;
        }

        /* Diagnostic Badge */
        .diag-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            border-radius: 12px;
            background: var(--diag-item-bg);
            margin-bottom: 12px;
            border: 1px solid var(--card-border);
        }

        .diag-item:last-child {
            margin-bottom: 0;
        }

        .diag-label {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-color);
        }

        .badge-status {
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .badge-success-custom {
            background-color: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-warning-custom {
            background-color: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        /* Forms Styling */
        .form-label-custom {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-color);
            margin-bottom: 8px;
        }

        .form-control-custom {
            height: 50px;
            border-radius: 12px;
            border: 1px solid var(--input-border);
            padding: 0 16px;
            font-size: 14px;
            background: var(--input-bg);
            color: var(--text-color);
        }

        .form-control-custom::placeholder {
            color: var(--text-muted);
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
            background: var(--input-bg);
            color: var(--text-color);
        }

        .input-group-text-custom {
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            color: var(--text-muted);
        }

        /* Buttons styling */
        .btn-custom {
            height: 48px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0 24px;
            border: none;
            cursor: pointer;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.2);
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
            color: white;
        }

        .btn-secondary-custom {
            background: var(--diag-item-bg);
            color: var(--text-color);
            border: 1px solid var(--card-border);
        }

        .btn-secondary-custom:hover {
            background: var(--input-border);
            color: var(--text-title);
            transform: translateY(-2px);
        }

        /* Theme Toggle Button */
        .theme-toggle-btn {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--diag-item-bg);
            border: 1px solid var(--card-border);
            color: var(--text-color);
            cursor: pointer;
            font-size: 20px;
        }

        .theme-toggle-btn:hover {
            background: var(--input-border);
            transform: scale(1.05);
        }

        /* Password Strength Meter */
        .strength-meter {
            height: 6px;
            border-radius: 3px;
            background-color: var(--input-border);
            margin-top: 8px;
            overflow: hidden;
            position: relative;
        }

        .strength-bar {
            height: 100%;
            width: 0%;
            background-color: #ef4444;
            transition: width 0.4s ease, background-color 0.4s ease;
        }

        /* Visual Console Logger */
        .console-container {
            background: var(--console-bg);
            border-radius: 16px;
            padding: 20px;
            color: #34d399;
            font-family: 'Courier New', Courier, monospace;
            box-shadow: inset 0 4px 20px rgba(0, 0, 0, 0.3);
            margin-top: 30px;
            border: 1px solid var(--console-border);
        }

        .console-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--console-border);
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .console-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }

        .console-dots {
            display: flex;
            gap: 6px;
        }

        .dot-red { background-color: #ef4444; }
        .dot-yellow { background-color: #eab308; }
        .dot-green { background-color: #22c55e; }

        .console-body {
            max-height: 220px;
            overflow-y: auto;
            font-size: 13px;
            line-height: 1.6;
        }

        .console-line {
            margin-bottom: 8px;
            display: flex;
            gap: 10px;
        }

        .console-time {
            color: #64748b;
            user-select: none;
        }

        .console-prompt {
            color: var(--primary);
            font-weight: bold;
            user-select: none;
        }

        .console-msg {
            color: #f8fafc;
        }

        .console-msg.success { color: #34d399; }
        .console-msg.info { color: #38bdf8; }
        .console-msg.warning { color: #fbbf24; }

        /* Toast Container */
        .toast-container-custom {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 1060;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .toast-custom {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 16px 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            border-left: 6px solid var(--primary);
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 320px;
            max-width: 450px;
            transform: translateX(120%);
            animation: slideIn 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            color: var(--text-color);
        }

        @keyframes slideIn {
            to { transform: translateX(0); }
        }

        .toast-custom.success { border-left-color: #10b981; }
        .toast-custom.error { border-left-color: #ef4444; }
        .toast-custom.warning { border-left-color: #f59e0b; }

        .toast-icon {
            font-size: 22px;
        }
        .toast-custom.success .toast-icon { color: #10b981; }
        .toast-custom.error .toast-icon { color: #ef4444; }
        .toast-custom.warning .toast-icon { color: #f59e0b; }

        .toast-content {
            flex-grow: 1;
        }

        .toast-title {
            font-weight: 700;
            font-size: 14px;
            color: var(--text-title);
            margin: 0 0 2px 0;
        }

        .toast-desc {
            font-size: 13px;
            color: var(--text-muted);
            margin: 0;
        }

        .toast-close {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 16px;
        }

        .toast-close:hover {
            color: var(--text-title);
        }
    </style>
</head>

<body>

    <div class="test-container">
        
        <!-- Header -->
        <header class="glass-header">
            <div class="brand-section">
                <div class="logo-container">
                    <i class="bi bi-mortarboard"></i>
                </div>
                <div>
                    <h1 class="brand-title">SIKEMA Auth & UI Suite</h1>
                    <p class="brand-subtitle">Testing & Diagnostik Suite</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <!-- Theme Switcher -->
                <button type="button" class="theme-toggle-btn" id="themeToggle" title="Ubah Tema" onclick="toggleTheme()">
                    <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                </button>

                <a href="/login" class="btn-custom btn-secondary-custom">
                    <i class="bi bi-arrow-left"></i> Kembali ke Login
                </a>
            </div>
        </header>

        <div class="row g-4">
            
            <!-- Left Side: Diagnostics -->
            <div class="col-md-5">
                <div class="glass-card">
                    <h2 class="card-title-custom">
                        <i class="bi bi-cpu"></i> Status Sistem & Blade
                    </h2>
                    
                    <div class="diag-item">
                        <span class="diag-label">Blade Engine</span>
                        <span class="badge-status badge-success-custom">
                            <i class="bi bi-check-circle-fill"></i> Terkoneksi
                        </span>
                    </div>

                    <div class="diag-item">
                        <span class="diag-label">CSRF Security Protection</span>
                        <span class="badge-status badge-success-custom">
                            <i class="bi bi-shield-check"></i> Aktif (CSRF Token Valid)
                        </span>
                    </div>

                    <div class="diag-item">
                        <span class="diag-label">Laravel Session Handler</span>
                        <span class="badge-status badge-success-custom">
                            <i class="bi bi-database-check"></i> Driver: File
                        </span>
                    </div>

                    <div class="diag-item">
                        <span class="diag-label">App Environment</span>
                        <span class="badge-status badge-warning-custom">
                            <i class="bi bi-exclamation-triangle-fill"></i> Local (Debug: On)
                        </span>
                    </div>

                    <div class="diag-item">
                        <span class="diag-label">Encryption Key</span>
                        <span class="badge-status badge-success-custom">
                            <i class="bi bi-key-fill"></i> Configured
                        </span>
                    </div>

                    <div class="mt-4 pt-2 border-top">
                        <h6 class="fw-bold mb-3" style="color: var(--text-title);"><i class="bi bi-info-circle text-primary me-2"></i>Informasi Request</h6>
                        <table class="table table-sm table-borderless fs-6" style="color: var(--text-color);">
                            <tr>
                                <td class="py-1" style="width: 130px; color: var(--text-muted);">Host:</td>
                                <td class="fw-medium py-1">{{ request()->getHost() }}</td>
                            </tr>
                            <tr>
                                <td class="py-1" style="color: var(--text-muted);">IP Pengguna:</td>
                                <td class="fw-medium py-1">{{ request()->ip() }}</td>
                            </tr>
                            <tr>
                                <td class="py-1" style="color: var(--text-muted);">Method:</td>
                                <td class="fw-medium py-1">
                                    <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1">{{ request()->method() }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1" style="color: var(--text-muted);">Laravel Versi:</td>
                                <td class="fw-medium py-1">{{ app()->version() }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Side: Interaction Center -->
            <div class="col-md-7">
                <div class="glass-card">
                    <h2 class="card-title-custom">
                        <i class="bi bi-input-cursor-text"></i> Simulator Input & Notifikasi
                    </h2>

                    <!-- Email Validator -->
                    <div class="mb-4">
                        <label class="form-label form-label-custom" for="testEmail">Uji Validasi Email Realtime</label>
                        <div class="input-group">
                            <span class="input-group-text input-group-text-custom border-end-0 rounded-start-3"><i class="bi bi-envelope"></i></span>
                            <input type="email" id="testEmail" class="form-control form-control-custom border-start-0 rounded-end-3" placeholder="contoh@domain.com">
                        </div>
                        <div id="emailFeedback" class="form-text mt-2" style="color: var(--text-muted);">Ketik email untuk melihat pola validasi regex.</div>
                    </div>

                    <!-- Password Checker -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="form-label form-label-custom" for="testPassword">Uji Kekuatan Kata Sandi</label>
                            <div class="input-group">
                                <span class="input-group-text input-group-text-custom border-end-0 rounded-start-3"><i class="bi bi-lock"></i></span>
                                <input type="password" id="testPassword" class="form-control form-control-custom border-start-0 rounded-end-3" placeholder="Sandi baru">
                            </div>
                            <div class="strength-meter">
                                <div id="strengthBar" class="strength-bar"></div>
                            </div>
                            <div id="passwordFeedback" class="form-text mt-2" style="color: var(--text-muted);">Min. 8 karakter + angka/simbol.</div>
                        </div>

                        <!-- Password Confirmation Matcher -->
                        <div class="col-sm-6">
                            <label class="form-label form-label-custom" for="confirmPassword">Konfirmasi Kata Sandi</label>
                            <div class="input-group">
                                <span class="input-group-text input-group-text-custom border-end-0 rounded-start-3"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" id="confirmPassword" class="form-control form-control-custom border-start-0 rounded-end-3" placeholder="Ulangi sandi">
                            </div>
                            <div id="matchFeedback" class="form-text mt-2" style="color: var(--text-muted);">Ulangi sandi untuk menguji kecocokan.</div>
                        </div>
                    </div>

                    <!-- Demo Helpers & Notification Simulator -->
                    <div class="mb-4">
                        <label class="form-label form-label-custom d-block">Alat Bantu Pengujian</label>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn-custom btn-secondary-custom" onclick="autofillDemo()">
                                <i class="bi bi-pencil-square text-success"></i> Isi Data Demo
                            </button>
                            <button type="button" class="btn-custom btn-secondary-custom" onclick="resetForm()">
                                <i class="bi bi-trash text-danger"></i> Reset Form
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="form-label form-label-custom d-block">Uji Notifikasi Alert (Toast)</label>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn-custom btn-primary-custom" onclick="showToast('success', 'Akses Diberikan', 'Simulasi Login berhasil!')">
                                <i class="bi bi-check-lg"></i> Sukses Toast
                            </button>
                            <button type="button" class="btn-custom btn-secondary-custom border-danger text-danger bg-danger bg-opacity-10" onclick="showToast('error', 'Kredensial Salah', 'Username atau password tidak cocok.')">
                                <i class="bi bi-x-circle"></i> Error Toast
                            </button>
                            <button type="button" class="btn-custom btn-secondary-custom border-warning text-warning bg-warning bg-opacity-10" onclick="showToast('warning', 'Sesi Berakhir', 'Sesi aktif Anda akan segera habis.')">
                                <i class="bi bi-exclamation-triangle"></i> Warning Toast
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- System Log Console -->
        <div class="console-container">
            <div class="console-header">
                <div class="console-dots">
                    <span class="console-dot dot-red"></span>
                    <span class="console-dot dot-yellow"></span>
                    <span class="console-dot dot-green"></span>
                </div>
                <div class="fs-6 fw-medium" style="color: #34d399;"><i class="bi bi-terminal me-2"></i>SIKEMA Auth Terminal Log</div>
            </div>
            <div class="console-body" id="consoleLogs">
                <div class="console-line">
                    <span class="console-time">[16:47:00]</span>
                    <span class="console-prompt">SIKEMA-SYS $</span>
                    <span class="console-msg info">Menginisialisasi modul pengujian sistem autentikasi...</span>
                </div>
                <div class="console-line">
                    <span class="console-time">[16:47:01]</span>
                    <span class="console-prompt">SIKEMA-SYS $</span>
                    <span class="console-msg success">Blade Engine berhasil dirender dengan parser versi {{ app()->version() }}.</span>
                </div>
                <div class="console-line">
                    <span class="console-time">[16:47:02]</span>
                    <span class="console-prompt">SIKEMA-SYS $</span>
                    <span class="console-msg">Menunggu interaksi pengguna untuk pengujian real-time.</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Custom Toasts Container -->
    <div class="toast-container-custom" id="toastContainer"></div>

    <script>
        // System Theme Handler
        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const targetTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', targetTheme);
            localStorage.setItem('theme', targetTheme);
            
            const themeIcon = document.getElementById('themeIcon');
            if (targetTheme === 'dark') {
                themeIcon.className = 'bi bi-sun-fill';
                addLog('Tema visual diubah ke Mode Gelap (Dark Mode)', 'info');
            } else {
                themeIcon.className = 'bi bi-moon-stars-fill';
                addLog('Tema visual diubah ke Mode Terang (Light Mode)', 'info');
            }
        }

        // Initialize Saved Theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        document.getElementById('themeIcon').className = savedTheme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';

        // Log Helper
        function addLog(message, type = 'default') {
            const consoleLogs = document.getElementById('consoleLogs');
            const now = new Date();
            const timeStr = now.toTimeString().split(' ')[0];
            
            const line = document.createElement('div');
            line.className = 'console-line';
            
            let classColor = '';
            if (type === 'success') classColor = 'success';
            if (type === 'error') classColor = 'warning'; // highlight errors
            if (type === 'info') classColor = 'info';
            
            line.innerHTML = `
                <span class="console-time">[${timeStr}]</span>
                <span class="console-prompt">SIKEMA-SYS $</span>
                <span class="console-msg ${classColor}">${message}</span>
            `;
            
            consoleLogs.appendChild(line);
            consoleLogs.scrollTop = consoleLogs.scrollHeight;
        }

        // Email Validator Trigger
        const emailInput = document.getElementById('testEmail');
        const emailFeedback = document.getElementById('emailFeedback');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        emailInput.addEventListener('input', (e) => {
            const val = e.target.value;
            if (!val) {
                emailFeedback.textContent = 'Ketik email untuk melihat pola validasi regex.';
                emailFeedback.style.color = 'var(--text-muted)';
                emailFeedback.style.fontWeight = 'normal';
                return;
            }

            if (emailRegex.test(val)) {
                emailFeedback.textContent = '✓ Format email valid.';
                emailFeedback.style.color = '#10b981';
                emailFeedback.style.fontWeight = '600';
                addLog(`Uji Email Valid: "${val}"`, 'success');
            } else {
                emailFeedback.textContent = '✗ Format email tidak valid.';
                emailFeedback.style.color = '#ef4444';
                emailFeedback.style.fontWeight = '600';
            }
        });

        // Password Strength & Match Analyzer
        const pwdInput = document.getElementById('testPassword');
        const confirmInput = document.getElementById('confirmPassword');
        const strengthBar = document.getElementById('strengthBar');
        const pwdFeedback = document.getElementById('passwordFeedback');
        const matchFeedback = document.getElementById('matchFeedback');

        function checkPasswordMatch() {
            const pwdVal = pwdInput.value;
            const confirmVal = confirmInput.value;

            if (!confirmVal) {
                matchFeedback.textContent = 'Ulangi sandi untuk menguji kecocokan.';
                matchFeedback.style.color = 'var(--text-muted)';
                matchFeedback.style.fontWeight = 'normal';
                return;
            }

            if (pwdVal === confirmVal) {
                matchFeedback.textContent = '✓ Kata sandi cocok.';
                matchFeedback.style.color = '#10b981';
                matchFeedback.style.fontWeight = '600';
                addLog('Konfirmasi Kata Sandi: Cocok dan Tervalidasi.', 'success');
            } else {
                matchFeedback.textContent = '✗ Kata sandi belum cocok.';
                matchFeedback.style.color = '#ef4444';
                matchFeedback.style.fontWeight = '600';
            }
        }

        pwdInput.addEventListener('input', (e) => {
            const val = e.target.value;
            let score = 0;

            if (!val) {
                strengthBar.style.width = '0%';
                pwdFeedback.textContent = 'Min. 8 karakter + angka/simbol.';
                pwdFeedback.style.color = 'var(--text-muted)';
                pwdFeedback.style.fontWeight = 'normal';
                checkPasswordMatch();
                return;
            }

            // Length check
            if (val.length >= 8) score += 25;
            // Has numbers
            if (/\d/.test(val)) score += 25;
            // Has lowercase & uppercase
            if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score += 25;
            // Has special chars
            if (/[^A-Za-z0-9]/.test(val)) score += 25;

            strengthBar.style.width = `${score}%`;

            if (score <= 25) {
                strengthBar.style.backgroundColor = '#ef4444'; // Red
                pwdFeedback.textContent = 'Sangat Lemah ✗';
                pwdFeedback.style.color = '#ef4444';
                pwdFeedback.style.fontWeight = '600';
            } else if (score <= 50) {
                strengthBar.style.backgroundColor = '#f97316'; // Orange
                pwdFeedback.textContent = 'Sedang (Tambahkan huruf besar/simbol)';
                pwdFeedback.style.color = '#f97316';
                pwdFeedback.style.fontWeight = '600';
            } else if (score <= 75) {
                strengthBar.style.backgroundColor = '#eab308'; // Yellow
                pwdFeedback.textContent = 'Kuat (Tambahkan simbol unik)';
                pwdFeedback.style.color = '#eab308';
                pwdFeedback.style.fontWeight = '600';
            } else {
                strengthBar.style.backgroundColor = '#10b981'; // Emerald Green
                pwdFeedback.textContent = 'Sangat Kuat ✓✓';
                pwdFeedback.style.color = '#10b981';
                pwdFeedback.style.fontWeight = '600';
                addLog('Kekuatan Kata Sandi: Sangat Kuat.', 'success');
            }

            checkPasswordMatch();
        });

        confirmInput.addEventListener('input', checkPasswordMatch);

        // Autofill Demo Data
        function autofillDemo() {
            emailInput.value = 'admin@sikema.madrasah.id';
            pwdInput.value = 'Admin@2026!Sikema';
            confirmInput.value = 'Admin@2026!Sikema';
            
            // Dispatch input events
            emailInput.dispatchEvent(new Event('input'));
            pwdInput.dispatchEvent(new Event('input'));
            confirmInput.dispatchEvent(new Event('input'));
            
            addLog('Mengisi otomatis formulir dengan data kredensial demonstrasi.', 'info');
            showToast('success', 'Form Diisi', 'Data demo berhasil dimasukkan.');
        }

        // Reset Form
        function resetForm() {
            emailInput.value = '';
            pwdInput.value = '';
            confirmInput.value = '';
            
            emailInput.dispatchEvent(new Event('input'));
            pwdInput.dispatchEvent(new Event('input'));
            confirmInput.dispatchEvent(new Event('input'));
            
            addLog('Mengosongkan semua isian formulir.', 'info');
            showToast('warning', 'Formulir Direset', 'Semua inputan telah dibersihkan.');
        }

        // Beautiful Toast Trigger
        function showToast(type, title, desc) {
            const container = document.getElementById('toastContainer');
            
            const toast = document.createElement('div');
            toast.className = `toast-custom ${type}`;
            
            let iconClass = 'bi-check-circle-fill';
            if (type === 'error') iconClass = 'bi-x-circle-fill';
            if (type === 'warning') iconClass = 'bi-exclamation-triangle-fill';

            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="bi ${iconClass}"></i>
                </div>
                <div class="toast-content">
                    <h5 class="toast-title">${title}</h5>
                    <p class="toast-desc">${desc}</p>
                </div>
                <button type="button" class="toast-close" onclick="closeToast(this)">
                    <i class="bi bi-x-lg"></i>
                </button>
            `;

            container.appendChild(toast);
            addLog(`Memicu Notifikasi: [${type.toUpperCase()}] ${title} - ${desc}`, type);

            // Auto dismiss toast after 4s
            setTimeout(() => {
                toast.style.animation = 'slideIn 0.3s ease reverse forwards';
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 4000);
        }

        function closeToast(button) {
            const toast = button.closest('.toast-custom');
            toast.style.animation = 'slideIn 0.3s ease reverse forwards';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }
    </script>
</body>

</html>
