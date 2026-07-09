<!-- FullCalendar CSS CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.min.css">

<!-- Premium Calendar Module Styling (Inline to ensure loading) -->
<style>
    :root {
        --exam-color: #f59e0b;
        --exam-bg: #fef3c7;
        --exam-border: #fcd34d;
        --exam-text: #92400e;
        
        --holiday-color: #ef4444;
        --holiday-bg: #fee2e2;
        --holiday-border: #fca5a5;
        --holiday-text: #991b1b;
        
        --important-color: #3b82f6;
        --important-bg: #dbeafe;
        --important-border: #93c5fd;
        --important-text: #1e40af;
        
        --working-color: #6b7280;
        --working-bg: #f3f4f6;
        --working-border: #e5e7eb;
        --working-text: #374151;
        
        --shadow-sm: 0 2px 4px rgba(14, 47, 94, 0.03);
        --shadow-md: 0 8px 30px rgba(14, 47, 94, 0.05);
        --shadow-lg: 0 20px 40px rgba(14, 47, 94, 0.08);
        --radius-md: 14px;
        --radius-lg: 20px;
    }

    /* Custom scrollbars */
    .academic-calendar-container *::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .academic-calendar-container *::-webkit-scrollbar-track {
        background: transparent;
    }
    .academic-calendar-container *::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 99px;
    }
    .academic-calendar-container *::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .academic-calendar-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
        font-family: 'Poppins', system-ui, -apple-system, sans-serif;
    }

    /* Premium Hero Section */
    .calendar-hero-section {
        position: relative;
        background: linear-gradient(135deg, #0b2545, #134074);
        border-radius: 24px;
        padding: 60px 40px;
        margin-bottom: 40px;
        overflow: hidden;
        box-shadow: 0 12px 36px rgba(11, 37, 69, 0.15);
        text-align: left;
    }
    
    .calendar-hero-overlay {
        position: absolute;
        inset: 0;
        background-image: radial-gradient(circle at 80% 20%, rgba(237, 165, 42, 0.18), transparent 50%);
        pointer-events: none;
    }

    .hero-tagline {
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: #eda52a; /* Gold */
        background: rgba(237, 165, 42, 0.12);
        padding: 6px 14px;
        border-radius: 50px;
        margin-bottom: 18px;
        border: 1px solid rgba(237, 165, 42, 0.25);
    }

    .hero-title {
        font-family: 'Playfair Display', serif;
        font-size: 40px;
        font-weight: 800;
        color: #fff;
        margin: 0 0 12px 0;
        letter-spacing: -0.5px;
    }

    .hero-desc {
        font-size: 15.5px;
        color: #d7e2f4;
        max-width: 650px;
        margin: 0;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .calendar-hero-section {
            padding: 40px 24px;
            border-radius: 18px;
        }
        .hero-title {
            font-size: 30px;
        }
        .hero-desc {
            font-size: 14px;
        }
    }

    /* Responsive Layout Grid */
    .calendar-layout-grid {
        display: grid;
        grid-template-columns: 330px 1fr;
        gap: 30px;
        align-items: start;
    }

    @media (max-width: 991px) {
        .calendar-layout-grid {
            display: flex;
            flex-direction: column-reverse;
        }
    }

    /* Glassmorphism Cards */
    .premium-card {
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(16px) saturate(180%);
        -webkit-backdrop-filter: blur(16px) saturate(180%);
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: var(--radius-lg);
        padding: 26px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-md);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    
    .premium-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-4px);
        border-color: rgba(203, 213, 225, 0.8);
    }

    .card-title {
        font-size: 16px;
        font-weight: 700;
        color: #0e2f5e;
        margin: 0 0 18px 0;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1.5px solid rgba(226, 232, 240, 0.5);
        padding-bottom: 10px;
    }

    /* Form Controls */
    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        font-size: 11.5px;
        font-weight: 650;
        color: #64748b;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .select-wrapper {
        position: relative;
    }

    .select-wrapper select {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid #e2e8f0;
        border-radius: var(--radius-md);
        background-color: #fff;
        color: #1f2937;
        font-size: 14.5px;
        font-weight: 550;
        appearance: none;
        outline: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .select-wrapper select:focus {
        border-color: var(--primary, #3b82f6);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }

    .select-wrapper::after {
        content: '';
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 6px solid #6b7280;
        pointer-events: none;
    }

    /* PDF download button */
    .btn-pdf-download {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        background: linear-gradient(135deg, #0e2f5e, #1f5aa8);
        color: #fff;
        font-size: 14.5px;
        font-weight: 600;
        padding: 13px 20px;
        border-radius: var(--radius-md);
        text-decoration: none;
        box-shadow: 0 4px 14px rgba(14, 47, 94, 0.18);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-pdf-download:hover {
        opacity: 0.95;
        box-shadow: 0 6px 20px rgba(14, 47, 94, 0.28);
        transform: translateY(-1.5px);
    }

    /* Progress Card styling */
    .progress-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .term-badge {
        font-size: 12px;
        font-weight: 700;
        background: #eff6ff;
        color: #2563eb;
        padding: 4px 10px;
        border-radius: 99px;
        border: 1px solid #bfdbfe;
    }

    .progress-status {
        font-size: 13px;
        font-weight: 700;
        color: #16a34a;
    }

    .progress-bar-container {
        width: 100%;
        height: 8px;
        background: #f1f5f9;
        border-radius: 99px;
        overflow: hidden;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #3b82f6, #60a5fa);
        border-radius: 99px;
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Stats Card styling */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        text-align: center;
    }

    .stat-item {
        padding: 14px 6px;
        border-radius: var(--radius-md);
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        transition: transform 0.2s ease;
    }
    .stat-item:hover {
        transform: scale(1.03);
    }

    .stat-number {
        display: block;
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
    }

    .stat-label {
        font-size: 9.5px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 4px;
        display: block;
    }

    .stat-item.working {
        background: #f0fdf4;
        border-color: #dcfce7;
    }
    .stat-item.working .stat-number {
        color: #16a34a;
    }

    .stat-item.non-working {
        background: #fff5f5;
        border-color: #fee2e2;
    }
    .stat-item.non-working .stat-number {
        color: #dc2626;
    }

    /* Upcoming List with pulse */
    .upcoming-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .upcoming-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border-radius: var(--radius-md);
        background: rgba(255, 255, 255, 0.6);
        border: 1px solid #f1f5f9;
        border-left: 4.5px solid transparent;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .upcoming-item:hover {
        border-color: #cbd5e1;
        background: #fff;
        transform: translateX(6px);
        box-shadow: var(--shadow-sm);
    }
    
    .upcoming-item.category-exam { border-left-color: var(--exam-color); }
    .upcoming-item.category-holiday { border-left-color: var(--holiday-color); }
    .upcoming-item.category-important_date { border-left-color: var(--important-color); }
    .upcoming-item.category-working_day_note { border-left-color: var(--working-color); }

    .upcoming-item-styled {
        border-left: 4.5px solid transparent;
        padding: 8px 8px 8px 12px;
        background: transparent;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .upcoming-item-styled:hover {
        transform: translateX(4px);
        background: rgba(255, 255, 255, 0.85);
        border-radius: 8px;
        box-shadow: var(--shadow-sm);
    }

    .upcoming-badge-container {
        position: relative;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        width: 58px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .upcoming-date {
        font-size: 12.5px;
        font-weight: 700;
        color: #334155;
        text-align: center;
        line-height: 1.2;
    }

    .pulse-indicator {
        position: absolute;
        top: -4px;
        right: -4px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: block;
    }

    /* Pulsing CSS Keyframe Animations */
    .pulse-indicator.category-exam {
        background-color: var(--exam-color);
        animation: glow-pulse-red 2s infinite;
    }
    .pulse-indicator.category-holiday {
        background-color: var(--holiday-color);
        animation: glow-pulse-yellow 2s infinite;
    }
    .pulse-indicator.category-important_date {
        background-color: var(--important-color);
        animation: glow-pulse-blue 2s infinite;
    }
    .pulse-indicator.category-working_day_note {
        background-color: var(--working-color);
        animation: glow-pulse-grey 2s infinite;
    }

    @keyframes glow-pulse-red {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
    @keyframes glow-pulse-yellow {
        0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(245, 158, 11, 0); }
        100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
    }
    @keyframes glow-pulse-blue {
        0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(59, 130, 246, 0); }
        100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
    }
    @keyframes glow-pulse-grey {
        0% { box-shadow: 0 0 0 0 rgba(107, 114, 128, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(107, 114, 128, 0); }
        100% { box-shadow: 0 0 0 0 rgba(107, 114, 128, 0); }
    }

    .upcoming-details {
        min-w-0 flex-1;
    }

    .upcoming-item-title {
        font-size: 13.5px;
        font-weight: 600;
        color: #1e293b;
        margin: 0 0 4px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* View Mode Tabs */
    .view-mode-tabs {
        display: inline-flex;
        background: #f1f5f9;
        padding: 4px;
        border-radius: var(--radius-md);
        margin-bottom: 24px;
    }

    .tab-button {
        border: none;
        background: transparent;
        padding: 9px 22px;
        font-size: 14px;
        font-weight: 600;
        color: #64748b;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .tab-button.active {
        background: #fff;
        color: #0e2f5e;
        box-shadow: 0 4px 12px rgba(14, 47, 94, 0.06);
    }

    /* Workspace Card styling */
    .card {
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        margin-bottom: 24px;
    }

    /* Badges styling */
    .badge {
        display: inline-flex;
        align-items: center;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 99px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 1px solid transparent;
    }

    .badge.exam {
        background: var(--exam-bg);
        color: var(--exam-text);
        border-color: var(--exam-border);
    }

    .badge.holiday {
        background: var(--holiday-bg);
        color: var(--holiday-text);
        border-color: var(--holiday-border);
    }

    .badge.important_date {
        background: var(--important-bg);
        color: var(--important-text);
        border-color: var(--important-border);
    }

    .badge.working_day_note {
        background: var(--working-bg);
        color: var(--working-text);
        border-color: var(--working-border);
    }

    .badge.class-badge {
        background: #f3e8ff;
        color: #6b21a8;
        border-color: #e9d5ff;
    }
    
    .badge.sub-type-badge {
        background: #f1f5f9;
        color: #475569;
        border-color: #e2e8f0;
        text-transform: none;
    }

    /* Timeline / List View */
    .term-entries-timeline {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .timeline-card {
        display: flex;
        gap: 20px;
        background: #fff;
        border: 1.5px solid #f1f5f9;
        border-radius: var(--radius-md);
        padding: 20px;
        transition: all 0.25s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        overflow: hidden;
    }

    .timeline-card:hover {
        border-color: #e2e8f0;
        transform: translateX(6px);
        box-shadow: var(--shadow-md);
    }
    
    .timeline-card::after {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
    }
    .timeline-card.category-exam::after { background: var(--exam-color); }
    .timeline-card.category-holiday::after { background: var(--holiday-color); }
    .timeline-card.category-important_date::after { background: var(--important-color); }
    .timeline-card.category-working_day_note::after { background: var(--working-color); }

    .timeline-date-block {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        width: 64px;
        height: 64px;
        flex-shrink: 0;
    }

    .timeline-day {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }

    .timeline-month {
        font-size: 11.5px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        margin-top: 2px;
    }

    .timeline-content-block {
        flex: 1;
        min-w-0;
    }

    .timeline-header-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .sub-type-text {
        font-size: 12.5px;
        font-weight: 600;
        color: #64748b;
    }

    /* Year mini-calendar grid */
    .year-calendar-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 26px;
    }

    .mini-month-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: var(--radius-md);
        padding: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        transition: all 0.2s ease;
    }
    .mini-month-card:hover {
        box-shadow: var(--shadow-md);
        border-color: #cbd5e1;
    }

    .mini-month-title {
        font-size: 14.5px;
        font-weight: 700;
        color: #0e2f5e;
        border-bottom: 1.5px solid #f1f5f9;
        padding-bottom: 6px;
        margin-bottom: 10px;
    }

    .mini-days-header-grid, .mini-days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
    }

    .mini-day, .mini-day-blank {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 28px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 600;
        position: relative;
    }

    .mini-day {
        color: #334155;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .mini-day:hover {
        background: #f1f5f9;
        transform: scale(1.08);
    }

    .mini-day.day-sunday {
        color: #dc2626;
    }
    .mini-day.day-saturday {
        color: #ea580c;
    }
    .mini-day.day-weekday {
        color: #16a34a;
    }
    .mini-day.holiday {
        color: #94a3b8;
    }
    .mini-day.is-today {
        box-shadow: 0 0 0 2px #3b82f6;
    }

    .mini-day.has-entries {
        font-weight: 800;
        color: #0f172a;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .mini-dots-container {
        display: flex;
        justify-content: center;
        gap: 2px;
        position: absolute;
        bottom: 2px;
        width: 100%;
    }

    .mini-dot {
        width: 4px;
        height: 4px;
        border-radius: 50%;
        display: block;
    }

    .mini-dot.dot-exam { background: var(--exam-color); }
    .mini-dot.dot-holiday { background: var(--holiday-color); }
    .mini-dot.dot-important_date { background: var(--important-color); }
    .mini-dot.dot-working_day_note { background: var(--working-color); }

    /* Modal Overlay & Drawer style */
    .calendar-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .modal-overlay {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(6px);
    }

    .modal-content-wrapper {
        position: relative;
        width: 100%;
        max-width: 520px;
        z-index: 101;
    }

    .modal-box {
        background: #fff;
        border-radius: var(--radius-lg);
        box-shadow: 0 25px 50px -12px rgba(14, 47, 94, 0.25);
        padding: 28px;
        max-height: 85vh;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
    }

    /* FullCalendar Customizations */
    .fc {
        --fc-border-color: #f1f5f9;
        --fc-button-bg-color: #0e2f5e;
        --fc-button-border-color: #0e2f5e;
        --fc-button-hover-bg-color: #1f5aa8;
        --fc-button-hover-border-color: #1f5aa8;
        --fc-button-active-bg-color: #1f5aa8;
        --fc-button-active-border-color: #1f5aa8;
        --fc-today-bg-color: #f0f7ff;
        font-family: inherit;
    }

    .fc .fc-toolbar-title {
        font-size: 20px;
        font-weight: 800;
        color: #0e2f5e;
    }

    .fc .fc-button {
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 600;
        text-transform: capitalize;
        padding: 8px 16px;
    }

    .fc .fc-col-header-cell-cushion {
        font-size: 12.5px;
        font-weight: 700;
        text-transform: uppercase;
        padding: 10px 0;
    }

    .fc .fc-daygrid-day-number {
        font-size: 14px;
        font-weight: 700;
        color: #475569;
        padding: 10px;
    }

    .fc .fc-day-today .fc-daygrid-day-number {
        color: #2563eb;
        font-weight: 800;
        background: #dbeafe;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 4px;
    }

    .fc-event {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .fc-event:hover {
        transform: translateY(-1px) scale(1.01);
        box-shadow: 0 4px 6px rgba(0,0,0,0.08);
    }
</style>

<!-- FullCalendar JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js" defer></script>

<!-- Back to Home Link -->
<div style="margin-bottom: 24px;">
    <a href="{{ url('/') }}" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; color: #64748b; font-weight: 650; font-size: 14px; transition: all 0.2s; font-family: 'Poppins', sans-serif;" onmouseover="this.style.color='#0e2f5e'; this.style.transform='translateX(-2px)';" onmouseout="this.style.color='#64748b'; this.style.transform='translateX(0)';">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 16px; height: 16px;"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back to Home Page
    </a>
</div>

<!-- Hero Section -->
<div class="calendar-hero-section">
    <div class="calendar-hero-overlay"></div>
    <div class="calendar-hero-content">
        <span class="hero-tagline">Academic Session 2026-27</span>
        <h1 class="hero-title">Prayaag Academic Calendar</h1>
        <p class="hero-desc">Access school terms, vacations, examination schedules, working Saturdays, PTM announcements, and important academic milestones.</p>
    </div>
</div>

<livewire:academic-calendar-view :sessionId="$selectedSessionId" :termId="$selectedTermId" />
