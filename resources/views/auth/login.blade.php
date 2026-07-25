<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login &mdash; Bulk Email Sender</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --dark: #0f172a;
            --light: #f1f5f9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--light);
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            -webkit-font-smoothing: antialiased;
        }

        .login-wrap {
            width: 100%;
            max-width: 400px;
            padding: 24px;
        }

        /* ── Material card ── */
        .card {
            background: #fff;
            border-radius: 16px;
            padding: 40px 32px 36px;
            box-shadow:
                0 1px 3px rgba(15, 23, 42, 0.06),
                0 8px 24px rgba(15, 23, 42, 0.06);
        }

        .card-header {
            margin-bottom: 32px;
        }

        .card-header .brand {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: var(--dark);
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .card-header .brand svg {
            width: 20px;
            height: 20px;
            color: #fff;
        }

        .card-header h1 {
            font-size: 20px;
            font-weight: 600;
            letter-spacing: -0.3px;
            margin-bottom: 4px;
        }

        .card-header p {
            font-size: 13px;
            color: #64748b;
        }

        /* ── Form ── */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 6px;
            color: #334155;
        }

        .form-group input {
            width: 100%;
            height: 46px;
            padding: 0 14px;
            font-size: 14px;
            font-family: inherit;
            color: var(--dark);
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus {
            border-color: var(--dark);
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.06);
            background: #fff;
        }

        .form-group input.is-invalid {
            border-color: #ef4444;
        }

        .invalid-feedback {
            font-size: 12px;
            color: #ef4444;
            margin-top: 5px;
        }

        .btn {
            width: 100%;
            height: 46px;
            font-size: 14px;
            font-weight: 500;
            font-family: inherit;
            color: #fff;
            background: var(--dark);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 6px;
            transition: opacity 0.15s, transform 0.1s;
        }

        .btn:hover {
            opacity: 0.92;
        }

        .btn:active {
            transform: scale(0.98);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .card-footer {
            margin-top: 32px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }

        .card-footer a {
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
        }

        .card-footer a:hover {
            text-decoration: underline;
        }

        .loading {
            display: none;
            align-items: center;
            gap: 8px;
        }

        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <div class="login-wrap">
        <div class="card">
            <div class="card-header">
                <div class="brand">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h1>Sign in</h1>
                <p>Enter your credentials to continue</p>
            </div>

            <form action="{{ route('login') }}" method="POST" id="loginForm">
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           class="@error('email') is-invalid @enderror"
                           placeholder="you@example.com"
                           required
                           autocomplete="email"
                           autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password"
                           id="password"
                           name="password"
                           class="@error('password') is-invalid @enderror"
                           placeholder="Enter your password"
                           required
                           autocomplete="current-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn" id="loginBtn">
                    <span class="btn-text">Sign in</span>
                    <span class="loading">
                        <span class="spinner"></span>
                        Signing in&hellip;
                    </span>
                </button>
            </form>

            <div class="card-footer">
                <p>
                    Developed by
                    <a href="https://facebook.com/arafathossain000" target="_blank">Arafat Hossain</a>
                </p>
                <p style="margin-top:4px;">{{ date('Y') }} &copy; All Rights Reserved</p>
            </div>
        </div>
    </div>

    <script>
        localStorage.removeItem('instructionUnderstood');

        document.getElementById('loginForm').addEventListener('submit', function () {
            const btn = document.getElementById('loginBtn');
            btn.querySelector('.btn-text').style.display = 'none';
            btn.querySelector('.loading').style.display = 'inline-flex';
            btn.disabled = true;
        });
    </script>
</body>
</html>
