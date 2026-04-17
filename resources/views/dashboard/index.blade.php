@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')

@section('content')
@php
    $user = auth()->user();
@endphp

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;">
    <div class="stat-card fade-up" style="--c:#6c63ff">
        <span class="stat-icon">📄</span>
        <div class="stat-num">{{ $stats['total_resumes'] }}</div>
        <div class="stat-lbl">Resumes Analyzed</div>
    </div>
    <div class="stat-card fade-up" style="--c:#38bdf8;animation-delay:.05s">
        <span class="stat-icon">📊</span>
        <div class="stat-num">{{ $stats['avg_score'] ?: '—' }}</div>
        <div class="stat-lbl">Average Score</div>
    </div>
    <div class="stat-card fade-up" style="--c:#10d9a0;animation-delay:.1s">
        <span class="stat-icon">🏆</span>
        <div class="stat-num">{{ $stats['best_score'] ?: '—' }}</div>
        <div class="stat-lbl">Best Score</div>
    </div>
    <div class="stat-card fade-up" style="--c:#fbbf24;animation-delay:.15s">
        <span class="stat-icon">⏳</span>
        <div class="stat-num">{{ $stats['pending_count'] }}</div>
        <div class="stat-lbl">Processing</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:16px;margin-bottom:16px;">

    <div class="card card-p fade-up">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
            <div>
                <div style="font-size:14px;font-weight:700;color:#f8fafc;margin-bottom:2px;">Score Trend</div>
                <div style="font-size:12px;color:#475569;">ATS scores over your last 10 uploads</div>
            </div>
            <span class="tag tag-green">Last 10</span>
        </div>
        @if($scoreTrend->count() > 0)
            <div style="position: relative; height: 250px; width: 100%;">
                <canvas id="trendChart"></canvas>
            </div>
        @else
            <div style="text-align:center;padding:50px 0;color:#334155;">
                <div style="font-size:36px;margin-bottom:10px;">📈</div>
                <div style="font-size:13px;">Upload your first resume to see trends</div>
            </div>
        @endif
    </div>

    <div class="card card-p fade-up" style="animation-delay:.05s;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div style="font-size:14px;font-weight:700;color:#f8fafc;">Recent</div>
            <a href="{{ route('resumes.index') }}" style="font-size:12px;color:#6c63ff;text-decoration:none;">
                View all →
            </a>
        </div>

        @forelse($recentResumes as $resume)
        @php
            $sc = $resume->analysis?->overall_score;
            $scColor = $sc === null ? '#475569' : ($sc>=80?'#10d9a0':($sc>=60?'#38bdf8':($sc>=40?'#fbbf24':'#f87171')));
        @endphp
        <a href="{{ $resume->status==='analyzed' ? route('resumes.show',$resume) : '#' }}"
           style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid #1e2130;text-decoration:none;transition:opacity .15s;"
           onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
            <div style="font-size:18px;flex-shrink:0;">
                @if($resume->file_type==='pdf')📕@elseif(in_array($resume->file_type,['docx','doc']))📘@else📄@endif
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:12px;font-weight:500;color:#e2e8f0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ $resume->candidate_name ?? $resume->original_filename }}
                </div>
                <div style="font-size:11px;color:#475569;margin-top:1px;">
                    {{ $resume->created_at->diffForHumans() }}
                    @if($resume->target_job_title)· {{ \Illuminate\Support\Str::limit($resume->target_job_title,22) }}@endif
                </div>
            </div>
            <div style="flex-shrink:0;text-align:right;">
                @if($resume->status==='analyzed' && $sc !== null)
                    <div style="font-size:16px;font-weight:700;color:{{ $scColor }};font-family:'JetBrains Mono',monospace;line-height:1;">{{ $sc }}</div>
                    <div style="font-size:10px;color:#334155;">/100</div>
                @elseif($resume->status==='processing')
                    <span style="width:8px;height:8px;background:#fbbf24;border-radius:50%;display:inline-block;animation:pulse 1.2s infinite;"></span>
                @elseif($resume->status==='failed')
                    <span style="color:#f87171;font-size:14px;">⚠</span>
                @else
                    <span style="color:#334155;font-size:13px;">⏳</span>
                @endif
            </div>
        </a>
        @empty
        <div style="text-align:center;padding:32px 0;color:#334155;">
            <div style="font-size:28px;margin-bottom:8px;">📭</div>
            <div style="font-size:12px;margin-bottom:14px;">No resumes yet</div>
            <button class="btn-primary" style="font-size:12px;padding:7px 14px;" onclick="$('#openUploadTop').click()">
                Upload First Resume
            </button>
        </div>
        @endforelse
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

    @if($topMissingKeywords->count() > 0)
    <div class="card card-p fade-up">
        <div style="font-size:14px;font-weight:700;color:#f8fafc;margin-bottom:4px;">🔑 Commonly Missing Keywords</div>
        <div style="font-size:12px;color:#475569;margin-bottom:14px;">Keywords absent from your resumes but common in job postings</div>
        <div class="kw-cloud">
            @foreach($topMissingKeywords as $kw)
                <span class="kw missing">+ {{ $kw }}</span>
            @endforeach
        </div>
    </div>
    @endif

    <div class="card card-p fade-up">
        <div style="font-size:14px;font-weight:700;color:#f8fafc;margin-bottom:14px;">💡 ATS Quick Tips</div>
        @foreach([
            ['Use exact section headers','"Work Experience" not "My Journey" — ATS matches strings literally.','Format'],
            ['Quantify every achievement','"Grew revenue 34%" beats "improved revenue" every time.','Content'],
            ['Mirror the job description','Copy exact phrases from the posting into your bullet points.','Keywords'],
            ['Avoid tables and columns','Most ATS parsers skip text inside tables or multi-column layouts.','ATS'],
        ] as [$title,$desc,$label])
        <div style="display:flex;gap:10px;padding:9px 0;border-bottom:1px solid #1e2130;">
            <div style="flex:1;">
                <div style="font-size:12px;font-weight:600;color:#e2e8f0;margin-bottom:2px;display:flex;align-items:center;gap:6px;">
                    {{ $title }} <span class="tag tag-gray" style="font-size:10px;">{{ $label }}</span>
                </div>
                <div style="font-size:12px;color:#475569;">{{ $desc }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
@keyframes pulse{0%,100%{opacity:.4}50%{opacity:1}}
</style>
@endsection

@push('scripts')
<script>
$(function(){
    @if($scoreTrend->count() > 0)
    var td = @json($scoreTrend);
    var ctx = document.getElementById('trendChart').getContext('2d');
    var g = ctx.createLinearGradient(0,0,0,180);
    g.addColorStop(0,'rgba(108,99,255,.2)');g.addColorStop(1,'rgba(108,99,255,0)');
    new Chart(ctx,{
        type:'line',
        data:{
            labels:td.map(d=>d.date),
            datasets:[{label:'Score',data:td.map(d=>d.score),fill:true,backgroundColor:g,borderColor:'#6c63ff',borderWidth:2,pointBackgroundColor:'#a5b4fc',pointRadius:4,tension:.4}]
        },
        options:{
            responsive:true,maintainAspectRatio:true,
            plugins:{legend:{display:false},tooltip:{backgroundColor:'#12141e',borderColor:'#2d3148',borderWidth:1,titleColor:'#f8fafc',bodyColor:'#94a3b8',padding:10,callbacks:{label:c=>' Score: '+c.raw+'/100'}}},
            scales:{
                x:{grid:{color:'#1e2130',drawBorder:false},ticks:{color:'#475569',font:{family:'Inter',size:11}}},
                y:{min:0,max:100,grid:{color:'#1e2130',drawBorder:false},ticks:{color:'#475569',font:{family:'JetBrains Mono',size:10},stepSize:25}}
            }
        }
    });
    @endif

    // Poll any processing resumes
    @php $pIds = $recentResumes->whereIn('status',['uploaded','processing'])->pluck('id'); @endphp
    @if($pIds->count())
    var ids = @json($pIds);
    var iv = setInterval(function(){
        var done=true;
        ids.forEach(function(id){
            $.get('/api/resumes/'+id+'/status',function(d){
                if(d.status==='analyzed'){showToast('✅ Analysis done! Score: '+d.score,'green');setTimeout(function(){location.reload()},1000);}
                else if(d.status!=='failed') done=false;
            });
        });
        if(done) clearInterval(iv);
    },4000);
    @endif
});
</script>
@endpush
