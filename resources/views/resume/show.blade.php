@extends('layouts.app')
@section('title','Analysis')
@section('page-title','Resume Analysis')

@section('content')
@php
    $analysis = $resume->analysis;
    $sc = fn(int $s): string => $s>=80?'#10d9a0':($s>=60?'#38bdf8':($s>=40?'#fbbf24':'#f87171'));
    $sl = fn(int $s): string => $s>=80?'Excellent':($s>=60?'Good':($s>=40?'Fair':'Needs Work'));
    $overall = $analysis->overall_score;
    $oColor  = $sc($overall);
@endphp

{{-- HERO HEADER --}}
<div style="background:#12141e;border:1px solid #1e2130;border-radius:14px;padding:24px;margin-bottom:18px;display:flex;align-items:center;gap:24px;flex-wrap:wrap;">

    {{-- Score ring --}}
    <div style="position:relative;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="110" height="110" class="ring-svg" viewBox="0 0 110 110">
            <circle class="ring-track" cx="55" cy="55" r="46"/>
            <circle class="ring-fill" cx="55" cy="55" r="46"
                    stroke="{{ $oColor }}"
                    stroke-dasharray="{{ round(2*M_PI*46,2) }}"
                    stroke-dashoffset="{{ round(2*M_PI*46,2) }}"
                    data-ring="{{ $overall }}"/>
        </svg>
        <div style="position:absolute;text-align:center;">
            <div style="font-size:24px;font-weight:700;color:{{ $oColor }};font-family:'JetBrains Mono',monospace;line-height:1;">{{ $overall }}</div>
            <div style="font-size:10px;color:#475569;margin-top:1px;">ATS Score</div>
        </div>
    </div>

    {{-- Info --}}
    <div style="flex:1;min-width:200px;">
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:8px;">
            <div style="font-size:18px;font-weight:700;color:#f8fafc;">
                {{ $resume->candidate_name ?? $resume->original_filename }}
            </div>
            <span class="tag" style="background:rgba({{ $overall>=80?'16,217,160':($overall>=60?'56,189,248':($overall>=40?'245,158,11':'239,68,68')) }},.1);color:{{ $oColor }};border:1px solid rgba({{ $overall>=80?'16,217,160':($overall>=60?'56,189,248':($overall>=40?'245,158,11':'239,68,68')) }},.25);">
                {{ $sl($overall) }}
            </span>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:12px;">
            @if($resume->candidate_email)
            <span style="font-size:12px;color:#64748b;"><i class="bi bi-envelope" style="color:#6c63ff;margin-right:3px;"></i>{{ $resume->candidate_email }}</span>
            @endif
            @if($resume->candidate_location)
            <span style="font-size:12px;color:#64748b;"><i class="bi bi-geo-alt" style="color:#6c63ff;margin-right:3px;"></i>{{ $resume->candidate_location }}</span>
            @endif
            @if($analysis->total_experience_years)
            <span style="font-size:12px;color:#64748b;"><i class="bi bi-briefcase" style="color:#6c63ff;margin-right:3px;"></i>{{ $analysis->total_experience_years }}+ yrs exp.</span>
            @endif
            @if($analysis->highest_degree)
            <span style="font-size:12px;color:#64748b;"><i class="bi bi-mortarboard" style="color:#6c63ff;margin-right:3px;"></i>{{ $analysis->highest_degree }}</span>
            @endif
        </div>

        <div style="display:flex;gap:7px;flex-wrap:wrap;">
            @php
                $atsColors=['excellent'=>'#10d9a0','good'=>'#38bdf8','fair'=>'#fbbf24','poor'=>'#f87171'];
                $atsC = $atsColors[$analysis->ats_compatibility] ?? '#64748b';
            @endphp
            <span class="tag" style="color:{{ $atsC }};background:rgba(100,100,100,.08);border:1px solid rgba(100,100,100,.2);">
                <i class="bi bi-shield-check"></i> ATS {{ ucfirst($analysis->ats_compatibility) }}
            </span>
            @if($resume->target_job_title)
            <span class="tag tag-purple"><i class="bi bi-briefcase"></i> {{ $resume->target_job_title }}</span>
            @endif
        </div>
    </div>

    {{-- Actions --}}
    <div style="display:flex;flex-direction:column;gap:7px;flex-shrink:0;">
        <button class="btn-ghost" id="reanalyzeBtn" style="font-size:12px;padding:7px 14px;">
            <i class="bi bi-arrow-clockwise"></i> Re-analyze
        </button>
        <button class="btn-ghost" style="font-size:12px;padding:7px 14px;color:#f87171;border-color:rgba(239,68,68,.2);" id="delBtn">
            <i class="bi bi-trash3"></i> Delete
        </button>
    </div>
