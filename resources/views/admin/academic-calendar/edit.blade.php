@extends('admin.layout')

@section('title', 'Edit Calendar Entry')
@section('subtitle', 'Modify academic dates, categories, classes or attach circular notices.')

@section('actions')
    <a href="{{ route('admin.academic-calendar-entries.index') }}" class="btn"><x-admin.icon name="chevron-left"/> Back to Calendar</a>
@endsection

@section('content')

@if ($errors->any())
    <div class="card" style="border-color:var(--danger);background:var(--danger-soft);color:var(--danger);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">
        <ul style="margin:0;padding-left:16px">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div x-data="{
    title: '{{ addslashes($entry->title) }}',
    category: '{{ $entry->category }}',
    sub_type: '{{ addslashes($entry->sub_type) }}',
    start_date: '{{ $entry->start_date->toDateString() }}',
    end_date: '{{ $entry->end_date ? $entry->end_date->toDateString() : '' }}',
    is_working_day: {{ $entry->is_working_day ? 'true' : 'false' }},
    color_tag: '{{ $entry->color_tag }}',
    status: '{{ $entry->status }}',
    getColorHex() {
        if (this.category === 'exam') return { bg: '#fee2e2', border: '#fca5a5', text: '#991b1b' };
        if (this.category === 'holiday') return { bg: '#fef3c7', border: '#fcd34d', text: '#92400e' };
        if (this.category === 'important_date') return { bg: '#dbeafe', border: '#93c5fd', text: '#1e40af' };
        return { bg: '#f3f4f6', border: '#e5e7eb', text: '#374151' };
    }
}">
    
    <div style="display:grid;grid-template-columns: 2fr 1fr;gap:24px;align-items:start">
        
        <!-- Form Column -->
        <div class="card" style="padding:24px">
            <form method="POST" action="{{ route('admin.academic-calendar-entries.update', $entry->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group" style="margin-bottom:20px">
                    <label style="display:block;font-weight:600;margin-bottom:8px">Entry Title *</label>
                    <input type="text" name="title" x-model="title" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px">
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
                    <div class="form-group">
                        <label style="display:block;font-weight:600;margin-bottom:8px">Category *</label>
                        <select name="category" x-model="category" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;background:#fff">
                            <option value="exam">Exam</option>
                            <option value="holiday">Holiday/Vacation</option>
                            <option value="important_date">Important Date (PTM/Sports etc)</option>
                            <option value="working_day_note">Working Day Note</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label style="display:block;font-weight:600;margin-bottom:8px">Sub-Type (Optional)</label>
                        <input type="text" name="sub_type" x-model="sub_type" placeholder="e.g. Unit Test 1, Summer Vacation" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:20px">
                    <label style="display:block;font-weight:600;margin-bottom:8px">Academic Session *</label>
                    <select name="session_id" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;background:#fff">
                        @foreach($sessions as $s)
                            <option value="{{ $s->id }}" @selected($entry->session_id === $s->id)>{{ $s->session_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
                    <div class="form-group">
                        <label style="display:block;font-weight:600;margin-bottom:8px">Start Date *</label>
                        <input type="date" name="start_date" x-model="start_date" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px">
                    </div>

                    <div class="form-group">
                        <label style="display:block;font-weight:600;margin-bottom:8px">End Date (Optional - for range)</label>
                        <input type="date" name="end_date" x-model="end_date" :min="start_date" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px">
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
                    <div class="form-group">
                        <label style="display:block;font-weight:600;margin-bottom:8px">Class Relevance (Optional)</label>
                        <select name="class_id" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;background:#fff">
                            <option value="">— All Classes —</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" @selected($entry->class_id === $c->id)>{{ $c->class_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label style="display:block;font-weight:600;margin-bottom:8px">Color Tag *</label>
                        <select name="color_tag" x-model="color_tag" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;background:#fff">
                            <option value="red">Red (Exam)</option>
                            <option value="yellow">Yellow (Holiday)</option>
                            <option value="blue">Blue (Important)</option>
                            <option value="grey">Grey (Working Note)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:20px">
                    <label style="display:block;font-weight:600;margin-bottom:8px">Description</label>
                    <textarea name="description" placeholder="Write calendar event details..." style="width:100%;height:100px;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;font-family:inherit;resize:vertical">{{ $entry->description }}</textarea>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
                    <div class="form-group">
                        <label style="display:block;font-weight:600;margin-bottom:8px">Attachment Notice / Circular (PDF/Image)</label>
                        @if ($entry->attachment)
                            <div style="margin-bottom:8px;font-size:13px">
                                📄 <a href="{{ asset('storage/' . $entry->attachment) }}" target="_blank" style="color:var(--primary);text-decoration:none">Current Attachment</a>
                            </div>
                        @endif
                        <input type="file" name="attachment" style="width:100%;padding:8px 0;font-size:14px">
                    </div>

                    <div class="form-group">
                        <label style="display:block;font-weight:600;margin-bottom:8px">Publishing Status *</label>
                        <select name="status" x-model="status" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;background:#fff">
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:24px">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input type="checkbox" name="is_working_day" x-model="is_working_day" value="1" style="width:16px;height:16px">
                        <span style="font-size:14px;font-weight:600">Is this scheduled day a Working Day?</span>
                    </label>
                </div>

                <div style="border-top:1px solid #e2e8f0;padding-top:20px;text-align:right">
                    <a href="{{ route('admin.academic-calendar-entries.index') }}" class="btn" style="margin-right:8px">Cancel</a>
                    <button type="submit" class="btn primary">Update Entry</button>
                </div>
            </form>
        </div>

        <!-- Preview Column -->
        <div class="card" style="padding:24px;position:sticky;top:80px">
            <h3 style="font-size:15px;font-weight:700;color:#0e2f5e;margin-top:0;margin-bottom:16px">Live Preview</h3>
            
            <div :style="'border: 1px solid ' + getColorHex().border + '; background-color: ' + getColorHex().bg + '; color: ' + getColorHex().text + '; border-radius: 8px; padding: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05)'">
                <div style="display:flex;justify-content:between;align-items:center;margin-bottom:10px">
                    <span :style="'font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; border: 1px solid ' + getColorHex().border + '; background: #fff; color: ' + getColorHex().text" x-text="category.replace('_', ' ')"></span>
                    <span x-show="!is_working_day" style="font-size: 10px; font-weight: 700; background: #fff5f5; color: #dc2626; border: 1px solid #fecaca; padding: 2px 6px; border-radius: 4px">Non-Working</span>
                </div>
                <h4 style="font-size: 16px; font-weight: 700; margin: 0 0 6px 0" x-text="title || 'Untranslated Entry Title'"></h4>
                <div style="font-size: 12px; opacity: 0.8">
                    <span x-text="start_date ? new Date(start_date).toLocaleDateString() : 'Start Date'"></span>
                    <span x-show="end_date"> to <span x-text="new Date(end_date).toLocaleDateString()"></span></span>
                </div>
                <div x-show="sub_type" style="font-size: 11px; opacity: 0.7; margin-top: 4px; font-weight: 500">
                    Sub-type: <span x-text="sub_type"></span>
                </div>
            </div>

            <div style="margin-top:20px;font-size:12.5px;color:#64748b;line-height:1.5">
                <p>💡 <strong>Note:</strong> Overlapping exam dates will display a warning upon saving, but will allow the action to execute.</p>
            </div>
        </div>

    </div>
</div>

@endsection
