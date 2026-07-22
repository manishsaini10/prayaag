<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Weekly Mess Menu – Prayaag International School</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #1f2937;
            background: #fff;
            padding: 28px 32px;
            line-height: 1.5;
        }

        /* ── Header ── */
        .header {
            text-align: center;
            border-bottom: 3px solid #047857;
            padding-bottom: 14px;
            margin-bottom: 22px;
        }
        .school-name {
            font-size: 19px;
            font-weight: 700;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .school-sub {
            font-size: 10px;
            color: #6b7280;
            margin-top: 3px;
        }
        .doc-title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .validity {
            font-size: 10px;
            color: #6b7280;
            margin-top: 4px;
        }

        /* ── Today Highlight Box ── */
        .today-box {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-left: 4px solid #047857;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 18px;
        }
        .today-box .label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            color: #059669;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .today-box .day-name {
            font-size: 14px;
            font-weight: 700;
            color: #047857;
        }
        .today-box .dishes {
            font-size: 11px;
            color: #374151;
            margin-top: 4px;
        }

        /* ── Table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
        }
        thead tr {
            background-color: #047857;
            color: #fff;
        }
        thead th {
            padding: 10px 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            text-align: left;
        }
        tbody tr {
            border-bottom: 1px solid #f3f4f6;
        }
        tbody tr.today-row {
            background-color: #f0fdf4;
        }
        tbody tr:hover { background-color: #f9fafb; }
        tbody td {
            padding: 10px 12px;
            vertical-align: top;
        }
        .day-cell { font-weight: 700; color: #111827; white-space: nowrap; }
        .date-cell { color: #6b7280; white-space: nowrap; font-size: 10.5px; }
        .today-badge {
            display: inline-block;
            background: #047857;
            color: #fff;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 1px 6px;
            border-radius: 4px;
            margin-right: 4px;
        }
        .dish-tag {
            display: inline-block;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            color: #374151;
            font-size: 10.5px;
            padding: 3px 8px;
            border-radius: 5px;
            margin: 2px 2px 2px 0;
        }
        .dish-tag-today {
            background: #d1fae5;
            border-color: #6ee7b7;
            color: #065f46;
        }
        .status-badge {
            display: inline-block;
            font-size: 9.5px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 12px;
            border: 1px solid;
            white-space: nowrap;
        }
        .status-serving { background: #047857; color: #fff; border-color: #047857; }
        .status-scheduled { background: #f0fdf4; color: #047857; border-color: #a7f3d0; }
        .status-closed { background: #f9fafb; color: #9ca3af; border-color: #e5e7eb; }
        .note { font-size: 10px; color: #059669; margin-top: 4px; }

        /* ── Footer ── */
        .footer {
            margin-top: 22px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            font-size: 9.5px;
            color: #9ca3af;
        }
        .footer .left { text-align: left; }
        .footer .right { text-align: right; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <div class="school-name">Prayaag International School</div>
        <div class="school-sub">CBSE Affiliated · Nutritious · Hygienic · Balanced</div>
        <div class="doc-title">Weekly Mess Menu Schedule</div>
        <div class="validity">
            @if($menu->effective_from)
                Valid: {{ $menu->effective_from->format('d M Y') }}
                @if($menu->effective_to) – {{ $menu->effective_to->format('d M Y') }} @endif
            @endif
            &nbsp;·&nbsp; Generated on: {{ now()->format('d M Y, h:i A') }}
        </div>
    </div>

    {{-- Today's Quick Highlight --}}
    @php
        $todayDay   = strtolower(\Illuminate\Support\Carbon::now()->format('l'));
        $todayLabel = ucfirst($todayDay);
        $todayData  = $grouped[$todayDay]['lunch'] ?? ['items' => [], 'notes' => ''];
        $todayItems = $todayData['items'] ?? [];
    @endphp
    @if(!empty($todayItems))
        <div class="today-box">
            <div class="label">🍽 Today's Lunch · {{ $todayLabel }}, {{ now()->format('d M Y') }}</div>
            <div class="dishes">{{ implode(' · ', $todayItems) }}</div>
        </div>
    @endif

    {{-- Menu Table --}}
    @php
        $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        $dayLabels = [
            'monday'=>'Monday','tuesday'=>'Tuesday','wednesday'=>'Wednesday',
            'thursday'=>'Thursday','friday'=>'Friday','saturday'=>'Saturday','sunday'=>'Sunday',
        ];
    @endphp

    <table>
        <thead>
            <tr>
                <th style="width:110px;">Day</th>
                <th style="width:90px;">Date</th>
                <th>Lunch Menu</th>
                <th style="width:90px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($days as $idx => $day)
                @php
                    $isToday  = ($day === $todayDay);
                    $dateObj  = $menu->effective_from->copy()->addDays($idx);
                    $mealData = ($isToday && isset($specialOverrides['lunch']))
                        ? $specialOverrides['lunch']
                        : ($grouped[$day]['lunch'] ?? ['items' => [], 'notes' => '']);
                    $dishes   = $mealData['items'] ?? [];
                    $note     = $mealData['notes'] ?? '';
                    $hasLunch = !empty($dishes);
                @endphp
                <tr class="{{ $isToday ? 'today-row' : '' }}">
                    <td class="day-cell">
                        @if($isToday)<span class="today-badge">Today</span>@endif
                        {{ $dayLabels[$day] }}
                    </td>
                    <td class="date-cell">{{ $dateObj->format('d M Y') }}</td>
                    <td>
                        @if($hasLunch)
                            @foreach($dishes as $dish)
                                <span class="dish-tag {{ $isToday ? 'dish-tag-today' : '' }}">{{ $dish }}</span>
                            @endforeach
                            @if($note)
                                <div class="note">💡 {{ $note }}</div>
                            @endif
                        @else
                            <span style="color:#9ca3af; font-style:italic; font-size:10.5px;">Not scheduled</span>
                        @endif
                    </td>
                    <td>
                        @if($isToday)
                            <span class="status-badge status-serving">Serving Now</span>
                        @elseif(!$hasLunch)
                            <span class="status-badge status-closed">Closed</span>
                        @else
                            <span class="status-badge status-scheduled">Scheduled</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <div class="left">⚠ Menu subject to change based on ingredient availability.</div>
        <div class="right">Prayaag International School · Mess Management</div>
    </div>

</body>
</html>
