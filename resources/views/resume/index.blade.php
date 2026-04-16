@extends('layouts.app')
@section('title','My Resumes')
@section('page-title','My Resumes')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;">
    <div>
        <div style="font-size:15px;font-weight:700;color:#f8fafc;">My Resumes</div>
        <div style="font-size:12px;color:#475569;margin-top:2px;">{{ $resumes->total() }} resume{{ $resumes->total()!==1?'s':'' }}</div>
    </div>
    <button class="btn-primary" id="openUploadTop">
        <i class="bi bi-plus-lg"></i> Upload New
    </button>
</div>

@if($resumes->isEmpty())
<div style="text-align:center;padding:80px 20px;">
    <div style="font-size:56px;margin-bottom:16px;opacity:.5;">📂</div>
    <div style="font-size:18px;font-weight:700;color:#f8fafc;margin-bottom:8px;">No resumes yet</div>
    <div style="font-size:13px;color:#475569;max-width:300px;margin:0 auto 22px;">
        Upload your first resume to get an instant ATS score and personalized improvement plan.
    </div>
    <button class="btn-primary" id="openUploadTop2">
        <i class="bi bi-cloud-upload"></i> Upload Resume
    </button>
</div>
@else
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;">
    @foreach($resumes as $resume)
    @php
        $sc = $resume->analysis?->overall_score;
        $scColor = $sc===null?'#475569':($sc>=80?'#10d9a0':($sc>=60?'#38bdf8':($sc>=40?'#fbbf24':'#f87171')));
    @endphp
    <div class="card" style="padding:18px;position:relative;overflow:hidden;display:flex;flex-direction:column;gap:14px;" data-id="{{ $resume->id }}">
        {{-- top accent line --}}
        @if($sc !== null)
        <div style="position:absolute;top:0;left:0;right:0;height:2px;background:{{ $scColor }};"></div>
        @endif

        {{-- Header --}}
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="font-size:22px;line-height:1;">
                    @if($resume->file_type==='pdf')📕@elseif(in_array($resume->file_type,['docx','doc']))📘@else📄@endif
                </div>
                <div style="min-width:0;">
                    <div style="font-size:13px;font-weight:600;color:#f8fafc;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px;">
                        {{ $resume->candidate_name ?? \Illuminate\Support\Str::limit($resume->original_filename,24) }}
                    </div>
                    <div style="font-size:10px;color:#334155;margin-top:1px;font-family:'JetBrains Mono',monospace;">
                        {{ strtoupper($resume->file_type) }} · {{ $resume->file_size_formatted }}
                    </div>
                </div>
            </div>
            {{-- Score / status --}}
            @if($resume->status==='analyzed' && $sc !== null)
                <div style="text-align:right;flex-shrink:0;">
                    <div style="font-size:22px;font-weight:700;color:{{ $scColor }};font-family:'JetBrains Mono',monospace;line-height:1;">{{ $sc }}</div>
                    <div style="font-size:10px;color:#334155;">/100</div>
                </div>
            @elseif($resume->status==='processing')
                <div style="display:flex;flex-direction:column;align-items:center;gap:4px;">
                    <span style="width:10px;height:10px;background:#fbbf24;border-radius:50%;display:inline-block;animation:pulse 1.2s infinite;"></span>
                    <span style="font-size:10px;color:#fbbf24;">Analyzing</span>
                </div>
            @elseif($resume->status==='failed')
                <span style="color:#f87171;font-size:18px;" title="{{ $resume->processing_error }}">⚠</span>
            @else
                <span style="color:#475569;font-size:16px;">⏳</span>
            @endif
        </div>

        {{-- Mini bars (analyzed only) --}}
        @if($resume->status==='analyzed' && $resume->analysis)
        <div>
            @foreach([['Keywords',$resume->analysis->keyword_score],['Format',$resume->analysis->format_score],['Content',$resume->analysis->content_score]] as [$l,$s])
            @php $c=$s>=80?'#10d9a0':($s>=60?'#38bdf8':($s>=40?'#fbbf24':'#f87171')); @endphp
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <span style="font-size:10px;color:#475569;width:52px;flex-shrink:0;">{{ $l }}</span>
                <div class="bar-track" style="flex:1;height:3px;">
                    <div class="bar-fill" data-fill="{{ $s }}" style="background:{{ $c }};"></div>
                </div>
                <span style="font-size:10px;color:{{ $c }};font-family:'JetBrains Mono',monospace;width:20px;text-align:right;">{{ $s }}</span>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Meta --}}
        <div style="display:flex;gap:5px;flex-wrap:wrap;">
            <span class="tag tag-gray" style="font-size:10px;">{{ $resume->created_at->format('M d, Y') }}</span>
            @if($resume->target_job_title)
                <span class="tag tag-purple" style="font-size:10px;">{{ \Illuminate\Support\Str::limit($resume->target_job_title,22) }}</span>
            @endif
        </div>

        {{-- Actions --}}
        <div style="display:flex;gap:7px;margin-top:auto;">
            @if($resume->status==='analyzed')
                <a href="{{ route('resumes.show',$resume) }}" class="btn-primary" style="flex:1;justify-content:center;font-size:12px;padding:7px 12px;">
                    <i class="bi bi-bar-chart-line"></i> View Analysis
                </a>
            @elseif($resume->status==='failed')
                <button class="btn-ghost retry-btn" data-id="{{ $resume->id }}" style="flex:1;justify-content:center;font-size:12px;padding:7px 12px;">
                    <i class="bi bi-arrow-clockwise"></i> Retry
                </button>
            @else
                <a href="{{ route('resumes.show',$resume) }}" class="btn-ghost" style="flex:1;justify-content:center;font-size:12px;padding:7px 12px;opacity:.6;pointer-events:none;">
                    <i class="bi bi-hourglass-split"></i> Processing…
                </a>
            @endif
            <button class="btn-icon del-btn" data-id="{{ $resume->id }}" style="color:#f87171;flex-shrink:0;" title="Delete">
                <i class="bi bi-trash3"></i>
            </button>
        </div>
    </div>
    @endforeach
