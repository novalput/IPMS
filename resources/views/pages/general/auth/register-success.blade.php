<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Berhasil - Integrated Project Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden !important;
        }

        html, body {
            height: 100%;
        }

        .success-container {
            position: relative;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            display: flex;
        }

        /* Left Section - Success Message */
        .left-section {
            flex: 0 0 500px;
            width: 500px;
            height: 100vh;
            background: #fff;
            display: flex;
            flex-direction: column;
            padding: 30px;
            z-index: 10;
            box-shadow: 2px 0 20px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
        }

        /* Custom scrollbar */
        .left-section::-webkit-scrollbar {
            width: 6px;
        }

        .left-section::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .left-section::-webkit-scrollbar-thumb {
            background: #009FDF;
            border-radius: 3px;
        }

        .left-section::-webkit-scrollbar-thumb:hover {
            background: #007bb5;
        }

        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: min-content;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo-section img {
            width: 160px;
            margin-bottom: 12px;
        }

        .logo-title {
            font-size: 24px;
            font-weight: 700;
            color: #003c71;
            margin-bottom: 4px;
        }

        .logo-subtitle {
            font-size: 13px;
            color: #666;
        }

        .divider {
            width: 100%;
            height: 1px;
            background: #e0e0e0;
            margin: 18px 0;
        }

        .success-icon {
            text-align: center;
            margin-bottom: 18px;
        }

        .success-icon i {
            font-size: 70px;
            color: #28a745;
        }

        .success-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .success-header h3 {
            font-size: 26px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .success-header p {
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
        }

        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #009FDF;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 18px;
        }

        .info-box-title {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .info-item {
            margin-bottom: 6px;
            font-size: 13px;
            color: #64748b;
        }

        .info-item strong {
            color: #1e293b;
            font-weight: 600;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .warning-box p {
            font-size: 13px;
            color: #856404;
            margin: 0;
            line-height: 1.6;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #009FDF 0%, #007bb5 100%);
            border: none;
            border-radius: 30px;
            color: white;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 159, 223, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .bottom-logo {
            text-align: center;
            margin-top: 20px;
            padding-bottom: 10px;
        }

        .bottom-logo img {
            width: 180px;
            opacity: 0.85;
        }

        /* Right Section - Background Image */
        .right-section {
            flex: 1;
            height: 100vh;
            position: relative;
            background-image: url('{{ asset("images/ipms-auth-background.svg") }}');
            background-size: cover;
            background-position: center;
            overflow: hidden;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 60, 113, 0.5) 0%, rgba(0, 159, 223, 0.3) 100%);
        }

        .ipms-logo-corner {
            position: absolute;
            top: 30px;
            right: 30px;
            z-index: 5;
        }

        .ipms-logo-corner img {
            width: 130px;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.3));
        }

        .info-overlay {
            position: absolute;
            bottom: 40px;
            left: 40px;
            right: 40px;
            max-width: 500px;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 15px;
            color: white;
            z-index: 3;
        }

        .info-overlay h4 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-overlay p {
            font-size: 14px;
            line-height: 1.7;
            margin: 0;
            opacity: 0.95;
        }

        /* Responsive untuk tablet */
        @media (max-width: 1024px) {
            .left-section {
                flex: 0 0 450px;
                width: 450px;
                padding: 25px;
            }

            .logo-section img {
                width: 140px;
            }

            .logo-title {
                font-size: 22px;
            }

            .logo-subtitle {
                font-size: 12px;
            }

            .success-icon i {
                font-size: 60px;
            }

            .success-header h3 {
                font-size: 24px;
            }

            .success-header p {
                font-size: 13px;
            }

            .info-box {
                padding: 14px;
            }

            .info-box-title {
                font-size: 14px;
            }

            .info-item {
                font-size: 12px;
            }

            .warning-box {
                padding: 12px;
            }

            .warning-box p {
                font-size: 12px;
            }

            .btn-login {
                padding: 12px;
                font-size: 14px;
            }

            .bottom-logo img {
                width: 160px;
            }
        }

        /* Responsive untuk mobile */
        @media (max-width: 992px) {
            .success-container {
                flex-direction: column;
            }

            .left-section {
                flex: none;
                width: 100vw;
                height: 100vh;
                padding: 25px;
            }

            .right-section {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .left-section {
                padding: 20px;
            }

            .logo-section img {
                width: 130px;
            }

            .logo-title {
                font-size: 20px;
            }

            .success-icon i {
                font-size: 56px;
            }

            .success-header h3 {
                font-size: 22px;
            }

            .bottom-logo img {
                width: 150px;
            }
        }

        @media (max-width: 576px) {
            .left-section {
                padding: 20px 15px;
            }

            .logo-section img {
                width: 120px;
            }

            .logo-title {
                font-size: 18px;
            }

            .success-icon i {
                font-size: 52px;
            }

            .success-header h3 {
                font-size: 20px;
            }

            .bottom-logo img {
                width: 140px;
            }
        }

        /* Untuk layar kecil vertical */
        @media (max-height: 700px) {
            .left-section {
                padding: 20px;
            }

            .logo-section {
                margin-bottom: 15px;
            }

            .logo-section img {
                width: 130px;
                margin-bottom: 10px;
            }

            .divider {
                margin: 15px 0;
            }

            .success-icon {
                margin-bottom: 15px;
            }

            .success-icon i {
                font-size: 55px;
            }

            .success-header {
                margin-bottom: 15px;
            }

            .success-header h3 {
                font-size: 22px;
                margin-bottom: 6px;
            }

            .info-box {
                padding: 12px;
                margin-bottom: 15px;
            }

            .warning-box {
                padding: 12px;
                margin-bottom: 15px;
            }

            .bottom-logo {
                margin-top: 15px;
            }

            .bottom-logo img {
                width: 150px;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <!-- Left Section - Success Message -->
        <div class="left-section">
            <div class="content">
                <div class="logo-section">
                    <img src="{{ asset('images/ipms-logo.svg') }}" alt="IPMS Logo">
                    <div class="logo-title">Integrated Project Management System</div>
                    <div class="logo-subtitle">Integrated Project Management System</div>
                </div>

                <div class="divider"></div>

                <div class="success-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>

                <div class="success-header">
                    <h3>Pendaftaran Berhasil!</h3>
                    <p>
                        Terima kasih telah mendaftar di sistem Integrated Project Management System.
                        Akun Anda telah berhasil dibuat namun masih menunggu persetujuan dari Administrator.
                    </p>
                </div>

                <div class="info-box">
                    <div class="info-box-title">Informasi Akun</div>
                    <div class="info-item">
                        <strong>Username:</strong> {{ session('username') }}
                    </div>
                    <div class="info-item">
                        <strong>Email:</strong> {{ session('email') }}
                    </div>
                    <div class="info-item">
                        <strong>Status:</strong> <span style="color: #ffc107; font-weight: 600;">Menunggu Persetujuan Admin</span>
                    </div>
                </div>

                <div class="warning-box">
                    <p>
                        <strong><i class="bi bi-exclamation-triangle-fill"></i> Pemberitahuan:</strong><br>
                        Silakan hubungi Administrator untuk penerimaan perizinan pembuatan akun Anda.
                    </p>
                </div>

                <a href="{{ route('login') }}" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i> Kembali ke Login
                </a>
            </div>

            <div class="bottom-logo">
                <img src="{{ Storage::url('images/logo-secondary.png') }}" alt="Secondary Logo" onerror="this.style.display='none'">
            </div>
        </div>

        <!-- Right Section - Background Image -->
        <div class="right-section">
            <div class="overlay"></div>
            
            <!-- IPMS Logo di pojok kanan atas -->
            <div class="ipms-logo-corner">
                <img src="{{ asset('images/ipms-logo.svg') }}" alt="IPMS Logo">
            </div>

            <!-- Info Overlay di bawah -->
            <div class="info-overlay">
                <h4>Selamat Bergabung!</h4>
                <p>
                    Anda telah berhasil mendaftar di platform Integrated Project Management System.
                    Tim Administrator kami akan segera memverifikasi akun Anda. 
                    Notifikasi akan dikirimkan melalui email setelah akun Anda disetujui.
                </p>
            </div>
        </div>
    </div>

    <script>
    // Prevent zoom on double tap (mobile)
    let lastTouchEnd = 0;
    document.addEventListener('touchend', function(event) {
        const now = (new Date()).getTime();
        if (now - lastTouchEnd <= 300) {
            event.preventDefault();
        }
        lastTouchEnd = now;
    }, false);
    </script>
</body>
</html>