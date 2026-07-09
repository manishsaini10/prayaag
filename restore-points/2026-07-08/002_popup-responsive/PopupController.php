<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Popup\Popup;
use App\Models\Popup\PopupCategory;
use App\Models\Popup\PopupTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PopupController extends Controller
{
    public function index()
    {
        $popups = Popup::with('category')->orderBy('created_at', 'desc')->paginate(20);
        $stats = [
            'total' => Popup::count(),
            'active' => Popup::where('status', 'active')->count(),
            'draft' => Popup::where('status', 'draft')->count(),
            'views' => Popup::sum('view_count'),
        ];
        return view('admin.popup-builder.index', compact('popups', 'stats'));
    }

    public function create()
    {
        $categories = PopupCategory::where('is_active', true)->get();
        $templates = PopupTemplate::where('is_active', true)->get();
        return view('admin.popup-builder.form', compact('categories', 'templates'));
    }

    public function store(Request $request)
    {
        $data = $this->validatePopup($request);
        $data['created_by'] = Auth::id();
        $popup = Popup::create($data);

        return redirect('/admin/popup-builder/' . $popup->id . '/edit')
            ->with('success', 'Popup created successfully.');
    }

    public function edit($id)
    {
        $popup = Popup::with(['category', 'rules'])->findOrFail($id);
        $categories = PopupCategory::where('is_active', true)->get();
        $templates = PopupTemplate::where('is_active', true)->get();
        return view('admin.popup-builder.form', compact('popup', 'categories', 'templates'));
    }

    public function update(Request $request, $id)
    {
        $popup = Popup::findOrFail($id);
        $data = $this->validatePopup($request, $popup);
        $data['updated_by'] = Auth::id();
        $popup->update($data);

        return redirect('/admin/popup-builder/' . $popup->id . '/edit')
            ->with('success', 'Popup updated successfully.');
    }

    public function duplicate($id)
    {
        $original = Popup::findOrFail($id);
        $copy = $original->replicate();
        $copy->title = $original->title . ' (Copy)';
        $copy->status = 'draft';
        $copy->view_count = 0;
        $copy->impression_count = 0;
        $copy->click_count = 0;
        $copy->conversion_count = 0;
        $copy->created_by = Auth::id();
        $copy->save();

        return redirect('/admin/popup-builder/' . $copy->id . '/edit')
            ->with('success', 'Popup duplicated successfully.');
    }

    public function publish($id)
    {
        $popup = Popup::findOrFail($id);
        $popup->update(['status' => 'active', 'updated_by' => Auth::id()]);
        return back()->with('success', 'Popup published successfully.');
    }

    public function unpublish($id)
    {
        $popup = Popup::findOrFail($id);
        $popup->update(['status' => 'draft', 'updated_by' => Auth::id()]);
        return back()->with('success', 'Popup unpublished.');
    }

    public function destroy($id)
    {
        $popup = Popup::findOrFail($id);
        $popup->delete();
        return redirect('/admin/popup-builder')->with('success', 'Popup deleted.');
    }

    public function analytics($id)
    {
        $popup = Popup::with('analytics')->findOrFail($id);
        return view('admin.popup-builder.analytics', compact('popup'));
    }

    public function leads($id)
    {
        $popup = Popup::with('leads')->findOrFail($id);
        return view('admin.popup-builder.leads', compact('popup'));
    }

    public function preview(Request $request, $id)
    {
        $popup = Popup::findOrFail($id);
        $settings = $popup->settings ?? [];
        $design = $popup->design ?? [];
        return view('admin.popup-builder.preview', compact('popup', 'settings', 'design'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,webp,svg,mp4,webm|max:10240',
            'type' => 'nullable|string|in:image,video',
        ]);

        $file = $request->file('file');
        $type = $request->input('type', 'image');
        $folder = $type === 'video' ? 'popup-videos' : 'popup-images';
        $path = $file->store($folder, 'public');

        return response()->json([
            'url' => Storage::url($path),
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
            'type' => $type,
        ]);
    }

    private function validatePopup(Request $request, ?Popup $existing = null): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:modal,floating_bar,announcement_bar,slide_in,fullscreen',
            'category_id' => 'nullable|exists:popup_categories,id',
            'status' => 'required|string|in:draft,active,inactive',
            'frequency_type' => 'nullable|string|in:once_per_session,once_per_day,weekly,monthly,once_only,always',
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string',
            'structure' => 'nullable|json',
        ];

        $data = $request->validate($rules);

        // Auto-generate slug from title (only for new popups)
        if (!$existing) {
            $data['slug'] = Str::slug($data['title']) . '-' . substr(uniqid(), -6);
        }

        // Build settings array from individual fields
        $settings = $existing->settings ?? [];
        if ($request->has('settings')) {
            foreach (['animation', 'position', 'width', 'delay', 'overlay_opacity', 'z_index', 'close_button', 'overlay_close', 'esc_close', 'autoClose'] as $key) {
                if ($request->has("settings.$key")) {
                    $settings[$key] = $request->input("settings.$key");
                }
            }
        }
        $data['settings'] = $settings;

        // Build design array from individual fields
        $design = $existing->design ?? [];
        if ($request->has('design')) {
            foreach (['background', 'borderRadius', 'boxShadow', 'backdropBlur'] as $key) {
                if ($request->has("design.$key")) {
                    $design[$key] = $request->input("design.$key");
                }
            }
        }
        $data['design'] = $design;

        // Parse structure JSON if sent as string
        if (isset($data['structure']) && is_string($data['structure'])) {
            $data['structure'] = json_decode($data['structure'], true) ?? [];
        }

        // Store content blocks in structure
        if ($request->has('blocks')) {
            $blocks = $request->input('blocks');
            $data['structure'] = [
                'blocks' => is_string($blocks) ? json_decode($blocks, true) : $blocks,
            ];
        }

        return $data;
    }
}
