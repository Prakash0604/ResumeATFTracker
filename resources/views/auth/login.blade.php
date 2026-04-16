<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — ResumeIQ</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;background:#0f1117;color:#e2e8f0;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
        .page{display:flex;width:100%;max-width:960px;min-height:540px;border-radius:20px;overflow:hidden;box-shadow:0 32px 80px rgba(0,0,0,0.6)}
        .left{flex:1;background:linear-gradient(135deg,#1a1560 0%,#0f1117 60%);padding:56px 48px;display:flex;flex-direction:column;justify-content:space-between}
        .left-brand{display:flex;align-items:center;gap:12px}
        .left-brand .icon{width:40px;height:40px;background:#6c63ff;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;box-shadow:0 0 24px rgba(108,99,255,0.5)}
        .left-brand .name{font-size:20px;font-weight:700;color:#fff}
        .left-body{flex:1;display:flex;flex-direction:column;justify-content:center;padding:32px 0 0}
        .left-body h2{font-size:28px;font-weight:700;color:#fff;margin-bottom:12px;line-height:1.2}
        .left-body p{font-size:14px;color:#94a3b8;line-height:1.6;margin-bottom:28px;max-width:320px}
        .feature{display:flex;align-items:center;gap:10px;margin-bottom:14px}
        .feature .dot{width:6px;height:6px;background:#6c63ff;border-radius:50%;flex-shrink:0}
        .feature span{font-size:13px;color:#94a3b8}
        .right{width:400px;background:#12141e;padding:48px 40px;display:flex;flex-direction:column;justify-content:center}
        .right h1{font-size:22px;font-weight:700;color:#f8fafc;margin-bottom:6px}
        .right .sub{font-size:13px;color:#64748b;margin-bottom:32px}
        .form-group{margin-bottom:18px}
        .form-group label{display:block;font-size:12px;font-weight:500;color:#94a3b8;margin-bottom:7px;text-transform:uppercase;letter-spacing:0.5px}
        .input-wrap{position:relative}
        .input-wrap input{width:100%;background:#0f1117;border:1px solid #2d3148;border-radius:10px;color:#f1f5f9;padding:13px 16px;font-family:'Inter',sans-serif;font-size:14px;outline:none;transition:border-color .2s,box-shadow .2s}
        .input-wrap input::placeholder{color:#475569}
        .input-wrap input:focus{border-color:#6c63ff;box-shadow:0 0 0 3px rgba(108,99,255,0.15)}
        .eye-btn{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:#475569;cursor:pointer;font-size:16px;padding:0}
        .eye-btn:hover{color:#94a3b8}
        .row{display:flex;align-items:center;justify-content:space-between;margin-bottom:6px}
        .remember{display:flex;align-items:center;gap:6px;font-size:13px;color:#64748b;cursor:pointer}
        .remember input{accent-color:#6c63ff;width:14px;height:14px}
        .forgot{font-size:13px;color:#6c63ff;text-decoration:none}
        .forgot:hover{color:#8b84ff}
        .err{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.3);border-radius:10px;padding:12px 16px;font-size:13px;color:#f87171;margin-bottom:20px;display:flex;align-items:center;gap:8px}
        .btn-submit{width:100%;background:#6c63ff;color:#fff;border:none;border-radius:10px;padding:14px;font-family:'Inter',sans-serif;font-size:14px;font-weight:600;cursor:pointer;transition:all .2s;margin-top:8px}
        .btn-submit:hover{background:#8b84ff;box-shadow:0 0 24px rgba(108,99,255,0.3);transform:translateY(-1px)}
        .btn-submit:disabled{opacity:.5;cursor:not-allowed;transform:none}
        .footer{text-align:center;font-size:13px;color:#475569;margin-top:20px}
        .footer a{color:#6c63ff;text-decoration:none}
        .footer a:hover{color:#8b84ff}
        @media(max-width:700px){.left{display:none}.right{width:100%;border-radius:20px}.page{min-height:auto}}
    </style>
</head>
<body>
<div class="page">
    <div class="left">
        <div class="left-brand">
            <div class="icon">⚡</div>
            <div class="name">ResumeIQ</div>
        </div>
        <div class="left-body">
            <h2>Land your dream job with a better resume</h2>
            <p>AI-powered ATS analysis that tells you exactly what to fix and how to fix it.</p>
            <div class="feature"><div class="dot"></div><span>Instant ATS compatibility score</span></div>
            <div class="feature"><div class="dot"></div><span>Keyword gap analysis vs job description</span></div>
            <div class="feature"><div class="dot"></div><span>Prioritized, actionable improvement tips</span></div>
            <div class="feature"><div class="dot"></div><span>Section-by-section scoring breakdown</span></div>
        </div>
        <div style="font-size:12px;color:#334155;">Trusted by 10,000+ job seekers</div>
    </div>

    <div class="right">
        <h1>Welcome back</h1>
        <div class="sub">Sign in to your ResumeIQ account</div>

        @if($errors->any())
        <div class="err">⚠ {{ $errors->first() }}</div>
        @endif

        @if(session('success'))
        <div style="background:rgba(16,217,160,.08);border:1px solid rgba(16,217,160,.3);border-radius:10px;padding:12px 16px;font-size:13px;color:#10d9a0;margin-bottom:20px;">
            ✓ {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" id="loginForm">
            @csrf
            <div class="form-group">
                <label>Email</label>
                <div class="input-wrap">
                    <input type="email" name="email" placeholder="you@example.com"
                           value="{{ old('email') }}" required autofocus>
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap">
                    <input type="password" name="password" id="pw" placeholder="••••••••" required>
                    <button type="button" class="eye-btn" id="eyeBtn">👁</button>
                </div>
            </div>
            <div class="row">
                <label class="remember">
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="#" class="forgot">Forgot password?</a>
            </div>
            <button type="submit" class="btn-submit" id="submitBtn">Sign In</button>
        </form>

        <div class="footer">
            Don't have an account? <a href="{{ route('register') }}">Create one free</a>
        </div>
    </div>
</div>
<script>
$('#eyeBtn').on('click',function(){
    var i=$('#pw');
    i.attr('type',i.attr('type')==='password'?'text':'password');
});
$('#loginForm').on('submit',function(){
    $('#submitBtn').prop('disabled',true).text('Signing in…');
});
</script>
</body>
</html>
