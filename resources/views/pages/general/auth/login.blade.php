<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Integrated Project Management System</title>
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

        .login-container {
            position: relative;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            display: flex;
        }

        /* Left Section - Login Form */
        .left-section {
            flex: 0 0 450px;
            width: 450px;
            height: 100vh;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px;
            z-index: 10;
            box-shadow: 2px 0 20px rgba(0, 0, 0, 0.1);
        }

        .form-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-section img {
            width: 180px;
            margin-bottom: 15px;
        }

        .logo-title {
            font-size: 28px;
            font-weight: 700;
            color: #003c71;
            margin-bottom: 5px;
        }

        .logo-subtitle {
            font-size: 14px;
            color: #666;
        }

        .divider {
            width: 100%;
            height: 1px;
            background: #e0e0e0;
            margin: 25px 0;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h3 {
            font-size: 26px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .login-header p {
            font-size: 14px;
            color: #64748b;
        }

        .form-group {
            position: relative;
            margin-bottom: 25px;
        }

        .form-control {
            width: 100%;
            padding: 12px 45px 12px 15px;
            border: none;
            border-bottom: 2px solid #cbd5e1;
            border-radius: 0;
            font-size: 15px;
            transition: all 0.3s ease;
            background: transparent;
            color: #333;
        }

        .form-control::placeholder {
            color: #94a3b8;
            font-size: 14px;
        }

        .form-control:focus {
            outline: none;
            border-bottom-color: #009FDF;
        }

        .form-control.is-invalid {
            border-bottom-color: #ef4444;
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .input-icon:hover {
            color: #009FDF;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .form-options a {
            color: #009FDF;
            text-decoration: none;
            font-weight: 500;
        }

        .form-options a:hover {
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #009FDF 0%, #007bb5 100%);
            border: none;
            border-radius: 30px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 159, 223, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .text-danger {
            font-size: 12px;
            margin-top: 5px;
            display: block;
            color: #ef4444;
        }

        .bottom-logo {
            text-align: center;
            margin-top: 20px;
        }

        .bottom-logo img {
            width: 200px;
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

        /* Responsive */
        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
            }

            .left-section {
                flex: none;
                width: 100vw;
                height: 100vh;
                padding: 30px;
            }

            .right-section {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .left-section {
                padding: 25px 20px;
            }

            .logo-section img {
                width: 150px;
            }

            .logo-title {
                font-size: 24px;
            }

            .login-header h3 {
                font-size: 22px;
            }

            .bottom-logo img {
                width: 160px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Section - Login Form -->
        <div class="left-section">
            <div class="form-content">
                <div class="logo-section">
                    <img src="{{ asset('images/ipms-logo.svg') }}" alt="IPMS Logo">
                    <div class="logo-title">Integrated Project Management System</div>
                </div>

                <div class="divider"></div>

                <div class="login-header">
                    <h3>Login</h3>
                    <p>Silahkan login terlebih dahulu untuk masuk aplikasi</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="form-group">
                        <input 
                            type="text" 
                            class="form-control @error('username') is-invalid @enderror" 
                            name="username" 
                            id="username" 
                            value="{{ old('username') ?? old('email') }}" 
                            required 
                            placeholder="Email atau Username" 
                            autocomplete="off">
                        <span class="input-icon">
                            <i class="bi bi-person-fill"></i>
                        </span>
                        @error('username')
                            <small class="text-danger">
                                <strong>{{ $message }}</strong>
                            </small>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <input 
                            type="password" 
                            class="form-control @error('password') is-invalid @enderror"
                            name="password" 
                            id="password" 
                            required 
                            placeholder="Password">
                        <span class="input-icon" onclick="togglePassword()">
                            <i class="bi bi-eye-slash-fill" id="toggleIcon"></i>
                        </span>
                        @error('password')
                            <small class="text-danger">
                                <strong>{{ $message }}</strong>
                            </small>
                        @enderror
                    </div>

                    <div class="form-options">
                        <span style="color: #64748b;">Belum punya akun?</span>
                        <a href="{{ route('register') }}">Register</a>
                    </div>

                    <button class="btn-login" type="submit">Login</button>
                </form>
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
                <h4>Integrated Project Management</h4>
                <p>
                    Platform digital terintegrasi untuk manajemen proyek Integrated Project Management System.
                    Mendukung tracking progress, dokumentasi, dan monitoring secara real-time.
                </p>
            </div>
        </div>
    </div>

    <script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('bi-eye-slash-fill');
            toggleIcon.classList.add('bi-eye-fill');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('bi-eye-fill');
            toggleIcon.classList.add('bi-eye-slash-fill');
        }
    }

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