<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — ResumeIQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        :root {
            --bg-base:     #0a0b0f;
            --bg-card:     #111318;
            --border:      #252836;
            --accent:      #6c63ff;
            --accent-glow: rgba(108,99,255,0.25);
            --text-primary:#eef0f8;
            --text-muted:  #545872;
            --emerald:     #10d9a0;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-base);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Radial glow BG */
        body::before {
            content: '';
            position: fixed;
            top: -200px; left: 50%;
            transform: translateX(-50%);
            width: 900px; height: 600px;
            background: radial-gradient(ellipse, rgba(108,99,255,0.12) 0%, transparent 65%);
            pointer-events: none;
        }

        .auth-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 48px 44px;
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
        }

        .brand {
            text-align: center;
            margin-bottom: 36px;
        }

        .brand-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, #6c63ff, #8b84ff);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
            margin: 0 auto 14px;
            box-shadow: 0 0 40px rgba(108,99,255,0.3);
        }

        .brand-name {
            font-family: 'Syne', sans-serif;
            font-size: 26px;
            font-weight: 800;
        }

        .brand-sub {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .form-group { margin-bottom: 18px; }

        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #8b90a8;
            margin-bottom: 7px;
        }

        .form-input {
            width: 100%;
            background: #1a1d26;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-primary);
            padding: 13px 16px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-input::placeholder { color: var(--text-muted); }

        .form-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .btn-submit {
            width: 100%;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 14px;
            font-family: 'Syne', sans-serif;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            letter-spacing: 0.3px;
            margin-top: 8px;
        }

        .btn-submit:hover {
            background: #8b84ff;
            box-shadow: 0 0 30px var(--accent-glow);
            transform: translateY(-1px);
        }

        .divider {
            display: flex; align-items: center; gap: 12px;
            margin: 24px 0;
            font-size: 12px; color: var(--text-muted);
        }

        .divider::before, .divider::after {
            content: ''; flex: 1;
            height: 1px; background: var(--border);
        }

        .auth-link {
            display: block; text-align: center;
            font-size: 14px; color: #8b90a8;
            margin-top: 20px;
        }

        .auth-link a { color: #8b84ff; text-decoration: none; font-weight: 500; }
        .auth-link a:hover { text-decoration: underline; }

        .error-msg {
            background: rgba(255,79,106,0.1);
            border: 1px solid rgba(255,79,106,0.3);
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 13px;
            color: #ff4f6a;
            margin-bottom: 20px;
        }

        .features {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 28px;
            flex-wrap: wrap;
        }

        .feature-chip {
            display: flex; align-items: center; gap: 6px;
            font-size: 12px; color: var(--text-muted);
        }

        .feature-chip span { color: var(--emerald); }
    </style>
</head>

<body>
    <div class="auth-card">

        {{-- Brand --}}
        <div class="brand">
            <div class="brand-icon">⚡</div>
            <div class="brand-name">ResumeIQ</div>
            <div class="brand-sub">ATF Resume Tracker — Sign in to continue</div>
        </div>

        {{-- Errors --}}
        @if($errors->any())
            <div class="error-msg">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-input"
                       placeholder="you@example.com"
                       value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label>
                    Password
                    <a href="#" style="float:right;color:#8b84ff;font-size:12px;text-decoration:none;">
                        Forgot password?
                    </a>
                </label>
                <div style="position:relative;">
                    <input type="password" name="password" class="form-input" id="passwordInput"
                           placeholder="••••••••" required>
                    <button type="button" id="togglePassword"
                            style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:16px;">
                        <i class="bi bi-eye-slash" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <input type="checkbox" name="remember" id="remember"
                       style="accent-color:var(--accent);width:15px;height:15px;">
                <label for="remember" style="margin:0;font-size:13px;color:var(--text-muted);">
                    Remember me for 30 days
                </label>
            </div>

            <button type="submit" class="btn-submit">
                Sign In to ResumeIQ →
            </button>
        </form>

        <p class="auth-link">
            Don't have an account? <a href="{{ route('register') }}">Create one free</a>
        </p>

        {{-- Feature highlights --}}
        <div class="features">
            <div class="feature-chip"><span>⚡</span> AI-Powered</div>
            <div class="feature-chip"><span>🎯</span> ATS Scoring</div>
            <div class="feature-chip"><span>🔑</span> Keyword Analysis</div>
        </div>

    </div>

    <script>
    $('#togglePassword').on('click', function () {
        const $input = $('#passwordInput');
        const $icon  = $('#toggleIcon');
        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $icon.removeClass('bi-eye-slash').addClass('bi-eye');
        } else {
            $input.attr('type', 'password');
            $icon.removeClass('bi-eye').addClass('bi-eye-slash');
        }
    });
    </script>
</body>
</html>
