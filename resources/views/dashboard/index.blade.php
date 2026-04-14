@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- ═══ STAT CARDS ═══════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    <div class="col-6 col-lg-3 fade-up stagger-1">
        <div class="stat-card" style="--stat-color:var(--accent);--stat-bg:rgba(108,99,255,0.12);">
            <div class="stat-icon">📄</div>
            <div class="stat-value">{{ $stats['total_resumes'] }}</div>
            <div class="stat-label">Resumes Analyzed</div>
        </div>
    </div>

    <div class="col-6 col-lg-3 fade-up stagger-2">
        <div class="stat-card" style="--stat-color:var(--sky);--stat-bg:var(--sky-dim);">
            <div class="stat-icon">📊</div>
            <div class="stat-value">{{ $stats['avg_score'] ?: '—' }}</div>
            <div class="stat-label">Average ATS Score</div>
        </div>
    </div>

    <div class="col-6 col-lg-3 fade-up stagger-3">
        <div class="stat-card" style="--stat-color:var(--emerald);--stat-bg:var(--emerald-dim);">
            <div class="stat-icon">🏆</div>
            <div class="stat-value">{{ $stats['best_score'] ?: '—' }}</div>
            <div class="stat-label">Best Score</div>
        </div>
    </div>

    <div class="col-6 col-lg-3 fade-up stagger-4">
        <div class="stat-card" style="--stat-color:var(--amber);--stat-bg:var(--amber-dim);">
            <div class="stat-icon">⏳</div>
            <div class="stat-value">{{ $stats['pending_count'] }}</div>
            <div class="stat-label">Processing</div>
        </div>
    </div>
</div>

