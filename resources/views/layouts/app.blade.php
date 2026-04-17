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
    <link rel="stylesheet" href="{{ asset('style/style.css') }}">
    @stack('styles')
</head>
<body>
<div class="layout">

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

<div id="sbOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:99;" onclick="closeSidebar()"></div>

<div class="modal-overlay" id="uploadModal">
    <div class="modal-box">
        <div class="modal-hd">
            <h3><i class="bi bi-cpu" style="color:#6c63ff;margin-right:6px;"></i>Analyze Resume</h3>
            <button class="modal-close" id="closeModal">✕</button>
        </div>
        <div class="modal-body">
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

    window.closeSidebar=function(){$('#sidebar').removeClass('open');$('#sbOverlay').hide()};
    $('#sbToggle').on('click',function(){$('#sidebar').addClass('open');$('#sbOverlay').show()});

    function openModal(){resetUpload();$('#uploadModal').addClass('open')}
    function closeModal(){$('#uploadModal').removeClass('open')}
    $('#openUploadSb,#openUploadTop').on('click',openModal);
    $('#closeModal,#cancelModal').on('click',closeModal);
    $('#uploadModal').on('click',function(e){if($(e.target).is('#uploadModal'))closeModal()});

    $('#resumeInput').on('change',function(){
        if(this.files&&this.files.length>0)handleFile(this.files[0]);
    });

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

    window.showToast=function(msg,type){
        var c={green:'#10d9a0',red:'#f87171',amber:'#fbbf24',blue:'#38bdf8'};
        var $t=$('<div class="toast">').css('border-left','2px solid '+(c[type]||c.blue))
              .html('<span style="color:'+(c[type]||c.blue)+'">'+msg+'</span>');
        $('#toasts').append($t);
        setTimeout(function(){$t.css({opacity:0,transition:'opacity .3s'});setTimeout(function(){$t.remove()},300)},3500);
    };

    setTimeout(function(){$('[data-fill]').each(function(){$(this).css('width',$(this).data('fill')+'%')})},200);

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
