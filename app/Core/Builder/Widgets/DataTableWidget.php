<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — Advanced Data Table.
 * Features searchable, responsive tabular data grid with status indicators.
 */
class DataTableWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-data-table';
    }

    public function label(): string
    {
        return 'Data Table';
    }

    public function category(): string
    {
        return 'pro-general';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Academic Records',
            'heading' => 'Class XII Toppers & Distinction List',
            'sub'     => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Audited board examination rankings.',
            'columns' => ['Roll No', 'Student Name', 'Stream', 'Percentage', 'Status'],
            'rows'    => [
                ['roll' => '202501', 'name' => 'Aarav Sharma', 'stream' => 'Science (PCM)', 'pct' => '98.6%', 'status' => 'Distinction'],
                ['roll' => '202502', 'name' => 'Ananya Verma', 'stream' => 'Commerce (Maths)', 'pct' => '97.8%', 'status' => 'Distinction'],
                ['roll' => '202503', 'name' => 'Rohan Gupta', 'stream' => 'Science (PCB)', 'pct' => '97.2%', 'status' => 'Distinction'],
                ['roll' => '202504', 'name' => 'Priya Nair', 'stream' => 'Humanities', 'pct' => '96.5%', 'status' => 'Distinction'],
                ['roll' => '202505', 'name' => 'Kabir Mehta', 'stream' => 'Science (PCM)', 'pct' => '95.8%', 'status' => 'Distinction'],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading'),
            $this->setting($settings, 'sub')
        );

        $cols = (array) $this->setting($settings, 'columns', []);
        $rows = (array) $this->setting($settings, 'rows', []);
        $tableId = 'ek-dt-' . uniqid();

        $thHtml = '';
        foreach ($cols as $c) {
            $thHtml .= '<th>' . $this->e($c) . '</th>';
        }

        $trHtml = '';
        foreach ($rows as $r) {
            $roll   = $this->e($r['roll'] ?? '');
            $name   = $this->e($r['name'] ?? '');
            $stream = $this->e($r['stream'] ?? '');
            $pct    = $this->e($r['pct'] ?? '');
            $status = $this->e($r['status'] ?? 'Passed');

            $trHtml .= <<<HTML
            <tr>
                <td><strong>{$roll}</strong></td>
                <td>{$name}</td>
                <td><span class="ek-dt-tag">{$stream}</span></td>
                <td><strong style="color: #10b981;">{$pct}</strong></td>
                <td><span class="ek-dt-badge">{$status}</span></td>
            </tr>
            HTML;
        }

        return <<<HTML
        <style>
        .ek-dt-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 18px; padding: 24px; max-width: 1000px; margin: 30px auto; box-shadow: 0 10px 30px rgba(11,37,69,.06); }
        .ek-dt-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; gap: 12px; flex-wrap: wrap; }
        .ek-dt-search { padding: 8px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 13px; outline: none; width: 220px; transition: border-color .2s; }
        .ek-dt-search:focus { border-color: #0b2545; }
        .ek-dt-table-wrap { overflow-x: auto; }
        .ek-dt-table { width: 100%; border-collapse: collapse; font-size: 14px; text-align: left; }
        .ek-dt-table th { background: #f8fafc; padding: 12px 16px; font-weight: 700; color: #0b2545; border-bottom: 2px solid #e2e8f0; font-size: 12.5px; text-transform: uppercase; letter-spacing: .5px; }
        .ek-dt-table td { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        .ek-dt-table tr:hover td { background: #f8fafc; }
        .ek-dt-tag { background: #f1f5f9; color: #475569; font-size: 11.5px; font-weight: 600; padding: 3px 8px; border-radius: 6px; }
        .ek-dt-badge { background: rgba(16,185,129,.12); color: #10b981; font-size: 11.5px; font-weight: 700; padding: 3px 10px; border-radius: 999px; }
        </style>

        <section class="ek-dt-sec">
            {$head}
            <div class="ek-dt-card">
                <div class="ek-dt-top">
                    <input type="text" class="ek-dt-search" placeholder="🔍 Search records..." oninput="
                        var q = this.value.toLowerCase();
                        document.querySelectorAll('#{$tableId} tbody tr').forEach(function(r) {
                            r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
                        });
                    ">
                    <span style="font-size: 12px; color: #64748b; font-weight: 600;">Showing Top Performers</span>
                </div>
                <div class="ek-dt-table-wrap">
                    <table class="ek-dt-table" id="{$tableId}">
                        <thead><tr>{$thHtml}</tr></thead>
                        <tbody>{$trHtml}</tbody>
                    </table>
                </div>
            </div>
        </section>
        HTML;
    }
}
