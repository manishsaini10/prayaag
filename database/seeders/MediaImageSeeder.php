<?php

namespace Database\Seeders;

use App\Core\Media\MediaImporter;
use App\Models\MediaFolder;
use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Imports ALL external images referenced by the website into the local
 * media library, then rewrites settings and page content to use the local
 * storage URLs — WordPress-style media import.
 *
 * Idempotent: re-running only imports URLs that haven't been seen before.
 *
 *   php artisan db:seed --class=Database\\Seeders\\MediaImageSeeder
 */
class MediaImageSeeder extends Seeder
{
    public function run(): void
    {
        $importer = app(MediaImporter::class);

        $folder = MediaFolder::firstOrCreate(
            ['slug' => 'imported'],
            ['name' => 'Imported', 'path' => 'imported']
        );

        $urls = $this->collectImageUrls();

        $this->command?->info('Found ' . count($urls) . ' unique image URLs to import.');

        $mapping = [];
        foreach ($urls as $url) {
            $localPath = $importer->import($url, $folder);
            if ($localPath !== null) {
                $mapping[$url] = $localPath;
            } else {
                $this->command?->warn('  FAILED: ' . $url);
            }
        }

        $this->command?->info('Imported ' . count($mapping) . ' images successfully.');

        $this->rewriteSettings($mapping);
        $this->rewritePages($mapping);
        $this->rewritePageSeo($mapping);

        \App\Core\Theme\ThemeRenderer::flush();

        $this->command?->info('Settings and page content rewritten to use local storage URLs.');
    }

    protected function collectImageUrls(): array
    {
        $wpBase = 'https://prayaaginternationalschool.com/wp-content/uploads/';
        $urls = [];

        // Settings images
        $urls[] = $wpBase . '2021/12/prayaag-school-logo.png';
        $urls[] = $wpBase . '2022/01/british-council-logo-150x150.jpg';

        // HeroWidget / HomePage defaults
        $urls[] = $wpBase . '2022/01/About-Prayaag-International-School.webp';

        // home.blade.php fixed images
        $homeImgs = [
            $wpBase . '2021/12/cropped-prayaag-school-logo-270x270.png',
            $wpBase . '2022/01/school-png-icon-150x150.png',
            $wpBase . '2022/01/school-bag-png-Icon-150x150.png',
            $wpBase . '2022/01/student-login-icon-150x150.png',
            $wpBase . '2022/01/Admin-Login-150x150.png',
            $wpBase . '2022/01/Online-Payment-150x150.png',
            $wpBase . '2023/08/facebook-social-icon.png',
            $wpBase . '2023/08/instagram-social-icon.png',
            $wpBase . '2023/08/x-social-icon.png',
            $wpBase . '2023/08/linkedin-social-icon.png',
            $wpBase . '2023/08/youtube-social-icon.png',
        ];
        array_push($urls, ...$homeImgs);

        // Extract ALL wp-content image URLs from the ImportedPagesSeeder source
        $seederPath = database_path('seeders/ImportedPagesSeeder.php');
        if (file_exists($seederPath)) {
            $source = file_get_contents($seederPath);
            // Match any wp-content/uploads/... URL (image or PDF)
            preg_match_all('/https?:\/\/prayaaginternationalschool\.com\/wp-content\/uploads\/[^\s"\'<>]+/i', $source, $matches);
            foreach ($matches[0] as $match) {
                $clean = rtrim($match, ')"\'');
                // Only keep image extensions
                if (preg_match('/\.(jpg|jpeg|png|gif|webp|svg|ico|avif)(\?|$)/i', $clean)) {
                    $urls[] = $clean;
                }
            }
        }

        // Placeholder images from Phase13Seeder
        $urls[] = 'https://placehold.co/600x400?text=Campus+1';
        $urls[] = 'https://placehold.co/600x400?text=Campus+2';
        $urls[] = 'https://placehold.co/600x400?text=Campus+3';
        $urls[] = 'https://placehold.co/600x400?text=Campus+4';
        $urls[] = 'https://placehold.co/1200x500?text=Welcome';
        $urls[] = 'https://placehold.co/1200x500?text=Admissions+Open';

        // Extract URLs from already-seeded pages (if ImportedPagesSeeder ran)
        foreach ($this->getSeededPageHtml() as $html) {
            preg_match_all('/https?:\/\/[^\s"\'<>]+/i', $html, $matches);
            foreach ($matches[0] as $match) {
                $clean = rtrim($match, ')"\'');
                if (preg_match('/\.(jpg|jpeg|png|gif|webp|svg|ico|avif)(\?|$)/i', $clean)) {
                    $urls[] = $clean;
                }
            }
        }

        return array_values(array_unique(array_filter($urls)));
    }

    protected function getSeededPageHtml(): array
    {
        return Page::all()->map(function (Page $page) {
            $html = '';
            foreach ($page->sections ?? [] as $section) {
                foreach ($section['rows'] ?? [] as $row) {
                    foreach ($row['columns'] ?? [] as $col) {
                        foreach ($col['widgets'] ?? [] as $widget) {
                            if (($widget['type'] ?? '') === 'html') {
                                $html .= $widget['settings']['html'] ?? '';
                            }
                        }
                    }
                }
            }
            return $html;
        })->filter()->values()->toArray();
    }

    protected function rewriteSettings(array $mapping): void
    {
        $old = array_keys($mapping);
        $new = array_map(fn (string $p) => Storage::disk('public')->url($p), $mapping);

        foreach (\App\Models\Setting::all() as $setting) {
            $replaced = str_replace($old, $new, (string) $setting->value);
            if ($replaced !== (string) $setting->value) {
                $setting->value = $replaced;
                $setting->save();
            }
        }
    }

    protected function rewritePages(array $mapping): void
    {
        if (empty($mapping)) return;

        $old = array_keys($mapping);
        $new = array_map(fn (string $p) => Storage::disk('public')->url($p), $mapping);

        foreach (Page::all() as $page) {
            $sections = $page->sections ?? [];
            $dirty = false;
            foreach ($sections as $si => &$section) {
                foreach ($section['rows'] ?? [] as $ri => &$row) {
                    foreach ($row['columns'] ?? [] as $ci => &$col) {
                        foreach ($col['widgets'] ?? [] as $wi => &$widget) {
                            if (($widget['type'] ?? '') === 'html') {
                                $original = $widget['settings']['html'] ?? '';
                                $updated = str_replace($old, $new, $original);
                                if ($updated !== $original) {
                                    $widget['settings']['html'] = $updated;
                                    $dirty = true;
                                }
                            }
                        }
                    }
                }
            }

            if ($dirty) {
                $page->sections = $sections;
                $page->save();
            }
        }
    }

    protected function rewritePageSeo(array $mapping): void
    {
        if (empty($mapping)) return;

        $old = array_keys($mapping);
        $new = array_map(fn (string $p) => Storage::disk('public')->url($p), $mapping);

        foreach (Page::all() as $page) {
            $seo = $page->seo ?? [];
            if (empty($seo)) continue;

            $dirty = false;
            foreach (['og_image', 'twitter_image'] as $field) {
                if (! empty($seo[$field])) {
                    $replaced = str_replace($old, $new, $seo[$field]);
                    if ($replaced !== $seo[$field]) {
                        $seo[$field] = $replaced;
                        $dirty = true;
                    }
                }
            }

            if ($dirty) {
                $page->seo = $seo;
                $page->save();
            }
        }
    }
}
