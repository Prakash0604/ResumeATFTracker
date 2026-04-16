<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — ResumeIQ</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;background:#0f1117;color:#e2e8f0;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:32px 24px}
        .card{background:#12141e;border:1px solid #2d3148;border-radius:20px;padding:44px 40px;width:100%;max-width:460px;box-shadow:0 24px 64px rgba(0,0,0,.5)}
        .brand{display:flex;align-items:center;gap:10px;margin-bottom:28px}
        .brand .icon{width:36px;height:36px;background:#6c63ff;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:16px}
        .brand .name{font-size:17px;font-weight:700;color:#f8fafc}
        h1{font-size:20px;font-weight:700;color:#f8fafc;margin-bottom:4px}
        .sub{font-size:13px;color:#64748b;margin-bottom:28px}
        .form-group{margin-bottom:16px}
        label{display:block;font-size:11px;font-weight:600;color:#94a3b8;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.6px}
        input[type=text],input[type=email],input[type=password]{width:100%;background:#0f1117;border:1px solid #2d3148;border-radius:9px;color:#f1f5f9;padding:12px 14px;font-family:'Inter',sans-serif;font-size:14px;outline:none;transition:border-color .2s,box-shadow .2s}
        input::placeholder{color:#475569}
        input:focus{border-color:#6c63ff;box-shadow:0 0 0 3px rgba(108,99,255,.12)}
        input.valid{border-color:#10d9a0}
        input.invalid{border-color:#ef4444}
        .pw-wrap{position:relative}
        .pw-wrap input{padding-right:44px}
        .eye{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:#475569;cursor:pointer;font-size:15px}
        .hint{font-size:11px;margin-top:5px;display:flex;align-items:center;gap:4px}
        .hint.ok{color:#10d9a0} .hint.err{color:#f87171}
        .strength-bar{height:3px;background:#2d3148;border-radius:99px;overflow:hidden;margin-top:7px}
        .strength-fill{height:100%;border-radius:99px;width:0%;transition:width .4s,background .4s}
        .reqs{display:grid;grid-template-columns:1fr 1fr;gap:4px 12px;margin-top:7px}
        .req{font-size:11px;color:#475569;display:flex;align-items:center;gap:4px;transition:color .2s}
        .req.met{color:#10d9a0}
        .terms-box{display:flex;align-items:flex-start;gap:10px;padding:12px 14px;background:#0f1117;border:1px solid #2d3148;border-radius:9px;cursor:pointer;margin-bottom:18px}
        .terms-box input[type=checkbox]{width:15px;height:15px;accent-color:#6c63ff;margin-top:2px;flex-shrink:0}
        .terms-box span{font-size:12px;color:#64748b;line-height:1.5}
        .terms-box a{color:#6c63ff;text-decoration:none}
        .err-alert{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.3);border-radius:9px;padding:12px 16px;font-size:13px;color:#f87171;margin-bottom:20px}
        .btn{width:100%;background:#6c63ff;color:#fff;border:none;border-radius:9px;padding:14px;font-family:'Inter',sans-serif;font-size:14px;font-weight:600;cursor:pointer;transition:all .2s}
        .btn:hover{background:#8b84ff;box-shadow:0 0 20px rgba(108,99,255,.3);transform:translateY(-1px)}
        .btn:disabled{opacity:.5;cursor:not-allowed;transform:none}
        .footer{text-align:center;font-size:13px;color:#475569;margin-top:18px}
        .footer a{color:#6c63ff;text-decoration:none}
        @media(max-width:500px){.card{padding:32px 24px}}
    </style>
</head>
<body>
<div class="card">
    <div class="brand">
        <div class="icon">⚡</div>
        <div class="name">ResumeIQ</div>
    </div>
    <h1>Create your account</h1>
    <div class="sub">Free forever · No credit card required</div>

    @if($errors->any())
    <div class="err-alert">
        @foreach($errors->all() as $e) <div>⚠ {{ $e }}</div> @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('register') }}" id="regForm" novalidate>
        @csrf
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" id="name" placeholder="Jane Doe"
                   value="{{ old('name') }}" autocomplete="name" required>
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" id="email" placeholder="jane@example.com"
                   value="{{ old('email') }}" autocomplete="email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <div class="pw-wrap">
                <input type="password" name="password" id="pw1" placeholder="Min 8 characters" autocomplete="new-password" required>
                <button type="button" class="eye" data-t="pw1">👁</button>
            </div>
            <div class="strength-bar"><div class="strength-fill" id="sbar"></div></div>
            <div class="reqs">
                <div class="req" id="r-len">✕ 8+ characters</div>
                <div class="req" id="r-up">✕ Uppercase</div>
                <div class="req" id="r-low">✕ Lowercase</div>
                <div class="req" id="r-num">✕ Number</div>
            </div>
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <div class="pw-wrap">
                <input type="password" name="password_confirmation" id="pw2" placeholder="Repeat password" autocomplete="new-password" required>
                <button type="button" class="eye" data-t="pw2">👁</button>
            </div>
            <div class="hint" id="matchHint" style="display:none"></div>
        </div>
        <div class="form-group">
            <label class="terms-box" for="terms">
                <input type="checkbox" id="terms" name="terms" value="1" {{ old('terms') ? 'checked' : '' }}>
                <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>. Your data is encrypted and never sold.</span>
            </label>
        </div>
        <button type="submit" class="btn" id="subBtn">Create Account</button>
    </form>
    <div class="footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></div>
</div>
<script>
$(function(){
    // Toggle eye
    $('.eye').on('click',function(){
        var t=$('#'+$(this).data('t'));
        t.attr('type',t.attr('type')==='password'?'text':'password');
    });
    // Password strength
    $('#pw1').on('input',function(){
        var v=$(this).val();
        var reqs={len:v.length>=8,up:/[A-Z]/.test(v),low:/[a-z]/.test(v),num:/[0-9]/.test(v)};
        var score=Object.values(reqs).filter(Boolean).length;
        var colors=['','#ef4444','#f97316','#eab308','#10d9a0'];
        var widths=['0%','25%','50%','75%','100%'];
        $('#sbar').css({width:widths[score],background:colors[score]||'#2d3148'});
        $('#r-len').toggleClass('met',reqs.len).html((reqs.len?'✓':'✕')+' 8+ characters');
        $('#r-up').toggleClass('met',reqs.up).html((reqs.up?'✓':'✕')+' Uppercase');
        $('#r-low').toggleClass('met',reqs.low).html((reqs.low?'✓':'✕')+' Lowercase');
        $('#r-num').toggleClass('met',reqs.num).html((reqs.num?'✓':'✕')+' Number');
        checkMatch();
    });
    // Confirm match
    $('#pw2').on('input',checkMatch);
    function checkMatch(){
        var v1=$('#pw1').val(),v2=$('#pw2').val();
        if(!v2.length){$('#matchHint').hide();return;}
        if(v1===v2){
            $('#matchHint').show().removeClass('err').addClass('ok').html('✓ Passwords match');
            $('#pw2').removeClass('invalid').addClass('valid');
        } else {
            $('#matchHint').show().removeClass('ok').addClass('err').html('✕ Passwords do not match');
            $('#pw2').removeClass('valid').addClass('invalid');
        }
    }
    // Name/email validate on blur
    $('#name').on('blur',function(){
        $(this).toggleClass('valid',$(this).val().trim().length>=2).toggleClass('invalid',$(this).val().trim().length<2&&$(this).val().trim().length>0);
    });
    $('#email').on('blur',function(){
        var ok=/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test($(this).val().trim());
        $(this).toggleClass('valid',ok&&$(this).val().trim().length>0).toggleClass('invalid',!ok&&$(this).val().trim().length>0);
    });
    // Submit
    $('#regForm').on('submit',function(){
        $('#subBtn').prop('disabled',true).text('Creating account…');
    });
});
</script>
</body>
</html>