</div>

{{-- MAIN GRID --}}
<div style="display:grid;grid-template-columns:320px 1fr;gap:16px;margin-bottom:16px;">

    {{-- LEFT COLUMN --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- Score breakdown --}}
        <div class="card card-p">
            <div style="font-size:13px;font-weight:700;color:#f8fafc;margin-bottom:16px;">📊 Score Breakdown</div>
            @foreach($analysis->score_breakdown as $item)
            @php $itemC = $sc($item['score']); @endphp
            <div class="bar-row">
                <div class="bar-hd">
                    <span class="bar-lbl">{{ $item['icon'] }} {{ $item['label'] }}</span>
                    <span class="bar-val" style="color:{{ $itemC }};">{{ $item['score'] }}</span>
                </div>
                <div class="bar-track">
                    <div class="bar-fill" data-fill="{{ $item['score'] }}" style="background:{{ $itemC }};"></div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Section checklist --}}
        <div class="card card-p">
            <div style="font-size:13px;font-weight:700;color:#f8fafc;margin-bottom:14px;">📋 Section Checklist</div>
            @php
            $sections = [
                'Contact Info'       => $analysis->has_contact_section,
                'Professional Summary'=> $analysis->has_summary_section,
                'Work Experience'    => $analysis->has_experience_section,
                'Education'          => $analysis->has_education_section,
                'Skills'             => $analysis->has_skills_section,
                'Projects'           => $analysis->has_projects_section,
                'Certifications'     => $analysis->has_certifications_section,
            ];
            @endphp
            @foreach($sections as $label => $present)
            <div style="display:flex;align-items:center;gap:8px;padding:7px 0;border-bottom:1px solid #1e2130;">
                @if($present)
                    <i class="bi bi-check-circle-fill" style="color:#10d9a0;font-size:13px;"></i>
                    <span style="font-size:12px;color:#94a3b8;">{{ $label }}</span>
                @else
                    <i class="bi bi-x-circle-fill" style="color:#f87171;font-size:13px;"></i>
                    <span style="font-size:12px;color:#475569;">{{ $label }}</span>
                    <span class="tag tag-red" style="margin-left:auto;font-size:10px;">Missing</span>
                @endif
            </div>
            @endforeach

            @if(count($analysis->ats_warnings ?? []))
            <div style="margin-top:12px;">
                <div style="font-size:10px;color:#334155;text-transform:uppercase;letter-spacing:.8px;margin-bottom:8px;">ATS Warnings</div>
                @foreach($analysis->ats_warnings as $w)
                <div style="display:flex;gap:7px;margin-bottom:6px;">
                    <i class="bi bi-exclamation-triangle-fill" style="color:#fbbf24;font-size:12px;flex-shrink:0;margin-top:2px;"></i>
                    <span style="font-size:11px;color:#64748b;">{{ $w }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Quick stats --}}
        <div class="card card-p">
            <div style="font-size:13px;font-weight:700;color:#f8fafc;margin-bottom:14px;">🔢 Resume Stats</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:9px;">
                @php $qs=[
                    ['Words',$analysis->word_count??'—','📝'],
                    ['Pages',$analysis->page_count??'—','📄'],
                    ['Metrics',$analysis->quantified_achievements,'📊'],
                    ['Skills',$resume->skills->count(),'⚡'],
                    ['Jobs',$analysis->job_count??'—','💼'],
                    ['KW Matched',count($analysis->matched_keywords??[]),'🎯'],
                ]; @endphp
                @foreach($qs as [$l,$v,$i])
                <div style="background:#0f1117;border-radius:8px;padding:11px 12px;">
                    <div style="font-size:16px;margin-bottom:3px;">{{ $i }}</div>
                    <div style="font-size:18px;font-weight:700;color:#f8fafc;font-family:'JetBrains Mono',monospace;line-height:1.1;">{{ $v }}</div>
                    <div style="font-size:10px;color:#475569;margin-top:2px;">{{ $l }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @php
        $feedback_items=$resume->feedback_items;
        $skills=$resume->skills;
    @endphp

    {{-- RIGHT COLUMN — Feedbacks --}}
    <div class="card card-p">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;flex-wrap:wrap;gap:8px;">
            <div>
                <div style="font-size:13px;font-weight:700;color:#f8fafc;">🎯 Improvement Actions</div>
                <div style="font-size:11px;color:#475569;margin-top:2px;">{{ $feedback_items->count() }} suggestions — click ✓ to mark done</div>
            </div>
            <div style="display:flex;gap:5px;flex-wrap:wrap;">
                <button class="fb-filter tag tag-purple active-filter" data-f="all" style="cursor:pointer;border:none;font-size:10px;">All</button>
                @foreach(['critical','high','medium','low'] as $p)
                @if($feedback_items->where('priority',$p)->count())
                <button class="fb-filter tag tag-gray" data-f="{{ $p }}" style="cursor:pointer;border:none;font-size:10px;">
                    {{ ucfirst($p) }} ({{ $feedback_items->where('priority',$p)->count() }})
                </button>
                @endif
                @endforeach
            </div>
        </div>

        {{-- Completion progress --}}
        @php $done=$feedback_items->where('is_addressed',true)->count(); $total=$feedback_items->count(); $pct=$total>0?round($done/$total*100):0; @endphp
        <div style="margin-bottom:16px;">
            <div style="display:flex;justify-content:space-between;font-size:11px;color:#475569;margin-bottom:5px;">
                <span>{{ $done }}/{{ $total }} completed</span>
                <span id="compPct" style="color:#10d9a0;">{{ $pct }}%</span>
            </div>
            <div class="bar-track">
                <div class="bar-fill" id="compBar" data-fill="{{ $pct }}" style="background:#10d9a0;"></div>
            </div>
        </div>

        {{-- Feedback list --}}
        <div id="fbList" style="max-height:540px;overflow-y:auto;padding-right:2px;">
            @foreach($feedback_items as $fb)
            @php
                $fColors=['critical'=>'#f87171','high'=>'#fb923c','medium'=>'#fbbf24','low'=>'#10d9a0'];
                $fbc=$fColors[$fb->priority]??'#6c63ff';
            @endphp
            <div class="fb-card {{ $fb->is_addressed?'addressed':'' }}" data-id="{{ $fb->id }}" data-pri="{{ $fb->priority }}" style="--bc:{{ $fbc }};">
                <div style="display:flex;align-items:flex-start;gap:10px;">
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:6px;">
                            <span class="tag pri-{{ $fb->priority }}" style="font-size:10px;">
                                {{ $fb->priority_icon }} {{ ucfirst($fb->priority) }}
                            </span>
                            <span class="tag tag-gray" style="font-size:10px;">{{ ucfirst($fb->category) }}</span>
                            @if($fb->is_addressed)
                            <span class="tag tag-green" style="font-size:10px;">✓ Done</span>
                            @endif
                        </div>
                        <div class="fb-title" style="font-size:13px;font-weight:600;color:#e2e8f0;margin-bottom:4px;">{{ $fb->title }}</div>
                        <div style="font-size:12px;color:#64748b;line-height:1.5;">{{ $fb->description }}</div>

                        {{-- Expandable suggestion --}}
                        <div class="fb-suggest" style="display:none;margin-top:10px;">
                            <div style="background:#12141e;border-radius:7px;padding:11px;border-left:2px solid {{ $fbc }};margin-bottom:8px;">
                                <div style="font-size:10px;color:#475569;text-transform:uppercase;letter-spacing:.6px;margin-bottom:5px;">💡 Suggested Fix</div>
                                <div style="font-size:12px;color:#94a3b8;line-height:1.5;">{{ $fb->suggestion }}</div>
                            </div>
                            @if($fb->example)
                            <div style="background:rgba(16,217,160,.04);border:1px solid rgba(16,217,160,.15);border-radius:7px;padding:11px;">
                                <div style="font-size:10px;color:#10d9a0;margin-bottom:5px;">📋 Example</div>
                                <div style="font-size:12px;color:#64748b;font-family:'JetBrains Mono',monospace;line-height:1.5;white-space:pre-wrap;">{{ $fb->example }}</div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px;flex-shrink:0;">
                        {{-- Expand --}}
                        <button class="btn-icon expand-btn" data-id="{{ $fb->id }}" title="Show suggestion" style="width:28px;height:28px;font-size:12px;">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        {{-- Address --}}
                        <button class="btn-icon addr-btn" data-id="{{ $fb->id }}" data-rid="{{ $resume->id }}"
                                title="{{ $fb->is_addressed?'Mark pending':'Mark done' }}"
                                style="width:28px;height:28px;font-size:12px;
                                       {{ $fb->is_addressed?'background:rgba(16,217,160,.1);border-color:rgba(16,217,160,.25);color:#10d9a0;':'' }}">
                            <i class="bi bi-{{ $fb->is_addressed?'check-circle-fill':'circle' }}"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
            @if($feedback_items->count() === 0)
            <div style="text-align:center;padding:40px;color:#334155;">🎉 No feedback — great resume!</div>
            @endif
        </div>
    </div>
</div>

{{-- KEYWORDS --}}
@if(count($analysis->matched_keywords??[]) || count($analysis->missing_keywords??[]))
<div class="sec-hd"><div class="line"></div><h3>🔑 Keyword Analysis</h3><div class="line"></div></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
    <div class="card card-p">
        <div style="font-size:13px;font-weight:700;color:#f8fafc;margin-bottom:4px;">✅ Matched <span class="tag tag-green" style="font-size:10px;margin-left:4px;">{{ count($analysis->matched_keywords??[]) }}</span></div>
        <div style="font-size:11px;color:#475569;margin-bottom:12px;">Found in both resume and job description</div>
        <div class="kw-cloud">
            @forelse($analysis->matched_keywords??[] as $k)
                <span class="kw matched">✓ {{ $k }}</span>
            @empty <span style="font-size:12px;color:#334155;">No target job set.</span>
            @endforelse
        </div>
    </div>
    <div class="card card-p">
        <div style="font-size:13px;font-weight:700;color:#f8fafc;margin-bottom:4px;">❌ Missing <span class="tag tag-red" style="font-size:10px;margin-left:4px;">{{ count($analysis->missing_keywords??[]) }}</span></div>
        <div style="font-size:11px;color:#475569;margin-bottom:12px;">In the job description but absent from your resume</div>
        <div class="kw-cloud">
            @forelse($analysis->missing_keywords??[] as $k)
                <span class="kw missing">+ {{ $k }}</span>
            @empty <span style="font-size:12px;color:#10d9a0;">🎉 All keywords matched!</span>
            @endforelse
        </div>
    </div>
</div>
@endif

{{-- SKILLS --}}
@if($skills->count() >0)
<div class="sec-hd"><div class="line"></div><h3>⚡ Skills</h3><div class="line"></div></div>
<div class="card card-p" style="margin-bottom:16px;">
    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:14px;" id="skillFilters">
        <button class="tag tag-purple skill-filt active-filt" data-f="all" style="cursor:pointer;border:none;">All</button>
        @foreach($skills->pluck('category')->unique() as $cat)
        <button class="tag tag-gray skill-filt" data-f="{{ $cat }}" style="cursor:pointer;border:none;">{{ ucfirst($cat) }}</button>
        @endforeach
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:8px;" id="skillGrid">
        @foreach($skills->sortByDesc('mention_count') as $skill)
        <div class="skill-item" data-cat="{{ $skill->category }}" style="
            background:{{ $skill->is_in_job_description?'rgba(16,217,160,.06)':'#1e2130' }};
            border:1px solid {{ $skill->is_in_job_description?'rgba(16,217,160,.2)':'#2d3148' }};
            border-radius:8px;padding:8px 12px;
        ">
            <div style="font-size:12px;font-weight:600;color:#e2e8f0;">{{ $skill->category_icon }} {{ $skill->name }}</div>
            <div style="font-size:10px;color:#475569;margin-top:2px;">{{ ucfirst($skill->proficiency) }}@if($skill->is_in_job_description) · <span style="color:#10d9a0;">In JD</span>@endif</div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- RE-ANALYZE MODAL --}}
