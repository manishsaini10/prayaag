<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Core\Settings\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdminTestimonialController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $featured = $request->get('featured');
        $search = $request->get('q');

        $query = Testimonial::query();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($featured !== null) {
            $query->where('featured', filter_var($featured, FILTER_VALIDATE_BOOLEAN));
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_name', 'like', "%{$search}%")
                  ->orWhere('testimonial', 'like', "%{$search}%");
            });
        }

        $testimonials = $query->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Stats calculation
        $stats = [
            'total' => Testimonial::count(),
            'pending' => Testimonial::where('status', 'pending')->count(),
            'approved' => Testimonial::where('status', 'approved')->count(),
            'rejected' => Testimonial::where('status', 'rejected')->count(),
            'archived' => Testimonial::where('status', 'archived')->count(),
            'featured' => Testimonial::where('featured', true)->count(),
        ];

        return view('admin.testimonials.index', compact('testimonials', 'stats', 'status', 'search', 'featured'));
    }

    public function view($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        return response()->json($testimonial);
    }

    public function approve($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->status = 'approved';
        $testimonial->approved_by = auth()->id();
        $testimonial->approved_at = now();
        $testimonial->save();

        // Send Email notification to parent if email exists
        if (!empty($testimonial->email)) {
            try {
                Mail::html("<p>Dear {$testimonial->name},</p><p>We are pleased to inform you that your testimonial has been approved and published on our website.</p><p>Thank you for sharing your experience with Prayaag International School.</p><p>Best regards,<br>Prayaag Team</p>", function ($message) use ($testimonial) {
                    $message->to($testimonial->email)
                            ->subject('Your Testimonial Has Been Approved!');
                });
            } catch (\Throwable $e) {
                Log::error("Failed to send approval email: " . $e->getMessage());
            }
        }

        return back()->with('success', 'Testimonial approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->status = 'rejected';
        if ($request->filled('rejection_reason')) {
            $testimonial->rejection_reason = $request->input('rejection_reason');
        }
        $testimonial->save();

        // Send Email notification to parent if email exists
        if (!empty($testimonial->email)) {
            try {
                Mail::html("<p>Dear {$testimonial->name},</p><p>Thank you for submitting your testimonial. Unfortunately, we were unable to publish your testimonial at this time.</p><p>We appreciate your feedback and support.</p><p>Best regards,<br>Prayaag Team</p>", function ($message) use ($testimonial) {
                    $message->to($testimonial->email)
                            ->subject('Update regarding your testimonial');
                });
            } catch (\Throwable $e) {
                Log::error("Failed to send rejection email: " . $e->getMessage());
            }
        }

        return back()->with('success', 'Testimonial rejected successfully.');
    }

    public function toggleFeatured($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->featured = !$testimonial->featured;
        $testimonial->save();

        $msg = $testimonial->featured ? 'Testimonial featured successfully.' : 'Testimonial unfeatured successfully.';
        return back()->with('success', $msg);
    }

    public function edit($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, $id)
    {
        $testimonial = Testimonial::findOrFail($id);

        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'student_name'     => 'nullable|string|max:255',
            'class'            => 'nullable|string|max:255',
            'relation'         => 'nullable|string|max:255',
            'phone'            => 'required|string|max:50',
            'email'            => 'nullable|email|max:255',
            'title'            => 'nullable|string|max:255',
            'testimonial'      => 'required|string|max:5000',
            'rating'           => 'required|integer|min:1|max:5',
            'status'           => 'required|string|in:pending,approved,rejected,archived',
            'featured'         => 'nullable|boolean',
            'display_location' => 'nullable|array',
            'image'            => 'nullable|string|max:2048',
            'sort_order'       => 'nullable|integer',
        ]);

        $data['featured'] = $request->has('featured');
        $data['display_location'] = $request->get('display_location', []);

        if ($request->has('is_verified')) {
            $data['is_verified'] = $request->boolean('is_verified');
        }

        if ($data['status'] === 'rejected' && $request->filled('rejection_reason')) {
            $data['rejection_reason'] = $request->input('rejection_reason');
        }

        $testimonial->update($data);

        return redirect()->route('admin.testimonials-console.index')->with('success', 'Testimonial updated successfully.');
    }

    public function toggleVerified($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->is_verified = !$testimonial->is_verified;
        $testimonial->save();

        $msg = $testimonial->is_verified ? 'Testimonial verified successfully.' : 'Testimonial verification removed.';
        return back()->with('success', $msg);
    }

    public function export()
    {
        $testimonials = Testimonial::orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="testimonials-export-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($testimonials) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Phone', 'Email', 'Student Name', 'Class', 'Relation',
                'Rating', 'Testimonial', 'Status', 'Featured', 'Verified',
                'Display Locations', 'Created At']);

            foreach ($testimonials as $t) {
                fputcsv($file, [
                    $t->id,
                    $t->name,
                    $t->phone,
                    $t->email,
                    $t->student_name,
                    $t->class,
                    $t->relation,
                    $t->rating,
                    $t->testimonial,
                    $t->status,
                    $t->featured ? 'Yes' : 'No',
                    $t->is_verified ? 'Yes' : 'No',
                    is_array($t->display_location) ? implode(', ', $t->display_location) : '',
                    $t->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $file = fopen($path, 'r');
        $header = fgetcsv($file);

        $imported = 0;
        $errors = [];

        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);

            try {
                $validator = Validator::make($data, [
                    'name' => 'required|string|max:255',
                    'testimonial' => 'required|string',
                    'rating' => 'nullable|integer|min:1|max:5',
                    'status' => 'nullable|in:pending,approved,rejected,archived',
                ]);

                if ($validator->fails()) {
                    $errors[] = 'Row ' . ($imported + 2) . ': ' . implode(', ', $validator->errors()->all());
                    continue;
                }

                Testimonial::create([
                    'name'             => $data['name'],
                    'phone'            => $data['phone'] ?? null,
                    'email'            => $data['email'] ?? null,
                    'student_name'     => $data['student_name'] ?? null,
                    'class'            => $data['class'] ?? null,
                    'relation'         => $data['relation'] ?? null,
                    'testimonial'      => $data['testimonial'],
                    'rating'           => (int) ($data['rating'] ?? 5),
                    'status'           => $data['status'] ?? 'pending',
                    'featured'         => filter_var($data['featured'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'is_verified'      => filter_var($data['verified'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'display_location' => isset($data['display_locations']) ? explode(',', $data['display_locations']) : ['home', 'testimonials'],
                    'ip_address'       => request()->ip(),
                ]);

                $imported++;
            } catch (\Throwable $e) {
                $errors[] = 'Row ' . ($imported + 2) . ': ' . $e->getMessage();
            }
        }

        fclose($file);

        $message = "Imported {$imported} testimonials successfully.";
        if (!empty($errors)) {
            $message .= ' Errors: ' . implode('; ', array_slice($errors, 0, 5));
        }

        return back()->with('success', $message);
    }

    public function duplicate($id)
    {
        $original = Testimonial::findOrFail($id);
        $clone = $original->replicate();
        $clone->name = $original->name . ' (Copy)';
        $clone->status = 'pending';
        $clone->featured = false;
        $clone->sort_order = (int) (Testimonial::max('sort_order') ?? 0) + 1;
        $clone->save();

        return back()->with('success', 'Testimonial duplicated successfully.');
    }

    public function destroy($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->delete();

        return back()->with('success', 'Testimonial deleted successfully.');
    }

    public function bulkAction(Request $request)
    {
        $action = $request->get('action');
        $ids = $request->get('ids', []);

        if (empty($ids)) {
            return back()->with('error', 'No testimonials selected.');
        }

        $testimonials = Testimonial::whereIn('id', $ids)->get();

        foreach ($testimonials as $t) {
            if ($action === 'approve') {
                $t->status = 'approved';
                $t->approved_by = auth()->id();
                $t->approved_at = now();
                $t->save();
            } elseif ($action === 'reject') {
                $t->status = 'rejected';
                $t->save();
            } elseif ($action === 'feature') {
                $t->featured = true;
                $t->save();
            } elseif ($action === 'unfeature') {
                $t->featured = false;
                $t->save();
            } elseif ($action === 'delete') {
                $t->delete();
            }
        }

        return back()->with('success', 'Bulk action completed successfully.');
    }

    public function settings()
    {
        $config = [
            'auto_approve' => Settings::get('testimonial_auto_approve', false),
            'require_image' => Settings::get('testimonial_require_image', false),
            'enable_rating' => Settings::get('testimonial_enable_rating', true),
            'max_chars' => Settings::get('testimonial_max_chars', 500),
            'min_chars' => Settings::get('testimonial_min_chars', 50),
            'blocked_words' => Settings::get('testimonial_blocked_words', 'abuse,fake,spam,cheat,worst,bad,worstschool,fraud'),
            'display_style' => Settings::get('testimonial_display_style', 'slider'),
            'display_limit' => Settings::get('testimonial_display_limit', 6),
            'slider_autoplay_interval' => Settings::get('testimonial_slider_autoplay_interval', 5),
        ];

        return view('admin.testimonials.settings', compact('config'));
    }

    public function updateSettings(Request $request)
    {
        Settings::set('testimonial_auto_approve', $request->has('auto_approve'), 'boolean');
        Settings::set('testimonial_require_image', $request->has('require_image'), 'boolean');
        Settings::set('testimonial_enable_rating', $request->has('enable_rating'), 'boolean');
        Settings::set('testimonial_max_chars', (int) $request->get('max_chars', 500), 'integer');
        Settings::set('testimonial_min_chars', (int) $request->get('min_chars', 50), 'integer');
        Settings::set('testimonial_blocked_words', (string) $request->get('blocked_words', ''), 'string');
        Settings::set('testimonial_display_style', (string) $request->get('display_style', 'slider'), 'string');
        Settings::set('testimonial_display_limit', (int) $request->get('display_limit', 6), 'integer');
        Settings::set('testimonial_slider_autoplay_interval', (int) $request->get('slider_autoplay_interval', 5), 'integer');

        return back()->with('success', 'Settings updated successfully.');
    }
}
