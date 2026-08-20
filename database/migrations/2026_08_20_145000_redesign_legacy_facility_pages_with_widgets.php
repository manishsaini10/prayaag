<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Redesign legacy facility & wing pages with modern dedicated widgets.
     * Pages: labs, library, sports, transportations, safety-security,
     *        tours-and-excursions, unesco, summer-camp,
     *        junior-wing-school-in-panipat, senior-wing-school-in-panipat
     */
    public function up(): void
    {
        $pages = [
            'labs'                           => 'labs-page',
            'library'                        => 'library-page',
            'sports'                         => 'sports-page',
            'transportations'                => 'transportation-page',
            'safety-security'                => 'safety-security-page',
            'tours-and-excursions'           => 'tours-excursions-page',
            'unesco'                         => 'unesco-page',
            'summer-camp'                    => 'summer-camp-page',
            'junior-wing-school-in-panipat'  => 'junior-wing-page',
            'senior-wing-school-in-panipat'  => 'senior-wing-page',
        ];

        foreach ($pages as $slug => $widgetType) {
            $page = DB::table('pages')->where('slug', $slug)->first();
            if (! $page) {
                continue;
            }

            // Delete all existing sections (cascade deletes rows/columns/widgets via DB constraints)
            $sectionIds = DB::table('page_sections')->where('page_id', $page->id)->pluck('id');
            foreach ($sectionIds as $sid) {
                $rowIds = DB::table('page_rows')->where('section_id', $sid)->pluck('id');
                foreach ($rowIds as $rid) {
                    $colIds = DB::table('page_columns')->where('row_id', $rid)->pluck('id');
                    foreach ($colIds as $cid) {
                        DB::table('page_widgets')->where('column_id', $cid)->delete();
                    }
                    DB::table('page_columns')->where('row_id', $rid)->delete();
                }
                DB::table('page_rows')->where('section_id', $sid)->delete();
            }
            DB::table('page_sections')->where('page_id', $page->id)->delete();

            // Create new single section
            $sectionId = (string) Str::ulid();
            DB::table('page_sections')->insert([
                'id'         => $sectionId,
                'page_id'    => $page->id,
                'sort_order' => 0,
                'settings'   => json_encode(['type' => 'flush']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create row
            $rowId = (string) Str::ulid();
            DB::table('page_rows')->insert([
                'id'         => $rowId,
                'section_id' => $sectionId,
                'sort_order' => 0,
                'settings'   => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create column (full-width)
            $colId = (string) Str::ulid();
            DB::table('page_columns')->insert([
                'id'         => $colId,
                'row_id'     => $rowId,
                'width'      => 12,
                'sort_order' => 0,
                'settings'   => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create the new modern widget
            $widgetId = (string) Str::ulid();
            DB::table('page_widgets')->insert([
                'id'          => $widgetId,
                'column_id'   => $colId,
                'widget_type' => $widgetType,
                'sort_order'  => 0,
                'settings'    => json_encode([]),  // uses widget defaultSettings()
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Soft rollback: just clear the modern widget sections (original HTML content is gone)
        $slugs = [
            'labs', 'library', 'sports', 'transportations', 'safety-security',
            'tours-and-excursions', 'unesco', 'summer-camp',
            'junior-wing-school-in-panipat', 'senior-wing-school-in-panipat',
        ];

        foreach ($slugs as $slug) {
            $page = DB::table('pages')->where('slug', $slug)->first();
            if (! $page) continue;

            $sectionIds = DB::table('page_sections')->where('page_id', $page->id)->pluck('id');
            foreach ($sectionIds as $sid) {
                $rowIds = DB::table('page_rows')->where('section_id', $sid)->pluck('id');
                foreach ($rowIds as $rid) {
                    $colIds = DB::table('page_columns')->where('row_id', $rid)->pluck('id');
                    foreach ($colIds as $cid) {
                        DB::table('page_widgets')->where('column_id', $cid)->delete();
                    }
                    DB::table('page_columns')->where('row_id', $rid)->delete();
                }
                DB::table('page_rows')->where('section_id', $sid)->delete();
            }
            DB::table('page_sections')->where('page_id', $page->id)->delete();
        }
    }
};
