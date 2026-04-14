<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — ResumeIQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        :root {
            --bg-base:      #0a0b0f;
            --bg-card:      #111318;
            --bg-input:     #1a1d26;
            --border:       #252836;
            --border-focus: #6c63ff;
            --accent:       #6c63ff;
            --accent-glow:  rgba(108, 99, 255, 0.25);
            --accent-light: #8b84ff;
            --emerald:      #10d9a0;
            --rose:         #ff4f6a;
            --amber:        #f5a623;
            --text-primary: #eef0f8;
            --text-muted:   #545872;
            --text-secondary: #8b90a8;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-base);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient background glow */
        body::before {
            content: '';
            position: fixed;
            top: -200px; left: 50%;
            transform: translateX(-50%);
            width: 900px; height: 600px;
            background: radial-gradient(ellipse, rgba(108,99,255,0.10) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        /* Dot grid texture */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            z-index: 0;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 1;
        }

        /* ── Card ───────────────────────────────── */
        .auth-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 44px 40px;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.5);
        }

        /* ── Brand ──────────────────────────────── */
        .brand { text-align: center; margin-bottom: 32px; }

        .brand-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
            margin: 0 auto 14px;
            box-shadow: 0 0 40px var(--accent-glow);
        }

        .brand-name {
            font-family: 'Syne', sans-serif;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .brand-sub {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* ── Form Groups ─────────────────────────── */
        .form-group { margin-bottom: 16px; }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 7px;
        }

        .input-wrap { position: relative; }

        .input-icon {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 15px;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .form-input {
            width: 100%;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 9px;
            color: var(--text-primary);
            padding: 13px 14px 13px 40px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            -webkit-appearance: none;
        }

        .form-input.no-icon { padding-left: 14px; }

        .form-input::placeholder { color: var(--text-muted); }

        .form-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
            background: #1d2030;
        }

        .form-input:focus + .input-icon,
        .input-wrap:focus-within .input-icon {
            color: var(--accent-light);
        }

        /* Fix icon position when inside input-wrap with icon on right too */
        .toggle-password {
            position: absolute;
            right: 13px; top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 16px;
            padding: 0;
            transition: color 0.2s;
        }
        .toggle-password:hover { color: var(--text-primary); }

        /* ── Field Validation States ─────────────── */
        .form-input.is-valid   { border-color: var(--emerald); }
        .form-input.is-invalid { border-color: var(--rose); }

        .field-hint {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .field-hint.error { color: var(--rose); }
        .field-hint.success { color: var(--emerald); }

        /* ── Password Strength Bar ───────────────── */
        .strength-wrap { margin-top: 8px; }

        .strength-track {
            height: 4px;
            background: var(--border);
            border-radius: 99px;
            overflow: hidden;
            margin-bottom: 5px;
        }

        .strength-fill {
            height: 100%;
            border-radius: 99px;
            width: 0%;
            transition: width 0.4s ease, background 0.4s ease;
        }

        .strength-label {
            font-size: 11px;
            color: var(--text-muted);
            transition: color 0.3s ease;
        }

        /* ── Password Requirements ───────────────── */
        .requirements-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 12px;
            margin-top: 8px;
        }

        .req-item {
            font-size: 11px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 5px;
            transition: color 0.2s ease;
        }

        .req-item.met { color: var(--emerald); }
        .req-item.met i { color: var(--emerald); }

        .req-item i {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* ── Checkbox ────────────────────────────── */
        .checkbox-wrap {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 14px;
            background: rgba(108,99,255,0.04);
            border: 1px solid rgba(108,99,255,0.15);
            border-radius: 9px;
            margin-bottom: 20px;
            cursor: pointer;
        }

        .checkbox-wrap input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: var(--accent);
            flex-shrink: 0;
            margin-top: 2px;
            cursor: pointer;
        }

        .checkbox-wrap .checkbox-text {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .checkbox-wrap .checkbox-text a {
            color: var(--accent-light);
            text-decoration: none;
        }

        .checkbox-wrap .checkbox-text a:hover { text-decoration: underline; }

        /* ── Submit Button ───────────────────────── */
        .btn-submit {
            width: 100%;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 9px;
            padding: 15px;
            font-family: 'Syne', sans-serif;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover:not(:disabled) {
            background: var(--accent-light);
            box-shadow: 0 0 32px var(--accent-glow);
            transform: translateY(-1px);
        }

        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* ── Error Alert ─────────────────────────── */
        .alert-error {
            background: rgba(255,79,106,0.1);
            border: 1px solid rgba(255,79,106,0.3);
            border-radius: 9px;
            padding: 14px 16px;
            font-size: 13px;
            color: var(--rose);
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        /* ── Benefits strip ──────────────────────── */
        .benefits {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            flex-wrap: wrap;
        }

        .benefit {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .benefit-icon { color: var(--emerald); font-size: 14px; }

        /* ── Auth link ───────────────────────────── */
        .auth-link {
            text-align: center;
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 20px;
        }

        .auth-link a {
            color: var(--accent-light);
            text-decoration: none;
            font-weight: 500;
        }

        .auth-link a:hover { text-decoration: underline; }

        /* Mobile */
        @media (max-width: 480px) {
            .auth-card { padding: 32px 24px; }
            .form-row  { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>
<div class="auth-wrapper">

    <div class="auth-card">

        {{-- ── Brand ──────────────────────────────── --}}
        <div class="brand">
            <div class="brand-icon">⚡</div>
            <div class="brand-name">ResumeIQ</div>
            <div class="brand-sub">Create your free account — no credit card required</div>
        </div>

        {{-- ── Validation Errors ───────────────────── --}}
        @if($errors->any())
            <div class="alert-error">
                <i class="bi bi-exclamation-triangle-fill" style="flex-shrink:0;font-size:16px;margin-top:1px;"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ── Registration Form ───────────────────── --}}
        <form method="POST" action="{{ route('register') }}" id="registerForm" novalidate>
            @csrf

            {{-- Full Name --}}
            <div class="form-group">
                <label for="name">Full Name</label>
                <div class="input-wrap">
                    <i class="bi bi-person input-icon"></i>
                    <input type="text" id="name" name="name"
                           class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           placeholder="Jane Doe"
                           value="{{ old('name') }}"
                           autocomplete="name"
                           required>
                </div>
                @error('name')
                    <div class="field-hint error"><i class="bi bi-x-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrap">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" id="email" name="email"
                           class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           placeholder="jane@example.com"
                           value="{{ old('email') }}"
                           autocomplete="email"
                           required>
                </div>
                @error('email')
                    <div class="field-hint error"><i class="bi bi-x-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" id="password" name="password"
                           class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                           placeholder="Create a strong password"
                           autocomplete="new-password"
                           required>
                    <button type="button" class="toggle-password" data-target="password" aria-label="Toggle password visibility">
                        <i class="bi bi-eye-slash" id="toggleIcon1"></i>
                    </button>
                </div>
                @error('password')
                    <div class="field-hint error"><i class="bi bi-x-circle"></i> {{ $message }}</div>
                @else
                    {{-- Password Strength Indicator --}}
                    <div class="strength-wrap" id="strengthWrap" style="display:none;">
                        <div class="strength-track">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <div class="strength-label" id="strengthLabel">Enter a password</div>

                        {{-- Requirements Checklist --}}
                        <div class="requirements-grid" id="reqGrid">
                            <div class="req-item" id="req-length">
                                <i class="bi bi-x-circle"></i> 8+ characters
                            </div>
                            <div class="req-item" id="req-upper">
                                <i class="bi bi-x-circle"></i> Uppercase letter
                            </div>
                            <div class="req-item" id="req-lower">
                                <i class="bi bi-x-circle"></i> Lowercase letter
                            </div>
                            <div class="req-item" id="req-number">
                                <i class="bi bi-x-circle"></i> Number
                            </div>
                        </div>
                    </div>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <div class="input-wrap">
                    <i class="bi bi-shield-lock input-icon"></i>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="form-input"
                           placeholder="Repeat your password"
                           autocomplete="new-password"
                           required>
                    <button type="button" class="toggle-password" data-target="password_confirmation" aria-label="Toggle confirm password">
                        <i class="bi bi-eye-slash" id="toggleIcon2"></i>
                    </button>
                </div>
                <div class="field-hint" id="confirmHint" style="display:none;"></div>
            </div>

            {{-- Terms Checkbox --}}
            <div class="form-group">
                <label class="checkbox-wrap" for="terms">
                    <input type="checkbox" id="terms" name="terms" value="1"
                           {{ old('terms') ? 'checked' : '' }}>
                    <span class="checkbox-text">
                        I agree to the <a href="#" onclick="return false;">Terms of Service</a>
                        and <a href="#" onclick="return false;">Privacy Policy</a>.
                        Your resume data is encrypted and never sold.
                    </span>
                </label>
                @error('terms')
                    <div class="field-hint error"><i class="bi bi-x-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-submit" id="submitBtn">
                <i class="bi bi-person-plus"></i>
                Create Free Account
            </button>
        </form>

        {{-- ── Already have account ────────────────── --}}
        <p class="auth-link">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
        </p>

        {{-- ── Benefit chips ────────────────────────── --}}
        <div class="benefits">
            <div class="benefit"><i class="bi bi-check-circle-fill benefit-icon"></i> Free forever plan</div>
            <div class="benefit"><i class="bi bi-shield-check benefit-icon"></i> Data encrypted</div>
            <div class="benefit"><i class="bi bi-lightning-charge benefit-icon"></i> AI-powered scoring</div>
        </div>

    </div>{{-- /auth-card --}}
</div>{{-- /auth-wrapper --}}

<script>
$(function () {

    // ── Password Strength Meter ────────────────────────────
    $('#password').on('input', function () {
        const val = $(this).val();

        if (val.length === 0) {
            $('#strengthWrap').hide();
            return;
        }

        $('#strengthWrap').show();

        // Requirements
        const reqs = {
            length: val.length >= 8,
            upper:  /[A-Z]/.test(val),
            lower:  /[a-z]/.test(val),
            number: /[0-9]/.test(val),
        };

        // Update requirement items
        $.each(reqs, function (key, met) {
            const $item = $('#req-' + key);
            $item.find('i').attr('class', met ? 'bi bi-check-circle-fill' : 'bi bi-x-circle');
            $item.toggleClass('met', met);
        });

        // Calculate strength score (0-4)
        const score = Object.values(reqs).filter(Boolean).length;

        const levels = [
            { label: 'Too weak',   color: '#ef4444', width: '15%' },
            { label: 'Weak',       color: '#f97316', width: '30%' },
            { label: 'Fair',       color: '#f5a623', width: '55%' },
            { label: 'Good',       color: '#38bdf8', width: '78%' },
            { label: 'Strong ✓',   color: '#10d9a0', width: '100%'},
        ];

        const level = levels[score] || levels[0];
        $('#strengthFill').css({ width: level.width, background: level.color });
        $('#strengthLabel').text(level.label).css('color', level.color);

        // Update input border
        $(this).removeClass('is-valid is-invalid');
        if (score >= 4) $(this).addClass('is-valid');
        if (score < 2 && val.length > 4) $(this).addClass('is-invalid');
    });

    // ── Password Match Check ───────────────────────────────
    $('#password_confirmation').on('input', function () {
        const pass    = $('#password').val();
        const confirm = $(this).val();
        const $hint   = $('#confirmHint');

        if (confirm.length === 0) { $hint.hide(); return; }

        if (pass === confirm) {
            $hint.show().html('<i class="bi bi-check-circle-fill"></i> Passwords match')
                 .removeClass('error').addClass('success');
            $(this).addClass('is-valid').removeClass('is-invalid');
        } else {
            $hint.show().html('<i class="bi bi-x-circle"></i> Passwords do not match')
                 .removeClass('success').addClass('error');
            $(this).addClass('is-invalid').removeClass('is-valid');
        }
    });

    // ── Toggle Password Visibility ─────────────────────────
    $('.toggle-password').on('click', function () {
        const target = $(this).data('target');
        const $input = $('#' + target);
        const $icon  = $(this).find('i');

        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $icon.removeClass('bi-eye-slash').addClass('bi-eye');
        } else {
            $input.attr('type', 'password');
            $icon.removeClass('bi-eye').addClass('bi-eye-slash');
        }
    });

    // ── Submit: loading state ──────────────────────────────
    $('#registerForm').on('submit', function () {
        const $btn = $('#submitBtn');
        $btn.prop('disabled', true)
            .html('<i class="bi bi-hourglass-split"></i> Creating account...');
    });

    // ── Real-time name validation ──────────────────────────
    $('#name').on('blur', function () {
        const val = $(this).val().trim();
        if (val.length >= 2) {
            $(this).addClass('is-valid').removeClass('is-invalid');
        } else if (val.length > 0) {
            $(this).addClass('is-invalid').removeClass('is-valid');
        }
    });

    // ── Real-time email format check ───────────────────────
    $('#email').on('blur', function () {
        const val  = $(this).val().trim();
        const ok   = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
        if (val.length === 0) return;
        $(this).toggleClass('is-valid', ok).toggleClass('is-invalid', !ok);
    });

});
</script>
</body>
</html>
