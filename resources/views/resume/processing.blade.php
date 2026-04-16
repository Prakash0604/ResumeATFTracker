<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Analyzing Resume — ResumeIQ</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0f1117;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            background: #1a1d27;
            border: 1px solid #2d3148;
            border-radius: 20px;
            padding: 48px 40px;
            max-width: 480px;
            width: 100%;
            text-align: center;
        }

        /* Animated ring */
        .ring-wrap { position: relative; display: inline-flex; margin-bottom: 32px; }

        .ring-wrap svg { animation: rotate 2s linear infinite; }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        .ring-icon {
            position: absolute; inset: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px;
        }

        .title {
            font-size: 22px; font-weight: 700;
            margin-bottom: 10px; color: #f8fafc;
        }

        .subtitle {
            font-size: 14px; color: #94a3b8;
            line-height: 1.6; margin-bottom: 36px;
            max-width: 340px; margin-left: auto; margin-right: auto;
        }

        /* Step indicators */
        .steps { display: flex; flex-direction: column; gap: 12px; margin-bottom: 36px; text-align: left; }

        .step {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px;
            background: #12141e;
            border-radius: 10px;
            border: 1px solid #2d3148;
            transition: all 0.4s ease;
        }

        .step.active {
            border-color: #6c63ff;
            background: rgba(108,99,255,0.08);
        }

        .step.done {
            border-color: rgba(16,217,160,0.4);
            background: rgba(16,217,160,0.05);
        }

        .step-dot {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: #2d3148;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; flex-shrink: 0;
            transition: all 0.4s ease;
        }

        .step.active .step-dot {
            background: #6c63ff;
            box-shadow: 0 0 12px rgba(108,99,255,0.4);
            animation: pulse-dot 1.2s ease-in-out infinite;
        }

        .step.done .step-dot {
            background: #10d9a0;
        }

        @keyframes pulse-dot {
            0%, 100% { transform: scale(1); }
            50%       { transform: scale(1.15); }
        }

        .step-label { font-size: 13px; font-weight: 500; color: #94a3b8; }
        .step.active .step-label { color: #c4b5fd; }
        .step.done .step-label   { color: #10d9a0; }

        /* Progress bar */
        .progress-wrap { margin-bottom: 28px; }

        .progress-track {
            height: 4px; background: #2d3148;
            border-radius: 99px; overflow: hidden; margin-bottom: 8px;
        }

        .progress-fill {
            height: 100%; background: linear-gradient(90deg, #6c63ff, #8b84ff);
            border-radius: 99px; width: 5%;
            transition: width 0.8s ease;
        }

        .progress-label {
            font-size: 12px; color: #64748b;
            display: flex; justify-content: space-between;
        }

        /* File info */
        .file-info {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 16px;
            background: #12141e; border-radius: 10px;
            border: 1px solid #2d3148;
            margin-bottom: 24px;
            text-align: left;
        }

        .file-icon {
            width: 36px; height: 36px;
            background: rgba(108,99,255,0.15);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
        }

        .file-name { font-size: 13px; font-weight: 500; color: #e2e8f0; }
        .file-meta { font-size: 11px; color: #64748b; margin-top: 2px; }

        /* Error state */
        .error-box {
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.3);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .error-title { font-size: 16px; font-weight: 600; color: #f87171; margin-bottom: 8px; }
        .error-msg   { font-size: 13px; color: #fca5a5; line-height: 1.5; }

        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 28px;
            border-radius: 10px;
            font-size: 14px; font-weight: 600;
            cursor: pointer; border: none;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: #6c63ff; color: #fff;
        }
        .btn-primary:hover { background: #8b84ff; transform: translateY(-1px); }

        .btn-ghost {
            background: #1a1d27; color: #94a3b8;
            border: 1px solid #2d3148;
        }
        .btn-ghost:hover { border-color: #475569; color: #e2e8f0; }

        .back-link {
            margin-top: 20px;
            font-size: 13px; color: #64748b;
            text-decoration: none;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .back-link:hover { color: #94a3b8; }
    </style>
</head>
<body>

<div class="card" id="mainCard">

    @if($resume->status === 'failed')
    {{-- ── FAILED STATE ─────────────────────────── --}}
    <div style="font-size:48px;margin-bottom:20px;">⚠️</div>
    <div class="title">Analysis Failed</div>
    <div class="subtitle">Something went wrong while analyzing your resume.</div>

    <div class="error-box">
        <div class="error-title">Error Details</div>
        <div class="error-msg">{{ $resume->processing_error ?? 'An unexpected error occurred. Please try again.' }}</div>
    </div>

    <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
        <button class="btn btn-primary" id="retryBtn">
            ↺ Try Again
        </button>
        <a href="{{ route('resumes.index') }}" class="btn btn-ghost">
            ← Back to Resumes
        </a>
    </div>

    @else
    {{-- ── PROCESSING STATE ─────────────────────── --}}
    <div class="ring-wrap">
        <svg width="80" height="80" viewBox="0 0 80 80">
            <circle cx="40" cy="40" r="34" fill="none" stroke="#2d3148" stroke-width="5"/>
            <circle cx="40" cy="40" r="34" fill="none" stroke="#6c63ff"
                    stroke-width="5" stroke-linecap="round"
                    stroke-dasharray="213.6" stroke-dashoffset="160"/>
        </svg>
        <div class="ring-icon">⚡</div>
    </div>

    <div class="title">Analyzing Your Resume</div>
    <div class="subtitle">Our AI is reading your resume, scoring every section, and generating personalized feedback.</div>

    {{-- File info --}}
    <div class="file-info">
        <div class="file-icon">
            @if($resume->file_type === 'pdf') 📕
            @elseif(in_array($resume->file_type, ['docx','doc'])) 📘
            @else 📄 @endif
        </div>
        <div>
            <div class="file-name">{{ $resume->original_filename }}</div>
            <div class="file-meta">
                {{ $resume->file_size_formatted }}
                @if($resume->target_job_title)
                    · Targeting: {{ $resume->target_job_title }}
                @endif
            </div>
        </div>
    </div>

    {{-- Progress bar --}}
    <div class="progress-wrap">
        <div class="progress-track">
            <div class="progress-fill" id="progressFill"></div>
        </div>
        <div class="progress-label">
            <span id="progressStatus">Starting analysis…</span>
            <span id="progressPct">0%</span>
        </div>
    </div>

    {{-- Steps --}}
    <div class="steps">
        <div class="step active" id="step-0">
            <div class="step-dot">1</div>
            <div class="step-label">Extracting text from resume</div>
        </div>
        <div class="step" id="step-1">
            <div class="step-dot">2</div>
            <div class="step-label">Parsing sections & structure</div>
        </div>
        <div class="step" id="step-2">
            <div class="step-dot">3</div>
            <div class="step-label">Scoring ATS compatibility</div>
        </div>
        <div class="step" id="step-3">
            <div class="step-dot">4</div>
            <div class="step-label">Generating improvement feedback</div>
        </div>
    </div>

    {{-- Status message --}}
    <div id="statusMsg" style="font-size:13px;color:#64748b;">
        This usually takes 15–30 seconds…
    </div>

    @endif
</div>

<a href="{{ route('resumes.index') }}" class="back-link">← Back to all resumes</a>

<script>
$(function () {
    const resumeId = {{ $resume->id }};
    const token    = $('meta[name="csrf-token"]').attr('content');

    @if($resume->status === 'failed')
    // Retry button
    $('#retryBtn').on('click', function () {
        $(this).prop('disabled', true).text('Retrying…');
        $.ajax({
            url:     '/resumes/' + resumeId + '/reanalyze',
            type:    'POST',
            headers: { 'X-CSRF-TOKEN': token },
            success: function (res) {
                if (res.redirect) window.location.href = res.redirect;
                else location.reload();
            },
            error: function () {
                location.reload();
            }
        });
    });

    @else
    // ── POLLING LOGIC ──────────────────────────────────────
    let elapsed      = 0;
    let currentStep  = 0;
    const totalTime  = 30; // estimated seconds

    const stepMessages = [
        'Extracting text from resume…',
        'Parsing sections & structure…',
        'Scoring ATS compatibility…',
        'Generating improvement feedback…',
    ];

    const stepDurations = [0, 6, 14, 22]; // seconds when each step activates

    function updateStep(step) {
        for (let i = 0; i < 4; i++) {
            const $s = $('#step-' + i);
            if (i < step) {
                $s.removeClass('active').addClass('done');
                $s.find('.step-dot').html('✓');
            } else if (i === step) {
                $s.addClass('active').removeClass('done');
            } else {
                $s.removeClass('active done');
            }
        }
    }

    function updateProgress(pct, msg) {
        $('#progressFill').css('width', Math.min(pct, 95) + '%');
        $('#progressPct').text(Math.min(pct, 95) + '%');
        if (msg) $('#progressStatus').text(msg);
    }

    // Visual progress ticker (cosmetic — not tied to actual job)
    const ticker = setInterval(function () {
        elapsed++;
        const pct = Math.min(Math.round((elapsed / totalTime) * 90), 90);
        updateProgress(pct);

        // Advance steps based on elapsed time
        for (let i = stepDurations.length - 1; i >= 0; i--) {
            if (elapsed >= stepDurations[i] && i !== currentStep) {
                currentStep = i;
                updateStep(i);
                $('#progressStatus').text(stepMessages[i]);
                break;
            }
        }
    }, 1000);

    // Real polling — check actual DB status every 3 seconds
    const poll = setInterval(function () {
        $.ajax({
            url:     '/api/resumes/' + resumeId + '/status',
            type:    'GET',
            headers: { 'X-CSRF-TOKEN': token },
            success: function (data) {
                if (data.status === 'analyzed') {
                    clearInterval(poll);
                    clearInterval(ticker);

                    // Complete all steps
                    for (let i = 0; i < 4; i++) {
                        $('#step-' + i).removeClass('active').addClass('done');
                        $('#step-' + i).find('.step-dot').html('✓');
                    }
                    updateProgress(100, 'Analysis complete!');
                    $('#statusMsg').html(
                        '<span style="color:#10d9a0;font-weight:600;">✅ Done! Score: ' +
                        data.score + '/100 — Redirecting…</span>'
                    );

                    setTimeout(function () {
                        window.location.href = data.redirect;
                    }, 1200);

                } else if (data.status === 'failed') {
                    clearInterval(poll);
                    clearInterval(ticker);
                    location.reload();

                } else if (data.status === 'processing') {
                    // Job is actively running — boost visual progress
                    if (currentStep < 1) {
                        currentStep = 1;
                        updateStep(1);
                    }
                }
                // 'uploaded' = job queued but not started yet — keep waiting
            },
            error: function (xhr) {
                if (xhr.status === 401) {
                    // Session expired
                    window.location.href = '/';
                }
            }
        });
    }, 3000);

    // Safety timeout — if still pending after 3 minutes, reload page
    setTimeout(function () {
        clearInterval(poll);
        clearInterval(ticker);
        $('#statusMsg').html(
            '<span style="color:#f59e0b;">Taking longer than expected. ' +
            '<a href="" style="color:#fbbf24;" onclick="location.reload();return false;">Refresh</a></span>'
        );
    }, 180000);
    @endif
});
</script>

</body>
</html>
