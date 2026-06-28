<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — Sistem Monitoring Jaringan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* ===== VIDEO BACKGROUND ===== */
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        .video-background video {
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            object-fit: cover;
        }

        /* Overlay - sedikit gelap saja */
        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            /* 0.45 = sedikit gelap, video masih terlihat jelas */
            background: rgba(10, 14, 39, 0.50);
            z-index: 1;
            pointer-events: none;
        }

        /* Grid pattern di atas video */
        .grid-pattern {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                linear-gradient(rgba(37, 99, 235, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(37, 99, 235, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: 2;
            pointer-events: none;
        }

        .login-card {
            width: 100%;
            max-width: 380px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow:
                0 25px 50px -12px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(255, 255, 255, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            overflow: hidden;
            z-index: 10;
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-2px);
            box-shadow:
                0 35px 60px -15px rgba(0, 0, 0, 0.6),
                0 0 0 1px rgba(255, 255, 255, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        /* === HEADER DENGAN LOGO === */
        .login-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #0ea5e9 100%);
            color: #fff;
            padding: 1.2rem 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(45deg,
                    transparent,
                    transparent 10px,
                    rgba(255, 255, 255, 0.03) 10px,
                    rgba(255, 255, 255, 0.03) 20px);
            animation: slide 20s linear infinite;
        }

        @keyframes slide {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(50px, 50px);
            }
        }

        /* Logo wrapper */
        .logo-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }

        /* Logo custom */
        .logo-custom {
            width: 36px;
            height: 36px;
            object-fit: contain;
            filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.3));
            border-radius: 8px;
        }

        /* Ikon internet */
        .logo-icon {
            font-size: 1.8rem;
            filter: drop-shadow(0 0 15px rgba(14, 165, 233, 0.5));
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .login-header h5 {
            font-weight: 700;
            margin: 0;
            font-size: 1rem;
            position: relative;
            z-index: 2;
            letter-spacing: 0.5px;
        }

        .login-header small {
            opacity: 0.8;
            font-size: 0.7rem;
            position: relative;
            z-index: 2;
            display: block;
            margin-top: 0.15rem;
        }

        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.65rem;
            color: #22c55e;
            margin-top: 0.4rem;
            font-weight: 600;
            position: relative;
            z-index: 2;
        }

        .status-dot {
            width: 5px;
            height: 5px;
            background: #22c55e;
            border-radius: 50%;
            box-shadow: 0 0 6px #22c55e;
            animation: blink 2s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .login-body {
            padding: 1.25rem 1.5rem;
        }

        .login-title {
            font-size: 0.8rem;
            color: #64748b;
            text-align: center;
            margin-bottom: 1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }

        .login-title i {
            color: #0ea5e9;
            font-size: 0.9rem;
        }

        .form-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.3rem;
        }

        .input-group {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.2s ease;
        }

        .input-group:focus-within {
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15), 0 4px 12px rgba(14, 165, 233, 0.1);
        }

        .input-group-text {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-right: none;
            color: #0ea5e9;
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }

        .form-control {
            border: 1px solid #e2e8f0;
            border-left: none;
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
            background: #fff;
            transition: all 0.2s ease;
            min-height: auto;
        }

        .form-control:focus {
            border-color: #e2e8f0;
            box-shadow: none;
            background: #fff;
        }

        .form-control::placeholder {
            color: #94a3b8;
            font-size: 0.8rem;
        }

        .btn-toggle-password {
            border: 1px solid #e2e8f0;
            border-left: none;
            background: #f8fafc;
            color: #94a3b8;
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .btn-toggle-password:hover {
            background: #f1f5f9;
            color: #0ea5e9;
        }

        .mb-3 {
            margin-bottom: 0.75rem !important;
        }

        .form-check {
            margin-bottom: 1rem !important;
        }

        .form-check-input {
            width: 1em;
            height: 1em;
            border-radius: 5px;
            border: 2px solid #cbd5e1;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .form-check-input:checked {
            background-color: #0ea5e9;
            border-color: #0ea5e9;
        }

        .form-check-label {
            font-size: 0.75rem;
            color: #64748b;
            cursor: pointer;
            padding-left: 0.2rem;
        }

        .btn-login {
            background: linear-gradient(135deg, #0f172a 0%, #1e40af 50%, #0ea5e9 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 0.65rem;
            border-radius: 10px;
            width: 100%;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(14, 165, 233, 0.4);
            color: #fff;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            border-radius: 8px;
            border: none;
            font-size: 0.75rem;
            padding: 0.6rem 0.8rem;
            margin-bottom: 0.75rem;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
            border-left: 3px solid #22c55e;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border-left: 3px solid #ef4444;
        }

        .footer-info {
            background: rgba(248, 250, 252, 0.8);
            padding: 0.6rem 1.5rem;
            text-align: center;
            border-top: 1px solid rgba(226, 232, 240, 0.6);
            font-size: 0.65rem;
            color: #94a3b8;
            font-weight: 500;
        }

        .card-particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
            border-radius: 16px;
        }

        .card-particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: rgba(14, 165, 233, 0.3);
            border-radius: 50%;
            animation: float 6s infinite ease-in-out;
        }

        .card-particle:nth-child(1) {
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .card-particle:nth-child(2) {
            top: 20%;
            right: 15%;
            animation-delay: 1s;
        }

        .card-particle:nth-child(3) {
            bottom: 30%;
            left: 20%;
            animation-delay: 2s;
        }

        .card-particle:nth-child(4) {
            bottom: 10%;
            right: 10%;
            animation-delay: 3s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) scale(1);
                opacity: 0.3;
            }

            50% {
                transform: translateY(-15px) scale(1.2);
                opacity: 0.6;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                margin: 0.75rem;
                max-width: 100%;
            }

            .login-body {
                padding: 1rem 1.25rem;
            }

            .login-header {
                padding: 1rem 1.25rem;
            }

            .logo-custom {
                width: 32px;
                height: 32px;
            }

            .logo-icon {
                font-size: 1.6rem;
            }
        }
    </style>
