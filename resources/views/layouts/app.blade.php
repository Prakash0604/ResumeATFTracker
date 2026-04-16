<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','ResumeIQ') — ATS Tracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        html{scroll-behavior:smooth}
        body{font-family:'Inter',sans-serif;background:#0f1117;color:#e2e8f0;font-size:14px;line-height:1.6;min-height:100vh;overflow-x:hidden}
        ::-webkit-scrollbar{width:5px;height:5px}
        ::-webkit-scrollbar-track{background:#0f1117}
        ::-webkit-scrollbar-thumb{background:#2d3148;border-radius:99px}
        .layout{display:flex;min-height:100vh}

        /* SIDEBAR */
        .sidebar{width:230px;min-height:100vh;background:#12141e;border-right:1px solid #1e2130;display:flex;flex-direction:column;position:fixed;left:0;top:0;bottom:0;z-index:100;transition:transform .25s ease}
        .sb-brand{padding:18px 18px 14px;border-bottom:1px solid #1e2130;display:flex;align-items:center;gap:10px}
        .sb-brand .icon{width:32px;height:32px;background:#6c63ff;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0;box-shadow:0 0 16px rgba(108,99,255,.3)}
        .sb-brand .bname{font-size:14px;font-weight:700;color:#f8fafc}
        .sb-brand .btag{font-size:10px;color:#475569;text-transform:uppercase;letter-spacing:.8px}
        .sb-nav{flex:1;padding:10px 8px;overflow-y:auto}
        .sb-sec{font-size:10px;font-weight:600;color:#334155;text-transform:uppercase;letter-spacing:1.2px;padding:10px 10px 5px}
        .sb-link{display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:7px;color:#64748b;text-decoration:none;font-size:13px;font-weight:500;transition:all .15s;margin-bottom:1px;cursor:pointer;border:none;background:none;width:100%;text-align:left;position:relative}
        .sb-link i{font-size:14px;flex-shrink:0}
        .sb-link:hover{color:#e2e8f0;background:#1e2130}
        .sb-link.active{color:#a5b4fc;background:rgba(108,99,255,.12)}
        .sb-link.active::before{content:'';position:absolute;left:0;top:6px;bottom:6px;width:2px;background:#6c63ff;border-radius:0 2px 2px 0}
        .sb-badge{margin-left:auto;background:rgba(108,99,255,.2);color:#a5b4fc;font-size:10px;font-weight:700;padding:1px 6px;border-radius:99px;font-family:'JetBrains Mono',monospace}
        .sb-footer{padding:10px 8px;border-top:1px solid #1e2130}
        .sb-user{display:flex;align-items:center;gap:8px;padding:9px 10px;border-radius:8px;background:#1e2130}
        .sb-avatar{width:28px;height:28px;border-radius:50%;background:#6c63ff;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#fff;flex-shrink:0}
        .sb-uname{font-size:12px;font-weight:600;color:#e2e8f0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .sb-uplan{font-size:10px;color:#10d9a0;text-transform:uppercase;letter-spacing:.4px}
        .sb-logout{background:none;border:none;color:#475569;cursor:pointer;padding:4px;border-radius:5px;font-size:14px;flex-shrink:0;transition:color .15s;line-height:1}
        .sb-logout:hover{color:#e2e8f0}

        /* MAIN */
        .main{margin-left:230px;flex:1;display:flex;flex-direction:column;min-height:100vh}
        .topbar{height:54px;background:#12141e;border-bottom:1px solid #1e2130;display:flex;align-items:center;padding:0 24px;gap:12px;position:sticky;top:0;z-index:50}
        .tb-toggle{display:none;background:none;border:1px solid #2d3148;border-radius:6px;color:#94a3b8;padding:5px 8px;cursor:pointer;font-size:14px;line-height:1}
        .tb-title{font-size:14px;font-weight:600;color:#f8fafc;flex:1}
        .btn-analyze{display:flex;align-items:center;gap:6px;background:#6c63ff;color:#fff;border:none;border-radius:7px;padding:7px 14px;font-family:'Inter',sans-serif;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s}
        .btn-analyze:hover{background:#8b84ff;box-shadow:0 0 16px rgba(108,99,255,.25)}
        .page-body{flex:1;padding:24px}
        .flash{padding:11px 14px;border-radius:8px;font-size:13px;margin-bottom:18px;display:flex;align-items:center;gap:8px}
        .flash-ok{background:rgba(16,217,160,.07);border:1px solid rgba(16,217,160,.2);color:#10d9a0}
        .flash-err{background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);color:#f87171}

        /* CARDS */
        .card{background:#12141e;border:1px solid #1e2130;border-radius:12px}
        .card:hover{border-color:#2d3148}
        .card-p{padding:20px}
        .stat-card{background:#12141e;border:1px solid #1e2130;border-radius:11px;padding:18px;position:relative;overflow:hidden;transition:all .2s}
        .stat-card::after{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--c,#6c63ff)}
        .stat-card:hover{border-color:#2d3148;transform:translateY(-1px)}
        .stat-num{font-size:26px;font-weight:700;color:#f8fafc;font-family:'JetBrains Mono',monospace;line-height:1;margin-bottom:4px}
        .stat-lbl{font-size:12px;color:#64748b}
        .stat-icon{font-size:20px;margin-bottom:10px;display:block}

        /* SCORE RING */
        .ring-svg{transform:rotate(-90deg)}
        .ring-track{fill:none;stroke:#1e2130;stroke-width:5}
        .ring-fill{fill:none;stroke-width:5;stroke-linecap:round;transition:stroke-dashoffset 1.2s cubic-bezier(.4,0,.2,1)}

        /* BARS */
        .bar-row{margin-bottom:11px}
        .bar-hd{display:flex;justify-content:space-between;align-items:center;margin-bottom:4px}
        .bar-lbl{font-size:12px;color:#94a3b8}
        .bar-val{font-size:11px;font-family:'JetBrains Mono',monospace;color:#64748b}
        .bar-track{height:4px;background:#1e2130;border-radius:99px;overflow:hidden}
        .bar-fill{height:100%;border-radius:99px;width:0%;transition:width 1s cubic-bezier(.4,0,.2,1)}

        /* TAGS */
        .tag{display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:600}
        .tag-purple{background:rgba(108,99,255,.12);color:#a5b4fc;border:1px solid rgba(108,99,255,.2)}
        .tag-green{background:rgba(16,217,160,.08);color:#10d9a0;border:1px solid rgba(16,217,160,.2)}
        .tag-red{background:rgba(239,68,68,.08);color:#f87171;border:1px solid rgba(239,68,68,.2)}
        .tag-amber{background:rgba(245,158,11,.08);color:#fbbf24;border:1px solid rgba(245,158,11,.2)}
        .tag-sky{background:rgba(56,189,248,.08);color:#38bdf8;border:1px solid rgba(56,189,248,.2)}
        .tag-gray{background:#1e2130;color:#64748b;border:1px solid #2d3148}
        .pri-critical{background:rgba(239,68,68,.08);color:#f87171;border:1px solid rgba(239,68,68,.2)}
        .pri-high{background:rgba(249,115,22,.08);color:#fb923c;border:1px solid rgba(249,115,22,.2)}
        .pri-medium{background:rgba(245,158,11,.08);color:#fbbf24;border:1px solid rgba(245,158,11,.2)}
        .pri-low{background:rgba(16,217,160,.08);color:#10d9a0;border:1px solid rgba(16,217,160,.2)}

        /* KEYWORDS */
        .kw-cloud{display:flex;flex-wrap:wrap;gap:6px}
        .kw{display:inline-flex;align-items:center;gap:3px;padding:3px 10px;border-radius:99px;font-size:11px;font-family:'JetBrains Mono',monospace;border:1px solid #2d3148;background:#1e2130;color:#64748b}
        .kw.matched{background:rgba(16,217,160,.06);border-color:rgba(16,217,160,.25);color:#10d9a0}
        .kw.missing{background:rgba(239,68,68,.06);border-color:rgba(239,68,68,.25);color:#f87171}

        /* FEEDBACK */
        .fb-card{background:#0f1117;border:1px solid #1e2130;border-left:3px solid var(--bc,#6c63ff);border-radius:9px;padding:14px;margin-bottom:8px;transition:all .2s}
        .fb-card:hover{border-color:#2d3148;border-left-color:var(--bc)}
        .fb-card.addressed{opacity:.4}
        .fb-card.addressed .fb-title{text-decoration:line-through}

        /* BUTTONS */
        .btn-primary{display:inline-flex;align-items:center;gap:6px;background:#6c63ff;color:#fff;border:none;border-radius:7px;padding:8px 16px;font-family:'Inter',sans-serif;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .2s}
        .btn-primary:hover{background:#8b84ff;transform:translateY(-1px)}
        .btn-ghost{display:inline-flex;align-items:center;gap:6px;background:transparent;color:#64748b;border:1px solid #2d3148;border-radius:7px;padding:8px 16px;font-family:'Inter',sans-serif;font-size:13px;font-weight:500;cursor:pointer;text-decoration:none;transition:all .2s}
        .btn-ghost:hover{border-color:#475569;color:#e2e8f0;background:#1e2130}
        .btn-icon{display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#1e2130;border:1px solid #2d3148;color:#64748b;cursor:pointer;font-size:13px;transition:all .2s}
        .btn-icon:hover{background:#2d3148;color:#e2e8f0}

        /* FORM */
        .form-input{width:100%;background:#0f1117;border:1px solid #2d3148;border-radius:7px;color:#f1f5f9;padding:10px 13px;font-family:'Inter',sans-serif;font-size:13px;outline:none;transition:border-color .2s,box-shadow .2s}
        .form-input::placeholder{color:#475569}
        .form-input:focus{border-color:#6c63ff;box-shadow:0 0 0 3px rgba(108,99,255,.1)}
        textarea.form-input{resize:vertical}
        .field-lbl{display:block;font-size:11px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px}

        /* MODAL */
        .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.65);z-index:999;display:none;align-items:center;justify-content:center;padding:20px}
        .modal-overlay.open{display:flex}
        .modal-box{background:#12141e;border:1px solid #2d3148;border-radius:16px;width:100%;max-width:500px;box-shadow:0 32px 80px rgba(0,0,0,.6);animation:min .2s ease}
        @keyframes min{from{transform:translateY(14px);opacity:0}to{transform:translateY(0);opacity:1}}
        .modal-hd{padding:20px 22px 16px;border-bottom:1px solid #1e2130;display:flex;align-items:center;justify-content:space-between}
        .modal-hd h3{font-size:15px;font-weight:700;color:#f8fafc}
        .modal-close{background:none;border:none;color:#475569;font-size:17px;cursor:pointer;padding:3px;border-radius:5px;line-height:1;transition:color .15s}
        .modal-close:hover{color:#e2e8f0}
        .modal-body{padding:20px 22px}
        .modal-ft{padding:14px 22px;border-top:1px solid #1e2130;display:flex;gap:8px;justify-content:flex-end}

        /* UPLOAD ZONE */
        .upload-zone{border:2px dashed #2d3148;border-radius:10px;padding:28px 20px;text-align:center;cursor:pointer;transition:all .2s;background:#0f1117;position:relative;overflow:hidden}
        .upload-zone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;font-size:0}
        .upload-zone:hover,.upload-zone.over{border-color:#6c63ff;background:rgba(108,99,255,.03)}
        .uz-icon{font-size:30px;color:#334155;display:block;margin-bottom:8px;transition:color .2s}
        .upload-zone:hover .uz-icon,.upload-zone.over .uz-icon{color:#6c63ff}
        .uz-h{font-size:13px;font-weight:600;color:#94a3b8;margin-bottom:3px}
        .uz-p{font-size:11px;color:#475569}
        .uz-sel{margin-top:10px;padding:9px 12px;background:#1e2130;border-radius:7px;display:none;text-align:left;border:1px solid rgba(16,217,160,.25)}
        .uz-fname{font-size:12px;color:#10d9a0;font-weight:500}
        .uz-fsize{font-size:10px;color:#475569;margin-top:1px}
        .up-prog{display:none;margin-top:12px}
        .up-lbl{font-size:11px;color:#64748b;display:flex;justify-content:space-between;margin-bottom:5px}
        .up-track{height:3px;background:#1e2130;border-radius:99px;overflow:hidden}
        .up-fill{height:100%;background:linear-gradient(90deg,#6c63ff,#a5b4fc);border-radius:99px;width:0%;transition:width .3s}

        /* DIVIDER */
        .sec-hd{display:flex;align-items:center;gap:10px;margin:24px 0 16px}
        .sec-hd h3{font-size:13px;font-weight:700;color:#94a3b8;white-space:nowrap}
        .sec-hd .line{flex:1;height:1px;background:#1e2130}

        /* TOAST */
        #toasts{position:fixed;bottom:18px;right:18px;z-index:9999;display:flex;flex-direction:column;gap:7px}
        .toast{background:#1e2130;border:1px solid #2d3148;border-radius:9px;padding:11px 14px;font-size:13px;display:flex;align-items:center;gap:9px;box-shadow:0 8px 32px rgba(0,0,0,.4);animation:tin .2s ease;min-width:240px;max-width:320px}
        @keyframes tin{from{transform:translateX(14px);opacity:0}to{transform:translateX(0);opacity:1}}

        /* MISC */
        .fade-up{animation:fup .35s ease both}
        @keyframes fup{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}

        @media(max-width:900px){
            .sidebar{transform:translateX(-100%)}
            .sidebar.open{transform:translateX(0)}
            .main{margin-left:0}
            .tb-toggle{display:flex;align-items:center}
            .page-body{padding:16px}
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="layout">

{{-- SIDEBAR --}}
<aside class="sidebar" id="sidebar">
    <div class="sb-brand">
        <div class="icon">⚡</div>
        <div>
            <div class="bname">ResumeIQ</div>
            <div class="btag">ATS Tracker</div>
        </div>
    </div>
    <nav class="sb-nav">
        <div class="sb-sec">Main</div>
        <a href="{{ route('dashboard') }}" class="sb-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>
        <a href="{{ route('resumes.index') }}" class="sb-link {{ request()->routeIs('resumes.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-person"></i> My Resumes
            @php $pCount = auth()->user()->resumes()->whereIn('status',['uploaded','processing'])->count(); @endphp
            @if($pCount > 0)<span class="sb-badge">{{ $pCount }}</span>@endif
        </a>
        <div class="sb-sec" style="margin-top:6px;">Actions</div>
        <button class="sb-link" id="openUploadSb">
            <i class="bi bi-cloud-upload"></i> Upload Resume
        </button>
    </nav>
    <div class="sb-footer">
        <div class="sb-user">
            <div class="sb-avatar">{{ strtoupper(substr(auth()->user()->name,0,2)) }}</div>
            <div style="flex:1;min-width:0;">
                <div class="sb-uname">{{ auth()->user()->name }}</div>
                <div class="sb-uplan">{{ ucfirst(auth()->user()->plan) }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sb-logout" title="Sign out">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- MAIN --}}
<div class="main">
    <header class="topbar">
        <button class="tb-toggle" id="sbToggle"><i class="bi bi-list"></i></button>
        <div class="tb-title">@yield('page-title','Dashboard')</div>
        <div>
            <button class="btn-analyze" id="openUploadTop">
                <i class="bi bi-plus-lg"></i>
                <span>Analyze Resume</span>
            </button>
        </div>
    </header>

    @if(session('success'))
    <div style="padding:14px 24px 0;">
        <div class="flash flash-ok"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
    </div>
    @endif
    @if(session('error'))
    <div style="padding:14px 24px 0;">
        <div class="flash flash-err"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
    </div>
    @endif

    <main class="page-body">@yield('content')</main>
</div>
</div>

{{-- Sidebar overlay --}}
<div id="sbOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:99;" onclick="closeSidebar()"></div>

{{-- UPLOAD MODAL --}}
<div class="modal-overlay" id="uploadModal">
    <div class="modal-box">
        <div class="modal-hd">
            <h3><i class="bi bi-cpu" style="color:#6c63ff;margin-right:6px;"></i>Analyze Resume</h3>
            <button class="modal-close" id="closeModal">✕</button>
        </div>
        <div class="modal-body">
            {{-- Drop Zone — file input covers the whole zone so clicking anywhere works --}}
            <div class="upload-zone" id="dropZone">
                <input type="file" id="resumeInput" accept=".pdf,.docx,.doc,.txt">
                <span class="uz-icon"><i class="bi bi-cloud-arrow-up"></i></span>
                <div class="uz-h">Drop your resume here, or click to browse</div>
                <div class="uz-p">PDF · DOCX · TXT — Max 5 MB</div>
                <div class="uz-sel" id="fileSelected">
                    <div class="uz-fname" id="selName"></div>
                    <div class="uz-fsize" id="selSize"></div>
                </div>
            </div>

            <div id="uploadErr" style="display:none;margin-top:10px;" class="flash flash-err">
                <i class="bi bi-exclamation-triangle-fill"></i> <span id="uploadErrMsg"></span>
            </div>

            {{-- Job targeting --}}
            <div style="margin-top:18px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                    <div style="flex:1;height:1px;background:#1e2130;"></div>
                    <span style="font-size:10px;color:#334155;text-transform:uppercase;letter-spacing:.8px;">Optional — Match a Job</span>
                    <div style="flex:1;height:1px;background:#1e2130;"></div>
                </div>
                <div style="margin-bottom:10px;">
                    <label class="field-lbl">Job Title</label>
                    <input type="text" class="form-input" id="jobTitle" placeholder="e.g. Product Manager at Stripe">
                </div>
                <div>
                    <label class="field-lbl">Job Description <span style="font-weight:400;text-transform:none;color:#334155;">(paste for keyword analysis)</span></label>
                    <textarea class="form-input" id="jobDesc" rows="3" placeholder="Paste the job description here…"></textarea>
                </div>
            </div>

            <div class="up-prog" id="upProg">
                <div class="up-lbl"><span id="upLbl">Uploading…</span><span id="upPct">0%</span></div>
                <div class="up-track"><div class="up-fill" id="upFill"></div></div>
            </div>
        </div>
        <div class="modal-ft">
            <button class="btn-ghost" id="cancelModal">Cancel</button>
            <button class="btn-primary" id="uploadBtn">
                <i class="bi bi-cpu"></i> Analyze
            </button>
        </div>
    </div>
</div>

<div id="toasts"></div>

<script>
$(function(){
    $.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});

    // Sidebar
    window.closeSidebar=function(){$('#sidebar').removeClass('open');$('#sbOverlay').hide()};
    $('#sbToggle').on('click',function(){$('#sidebar').addClass('open');$('#sbOverlay').show()});

    // Modal
    function openModal(){resetUpload();$('#uploadModal').addClass('open')}
    function closeModal(){$('#uploadModal').removeClass('open')}
    $('#openUploadSb,#openUploadTop').on('click',openModal);
    $('#closeModal,#cancelModal').on('click',closeModal);
    $('#uploadModal').on('click',function(e){if($(e.target).is('#uploadModal'))closeModal()});

    // File input change — fires when user selects via dialog OR we programmatically set
    $('#resumeInput').on('change',function(){
        if(this.files&&this.files.length>0)handleFile(this.files[0]);
    });

    // Drag events on zone (file input overlay handles click-to-browse natively)
    $('#dropZone').on('dragover',function(e){
        e.preventDefault();e.stopPropagation();$(this).addClass('over');
    }).on('dragleave dragend',function(e){
        e.preventDefault();e.stopPropagation();$(this).removeClass('over');
    }).on('drop',function(e){
        e.preventDefault();e.stopPropagation();$(this).removeClass('over');
        var f=e.originalEvent.dataTransfer.files;
        if(f&&f.length>0)handleFile(f[0]);
    });

    var pickedFile=null;

    function handleFile(f){
        $('#uploadErr').hide();
        if(f.size>5*1024*1024){showErr('File too large — max 5 MB.');return}
        var ext=f.name.split('.').pop().toLowerCase();
        if(!['pdf','docx','doc','txt'].includes(ext)){showErr('Accepted formats: PDF, DOCX, DOC, TXT.');return}
        pickedFile=f;
        $('#selName').text(f.name);
        $('#selSize').text(fmtB(f.size));
        $('#fileSelected').show();
        $('#dropZone').css('border-color','#10d9a0');
    }

    function showErr(m){
        $('#uploadErrMsg').text(m);$('#uploadErr').show();
        pickedFile=null;$('#fileSelected').hide();$('#dropZone').css('border-color','');
    }

    function resetUpload(){
        pickedFile=null;
        $('#resumeInput').val('');
        $('#fileSelected').hide();
        $('#dropZone').css('border-color','');
        $('#uploadErr').hide();
        $('#upProg').hide();
        $('#upFill').css('width','0%');
        $('#jobTitle,#jobDesc').val('');
        $('#uploadBtn').prop('disabled',false).html('<i class="bi bi-cpu"></i> Analyze');
    }

    function fmtB(b){
        if(b<1024)return b+' B';
        if(b<1048576)return(b/1024).toFixed(1)+' KB';
        return(b/1048576).toFixed(1)+' MB';
    }

    // Submit
    $('#uploadBtn').on('click',function(){
        if(!pickedFile){showErr('Please select a resume file first.');return}
        var fd=new FormData();
        fd.append('resume',pickedFile,pickedFile.name);
        fd.append('_token',$('meta[name="csrf-token"]').attr('content'));
        fd.append('job_title',$('#jobTitle').val().trim());
        fd.append('job_description',$('#jobDesc').val().trim());

        $('#uploadBtn').prop('disabled',true).html('<i class="bi bi-hourglass-split"></i> Uploading…');
        $('#upProg').show();$('#uploadErr').hide();

        var pct=0,ticker=setInterval(function(){
            pct=Math.min(pct+Math.random()*10,85);
            $('#upFill').css('width',pct+'%');$('#upPct').text(Math.round(pct)+'%');
        },300);

        $.ajax({
            url:'/resumes/upload',type:'POST',data:fd,contentType:false,processData:false,
            success:function(r){
                clearInterval(ticker);
                $('#upFill').css('width','100%');$('#upPct').text('100%');$('#upLbl').text('Done! Redirecting…');
                showToast('✅ Uploaded — analysis queued!','green');
                setTimeout(function(){window.location.href=r.redirect},700);
            },
            error:function(xhr){
                clearInterval(ticker);$('#upProg').hide();
                $('#uploadBtn').prop('disabled',false).html('<i class="bi bi-cpu"></i> Analyze');
                var m='Upload failed. Please try again.';
                if(xhr.responseJSON){
                    if(xhr.responseJSON.message)m=xhr.responseJSON.message;
                    else if(xhr.responseJSON.errors)m=Object.values(xhr.responseJSON.errors).flat().join(' ');
                }
                showErr(m);
            }
        });
    });

    // Toast
    window.showToast=function(msg,type){
        var c={green:'#10d9a0',red:'#f87171',amber:'#fbbf24',blue:'#38bdf8'};
        var $t=$('<div class="toast">').css('border-left','2px solid '+(c[type]||c.blue))
              .html('<span style="color:'+(c[type]||c.blue)+'">'+msg+'</span>');
        $('#toasts').append($t);
        setTimeout(function(){$t.css({opacity:0,transition:'opacity .3s'});setTimeout(function(){$t.remove()},300)},3500);
    };

    // Animate bar fills
    setTimeout(function(){$('[data-fill]').each(function(){$(this).css('width',$(this).data('fill')+'%')})},200);

    // Animate score rings
    setTimeout(function(){
        $('[data-ring]').each(function(){
            var s=parseInt($(this).data('ring')),r=52,c=2*Math.PI*r;
            $(this).css('stroke-dashoffset',c-(s/100)*c);
        });
    },300);
});
</script>
@stack('scripts')
</body>
</html>
