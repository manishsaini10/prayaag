<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Upload Center. Accepts one or many files, stores them on the public disk,
 * and records a Media row per file (with image dimensions where applicable).
 * Files become web-accessible once `php artisan storage:link` has been run.
 */
class UploadController extends Controller
{
    public function index(): View
    {
        $recent = Media::latest()->limit(24)->get();

        return view('admin.upload.index', compact('recent'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'files'   => 'required|array',
            'files.*' => 'file|max:20480', // 20 MB each
        ]);

        $count = 0;
        foreach ($request->file('files', []) as $file) {
            $ext = strtolower($file->getClientOriginalExtension());
            $filename = (string) Str::ulid() . ($ext ? '.' . $ext : '');
            $path = $file->storeAs('uploads', $filename, 'public');

            $width = $height = null;
            if (str_starts_with((string) $file->getMimeType(), 'image/')) {
                $info = rescue(fn () => getimagesize(Storage::disk('public')->path($path)), null, false);
                if (is_array($info)) {
                    $width = $info[0] ?? null;
                    $height = $info[1] ?? null;
                }
            }

            Media::create([
                'disk'          => 'public',
                'path'          => $path,
                'filename'      => $filename,
                'original_name' => $file->getClientOriginalName(),
                'mime_type'     => $file->getMimeType(),
                'extension'     => $ext ?: null,
                'size'          => $file->getSize(),
                'width'         => $width,
                'height'        => $height,
            ]);
            $count++;
        }

        return back()->with('status', $count . ' ' . Str::plural('file', $count) . ' uploaded to the media library.');
    }
}
