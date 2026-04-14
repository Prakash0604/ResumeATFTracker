<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ATF Resume Tracker') — ResumeIQ</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts: Syne + DM Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        /* ═══════════════════════════════════════════════════════
           DESIGN SYSTEM — ATF Resume Tracker
           Theme: Dark Precision / Technical Elegance
           Fonts: Syne (headings) + DM Sans (body) + JetBrains Mono (code/data)
           ═══════════════════════════════════════════════════════ */

        :root {
            --bg-base:        #0a0b0f;
            --bg-surface:     #111318;
            --bg-card:        #161820;
            --bg-card-hover:  #1c1f28;
            --bg-input:       #1a1d26;
            --border:         #252836;
            --border-bright:  #363a52;

            --text-primary:   #eef0f8;
            --text-secondary: #8b90a8;
            --text-muted:     #545872;

            --accent:         #6c63ff;
            --accent-glow:    rgba(108, 99, 255, 0.25);
            --accent-light:   #8b84ff;
            --accent-dim:     #3d3888;

            --emerald:        #10d9a0;
            --emerald-dim:    rgba(16, 217, 160, 0.15);
            --amber:          #f5a623;
            --amber-dim:      rgba(245, 166, 35, 0.15);
            --rose:           #ff4f6a;
            --rose-dim:       rgba(255, 79, 106, 0.15);
            --sky:            #38bdf8;
            --sky-dim:        rgba(56, 189, 248, 0.15);

            --score-excellent: #10d9a0;
            --score-good:      #38bdf8;
            --score-fair:      #f5a623;
            --score-poor:      #ff4f6a;

            --radius-sm:  6px;
            --radius-md:  12px;
            --radius-lg:  18px;
            --radius-xl:  24px;

            --shadow-sm:  0 2px 8px rgba(0,0,0,0.4);
            --shadow-md:  0 4px 24px rgba(0,0,0,0.5);
            --shadow-lg:  0 8px 48px rgba(0,0,0,0.6);
            --shadow-accent: 0 0 40px rgba(108, 99, 255, 0.15);

            --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: 0.4s cubic-bezier(0.4, 0, 0.2, 1);

            --sidebar-w: 260px;
            --topbar-h:  64px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-base);
            color: var(--text-primary);
            font-size: 15px;
            line-height: 1.6;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Typography ─────────────────────────────────────── */
        h1, h2, h3, h4, h5, h6,
        .font-display { font-family: 'Syne', sans-serif; }

        .font-mono { font-family: 'JetBrains Mono', monospace; }

        /* ── Scrollbar ──────────────────────────────────────── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-base); }
        ::-webkit-scrollbar-thumb { background: var(--border-bright); border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--accent-dim); }

        /* ══════════════════════════════════════════════════════
           LAYOUT
           ══════════════════════════════════════════════════════ */

        .app-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* ── Sidebar ─────────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--bg-surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0; top: 0; bottom: 0;
            z-index: 100;
            transition: transform var(--transition-slow);
        }

        .sidebar-brand {
            padding: 24px 24px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            box-shadow: 0 0 20px var(--accent-glow);
            flex-shrink: 0;
        }

        .brand-name {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 18px;
            color: var(--text-primary);
            letter-spacing: -0.3px;
        }

        .brand-tagline {
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }

        .nav-section-label {
            font-size: 10px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 8px 12px 6px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all var(--transition);
            margin-bottom: 2px;
            position: relative;
        }

        .nav-link i { font-size: 16px; flex-shrink: 0; }

        .nav-link:hover {
            color: var(--text-primary);
            background: var(--bg-card);
        }

        .nav-link.active {
            color: var(--accent-light);
            background: rgba(108, 99, 255, 0.12);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 6px; bottom: 6px;
            width: 3px;
            background: var(--accent);
            border-radius: 0 3px 3px 0;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--accent-dim);
            color: var(--accent-light);
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 99px;
            font-family: 'JetBrains Mono', monospace;
        }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid var(--border);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            background: var(--bg-card);
        }

        .user-avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, var(--accent-dim), var(--accent));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .user-name { font-size: 13px; font-weight: 600; }
        .user-plan {
            font-size: 10px;
            color: var(--emerald);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── Main Content ─────────────────────────────────────── */
        .main-content {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── Topbar ───────────────────────────────────────────── */
        .topbar {
            height: var(--topbar-h);
            background: var(--bg-surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 50;
            gap: 16px;
        }

        .topbar-title {
            font-family: 'Syne', sans-serif;
            font-size: 18px;
            font-weight: 700;
            flex: 1;
        }

        .topbar-actions { display: flex; align-items: center; gap: 10px; }

        /* ── Page Content ─────────────────────────────────────── */
        .page-content {
            flex: 1;
            padding: 32px;
        }

        /* ══════════════════════════════════════════════════════
           COMPONENTS
           ══════════════════════════════════════════════════════ */

        /* ── Cards ───────────────────────────────────────────── */
        .card-dark {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            transition: border-color var(--transition), box-shadow var(--transition);
        }

        .card-dark:hover {
            border-color: var(--border-bright);
            box-shadow: var(--shadow-md);
        }

        .card-dark.card-accent {
            border-color: var(--accent-dim);
            box-shadow: var(--shadow-accent);
        }

        /* ── Stat Cards ──────────────────────────────────────── */
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            position: relative;
            overflow: hidden;
            transition: all var(--transition);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: var(--stat-color, var(--accent));
        }

        .stat-card:hover {
            transform: translateY(-2px);
            border-color: var(--border-bright);
            box-shadow: var(--shadow-md);
        }

        .stat-icon {
            width: 44px; height: 44px;
            background: var(--stat-bg, rgba(108,99,255,0.12));
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            margin-bottom: 16px;
        }

        .stat-value {
            font-family: 'Syne', sans-serif;
            font-size: 32px;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* ── Score Ring ──────────────────────────────────────── */
        .score-ring-wrap {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .score-ring-text {
            position: absolute;
            text-align: center;
        }

        .score-ring-text .score-num {
            font-family: 'Syne', sans-serif;
            font-size: 28px;
            font-weight: 800;
            display: block;
            line-height: 1;
        }

        .score-ring-text .score-label {
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ── Score Bar ───────────────────────────────────────── */
        .score-bar-wrap { margin-bottom: 14px; }

        .score-bar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .score-bar-label { font-size: 13px; font-weight: 500; }

        .score-bar-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            font-weight: 500;
        }

        .score-bar-track {
            height: 6px;
            background: var(--bg-input);
            border-radius: 99px;
            overflow: hidden;
        }

        .score-bar-fill {
            height: 100%;
            border-radius: 99px;
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ── Buttons ─────────────────────────────────────────── */
        .btn-primary-custom {
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            padding: 10px 20px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary-custom:hover {
            background: var(--accent-light);
            box-shadow: 0 0 20px var(--accent-glow);
            transform: translateY(-1px);
        }

        .btn-ghost {
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 10px 20px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-ghost:hover {
            border-color: var(--border-bright);
            color: var(--text-primary);
            background: var(--bg-card);
        }

        /* ── Tags / Badges ───────────────────────────────────── */
        .tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 600;
        }

        .tag-accent    { background: rgba(108,99,255,0.15); color: var(--accent-light); border: 1px solid rgba(108,99,255,0.3); }
        .tag-emerald   { background: var(--emerald-dim); color: var(--emerald); border: 1px solid rgba(16,217,160,0.3); }
        .tag-amber     { background: var(--amber-dim); color: var(--amber); border: 1px solid rgba(245,166,35,0.3); }
        .tag-rose      { background: var(--rose-dim); color: var(--rose); border: 1px solid rgba(255,79,106,0.3); }
        .tag-sky       { background: var(--sky-dim); color: var(--sky); border: 1px solid rgba(56,189,248,0.3); }
        .tag-muted     { background: rgba(255,255,255,0.05); color: var(--text-secondary); border: 1px solid var(--border); }

        /* Priority badges */
        .priority-critical { background: rgba(255,79,106,0.15); color: #ff4f6a; border: 1px solid rgba(255,79,106,0.35); }
        .priority-high     { background: rgba(249,115,22,0.15); color: #fb923c; border: 1px solid rgba(249,115,22,0.35); }
        .priority-medium   { background: rgba(245,166,35,0.15); color: var(--amber); border: 1px solid rgba(245,166,35,0.35); }
        .priority-low      { background: var(--emerald-dim); color: var(--emerald); border: 1px solid rgba(16,217,160,0.3); }

        /* ── Feedback Cards ──────────────────────────────────── */
        .feedback-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 20px;
            margin-bottom: 12px;
            transition: all var(--transition);
            position: relative;
            overflow: hidden;
        }

        .feedback-card::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: var(--fb-color, var(--accent));
        }

        .feedback-card:hover {
            border-color: var(--border-bright);
            background: var(--bg-card-hover);
        }

        .feedback-card.addressed {
            opacity: 0.5;
        }

        .feedback-card.addressed .feedback-title {
            text-decoration: line-through;
        }

        /* ── Keyword Tags ────────────────────────────────────── */
        .keyword-cloud { display: flex; flex-wrap: wrap; gap: 8px; }

        .keyword-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 12px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 500;
            border: 1px solid var(--border);
            background: var(--bg-input);
            color: var(--text-secondary);
            transition: all var(--transition);
            cursor: default;
            font-family: 'JetBrains Mono', monospace;
        }

        .keyword-tag.matched {
            background: var(--emerald-dim);
            border-color: rgba(16,217,160,0.4);
            color: var(--emerald);
        }

        .keyword-tag.missing {
            background: var(--rose-dim);
            border-color: rgba(255,79,106,0.4);
            color: var(--rose);
        }

        /* ── Upload Zone ─────────────────────────────────────── */
        .upload-zone {
            border: 2px dashed var(--border);
            border-radius: var(--radius-xl);
            padding: 60px 40px;
            text-align: center;
            cursor: pointer;
            transition: all var(--transition);
            position: relative;
            overflow: hidden;
            background: var(--bg-card);
        }

        .upload-zone::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at center, rgba(108,99,255,0.05) 0%, transparent 70%);
            opacity: 0;
            transition: opacity var(--transition);
        }

        .upload-zone:hover,
        .upload-zone.drag-over {
            border-color: var(--accent);
            box-shadow: 0 0 40px var(--accent-glow);
        }

        .upload-zone:hover::before,
        .upload-zone.drag-over::before { opacity: 1; }

        .upload-icon {
            font-size: 52px;
            color: var(--text-muted);
            display: block;
            margin-bottom: 16px;
            transition: all var(--transition);
        }

        .upload-zone:hover .upload-icon,
        .upload-zone.drag-over .upload-icon {
            color: var(--accent-light);
            transform: scale(1.1) translateY(-4px);
        }

        /* ── Form Controls ───────────────────────────────────── */
        .form-control-dark {
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            padding: 12px 16px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            width: 100%;
            transition: all var(--transition);
            outline: none;
        }

        .form-control-dark::placeholder { color: var(--text-muted); }

        .form-control-dark:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        /* ── Progress Ring (SVG) ─────────────────────────────── */
        .ring-svg { transform: rotate(-90deg); }
        .ring-track { fill: none; stroke: var(--border); stroke-width: 6; }
        .ring-fill  {
            fill: none;
            stroke-width: 6;
            stroke-linecap: round;
            transition: stroke-dashoffset 1.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ── Alerts ──────────────────────────────────────────── */
        .alert-custom {
            border-radius: var(--radius-md);
            padding: 14px 18px;
            border: 1px solid;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 14px;
        }

        .alert-success { background: var(--emerald-dim); border-color: rgba(16,217,160,0.3); color: var(--emerald); }
        .alert-warning { background: var(--amber-dim);   border-color: rgba(245,166,35,0.3);  color: var(--amber); }
        .alert-danger  { background: var(--rose-dim);    border-color: rgba(255,79,106,0.3);  color: var(--rose); }
        .alert-info    { background: var(--sky-dim);     border-color: rgba(56,189,248,0.3);  color: var(--sky); }

        /* ── Processing Spinner ──────────────────────────────── */
        .processing-pulse {
            width: 12px; height: 12px;
            background: var(--amber);
            border-radius: 50%;
            display: inline-block;
            animation: pulse-dot 1.4s ease-in-out infinite;
        }

        @keyframes pulse-dot {
            0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
            40%           { transform: scale(1.2); opacity: 1; }
        }

        /* ── Skeleton Loading ────────────────────────────────── */
        .skeleton {
            background: linear-gradient(90deg, var(--bg-card) 25%, var(--bg-card-hover) 50%, var(--bg-card) 75%);
            background-size: 200% 100%;
            animation: skeleton-shimmer 1.5s infinite;
            border-radius: var(--radius-sm);
        }

        @keyframes skeleton-shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* ── Tooltip ─────────────────────────────────────────── */
        [data-tooltip] { position: relative; cursor: help; }

        [data-tooltip]::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%);
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 6px 10px;
            font-size: 12px;
            white-space: nowrap;
            color: var(--text-primary);
            opacity: 0;
            pointer-events: none;
            transition: opacity var(--transition);
            z-index: 999;
        }

        [data-tooltip]:hover::after { opacity: 1; }

        /* ── Mobile Toggle ───────────────────────────────────── */
        .sidebar-toggle {
            display: none;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            padding: 8px 10px;
            cursor: pointer;
        }

        /* ── Section Divider ─────────────────────────────────── */
        .section-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 32px 0 24px;
        }

        .section-divider-title {
            font-family: 'Syne', sans-serif;
            font-size: 16px;
            font-weight: 700;
            white-space: nowrap;
        }

        .section-divider-line {
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* ── Toast Notification ──────────────────────────────── */
        #toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .toast-item {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 14px 18px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: var(--shadow-lg);
            animation: toast-in 0.3s ease;
            min-width: 280px;
        }

        @keyframes toast-in {
            from { transform: translateY(16px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }

        /* ── ATS Compatibility Badge ──────────────────────────── */
        .ats-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 13px;
            font-family: 'Syne', sans-serif;
        }

        /* ── Responsive ──────────────────────────────────────── */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar-toggle {
                display: flex;
                align-items: center;
            }
            .page-content { padding: 20px 16px; }
        }

        /* ── Animation Utilities ─────────────────────────────── */
        .fade-up {
            animation: fade-up 0.5s ease both;
        }

        @keyframes fade-up {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .stagger-1 { animation-delay: 0.05s; }
        .stagger-2 { animation-delay: 0.10s; }
        .stagger-3 { animation-delay: 0.15s; }
        .stagger-4 { animation-delay: 0.20s; }

        /* override Bootstrap defaults to match dark theme */
        .form-control, .form-select, textarea {
            background-color: var(--bg-input) !important;
            border-color: var(--border) !important;
            color: var(--text-primary) !important;
        }
        .form-control:focus, .form-select:focus, textarea:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 3px var(--accent-glow) !important;
        }
        .modal-content {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
        }
        .modal-header, .modal-footer {
            border-color: var(--border);
        }
    </style>

    @stack('styles')
</head>

<body>
<div class="app-wrapper">

    <!-- ══ SIDEBAR ═══════════════════════════════════════════ -->
    <aside class="sidebar" id="sidebar">
        <!-- Brand -->
        <div class="sidebar-brand">
            <div class="brand-icon">⚡</div>
            <div>
                <div class="brand-name">ResumeIQ</div>
                <div class="brand-tagline">ATF Tracker</div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <div class="nav-section-label">Main</div>

            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i>
                Dashboard
            </a>

            <a href="{{ route('resumes.index') }}"
               class="nav-link {{ request()->routeIs('resumes.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-person"></i>
                My Resumes
                {{-- @if(auth()->user()->resumes()->pending()->count() > 0)
                    <span class="nav-badge">{{ auth()->user()->resumes()->pending()->count() }}</span>
                @endif --}}
            </a>

            <div class="nav-section-label" style="margin-top:12px;">Actions</div>

            <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="bi bi-cloud-upload"></i>
                Upload Resume
            </a>

            <div class="nav-section-label" style="margin-top:12px;">Account</div>

            <a href="#" class="nav-link">
                <i class="bi bi-person"></i>
                Profile
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link w-100 text-start" style="border:none; background:none;">
                    <i class="bi bi-box-arrow-right"></i>
                    Sign Out
                </button>
            </form>
        </nav>

        <!-- User Footer -->
        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-plan">{{ ucfirst(auth()->user()->plan) }} Plan</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- ══ MAIN CONTENT ══════════════════════════════════════ -->
    <div class="main-content">

        <!-- Topbar -->
        <header class="topbar">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list" style="font-size:18px;"></i>
            </button>

            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>

            <div class="topbar-actions">
                <!-- Upload Button -->
                <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="bi bi-plus-lg"></i>
                    <span class="d-none d-md-inline">Analyze Resume</span>
                </button>

                <!-- Notification bell placeholder -->
                <button class="btn-ghost" style="padding:10px 12px;">
                    <i class="bi bi-bell" style="font-size:16px;"></i>
                </button>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div style="padding: 16px 32px 0;">
                <div class="alert-custom alert-success fade-up">
                    <i class="bi bi-check-circle-fill" style="font-size:18px;flex-shrink:0;"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div style="padding: 16px 32px 0;">
                <div class="alert-custom alert-danger fade-up">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size:18px;flex-shrink:0;"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <main class="page-content">
            @yield('content')
        </main>
    </div>

</div><!-- /app-wrapper -->

<!-- ══ UPLOAD MODAL ══════════════════════════════════════════ -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="border-color:var(--border);padding:24px 28px 20px;">
                <h5 class="modal-title font-display" id="uploadModalLabel"
                    style="font-size:20px;font-weight:700;">
                    ⚡ Analyze Your Resume
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:0 28px 28px;">

                <!-- Upload Form -->
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf

                    <!-- Drop Zone -->
                    <div class="upload-zone" id="dropZone" style="margin-bottom:24px;">
                        <span class="upload-icon"><i class="bi bi-file-earmark-arrow-up"></i></span>
                        <h4 style="font-family:'Syne',sans-serif;font-weight:700;margin-bottom:8px;">
                            Drop your resume here
                        </h4>
                        <p style="color:var(--text-secondary);font-size:14px;margin-bottom:20px;">
                            Supported formats: PDF, DOCX, TXT — Max 5MB
                        </p>
                        <input type="file" name="resume" id="resumeFile"
                               accept=".pdf,.docx,.doc,.txt"
                               style="display:none;" required>
                        <button type="button" class="btn-primary-custom" onclick="$('#resumeFile').click()">
                            <i class="bi bi-folder2-open"></i> Browse Files
                        </button>
                        <div id="fileNameDisplay" style="margin-top:16px;color:var(--emerald);font-size:14px;display:none;">
                            <i class="bi bi-file-check"></i> <span id="fileName"></span>
                        </div>
                    </div>

                    <!-- Optional Job Targeting -->
                    <div style="margin-bottom:20px;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                            <div style="flex:1;height:1px;background:var(--border);"></div>
                            <span style="font-size:12px;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;">
                                Optional: Target a Job
                            </span>
                            <div style="flex:1;height:1px;background:var(--border);"></div>
                        </div>
                        <p style="font-size:13px;color:var(--text-secondary);margin-bottom:14px;">
                            Add a job title or description to get keyword-matched analysis and ATS score for that specific role.
                        </p>
                        <div style="margin-bottom:12px;">
                            <label style="font-size:13px;color:var(--text-secondary);margin-bottom:6px;display:block;">
                                Job Title
                            </label>
                            <input type="text" name="job_title" id="jobTitle"
                                   class="form-control-dark"
                                   placeholder="e.g. Senior Software Engineer at Google">
                        </div>
                        <div>
                            <label style="font-size:13px;color:var(--text-secondary);margin-bottom:6px;display:block;">
                                Job Description <span style="color:var(--text-muted);">(paste from job posting)</span>
                            </label>
                            <textarea name="job_description" id="jobDescription"
                                      class="form-control-dark"
                                      rows="4"
                                      placeholder="Paste the full job description here for accurate keyword matching..."
                                      style="resize:vertical;"></textarea>
                        </div>
                    </div>

                    <!-- Upload Progress (hidden until submit) -->
                    <div id="uploadProgress" style="display:none;margin-bottom:16px;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                            <span class="processing-pulse"></span>
                            <span style="font-size:14px;color:var(--text-secondary);" id="progressLabel">
                                Uploading resume...
                            </span>
                        </div>
                        <div class="score-bar-track">
                            <div class="score-bar-fill" id="progressBar"
                                 style="background:var(--accent);width:0%;transition:width 0.3s ease;"></div>
                        </div>
                    </div>

                    <!-- Errors -->
                    <div id="uploadErrors" style="display:none;" class="alert-custom alert-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span id="uploadErrorText"></span>
                    </div>

                    <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:8px;">
                        <button type="button" class="btn-ghost" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-primary-custom" id="uploadBtn">
                            <i class="bi bi-cpu"></i> Analyze Resume
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container"></div>

<!-- Sidebar Overlay (mobile) -->
<div id="sidebarOverlay" style="
    display:none;position:fixed;inset:0;
    background:rgba(0,0,0,0.6);z-index:99;
" onclick="closeSidebar()"></div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(function () {

    // ── Sidebar Toggle (mobile) ────────────────────────────
    $('#sidebarToggle').on('click', function () {
        $('#sidebar').addClass('open');
        $('#sidebarOverlay').show();
    });

    window.closeSidebar = function () {
        $('#sidebar').removeClass('open');
        $('#sidebarOverlay').hide();
    };

    // ── CSRF Setup for AJAX ────────────────────────────────
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // ── Drag-and-Drop Upload Zone ──────────────────────────
    const $dropZone = $('#dropZone');

    $dropZone.on('dragover dragleave', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).toggleClass('drag-over', e.type === 'dragover');
    });

    $dropZone.on('drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('drag-over');
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            handleFileSelect(files[0]);
        }
    });

    $('#resumeFile').on('change', function () {
        if (this.files.length > 0) handleFileSelect(this.files[0]);
    });

    function handleFileSelect(file) {
        const allowed = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
        if (file.size > 5 * 1024 * 1024) {
            showUploadError('File too large. Maximum size is 5MB.');
            return;
        }
        // Create FileList-compatible object
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('resumeFile').files = dt.files;

        $('#fileName').text(file.name + ' (' + formatBytes(file.size) + ')');
        $('#fileNameDisplay').show();
        $('#uploadErrors').hide();
    }

    function formatBytes(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    // ── Upload Form Submission ─────────────────────────────
    $('#uploadForm').on('submit', function (e) {
        e.preventDefault();

        if (!$('#resumeFile')[0].files.length) {
            showUploadError('Please select a resume file first.');
            return;
        }

        const formData = new FormData(this);

        $('#uploadBtn').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Uploading...');
        $('#uploadProgress').show();
        $('#uploadErrors').hide();

        let progress = 0;
        const progressInterval = setInterval(function () {
            progress = Math.min(progress + Math.random() * 15, 85);
            $('#progressBar').css('width', progress + '%');
        }, 300);

        $.ajax({
            url:         '{{ route("resumes.upload") }}',
            type:        'POST',
            data:        formData,
            contentType: false,
            processData: false,
            success: function (response) {
                clearInterval(progressInterval);
                $('#progressBar').css('width', '100%');
                $('#progressLabel').text('Analysis queued! Redirecting...');

                showToast('✅ Resume uploaded! AI analysis starting...', 'success');

                setTimeout(function () {
                    window.location.href = response.redirect;
                }, 1200);
            },
            error: function (xhr) {
                clearInterval(progressInterval);
                $('#uploadProgress').hide();
                $('#uploadBtn').prop('disabled', false).html('<i class="bi bi-cpu"></i> Analyze Resume');

                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    const msgs = Object.values(errors).flat().join(' ');
                    showUploadError(msgs);
                } else {
                    showUploadError(xhr.responseJSON?.message || 'Upload failed. Please try again.');
                }
            }
        });
    });

    function showUploadError(msg) {
        $('#uploadErrorText').text(msg);
        $('#uploadErrors').show();
    }

    // ── Toast Notifications ────────────────────────────────
    window.showToast = function (message, type = 'info') {
        const icons = { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' };
        const colors = {
            success: 'var(--emerald)',
            error:   'var(--rose)',
            warning: 'var(--amber)',
            info:    'var(--sky)'
        };

        const $toast = $('<div class="toast-item">')
            .html(`<span>${icons[type] || 'ℹ️'}</span><span style="color:${colors[type]}">${message}</span>`);

        $('#toast-container').append($toast);

        setTimeout(function () {
            $toast.css({ opacity: 0, transform: 'translateY(8px)', transition: 'all 0.3s ease' });
            setTimeout(() => $toast.remove(), 300);
        }, 3500);
    };

    // ── Animate Score Bars on Page Load ───────────────────
    function animateScoreBars() {
        $('.score-bar-fill[data-target]').each(function () {
            const $bar = $(this);
            const target = $bar.data('target');
            setTimeout(function () {
                $bar.css('width', target + '%');
            }, 200);
        });
    }
    animateScoreBars();

    // ── Animate Score Rings ────────────────────────────────
    function animateRings() {
        $('[data-ring-score]').each(function () {
            const $ring = $(this);
            const score = parseInt($ring.data('ring-score'));
            const r     = 54;
            const circ  = 2 * Math.PI * r;
            const offset = circ - (score / 100) * circ;
            setTimeout(function () {
                $ring.css('stroke-dashoffset', offset);
            }, 300);
        });
    }
    animateRings();
});
</script>

@stack('scripts')
</body>
</html>
