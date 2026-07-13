@extends('admin.layout')

@section('title', 'Edit Testimonial')

@section('actions')
    <a href="{{ route('admin.testimonials-console.index') }}" class="btn-secondary inline-flex items-center gap-1.5">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        <span>Back to List</span>
    </a>
@endsection

@section('content')
<div class="space-y-6">
    <form action="{{ route('admin.testimonials-console.update', $testimonial->id) }}" method="POST" class="card space-y-6">
        @csrf
        
        <div>
            <h3 class="text-lg font-bold text-gray-800">Testimonial Details</h3>
            <p class="text-sm text-gray-500 mt-1">Review or override parent testimonial data manually.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Name --}}
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700">Parent Name *</label>
                <input type="text" name="name" value="{{ old('name', $testimonial->name) }}" class="w-full text-sm p-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary" required>
            </div>

            {{-- Relation --}}
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700">Relationship (e.g. Mother, Father)</label>
                <input type="text" name="relation" value="{{ old('relation', $testimonial->relation) }}" class="w-full text-sm p-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            {{-- Student Name --}}
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700">Student Name</label>
                <input type="text" name="student_name" value="{{ old('student_name', $testimonial->student_name) }}" class="w-full text-sm p-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            {{-- Class --}}
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700">Student Class</label>
                <input type="text" name="class" value="{{ old('class', $testimonial->class) }}" class="w-full text-sm p-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            {{-- Phone --}}
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700">Phone *</label>
                <input type="text" name="phone" value="{{ old('phone', $testimonial->phone) }}" class="w-full text-sm p-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary" required>
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $testimonial->email) }}" class="w-full text-sm p-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            {{-- Title --}}
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700">Short Summary / Title</label>
                <input type="text" name="title" value="{{ old('title', $testimonial->title) }}" class="w-full text-sm p-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary" placeholder="e.g. Best Academic Environment">
            </div>

            {{-- Rating --}}
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700">Rating Stars (1 to 5) *</label>
                <select name="rating" class="w-full text-sm p-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary" required>
                    @for($i=5; $i>=1; $i--)
                        <option value="{{ $i }}" {{ old('rating', $testimonial->rating) == $i ? 'selected' : '' }}>{{ $i }} Stars {!! str_repeat('★', $i) !!}</option>
                    @endfor
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1 text-gray-700">Testimonial Quote *</label>
            <textarea name="testimonial" rows="6" class="w-full text-sm p-3 border rounded focus:outline-none focus:ring-2 focus:ring-primary" required>{{ old('testimonial', $testimonial->testimonial) }}</textarea>
        </div>

        <hr style="border-top:1px solid var(--border)">

        {{-- Image, Status and display settings --}}
        <div>
            <h3 class="text-lg font-bold text-gray-800">Branding, Status & Layouts</h3>
            <p class="text-sm text-gray-500 mt-1">Set visibility targets and status flags.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Photo Path --}}
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700">Photo Path</label>
                <input type="text" name="image" value="{{ old('image', $testimonial->image) }}" class="w-full text-sm p-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary" placeholder="e.g. /uploads/testimonials/filename.jpg">
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700">Moderation Status *</label>
                <select name="status" class="w-full text-sm p-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary" required>
                    <option value="pending" {{ old('status', $testimonial->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ old('status', $testimonial->status) === 'approved' ? 'selected' : '' }}>Approved & Published</option>
                    <option value="rejected" {{ old('status', $testimonial->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="archived" {{ old('status', $testimonial->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>

            {{-- Sort Order --}}
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order) }}" class="w-full text-sm p-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            {{-- Rejection Reason (shown when status is rejected) --}}
            <div class="md:col-span-3" x-data="{ show: '{{ old('status', $testimonial->status) }}' === 'rejected' }">
                <label class="block text-sm font-semibold mb-1 text-gray-700">Rejection Reason</label>
                <textarea name="rejection_reason" rows="2" class="w-full text-sm p-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Optional reason for rejection...">{{ old('rejection_reason', $testimonial->rejection_reason) }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Included in the notification email sent to the parent.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-3">
            {{-- Featured toggle --}}
            <label class="flex items-start gap-3 cursor-pointer bg-gray-50 p-4 rounded-xl border border-dashed">
                <input type="checkbox" name="featured" value="1" {{ old('featured', $testimonial->featured) ? 'checked' : '' }} class="mt-1">
                <div>
                    <span class="font-bold block text-purple-700 text-sm">⭐ Mark as Featured</span>
                    <span class="text-xs text-gray-500">Exposes this testimonial to homepage sliders and cards.</span>
                </div>
            </label>

            {{-- Verified toggle --}}
            <label class="flex items-start gap-3 cursor-pointer bg-gray-50 p-4 rounded-xl border border-dashed">
                <input type="checkbox" name="is_verified" value="1" {{ old('is_verified', $testimonial->is_verified) ? 'checked' : '' }} class="mt-1">
                <div>
                    <span class="font-bold block text-emerald-700 text-sm">✓ Verified Parent</span>
                    <span class="text-xs text-gray-500">Shows a verified badge next to this testimonial.</span>
                </div>
            </label>

            {{-- Display Locations JSON --}}
            <div>
                <label class="block text-sm font-bold mb-2 text-gray-700">Display Locations</label>
                <div class="grid grid-cols-2 gap-2 bg-gray-50 p-3 rounded-lg border">
                    @php
                        $locs = $testimonial->display_location ?: [];
                    @endphp
                    @foreach(['home' => 'Homepage', 'testimonials' => 'Testimonials Page', 'about' => 'About Us Page', 'admission' => 'Admissions Page', 'sidebar' => 'Sidebar Widget', 'footer' => 'Footer Widget'] as $locKey => $locLbl)
                        <label class="flex items-center gap-2 cursor-pointer text-xs text-gray-700">
                            <input type="checkbox" name="display_location[]" value="{{ $locKey }}" {{ in_array($locKey, $locs) ? 'checked' : '' }}>
                            <span>{{ $locLbl }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-4 border-t">
            <a href="{{ route('admin.testimonials-console.index') }}" class="btn-secondary py-2 px-6 text-xs font-semibold">Cancel</a>
            <button type="submit" class="btn-primary py-2 px-6 text-xs font-bold">Update Testimonial</button>
        </div>
    </form>
</div>
@endsection