{{-- ═══ MAIN GRID ═════════════════════════════════════════════ --}}
<div class="row g-4">

    {{-- Score Trend Chart --}}
    <div class="col-lg-7 fade-up">
        <div class="card-dark" style="padding:24px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
                <div>
                    <h3 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;margin-bottom:2px;">
                        ATS Score Trend
                    </h3>
                    <p style="font-size:13px;color:var(--text-secondary);margin:0;">
                        How your resume scores have improved over time
                    </p>
                </div>
                <span class="tag tag-emerald">Last 10</span>
            </div>

            @if($scoreTrend->count() > 0)
                <canvas id="trendChart" height="220"></canvas>
            @else
                <div style="text-align:center;padding:60px 20px;color:var(--text-muted);">
                    <div style="font-size:48px;margin-bottom:12px;">📈</div>
                    <p style="font-size:14px;">Upload your first resume to see score trends</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Recent Resumes --}}
    <div class="col-lg-5 fade-up stagger-1">
        <div class="card-dark" style="padding:24px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <h3 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;">
                    Recent Resumes
                </h3>
                <a href="{{ route('resumes.index') }}"
                   style="font-size:13px;color:var(--accent-light);text-decoration:none;">
                    View all <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            @forelse($recentResumes as $resume)
                <a href="{{ $resume->status === 'analyzed' ? route('resumes.show', $resume) : '#' }}"
                   style="text-decoration:none;display:block;"
                   class="resume-row">
                    <div style="
                        display:flex;align-items:center;gap:14px;
                        padding:14px 0;
                        border-bottom:1px solid var(--border);
                        transition:all 0.2s ease;
                    " class="resume-row-inner">

                        {{-- File Icon --}}
                        <div style="
                            width:40px;height:40px;
                            background:rgba(108,99,255,0.12);
                            border-radius:8px;
                            display:flex;align-items:center;justify-content:center;
                            font-size:18px;flex-shrink:0;
                        ">
                            @if($resume->file_type === 'pdf') 📕
                            @elseif($resume->file_type === 'docx') 📘
                            @else 📄 @endif
                        </div>

                        {{-- Name & Meta --}}
                        <div style="flex:1;min-width:0;">
                            <div style="
                                font-size:13px;font-weight:600;
                                white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
                                color:var(--text-primary);
                            ">{{ $resume->original_filename }}</div>
                            <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">
                                {{ $resume->created_at->diffForHumans() }}
                                @if($resume->target_job_title)
                                    · <span style="color:var(--text-secondary);">{{ $resume->target_job_title }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Score or Status --}}
                        <div style="text-align:right;flex-shrink:0;">
                            @if($resume->status === 'analyzed' && $resume->analysis)
                                @php
                                    $score = $resume->analysis->overall_score;
                                    $color = $score >= 80 ? 'var(--emerald)' : ($score >= 60 ? 'var(--sky)' : ($score >= 40 ? 'var(--amber)' : 'var(--rose)'));
                                @endphp
                                <div style="
                                    font-family:'Syne',sans-serif;
                                    font-weight:800;font-size:18px;
                                    color:{{ $color }};
                                ">{{ $score }}</div>
                                <div style="font-size:10px;color:var(--text-muted);">/ 100</div>
                            @elseif($resume->status === 'processing')
                                <span class="processing-pulse"></span>
                            @elseif($resume->status === 'failed')
                                <i class="bi bi-exclamation-circle" style="color:var(--rose);font-size:18px;"></i>
                            @else
                                <i class="bi bi-hourglass" style="color:var(--text-muted);font-size:18px;"></i>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div style="text-align:center;padding:40px 20px;color:var(--text-muted);">
                    <div style="font-size:40px;margin-bottom:10px;">📭</div>
                    <p style="font-size:14px;">No resumes yet</p>
                    <button class="btn-primary-custom" style="margin-top:12px;"
                            data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="bi bi-plus-lg"></i> Upload First Resume
                    </button>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Common Missing Keywords --}}
    @if($topMissingKeywords->count() > 0)
    <div class="col-12 fade-up">
        <div class="card-dark" style="padding:24px;">
            <div style="margin-bottom:16px;">
                <h3 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;margin-bottom:4px;">
                    🔑 Commonly Missing Keywords
                </h3>
                <p style="font-size:13px;color:var(--text-secondary);margin:0;">
                    Keywords that frequently appear in job descriptions but are missing from your resumes
                </p>
            </div>
            <div class="keyword-cloud">
                @foreach($topMissingKeywords as $kw)
                    <span class="keyword-tag missing">
                        <i class="bi bi-plus-circle" style="font-size:10px;"></i>
                        {{ $kw }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Quick Tips --}}
    <div class="col-lg-6 fade-up">
        <div class="card-dark" style="padding:24px;">
            <h3 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;margin-bottom:20px;">
                💡 ATS Quick Tips
            </h3>
            @php
            $tips = [
                ['icon' => '📝', 'title' => 'Use Standard Section Headers', 'desc' => 'Use "Work Experience" not "Where I\'ve Been". ATS bots match exact phrases.', 'tag' => 'Format'],
                ['icon' => '🔑', 'title' => 'Mirror Job Description Keywords', 'desc' => 'Copy exact phrases from the job posting — ATS matches strings literally.', 'tag' => 'Keywords'],
                ['icon' => '📊', 'title' => 'Quantify Every Achievement', 'desc' => '"Grew revenue 34%" beats "Improved revenue" every time.', 'tag' => 'Content'],
                ['icon' => '🚫', 'title' => 'Avoid Tables & Text Boxes', 'desc' => 'Most ATS systems cannot parse text inside tables or graphics.', 'tag' => 'ATS'],
            ];
            @endphp
            @foreach($tips as $tip)
                <div style="display:flex;gap:14px;padding:14px 0;border-bottom:1px solid var(--border);">
                    <div style="font-size:20px;flex-shrink:0;margin-top:2px;">{{ $tip['icon'] }}</div>
                    <div>
                        <div style="font-size:14px;font-weight:600;margin-bottom:3px;display:flex;align-items:center;gap:8px;">
                            {{ $tip['title'] }}
                            <span class="tag tag-muted" style="font-size:10px;">{{ $tip['tag'] }}</span>
                        </div>
                        <div style="font-size:13px;color:var(--text-secondary);">{{ $tip['desc'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Upload CTA --}}
    <div class="col-lg-6 fade-up stagger-1">
        <div class="card-dark card-accent" style="padding:32px;text-align:center;height:100%;display:flex;flex-direction:column;justify-content:center;align-items:center;">
            <div style="font-size:52px;margin-bottom:16px;">🚀</div>
            <h3 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;margin-bottom:10px;">
                Ready to optimize?
            </h3>
            <p style="color:var(--text-secondary);font-size:14px;max-width:300px;margin:0 auto 24px;">
                Upload your resume and get a detailed ATS score with actionable feedback in under 30 seconds.
            </p>
            <button class="btn-primary-custom" style="padding:14px 32px;font-size:15px;"
                    data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="bi bi-cpu"></i> Analyze Now — It's Free
            </button>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
$(function () {

    // ── Score Trend Chart ──────────────────────────────────
    @if($scoreTrend->count() > 0)
    const trendData = @json($scoreTrend);

    const ctx = document.getElementById('trendChart').getContext('2d');

    const gradient = ctx.createLinearGradient(0, 0, 0, 220);
    gradient.addColorStop(0, 'rgba(108,99,255,0.3)');
    gradient.addColorStop(1, 'rgba(108,99,255,0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: trendData.map(d => d.date),
            datasets: [{
                label: 'ATS Score',
                data:  trendData.map(d => d.score),
                fill:  true,
                backgroundColor: gradient,
                borderColor:  '#6c63ff',
                borderWidth:  2.5,
                pointBackgroundColor: '#8b84ff',
                pointRadius: 5,
                pointHoverRadius: 7,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#161820',
                    borderColor: '#252836',
                    borderWidth: 1,
                    titleColor: '#eef0f8',
                    bodyColor:  '#8b90a8',
                    padding: 12,
                    callbacks: {
                        label: ctx => ` ATS Score: ${ctx.raw}/100`,
                    }
                }
            },
            scales: {
                x: {
                    grid:   { color: '#252836', drawBorder: false },
                    ticks:  { color: '#545872', font: { family: "'DM Sans'", size: 12 } }
                },
                y: {
                    min:  0,
                    max: 100,
                    grid:   { color: '#252836', drawBorder: false },
                    ticks:  { color: '#545872', font: { family: "'JetBrains Mono'", size: 11 }, stepSize: 20 }
                }
            }
        }
    });
    @endif

    // ── Poll processing resumes ────────────────────────────
    @php $processingIds = $recentResumes->where('status', 'processing')->pluck('id'); @endphp
    @if($processingIds->count() > 0)
        const processingIds = @json($processingIds);
        let pollInterval = setInterval(function () {
            let remaining = 0;
            processingIds.forEach(function (id) {
                $.get('/api/resumes/' + id + '/status', function (data) {
                    if (data.status === 'analyzed') {
                        showToast('✅ Resume analysis complete! Score: ' + data.score, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else if (data.status === 'failed') {
                        showToast('❌ Analysis failed for resume #' + id, 'error');
                    } else {
                        remaining++;
                    }
                });
            });
            if (remaining === 0) clearInterval(pollInterval);
        }, 4000);
    @endif

    // ── Resume row hover effect ────────────────────────────
    $('.resume-row').on('mouseenter mouseleave', function (e) {
        $(this).find('.resume-row-inner').css({
            'background':    e.type === 'mouseenter' ? 'rgba(108,99,255,0.04)' : 'transparent',
            'border-radius': e.type === 'mouseenter' ? '8px' : '0',
            'padding-left':  e.type === 'mouseenter' ? '8px' : '0',
        });
    });
});
</script>
@endpush
