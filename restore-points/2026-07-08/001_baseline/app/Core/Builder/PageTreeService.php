<?php

namespace App\Core\Builder;

use App\Models\Page;
use Illuminate\Support\Facades\DB;

/**
 * Persists a full page tree in one transaction. The editor sends the entire
 * sections/rows/columns/widgets structure; we replace what's stored. Child
 * rows/columns/widgets are removed via FK cascade when sections are deleted.
 */
class PageTreeService
{
    /**
     * @param  array<int, array<string, mixed>>  $sections
     */
    public function sync(Page $page, array $sections): Page
    {
        DB::transaction(function () use ($page, $sections) {
            // Bulk delete; FK cascadeOnDelete clears rows -> columns -> widgets.
            $page->sections()->delete();

            foreach (array_values($sections) as $sIndex => $sectionData) {
                $section = $page->sections()->create([
                    'section_type' => $sectionData['type'] ?? 'section',
                    'sort_order'   => $sIndex,
                    'settings'     => $sectionData['settings'] ?? null,
                ]);

                foreach (array_values($sectionData['rows'] ?? []) as $rIndex => $rowData) {
                    $row = $section->rows()->create([
                        'sort_order' => $rIndex,
                        'settings'   => $rowData['settings'] ?? null,
                    ]);

                    foreach (array_values($rowData['columns'] ?? []) as $cIndex => $columnData) {
                        $column = $row->columns()->create([
                            'width'      => $columnData['width'] ?? 12,
                            'sort_order' => $cIndex,
                            'settings'   => $columnData['settings'] ?? null,
                        ]);

                        foreach (array_values($columnData['widgets'] ?? []) as $wIndex => $widgetData) {
                            $column->widgets()->create([
                                'widget_type' => $widgetData['type'] ?? 'text',
                                'sort_order'  => $wIndex,
                                'settings'    => $widgetData['settings'] ?? [],
                            ]);
                        }
                    }
                }
            }
        });

        return $page->fresh();
    }
}