<div class="modal-overlay" id="reModal">
    <div class="modal-box">
        <div class="modal-hd">
            <h3>🔄 Re-Analyze</h3>
            <button class="modal-close" onclick="$('#reModal').removeClass('open')">✕</button>
        </div>
        <div class="modal-body">
            <div style="margin-bottom:12px;">
                <label class="field-lbl">Job Title</label>
                <input type="text" class="form-input" id="rJobTitle" value="{{ $resume->target_job_title }}">
            </div>
            <div>
                <label class="field-lbl">Job Description</label>
                <textarea class="form-input" id="rJobDesc" rows="5" placeholder="Paste updated job description…">{{ $resume->target_job_description }}</textarea>
            </div>
        </div>
        <div class="modal-ft">
            <button class="btn-ghost" onclick="$('#reModal').removeClass('open')">Cancel</button>
            <button class="btn-primary" id="doReanalyze"><i class="bi bi-cpu"></i> Start Analysis</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(function(){
    var rid={{ $resume->id }};

    // Re-analyze
    $('#reanalyzeBtn').on('click',function(){$('#reModal').addClass('open')});
    $('#reModal').on('click',function(e){if($(e.target).is('#reModal'))$('#reModal').removeClass('open')});

    $('#doReanalyze').on('click',function(){
        $(this).prop('disabled',true).html('<i class="bi bi-hourglass-split"></i> Starting…');
        $.ajax({
            url:'/resumes/'+rid+'/reanalyze',type:'POST',
            data:{job_title:$('#rJobTitle').val(),job_description:$('#rJobDesc').val()},
            success:function(r){
                showToast('🔄 Re-analysis started','blue');
                setTimeout(function(){window.location.href=r.redirect},600);
            },
            error:function(){$('#doReanalyze').prop('disabled',false).html('<i class="bi bi-cpu"></i> Start Analysis');}
        });
    });

    // Delete
    $('#delBtn').on('click',function(){
        if(!confirm('Delete this resume permanently?')) return;
        $.ajax({url:'/resumes/'+rid,type:'DELETE',success:function(){
            showToast('🗑 Resume deleted','amber');
            setTimeout(function(){window.location.href='/resumes'},700);
        }});
    });

    // Expand feedback
    $(document).on('click','.expand-btn',function(){
        var $card=$(this).closest('.fb-card');
        $card.find('.fb-suggest').slideToggle(180);
        $(this).find('i').toggleClass('bi-chevron-down bi-chevron-up');
    });

    // Address feedback
    $(document).on('click','.addr-btn',function(){
        var $btn=$(this),fid=$btn.data('id'),r=$btn.data('rid');
        $.post('/api/resumes/'+r+'/feedback/'+fid+'/address',function(d){
            var $card=$('.fb-card[data-id='+fid+']');
            if(d.addressed){
                $card.addClass('addressed');
                $btn.css({'background':'rgba(16,217,160,.1)','border-color':'rgba(16,217,160,.25)','color':'#10d9a0'})
                    .html('<i class="bi bi-check-circle-fill"></i>');
                showToast('✓ Marked done','green');
            } else {
                $card.removeClass('addressed');
                $btn.css({'background':'','border-color':'','color':''})
                    .html('<i class="bi bi-circle"></i>');
                showToast('↩ Marked pending','amber');
            }
            // Update progress
            var tot=$('.fb-card').length,done=$('.fb-card.addressed').length;
            var p=tot>0?Math.round(done/tot*100):0;
            $('#compBar').css('width',p+'%');$('#compPct').text(p+'%');
        });
    });

    // Filter feedbacks
    $(document).on('click','.fb-filter',function(){
        var f=$(this).data('f');
        $('.fb-filter').removeClass('tag-purple active-filter').addClass('tag-gray');
        $(this).removeClass('tag-gray').addClass('tag-purple active-filter');
        $('.fb-card').each(function(){
            $(this).toggle(f==='all'||$(this).data('pri')===f);
        });
    });

    // Filter skills
    $(document).on('click','.skill-filt',function(){
        var f=$(this).data('f');
        $('.skill-filt').removeClass('tag-purple active-filt').addClass('tag-gray');
        $(this).removeClass('tag-gray').addClass('tag-purple active-filt');
        $('.skill-item').each(function(){
            $(this).toggle(f==='all'||$(this).data('cat')===f);
        });
    });
});
</script>
@endpush
