<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKEMA - Login</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
    * {
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: #f4f7fb;
        height: 100vh;
        overflow: hidden;
    }

    .login-container {
        height: 100vh;
    }

    .left-side {
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        color: white;
        padding: 60px;
    }

    .left-side h1 {
        font-weight: 700;
        font-size: 42px;
    }

    .left-side p {
        opacity: 0.9;
        margin-top: 20px;
        line-height: 1.8;
    }

    .right-side {
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
    }

    .login-card {
        width: 100%;
        max-width: 420px;
    }

    .login-title {
        font-weight: 700;
        margin-bottom: 10px;
    }

    .login-subtitle {
        color: #6c757d;
        margin-bottom: 35px;
    }

    .form-control {
        height: 50px;
        border-radius: 12px;
    }

    .btn-login {
        height: 50px;
        border-radius: 12px;
        background: #1e3c72;
        border: none;
        font-weight: 600;
    }

    .btn-login:hover {
        background: #16325c;
    }

    .school-logo {
        width: 90px;
        margin-bottom: 25px;
    }

    @media(max-width: 991px) {
        .left-side {
            display: none;
        }

        .right-side {
            width: 100%;
        }
    }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row login-container">

            <!-- Left -->
            <div class="col-lg-6 left-side d-flex flex-column justify-content-center">
                <div>
                    <h1>SIKEMA</h1>
                    <h4>Sistem Keuangan Madrasah</h4>

                    <p>
                        Platform pengelolaan keuangan sekolah madrasah untuk
                        membantu administrasi pembayaran siswa menjadi lebih
                        cepat, rapi, dan efisien.
                    </p>
                </div>
            </div>

            <!-- Right -->
            <div class="col-lg-6 right-side">
                <div class="login-card">

                    <div class="text-center mb-4">
                        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135755.png" class="school-logo"
                            alt="Logo">
                    </div>

                    <h2 class="login-title">Selamat Datang 👋</h2>
                    <p class="login-subtitle">
                        Silakan login untuk melanjutkan
                    </p>

                    <form>

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" placeholder="Masukkan username">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" placeholder="Masukkan password">
                        </div>

                        <button type="submit" class="btn btn-primary btn-login w-100">
                            Login
                        </button>

                    </form>

                </div>
            </div>

        </div>
    </div>

</body>

</html>