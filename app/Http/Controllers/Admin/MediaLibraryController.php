<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\ImageOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaLibraryController extends Controller
{
    public function index(Request $request)
    {
        $query = Media::query()->orderBy('created_at', 'desc');

        if ($q = $request->input('q')) {
            $query->search($q);
        }

        $items = $query->paginate(36);
        $total = Media::count();

        return view('admin.media.index', [
            'items' => $items,
            'total' => $total,
            'q'     => $request->input('q', ''),
        ]);
    }

    public function loadMore(Request $request)
    {
        $query = Media::query()->orderBy('created_at', 'desc');

        if ($q = $request->input('q')) {
            $query->search($q);
        }

        $page = max((int) $request->input('page', 2), 2);
        $items = $query->paginate(36, ['*'], 'page', $page);

        return response()->json([
            'html' => [
                'grid'  => view('admin.media._items', ['items' => $items, 'mode' => 'grid'])->render(),
                'list'  => view('admin.media._items', ['items' => $items, 'mode' => 'list'])->render(),
                'large' => view('admin.media._items', ['items' => $items, 'mode' => 'large'])->render(),
            ],
            'hasMore' => $items->hasMorePages(),
            'nextPage'=> $items->currentPage() + 1,
        ]);
    }

    /**
     * API: List media items with pagination and filters.
     */
    public function apiList(Request $request)
    {
        $query = Media::query();

        // 1. Search Query
        if ($q = $request->input('q')) {
            $query->search($q);
        }

        // 2. Filter by Type
        if ($type = $request->input('type')) {
            if ($type === 'image') {
                $query->where('mime_type', 'like', 'image/%');
            } elseif ($type === 'video') {
                $query->where('mime_type', 'like', 'video/%');
            } elseif ($type === 'document') {
                $query->where(function($q) {
                    $q->where('mime_type', 'like', 'application/%')
                      ->orWhere('mime_type', 'like', 'text/%');
                });
            }
        }

        // 3. Filter by Size
        if ($sizeFilter = $request->input('size')) {
            if ($sizeFilter === 'small') {
                $query->where('size', '<', 102400); // < 100 KB
            } elseif ($sizeFilter === 'medium') {
                $query->whereBetween('size', [102400, 1048576]); // 100 KB - 1 MB
            } elseif ($sizeFilter === 'large') {
                $query->where('size', '>', 1048576); // > 1 MB
            }
        }

        // 4. Sorting
        $sort = $request->input('sort', 'date_desc');
        switch ($sort) {
            case 'date_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('original_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('original_name', 'desc');
                break;
            case 'size_desc':
                $query->orderBy('size', 'desc');
                break;
            case 'date_desc':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $paginated = $query->paginate(28);

        return response()->json([
            'items' => $paginated->items(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'has_more' => $paginated->hasMorePages(),
            'total' => $paginated->total(),
            'max_upload_size' => $this->getReadableMaxUploadSize(),
            'max_upload_size_bytes' => $this->getUploadMaxFilesizeInBytes(),
        ]);
    }

    /**
     * API: Upload multiple files.
     */
    public function apiUpload(Request $request)
    {
        $maxBytes = $this->getUploadMaxFilesizeInBytes();
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|max:' . ($maxBytes / 1024),
        ]);

        $uploaded = [];

        foreach ($request->file('files', []) as $file) {
            $ext = strtolower($file->getClientOriginalExtension());
            $filename = (string) Str::ulid() . ($ext ? '.' . $ext : '');
            $path = $file->storeAs('uploads', $filename, 'public');

            $width = $height = null;
            $mime = $file->getMimeType();

            if (str_starts_with((string) $mime, 'image/')) {
                $info = rescue(fn () => getimagesize(Storage::disk('public')->path($path)), null, false);
                if (is_array($info)) {
                    $width = $info[0] ?? null;
                    $height = $info[1] ?? null;
                }

                // Generate optimized sizes
                ImageOptimizerService::generateSizes($path, $mime, 'public');
            }

            $media = Media::create([
                'disk' => 'public',
                'path' => $path,
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $mime,
                'extension' => $ext ?: null,
                'size' => $file->getSize(),
                'width' => $width,
                'height' => $height,
            ]);

            $uploaded[] = $media;
        }

        return response()->json([
            'success' => true,
            'media' => $uploaded,
        ]);
    }

    /**
     * API: Update media metadata.
     */
    public function apiUpdate(Request $request, $id)
    {
        $media = Media::findOrFail($id);

        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'alt' => 'nullable|string|max:255',
            'caption' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $media->update($data);

        return response()->json([
            'success' => true,
            'media' => $media,
        ]);
    }

    /**
     * API: Check where media is used.
     */
    public function apiCheckUsage($id)
    {
        $media = Media::findOrFail($id);
        return response()->json([
            'usage' => $this->getMediaUsage($media),
            'in_use' => !empty($this->getMediaUsage($media)),
        ]);
    }

    /**
     * API: Delete media permanently.
     */
    public function apiDestroy(Request $request, $id)
    {
        $media = Media::findOrFail($id);

        // Prevent accidental deletion if used (force parameter allows bypass)
        $usage = $this->getMediaUsage($media);
        if (!empty($usage) && !$request->input('force')) {
            return response()->json([
                'success' => false,
                'message' => 'This media is currently in use and cannot be deleted.',
                'usage' => $usage,
            ], 422);
        }

        // Delete optimized sizes
        ImageOptimizerService::deleteSizes($media->path, $media->disk ?? 'public');

        // Delete original file
        if (Storage::disk($media->disk ?? 'public')->exists($media->path)) {
            Storage::disk($media->disk ?? 'public')->delete($media->path);
        }

        // Force delete database row
        $media->forceDelete();

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * API: Replace media file binary.
     */
    public function apiReplace(Request $request, $id)
    {
        $media = Media::findOrFail($id);

        $maxBytes = $this->getUploadMaxFilesizeInBytes();
        $request->validate([
            'file' => 'required|file|max:' . ($maxBytes / 1024),
        ]);

        $file = $request->file('file');

        // Delete old resized versions
        ImageOptimizerService::deleteSizes($media->path, $media->disk ?? 'public');

        // Overwrite original file
        Storage::disk($media->disk ?? 'public')->putFileAs('uploads', $file, $media->filename);

        $width = $height = null;
        $mime = $file->getMimeType();

        if (str_starts_with((string) $mime, 'image/')) {
            $info = rescue(fn () => getimagesize(Storage::disk($media->disk ?? 'public')->path($media->path)), null, false);
            if (is_array($info)) {
                $width = $info[0] ?? null;
                $height = $info[1] ?? null;
            }

            // Generate new optimized sizes
            ImageOptimizerService::generateSizes($media->path, $mime, $media->disk ?? 'public');
        }

        $media->update([
            'mime_type' => $mime,
            'size' => $file->getSize(),
            'width' => $width,
            'height' => $height,
        ]);

        return response()->json([
            'success' => true,
            'media' => $media,
        ]);
    }

    /**
     * Helper: Check references across all standard schemas.
     */
    private function getMediaUsage(Media $media): array
    {
        $usages = [];
        $filename = $media->filename;

        // 1. Check in Popups (JSON fields: structure, design)
        $popups = \App\Models\Popup\Popup::where('structure', 'like', "%{$filename}%")
            ->orWhere('design', 'like', "%{$filename}%")
            ->get(['id', 'title']);
        if ($popups->isNotEmpty()) {
            $usages['popups'] = $popups->map(fn($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'edit_url' => url("/admin/popup-builder/{$p->id}/edit"),
            ])->toArray();
        }

        // 2. Check in Posts (featured_image, body)
        $posts = \App\Models\Post::where('featured_image', 'like', "%{$filename}%")
            ->orWhere('body', 'like', "%{$filename}%")
            ->get(['id', 'title']);
        if ($posts->isNotEmpty()) {
            $usages['posts'] = $posts->map(fn($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'edit_url' => url("/admin/m/posts/{$p->id}/edit"),
            ])->toArray();
        }

        // 3. Check in PageWidget JSON settings
        $pageWidgets = \App\Models\PageWidget::where('settings', 'like', "%{$filename}%")->get();
        if ($pageWidgets->isNotEmpty()) {
            $pageIds = $pageWidgets->map(fn($w) => $w->column?->row?->section?->page_id)->filter()->unique();
            if ($pageIds->isNotEmpty()) {
                $pages = \App\Models\Page::whereIn('id', $pageIds)->get(['id', 'title']);
                if ($pages->isNotEmpty()) {
                    $usages['pages'] = $pages->map(fn($p) => [
                        'id' => $p->id,
                        'title' => $p->title,
                        'edit_url' => url("/admin/pages/{$p->id}/edit"),
                    ])->toArray();
                }
            }
        }

        // 4. Check in Galleries (GalleryImage)
        $galleryImages = \App\Models\GalleryImage::where('image', 'like', "%{$filename}%")->with('gallery')->get();
        if ($galleryImages->isNotEmpty()) {
            $usages['galleries'] = $galleryImages->map(fn($gi) => [
                'id' => $gi->gallery?->id,
                'title' => $gi->gallery?->title ?? 'Untitled Gallery',
                'edit_url' => $gi->gallery ? url("/admin/m/galleries/{$gi->gallery->id}/edit") : '#',
            ])->unique('id')->toArray();
        }

        // 5. Check in Sliders (Slide)
        $slides = \App\Models\Slide::where('image', 'like', "%{$filename}%")->with('slider')->get();
        if ($slides->isNotEmpty()) {
            $usages['sliders'] = $slides->map(fn($s) => [
                'id' => $s->slider?->id,
                'title' => $s->slider?->title ?? 'Untitled Slider',
                'edit_url' => $s->slider ? url("/admin/m/sliders/{$s->slider->id}/edit") : '#',
            ])->unique('id')->toArray();
        }

        // 6. Check in Testimonials
        $testimonials = \App\Models\Testimonial::where('photo', 'like', "%{$filename}%")->get(['id', 'author']);
        if ($testimonials->isNotEmpty()) {
            $usages['testimonials'] = $testimonials->map(fn($t) => [
                'id' => $t->id,
                'title' => "Testimonial by {$t->author}",
                'edit_url' => url("/admin/m/testimonials/{$t->id}/edit"),
            ])->toArray();
        }

        // 7. Check in Downloads
        $downloads = \App\Models\Download::where('file', 'like', "%{$filename}%")->get(['id', 'title']);
        if ($downloads->isNotEmpty()) {
            $usages['downloads'] = $downloads->map(fn($d) => [
                'id' => $d->id,
                'title' => "Download file: {$d->title}",
                'edit_url' => url("/admin/m/downloads/{$d->id}/edit"),
            ])->toArray();
        }

        return $usages;
    }

    /**
     * Size calculations
     */
    private function getReadableMaxUploadSize(): string
    {
        $maxSize = $this->getUploadMaxFilesizeInBytes();
        return $this->formatBytes($maxSize);
    }

    private function getUploadMaxFilesizeInBytes(): int
    {
        $val = trim(ini_get('upload_max_filesize'));
        $last = strtolower($val[strlen($val)-1] ?? '');
        $val = (int)$val;
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }

    private function formatBytes(int $bytes, int $precision = 0): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
