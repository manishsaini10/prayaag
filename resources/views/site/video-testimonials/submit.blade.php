{{-- Public Video Testimonial Submission Form --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Share your experience at Prayaag International School by submitting a video testimonial.">
    <title>Submit Your Video Testimonial — Prayaag International School</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;background:#f0f4f8;color:#0f172a;min-height:100vh;padding:2rem 1rem}
        .container{max-width:640px;margin:0 auto}
        .card{background:#fff;border-radius:1.25rem;padding:2rem;box-shadow:0 4px 24px rgba(0,0,0,.08);margin-bottom:1.5rem}
        .hero{text-align:center;padding:2.5rem 2rem;background:linear-gradient(135deg,#0e2f5e,#0f172a);border-radius:1.5rem;color:#fff;margin-bottom:2rem}
        .hero-eyebrow{display:inline-block;background:rgba(199,154,59,.2);border:1px solid rgba(199,154,59,.4);color:#eda52a;font-size:.75rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:.3rem .9rem;border-radius:99px;margin-bottom:1rem}
        .hero h1{font-size:clamp(1.4rem,4vw,2rem);font-weight:800;line-height:1.2;margin-bottom:.75rem}
        .hero p{color:#94a3b8;font-size:.9rem;line-height:1.6;max-width:480px;margin:0 auto}
        label{display:block;font-size:.85rem;font-weight:600;color:#374151;margin-bottom:.4rem}
        .required{color:#dc2626}
        input,select,textarea{width:100%;padding:.6rem .85rem;border:1.5px solid #d1d5db;border-radius:.625rem;font-size:.875rem;font-family:inherit;transition:border-color .15s,box-shadow .15s;outline:none;color:#0f172a}
        input:focus,select:focus,textarea:focus{border-color:#0e2f5e;box-shadow:0 0 0 3px rgba(14,47,94,.1)}
        .form-group{margin-bottom:1.25rem}
        .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
        @media(max-width:480px){.grid-2{grid-template-columns:1fr}}
        .section-title{font-weight:700;font-size:.95rem;color:#0f172a;margin-bottom:1.25rem;padding-bottom:.5rem;border-bottom:2px solid #f1f5f9}
        .upload-zone{border:2px dashed #d1d5db;border-radius:.75rem;padding:1.5rem;text-align:center;cursor:pointer;transition:border-color .15s;background:#fafafa}
        .upload-zone:hover,.upload-zone.drag-over{border-color:#0e2f5e;background:#eff6ff}
        .tab-btns{display:flex;gap:.5rem;background:#f1f5f9;padding:.35rem;border-radius:.625rem;margin-bottom:1rem}
        .tab-btn{flex:1;padding:.5rem;font-size:.8rem;font-weight:600;border:none;background:none;cursor:pointer;border-radius:.375rem;color:#64748b;transition:all .15s}
        .tab-btn.active{background:#fff;color:#0e2f5e;box-shadow:0 1px 4px rgba(0,0,0,.1)}
        .consent-box{background:#fffbeb;border:1.5px solid #fcd34d;border-radius:.75rem;padding:1.25rem}
        .consent-check{display:flex;align-items:flex-start;gap:.75rem;margin-top:1rem;cursor:pointer}
        .consent-check input{width:18px;height:18px;margin-top:.15rem;accent-color:#0e2f5e;flex-shrink:0}
        .consent-text{font-size:.82rem;line-height:1.6;color:#78350f}
        .btn-submit{width:100%;padding:.9rem;background:linear-gradient(135deg,#0e2f5e,#1e40af);color:#fff;font-size:.95rem;font-weight:700;border:none;border-radius:.75rem;cursor:pointer;transition:opacity .15s;font-family:inherit}
        .btn-submit:hover{opacity:.9}
        .btn-submit:disabled{opacity:.5;cursor:not-allowed}
        .alert-success{background:#dcfce7;color:#166534;border:1.5px solid #86efac;border-radius:.75rem;padding:1rem 1.25rem;font-size:.875rem;margin-bottom:1.5rem}
        .alert-error{background:#fee2e2;color:#991b1b;border:1.5px solid #fca5a5;border-radius:.75rem;padding:1rem 1.25rem;font-size:.875rem;margin-bottom:1.5rem}
        .note{font-size:.75rem;color:#64748b;margin-top:.35rem}
        .hidden{display:none}
    </style>
</head>
<body>
<div class="container">

    {{-- Hero --}}
    <div class="hero">
        <div class="hero-eyebrow">📹 Share Your Story</div>
        <h1>Submit a Video Testimonial</h1>
        <p>Help other families discover Prayaag International School by sharing your experience on video. All submissions are reviewed before publishing.</p>
    </div>

    @if(session('success'))
    <div class="alert-success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert-error">❌ {{ session('error') }}</div>
    @endif
    @if($errors->any())
    <div class="alert-error">
        <ul style="list-style:disc;padding-left:1.25rem">
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('video-testimonials.submit.store') }}" enctype="multipart/form-data"
          x-data="vtSubmitForm()" @submit="submitting = true">
        @csrf

        {{-- Student Info --}}
        <div class="card">
            <div class="section-title">🎓 Student Information</div>
            <div class="grid-2">
                <div class="form-group">
                    <label>Student Name <span class="required">*</span></label>
                    <input type="text" name="student_name" value="{{ old('student_name') }}" required placeholder="Arjun Sharma">
                </div>
                <div class="form-group">
                    <label>Class / Grade</label>
                    <input type="text" name="class_grade" value="{{ old('class_grade') }}" placeholder="Grade 10 / Class XII">
                </div>
            </div>
            <div class="form-group">
                <label>Video Title <span class="required">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       placeholder="e.g. My 3 years at Prayaag — a parent's perspective">
                <p class="note">This will appear as the caption below your video on the website.</p>
            </div>
        </div>

        {{-- Parent / Submitter --}}
        <div class="card">
            <div class="section-title">👤 Your Information (Parent / Guardian)</div>
            <div class="form-group">
                <label>Your Full Name <span class="required">*</span></label>
                <input type="text" name="submitted_by_name" value="{{ old('submitted_by_name') }}" required placeholder="Mrs. Sunita Sharma">
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="submitted_by_email" value="{{ old('submitted_by_email') }}" required placeholder="you@example.com">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="submitted_by_phone" value="{{ old('submitted_by_phone') }}" placeholder="+91 98765 43210">
                </div>
            </div>
        </div>

        {{-- Video Source --}}
        <div class="card">
            <div class="section-title">🎬 Your Video</div>
            <div class="tab-btns">
                <button type="button" class="tab-btn" :class="{ active: tab === 'url' }" @click="tab = 'url'">
                    🔗 YouTube Link
                </button>
                <button type="button" class="tab-btn" :class="{ active: tab === 'file' }" @click="tab = 'file'">
                    📁 Upload File
                </button>
            </div>

            <div x-show="tab === 'url'">
                <div class="form-group">
                    <label>YouTube Video Link</label>
                    <input type="url" name="video_url" value="{{ old('video_url') }}"
                           placeholder="https://youtu.be/your-video-id  or  https://www.youtube.com/watch?v=...">
                    <p class="note">Set your YouTube video to "Unlisted" before sharing the link here.</p>
                </div>
            </div>

            <div x-show="tab === 'file'">
                <div class="form-group">
                    <label>Upload Video File</label>
                    <div class="upload-zone" @dragover.prevent="$el.classList.add('drag-over')"
                         @dragleave="$el.classList.remove('drag-over')"
                         @drop.prevent="$el.classList.remove('drag-over')"
                         @click="$refs.fileInput.click()">
                        <p style="font-size:2rem">📹</p>
                        <p style="font-weight:600;margin:.5rem 0 .25rem">Click or drag to upload</p>
                        <p class="note">MP4, MOV, or WebM · Max {{ config('video.max_upload_mb', 250) }} MB</p>
                    </div>
                    <input type="file" name="video_file" x-ref="fileInput" accept="video/mp4,video/quicktime,video/webm"
                           class="hidden" @change="fileName = $event.target.files[0]?.name">
                    <p x-show="fileName" class="note mt-2" style="color:#0e2f5e">Selected: <span x-text="fileName"></span></p>
                </div>
            </div>
        </div>

        {{-- Consent --}}
        <div class="card">
            <div class="section-title">⚖️ Parental Consent</div>
            <div class="consent-box">
                <p style="font-size:.85rem;font-weight:600;color:#78350f;margin-bottom:.5rem">
                    ⚠️ This step is required — especially for videos featuring minors.
                </p>
                <div class="form-group">
                    <label>Your Name (Consent Signatory) <span class="required">*</span></label>
                    <input type="text" name="consent_signed_by" value="{{ old('consent_signed_by') }}" required
                           placeholder="Your full legal name">
                </div>
                <label class="consent-check">
                    <input type="checkbox" name="consent_confirmed" value="1" required
                           {{ old('consent_confirmed') ? 'checked' : '' }}>
                    <span class="consent-text">
                        I am the parent or legal guardian of the student featured in this video. I hereby consent to Prayaag International School using this video on their website, social media, and marketing materials. I understand the video will be reviewed before publishing and may be rejected without explanation.
                    </span>
                </label>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-submit" :disabled="submitting" x-text="submitting ? 'Submitting...' : 'Submit Video Testimonial 🎬'">
            Submit Video Testimonial 🎬
        </button>

        <p style="text-align:center;font-size:.75rem;color:#94a3b8;margin-top:1rem">
            Submissions are reviewed within 2–3 business days. We will notify you by email.
        </p>
    </form>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
<script>
function vtSubmitForm() {
    return { tab: 'url', fileName: '', submitting: false };
}
</script>
</body>
</html>
