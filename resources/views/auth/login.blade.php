<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKEMA - Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
    * {
        font-family: 'Poppins', sans-serif;
    }

    body {
        background-color: #f5faf7;
        background-image:
            radial-gradient(circle at center, rgba(16, 185, 129, 0.08) 2px, transparent 2px);
        background-size: 80px 80px;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    .login-wrapper {
        width: 100%;
        max-width: 560px;
        padding: 20px;
    }

    .login-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 45px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    }

    .logo-box {
        width: 110px;
        height: 110px;
        margin: auto;
        border-radius: 24px;
        background: linear-gradient(135deg, #10b981, #059669);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.25);
    }

    .logo-box i {
        font-size: 52px;
        color: white;
    }

    .title {
        font-size: 52px;
        font-weight: 700;
        color: #0f172a;
        text-align: center;
        margin-bottom: 5px;
    }

    .subtitle {
        text-align: center;
        color: #475569;
        margin-bottom: 45px;
        font-size: 18px;
    }

    .form-label {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 10px;
    }

    .form-control {
        height: 58px;
        border-radius: 14px;
        border: 1px solid #dbe2ea;
        padding-left: 18px;
        font-size: 16px;
        box-shadow: none !important;
    }

    .form-control:focus {
        border-color: #10b981;
    }

    .password-wrapper {
        position: relative;
    }

    .password-wrapper i {
        position: absolute;
        right: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        cursor: pointer;
    }

    .remember-me {
        margin-top: 18px;
        margin-bottom: 28px;
    }

    .remember-me label {
        font-weight: 500;
        color: #334155;
    }

    .btn-login {
        width: 100%;
        height: 58px;
        border: none;
        border-radius: 14px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        font-size: 20px;
        font-weight: 600;
        transition: 0.3s ease;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(16, 185, 129, 0.25);
    }

    .divider {
        margin: 35px 0 25px;
        border-top: 1px solid #e2e8f0;
    }

    .demo-info {
        text-align: center;
        color: #64748b;
        font-size: 15px;
    }

    .demo-info span {
        color: #10b981;
        font-weight: 700;
    }

    @media(max-width: 576px) {

        .login-card {
            padding: 30px 22px;
        }

        .title {
            font-size: 42px;
        }
    }
    </style>
</head>

<body>

    <div class="login-wrapper">

        <div class="login-card">

            <div class="logo-box">
                <i class="bi bi-mortarboard"></i>
            </div>

            <h1 class="title">SIKEMA</h1>

            <p class="subtitle">
                Sistem Keuangan Madrasah
            </p>

            <!-- FORM UI ONLY -->
            <form>

                <div class="mb-4">
                    <label class="form-label">
                        Username / Email
                    </label>

                    <input type="text" class="form-control" placeholder="Masukkan username atau email">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Password
                    </label>

                    <div class="password-wrapper">

                        <input type="password" class="form-control" placeholder="Masukkan password">

                        <i class="bi bi-eye"></i>

                    </div>
                </div>

                <div class="form-check remember-me">
                    <input class="form-check-input" type="checkbox" id="remember">

                    <label class="form-check-label" for="remember">
                        Ingat saya
                    </label>
                </div>

                <button type="submit" class="btn-login">
                    Masuk
                </button>

            </form>

            <div class="divider"></div>

            <div class="demo-info">
                Demo: username <span>admin</span>,
                password <span>admin</span>
            </div>

            <div class="mt-4 text-center">
                <a href="/auth/test" class="fw-semibold text-decoration-none" style="color: #10b981; font-size: 14px; transition: color 0.2s;">
                    <i class="bi bi-cpu-fill me-1"></i> Buka Auth Test Playground
                </a>
            </div>

        </div>

    </div>

</body>

</html>