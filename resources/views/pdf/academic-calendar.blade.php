<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Academic Calendar - {{ $session->session_name }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 10px;
            font-size: 13px;
            line-height: 1.4;
        }

        /* Header styling */
        .header {
            text-align: center;
            border-bottom: 2px solid #0e2f5e;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .school-name {
            font-size: 22px;
            font-weight: bold;
            color: #0e2f5e;
            margin: 0;
            text-transform: uppercase;
        }

        .school-sub {
            font-size: 11px;
            color: #666;
            margin: 4px 0 0 0;
        }

        .document-title {
            font-size: 16px;
            font-weight: bold;
            color: #1f5aa8;
            margin: 10px 0 0 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Section Layouts */
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #0e2f5e;
            background: #f0f4f8;
            padding: 6px 10px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 4px solid #0e2f5e;
            text-transform: uppercase;
        }

        /* Tables styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th {
            background-color: #0e2f5e;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            font-size: 11px;
            text-transform: uppercase;
        }

        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
        }

        tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        /* Color tags for categories */
        .badge {
            display: inline-block;
            font-size: 10px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
            text-align: center;
        }

        .badge-exam {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-holiday {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-important_date {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-working_day_note {
            background-color: #f3f4f6;
            color: #374151;
        }

        /* Month section layouts */
        .month-block {
            page-break-inside: avoid;
            margin-bottom: 25px;
        }

        .month-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f5aa8;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        /* Footer notice */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    <!-- Header Block -->
    <div class="header">
        <h1 class="school-name">Prayaag International School</h1>
        <p class="school-sub">CBSE Affiliation No. : 531592 | School Code : 41568</p>
        <div class="document-title">Academic Calendar (Session: {{ $session->session_name }})</div>
    </div>

    <!-- Terms Schedule Section -->
    <div class="section-title">Academic Terms / Semesters Overview</div>
    <table>
        <thead>
            <tr>
                <th>Term Name</th>
                <th>Start Date</th>
                <th>End Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($terms as $term)
                <tr>
                    <td><strong>{{ $term->term_name }}</strong></td>
                    <td>{{ $term->start_date->format('F d, Y') }}</td>
                    <td>{{ $term->end_date->format('F d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: #888;">No term breakdown configured for this session.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- Monthly Breakdown Section -->
    <div class="section-title">Academic Schedule Breakdown (Month-by-Month)</div>

    @forelse($groupedEntries as $monthYear => $monthEntries)
        <div class="month-block">
            <div class="month-title">{{ $monthYear }}</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">Date(s)</th>
                        <th style="width: 15%;">Category</th>
                        <th style="width: 50%;">Title / Sub-type</th>
                        <th style="width: 20%;">Class Relevance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthEntries as $entry)
                        <tr>
                            <td>
                                <strong>{{ $entry->start_date->format('M d') }}</strong>
                                @if($entry->end_date)
                                    - {{ $entry->end_date->format('M d') }}
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $entry->category }}">
                                    {{ str_replace('_', ' ', $entry->category) }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ $entry->title }}</strong>
                                @if($entry->sub_type)
                                    <span style="color: #666; font-size: 11px;">({{ $entry->sub_type }})</span>
                                @endif
                                @if(!$entry->is_working_day && $entry->category !== 'holiday')
                                    <span style="color: #c2410c; font-size: 10px; font-weight: bold; margin-left: 5px;">(Non-Working)</span>
                                @endif
                            </td>
                            <td>
                                {{ $entry->class ? $entry->class->class_name : 'All Classes' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div style="text-align: center; color: #888; padding: 20px;">No academic calendar entries scheduled for this session.</div>
    @endforelse

    <!-- Document Footer -->
    <div class="footer">
        Generated on {{ now()->format('F d, Y') }} | Prayaag International School © {{ now()->format('Y') }}
    </div>

</body>
</html>
