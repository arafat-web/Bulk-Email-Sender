<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Bulk Email Sender - Professional Email Marketing Solution">
    <meta name="author" content="Arafat Hossain">
    <title>Login - Bulk Email Sender</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #667eea;
            --primary-dark: #5a6fd8;
            --secondary-color: #764ba2;
            --gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --shadow: 0 15px 35px rgba(102, 126, 234, 0.1);
            --border-radius: 15px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="%23ffffff08" points="0,1000 1000,0 1000,1000"/></svg>');
            z-index: 1;
        }

        .login-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
        }

        .login-header {
            padding: 40px 40px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .logo-container {
            margin-bottom: 30px;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            background: var(--gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .logo-icon i {
            font-size: 35px;
            color: white;
        }

        .app-title {
            font-size: 28px;
            font-weight: 700;
            color: #2d3436;
            margin-bottom: 10px;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .app-subtitle {
            color: #636e72;
            font-size: 14px;
            font-weight: 400;
            margin-bottom: 0;
        }

        .welcome-text {
            font-size: 24px;
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 8px;
        }

        .welcome-subtitle {
            color: #636e72;
            font-size: 15px;
            font-weight: 400;
        }

        .login-body {
            padding: 30px 40px 40px;
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-floating .form-control {
            height: 60px;
            padding: 20px 16px 8px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-floating .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: white;
        }

        .form-floating label {
            padding: 20px 16px 8px;
            color: #6c757d;
            font-weight: 500;
        }

        .input-group-text {
            background: transparent;
            border: 2px solid #e9ecef;
            border-left: none;
            border-radius: 0 12px 12px 0;
        }

        .btn-login {
            width: 100%;
            height: 55px;
            background: var(--gradient);
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            margin-top: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .invalid-feedback {
            font-size: 13px;
            font-weight: 500;
            margin-top: 8px;
        }

        .login-footer {
            padding: 20px 40px 30px;
            text-align: center;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            background: rgba(248, 249, 250, 0.5);
        }

        .footer-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .footer-link:hover {
            color: var(--primary-dark);
        }

        .copyright {
            color: #6c757d;
            font-size: 13px;
            margin-top: 10px;
        }

        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 60px;
            height: 60px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 80px;
            height: 80px;
            top: 70%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 40px;
            height: 40px;
            top: 30%;
            right: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        @media (max-width: 576px) {
            .login-header, .login-body, .login-footer {
                padding-left: 25px;
                padding-right: 25px;
            }

            .app-title {
                font-size: 24px;
            }

            .welcome-text {
                font-size: 20px;
            }
        }

        .loading {
            display: none;
        }

        .loading .spinner-border {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-container">
                    <div class="logo-icon">
                        <i class="bi bi-envelope-paper"></i>
                    </div>
                    <h1 class="app-title">Bulk Email Sender</h1>
                    <p class="app-subtitle">Professional Email Marketing Solution</p>
                </div>
                <h2 class="welcome-text">Welcome Back!</h2>
                <p class="welcome-subtitle">Please sign in to your account</p>
            </div>

            <div class="login-body">
                <form action="{{ route('login') }}" method="POST" id="loginForm">
                    @csrf

                    <div class="form-floating mb-3">
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="name@example.com"
                               required
                               autocomplete="email"
                               autofocus>
                        <label for="email"><i class="bi bi-envelope me-2"></i>Email Address</label>
                        @error('email')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password"
                               name="password"
                               placeholder="Password"
                               required
                               autocomplete="current-password">
                        <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
                        @error('password')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-login" id="loginBtn">
                        <span class="btn-text">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                        </span>
                        <span class="loading">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                            Signing in...
                        </span>
                    </button>
                </form>
            </div>

            <div class="login-footer">
                <p class="mb-0">
                    Developed by
                    <a href="https://facebook.com/arafathossain000" target="_blank" class="footer-link">
                        <i class="bi bi-person-circle me-1"></i>Arafat Hossain
                    </a>
                </p>
                <p class="copyright">{{ date('Y') }} © All Rights Reserved</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Clear localStorage
        localStorage.removeItem('instructionUnderstood');

        // Form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            const btnText = btn.querySelector('.btn-text');
            const loading = btn.querySelector('.loading');

            btnText.style.display = 'none';
            loading.style.display = 'inline-flex';
            btn.disabled = true;
        });

        // Add floating animation to shapes
        document.addEventListener('DOMContentLoaded', function() {
            const shapes = document.querySelectorAll('.shape');
            shapes.forEach((shape, index) => {
                shape.style.animationDuration = (4 + index * 2) + 's';
            });
        });

        // Focus effect for form inputs
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>