</div>

@if($resumes->hasPages())
<div style="display:flex;justify-content:center;margin-top:28px;">
    {{ $resumes->links() }}
</div>
@endif
@endif

<style>
@keyframes pulse{0%,100%{opacity:.4}50%{opacity:1}}
</style>
@endsection

@push('scripts')
<script>
$(function(){
    // Extra open buttons
    $('#openUploadTop2').on('click',function(){$('#openUploadTop').click()});

    // Delete
    $(document).on('click','.del-btn',function(){
        if(!confirm('Delete this resume permanently?')) return;
        var id=$(this).data('id');
        var $card=$(this).closest('.card');
        $.ajax({url:'/resumes/'+id,type:'DELETE',success:function(){
            $card.fadeOut(250,function(){$(this).remove()});
            showToast('🗑 Resume deleted','amber');
        }});
    });

    // Retry failed
    $(document).on('click','.retry-btn',function(){
        var id=$(this).data('id');
        var $btn=$(this);
        $btn.prop('disabled',true).html('<i class="bi bi-hourglass-split"></i> Queuing…');
        $.ajax({url:'/resumes/'+id+'/reanalyze',type:'POST',
            success:function(r){
                showToast('🔄 Re-analysis queued','blue');
                setTimeout(function(){window.location.href=r.redirect},600);
            },
            error:function(){$btn.prop('disabled',false).html('<i class="bi bi-arrow-clockwise"></i> Retry');}
        });
    });

    // Poll processing
    @php $pIds = $resumes->whereIn('status',['uploaded','processing'])->pluck('id'); @endphp
    @if($pIds->count())
    var ids=@json($pIds);
    setInterval(function(){
        ids.forEach(function(id){
            $.get('/api/resumes/'+id+'/status',function(d){
                if(d.status==='analyzed'){showToast('✅ Analysis done! Score: '+d.score,'green');setTimeout(function(){location.reload()},1000);}
            });
        });
    },4000);
    @endif
});
</script>
@endpush
