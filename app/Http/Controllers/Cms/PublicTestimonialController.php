<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Core\Settings\Settings;
use App\Core\Theme\ThemeRenderer;
use App\Services\SpamFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PublicTestimonialController extends Controller
{
    protected $spamFilter;

    public function __construct(SpamFilterService $spamFilter)
    {
        $this->spamFilter = $spamFilter;
    }

    public function store(Request $request)
    {
        // Fetch Settings
        $autoApprove = Settings::get('testimonial_auto_approve', false);
        $requireImage = Settings::get('testimonial_require_image', false);
        $enableRating = Settings::get('testimonial_enable_rating', true);
        $maxChars = Settings::get('testimonial_max_chars', 500);
        $minChars = Settings::get('testimonial_min_chars', 50);

        // Validation rules
        $rules = [
            'name'         => 'required|string|max:255',
            'phone'        => 'required|string|max:50',
            'student_name' => 'nullable|string|max:255',
            'class'        => 'nullable|string|max:255',
            'relation'     => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:255',
            'title'        => 'nullable|string|max:255',
            'testimonial'  => "required|string|min:{$minChars}|max:{$maxChars}",
            'photo'        => ($requireImage ? 'required' : 'nullable') . '|image|max:2048|mimes:jpeg,png,jpg,webp',
        ];

        if ($enableRating) {
            $rules['rating'] = 'required|integer|min:1|max:5';
        }

        // Custom validation messages
        $messages = [
            'testimonial.min' => "Your testimonial must be at least {$minChars} characters.",
            'testimonial.max' => "Your testimonial cannot exceed {$maxChars} characters.",
            'photo.required'  => "A photo upload is required.",
        ];

        if ($request->wantsJson()) {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()->all()
                ], 422);
            }
            $data = $validator->validated();
        } else {
            $data = $request->validate($rules, $messages);
        }

        // Spam Profanity Check
        if ($this->spamFilter->hasProfanity($data['testimonial'] ?? '')) {
            $errorMsg = 'Your testimonial contains blocked keywords and cannot be submitted.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => [$errorMsg]], 422);
            }
            return back()->withInput()->withErrors(['testimonial' => $errorMsg]);
        }

        // Duplicate Check
        $ip = $request->ip() ?? '127.0.0.1';
        if ($this->spamFilter->isDuplicate($data['name'], $ip, $data['testimonial'])) {
            $errorMsg = 'You have already submitted this testimonial.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => [$errorMsg]], 422);
            }
            return back()->withInput()->withErrors(['testimonial' => $errorMsg]);
        }

        // Process Upload Photo (Square crop resizing using native GD)
        $imagePath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = uniqid('parent_') . '.' . $file->getClientOriginalExtension();
            $isTesting = app()->environment('testing');
            $subFolder = $isTesting ? 'uploads/testimonials_test' : 'uploads/testimonials';
            $destDir = public_path($subFolder);

            if (!file_exists($destDir)) {
                mkdir($destDir, 0755, true);
            }

            $mainPath = $destDir . '/main_' . $filename;
            $thumbPath = $destDir . '/thumb_' . $filename;

            // Crop/Resize primary 800x800 and thumb 250x250
            $this->resizeAndCropGD($file->getPathname(), $mainPath, 800, $file->getClientOriginalExtension());
            $this->resizeAndCropGD($file->getPathname(), $thumbPath, 250, $file->getClientOriginalExtension());

            $imagePath = $subFolder . '/main_' . $filename;
        }

        // Save Testimonial
        $status = $autoApprove ? 'approved' : 'pending';
        $testimonial = Testimonial::create([
            'name'             => $data['name'],
            'phone'            => $data['phone'],
            'student_name'     => $data['student_name'] ?? null,
            'class'            => $data['class'] ?? null,
            'relation'         => $data['relation'] ?? null,
            'email'            => $data['email'] ?? null,
            'title'            => $data['title'] ?? null,
            'testimonial'      => $data['testimonial'],
            'rating'           => $data['rating'] ?? 5,
            'image'            => $imagePath,
            'status'           => $status,
            'featured'         => false,
            'display_location' => ['home', 'testimonials'],
            'approved_at'      => $autoApprove ? now() : null,
            'ip_address'       => $ip,
            'browser'          => $request->userAgent(),
        ]);

        // Create Admin Notification Alert if pending moderation
        if ($status === 'pending') {
            try {
                \App\Models\AdminNotification::record('testimonial', "New testimonial from {$testimonial->name} is pending moderation", [
                    'level' => 'info',
                    'url'   => '/admin/testimonials-console?status=pending',
                    'icon'  => 'star',
                ]);
            } catch (\Throwable $e) {
                Log::error("Failed to record admin notification for pending testimonial: " . $e->getMessage());
            }
        }

        // Send Queued Email Alert to Admin
        try {
            $adminEmail = config('mail.from.address', 'admin@prayaag.in');
            $subject = 'New Testimonial Received';
            $body = "A new parent testimonial has been submitted by {$testimonial->name}.\n\nSnippet:\n\"" . substr($testimonial->testimonial, 0, 150) . "...\"\n\nPlease review and approve this testimonial in the admin panel.";
            Mail::to($adminEmail)->queue(new \App\Notifications\MailNotification($subject, $body, url('/admin/testimonials-console?status=pending'), 'Review Testimonial'));
        } catch (\Throwable $e) {
            Log::error("Failed to send admin submission notification email: " . $e->getMessage());
        }

        $successMsg = $autoApprove 
            ? 'Thank you! Your testimonial has been published successfully.' 
            : 'Thank you! Your experience has been submitted successfully for moderation.';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'testimonial' => $testimonial
            ]);
        }

        return back()->with('success', $successMsg);
    }

    public function index(Request $request, ThemeRenderer $theme)
    {
        $rating = $request->get('rating');
        $class = $request->get('class');
        $featured = $request->get('featured');
        $search = $request->get('q');

        $query = Testimonial::published();

        if ($rating) {
            $query->where('rating', (int)$rating);
        }

        if ($class) {
            $query->where('class', 'like', "%{$class}%");
        }

        if ($featured) {
            $query->where('featured', true);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_name', 'like', "%{$search}%")
                  ->orWhere('testimonial', 'like', "%{$search}%");
            });
        }

        $testimonials = $query->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Fetch meta details for SEO schema aggregate calculations
        $ratingStats = [
            'count' => Testimonial::published()->count(),
            'avg'   => round(Testimonial::published()->avg('rating') ?? 5.0, 1),
        ];

        $content = view('themes.school.testimonials', compact('testimonials', 'ratingStats', 'search', 'rating', 'class'))->render();

        return view('themes.school.layout', [
            'title'        => 'Parent Testimonials',
            'siteName'     => Settings::get('site_name', 'Prayaag International School'),
            'content'      => $content,
            'header'       => $theme->header(),
            'footer'       => $theme->footer(),
            'themeHead'    => $theme->themeHead(),
            'primaryColor' => Settings::get('theme_primary_color', '#0b2545'),
            'seo'          => [
                'title' => 'Parents Testimonials | Prayaag International School',
                'description' => 'Read what our parents say about the academic excellence, student support, and infrastructure at Prayaag International School.',
            ]
        ]);
    }

    public function showForm(Request $request, ThemeRenderer $theme)
    {
        $content = view('themes.school.post-testimonial')->render();

        return view('themes.school.layout', [
            'title'        => 'Post Your Testimonial',
            'siteName'     => Settings::get('site_name', 'Prayaag International School'),
            'content'      => $content,
            'header'       => $theme->header(),
            'footer'       => $theme->footer(),
            'themeHead'    => $theme->themeHead(),
            'primaryColor' => Settings::get('theme_primary_color', '#0b2545'),
            'seo'          => [
                'title' => 'Post Your Testimonial | Prayaag International School',
                'description' => 'Share your feedback and experience with the Prayaag International School community.',
            ]
        ]);
    }

    public function apiList()
    {
        $testimonials = Testimonial::published()
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($testimonials);
    }

    public function apiFeatured()
    {
        $testimonials = \Illuminate\Support\Facades\Cache::remember('testimonials.featured', 3600, function () {
            return Testimonial::published()
                ->featured()
                ->forLocation('home')
                ->orderBy('sort_order', 'asc')
                ->get();
        });

        return response()->json($testimonials);
    }

    /**
     * Native GD Image Resizing & Aspect Ratio Square Crop Helper
     */
    protected function resizeAndCropGD(string $srcPath, string $dstPath, int $size, string $ext): bool
    {
        $ext = strtolower($ext);
        $src = match($ext) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($srcPath),
            'png' => @imagecreatefrompng($srcPath),
            'webp' => @imagecreatefromwebp($srcPath),
            default => null,
        };

        if (!$src) {
            return false;
        }

        $width = imagesx($src);
        $height = imagesy($src);

        // Calculate source offsets for square crop
        $min = min($width, $height);
        $srcX = (int) (($width - $min) / 2);
        $srcY = (int) (($height - $min) / 2);

        $dst = imagecreatetruecolor($size, $size);

        // Preserve transparency for PNG and WebP
        if ($ext === 'png' || $ext === 'webp') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        }

        imagecopyresampled(
            $dst, $src,
            0, 0, $srcX, $srcY,
            $size, $size, $min, $min
        );

        $success = match($ext) {
            'jpg', 'jpeg' => imagejpeg($dst, $dstPath, 90),
            'png' => imagepng($dst, $dstPath),
            'webp' => imagewebp($dst, $dstPath, 90),
            default => false,
        };

        imagedestroy($src);
        imagedestroy($dst);

        return $success;
    }
}