</head>

<body>
    <!-- VIDEO BACKGROUND -->
    <div class="video-background">
        <video id="bg-video" autoplay muted loop playsinline poster="{{ asset('images/video-poster.jpg') }}">
            <source src="{{ asset('videos/1.mp4') }}" type="video/mp4">
            Browser Anda tidak mendukung video tag.
        </video>
    </div>

    <!-- Overlay sedikit gelap -->
    <div class="bg-overlay"></div>
    <!-- Grid pattern -->
    <div class="grid-pattern"></div>

    <div class="login-card">
        <div class="card-particles">
            <div class="card-particle"></div>
            <div class="card-particle"></div>
            <div class="card-particle"></div>
            <div class="card-particle"></div>
        </div>

        <div class="login-header">
            <div class="logo-wrapper">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-custom"
                    onerror="this.style.display='none'">
                <div class="logo-icon">🌐</div>
            </div>
            <h5>Sistem Monitoring Jaringan</h5>
            <small>DISNAKERTRANS PROVINSI MALUKU</small>
        </div>

        <div class="login-body">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person-fill"></i>
                        </span>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            placeholder="Email" value="{{ old('email') }}" required autofocus>
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock-fill"></i>
                        </span>
                        <input type="password" name="password" id="password"
                            class="form-control @error('password') is-invalid @enderror" placeholder="••••••••"
                            required>
                        <button class="btn btn-toggle-password" type="button" onclick="togglePassword()"
                            title="Tampilkan/sembunyikan password">
                            <i class="bi bi-eye" id="eye-icon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Ingat saya di perangkat ini</label>
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
                </button>
            </form>
        </div>

        <div class="footer-info">
            <i class="bi bi-hdd-network me-1"></i>
            &copy; {{ date('Y') }} DISNAKERTRANS Provinsi Maluku
            <span class="mx-1">·</span>
        </div>
    </div>

    <script>
        // Toggle password
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        // Kecepatan video - sedikit lebih lambat dari normal
        document.addEventListener('DOMContentLoaded', function () {
            const video = document.getElementById('bg-video');
            // 0.6 = 60% kecepatan normal (sedikit slow, tidak terlalu lambat)
            video.playbackRate = 0.4;
        });
    </script>
</body>

</html>