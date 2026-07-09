@extends('admin.layout')

@section('title', 'Academic Calendar Import Utility')
@section('subtitle', 'Populate calendar entries using Excel/CSV spreadsheets or automate schedule extraction from notice images using Gemini AI.')

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
    activeTab: 'ai',
    sessionId: '{{ $sessions->firstWhere('is_current', true)->id ?? ($sessions->first()->id ?? '') }}',
    apiKey: '',
    isProcessing: false,
    extractedData: [],
    errorMessage: '',
    
    uploadImage() {
        const fileInput = document.getElementById('ai_image_input');
        if (!fileInput || !fileInput.files[0]) {
            alert('Please select a calendar schedule image file first.');
            return;
        }

        const formData = new FormData();
        formData.append('image_file', fileInput.files[0]);
        formData.append('session_id', this.sessionId);
        formData.append('api_key', this.apiKey);
        formData.append('_token', '{{ csrf_token() }}');

        this.isProcessing = true;
        this.errorMessage = '';
        this.extractedData = [];

        fetch('{{ route('admin.academic-calendar-entries.import.ai') }}', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            this.isProcessing = false;
            if (res.success) {
                this.extractedData = res.data.map(item => ({
                    ...item,
                    selected: true,
                    is_working_day: item.is_working_day !== undefined ? item.is_working_day : true,
                    sub_type: item.sub_type || '',
                    description: item.description || '',
                    end_date: item.end_date || ''
                }));
            } else {
                this.errorMessage = res.message || 'An error occurred during AI extraction.';
            }
        })
        .catch(err => {
            this.isProcessing = false;
            this.errorMessage = 'Network connection error or invalid request.';
            console.error(err);
        });
    }
}">

    <!-- Tab Navigation -->
    <div class="card" style="padding:4px; display:inline-flex; background:#f1f5f9; border-radius:10px; margin-bottom:24px">
        <button @click="activeTab = 'ai'" :class="activeTab === 'ai' ? 'bg-[#fff] text-[#0e2f5e] shadow-sm' : 'text-slate-500'" style="border:none; padding:8px 20px; font-size:13.5px; font-weight:700; border-radius:8px; cursor:pointer; transition:all 0.2s">
            🤖 AI Image Extractor
        </button>
        <button @click="activeTab = 'csv'" :class="activeTab === 'csv' ? 'bg-[#fff] text-[#0e2f5e] shadow-sm' : 'text-slate-500'" style="border:none; padding:8px 20px; font-size:13.5px; font-weight:700; border-radius:8px; cursor:pointer; transition:all 0.2s">
            📊 Excel / CSV Import
        </button>
    </div>

    <!-- AI Extractor Tab -->
    <div x-show="activeTab === 'ai'" class="card" style="padding:28px; margin-bottom:24px">
        <div style="border-bottom:1px solid #e2e8f0; padding-bottom:14px; margin-bottom:20px">
            <h3 style="font-size:18px; font-weight:750; color:#0e2f5e; margin:0">Gemini AI Schedule Extractor</h3>
            <p style="font-size:13px; color:#64748b; margin:6px 0 0 0">Upload a school circular, calendar photo, or notice memo image. The AI will extract all events, dates, and categories accurately for review.</p>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px">
            <div class="form-group">
                <label style="display:block; font-weight:600; margin-bottom:8px">Target Academic Session *</label>
                <select x-model="sessionId" style="width:100%; padding:10px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px; background:#fff">
                    @foreach($sessions as $s)
                        <option value="{{ $s->id }}" @selected($s->is_current)>{{ $s->session_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label style="display:block; font-weight:600; margin-bottom:8px">Gemini API Key (Optional)</label>
                <input type="password" x-model="apiKey" placeholder="Leave blank to run in Demonstration/Simulation mode" style="width:100%; padding:10px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px">
            </div>
        </div>

        <div class="form-group" style="margin-bottom:24px">
            <label style="display:block; font-weight:600; margin-bottom:8px">Upload Calendar/Notice Image *</label>
            <div style="border:2px dashed #cbd5e1; border-radius:12px; padding:30px; text-align:center; background:#f8fafc; cursor:pointer" onclick="document.getElementById('ai_image_input').click()">
                <input type="file" id="ai_image_input" accept="image/*" style="display:none" onchange="document.getElementById('image_filename').innerText = this.files[0] ? this.files[0].name : ''">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:40px; height:40px; color:#94a3b8; margin:0 auto 10px auto"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
                <span id="image_filename" style="display:block; font-weight:700; font-size:14px; color:#475569">Choose calendar photo...</span>
                <span style="font-size:12px; color:#94a3b8; margin-top:4px; display:block">Supports PNG, JPG, JPEG up to 10MB</span>
            </div>
        </div>

        <button type="button" @click="uploadImage()" :disabled="isProcessing" class="btn primary" style="padding:12px 24px; font-size:14px">
            <span x-show="!isProcessing">⚡ Process Image with AI</span>
            <span x-show="isProcessing">⏳ Extracting entries from image...</span>
        </button>

        <!-- Loading spinner -->
        <div x-show="isProcessing" style="margin-top:24px; text-align:center">
            <div style="display:inline-block; width:36px; height:36px; border:4px solid #cbd5e1; border-top-color:#3b82f6; border-radius:50%; animation:spin 1s linear infinite"></div>
            <p style="font-size:13px; color:#64748b; margin-top:10px; font-weight:600">The AI model is analyzing the image layouts and transcribing text nodes...</p>
        </div>

        <!-- Error display -->
        <div x-show="errorMessage" style="margin-top:20px; border-color:var(--danger); background:var(--danger-soft); color:var(--danger); padding:12px 16px; border-radius:8px; font-size:13.5px; font-weight:600" x-text="errorMessage"></div>

        <!-- AI Review Grid -->
        <div x-show="extractedData.length > 0" style="margin-top:40px; border-top: 2px solid #e2e8f0; padding-top:30px">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px">
                <div>
                    <h4 style="font-size:16px; font-weight:750; color:#0e2f5e; margin:0">Review Extracted Entries</h4>
                    <p style="font-size:12.5px; color:#64748b; margin:4px 0 0 0">Inspect, modify, and choose which extracted events to save directly into the calendar database.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.academic-calendar-entries.import.save-review') }}">
                @csrf
                <input type="hidden" name="session_id" :value="sessionId">

                <div class="card" style="overflow-x:auto; padding:0; border-radius:12px; margin-bottom:24px">
                    <table style="width:100%; border-collapse:collapse; min-width:900px">
                        <thead>
                            <tr style="background:#f8fafc; border-bottom:1.5px solid #e2e8f0">
                                <th style="padding:12px 16px; width:40px; text-align:center"><input type="checkbox" @change="extractedData.forEach(i => i.selected = $el.checked)" checked></th>
                                <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:750; color:#475569">Title</th>
                                <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:750; color:#475569; width:150px">Category</th>
                                <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:750; color:#475569; width:100px">Sub-Type</th>
                                <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:750; color:#475569; width:130px">Start Date</th>
                                <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:750; color:#475569; width:130px">End Date</th>
                                <th style="padding:12px 16px; text-align:center; font-size:12px; font-weight:750; color:#475569; width:90px">Working Day</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(entry, index) in extractedData" :key="index">
                                <tr style="border-bottom:1px solid #f1f5f9">
                                    <td style="padding:12px 16px; text-align:center">
                                        <input type="checkbox" :name="'entries['+index+'][selected]'" value="1" x-model="entry.selected">
                                    </td>
                                    <td style="padding:8px">
                                        <input type="text" :name="'entries['+index+'][title]'" x-model="entry.title" required style="width:100%; padding:6px 10px; border:1px solid #cbd5e1; border-radius:4px; font-size:13px">
                                    </td>
                                    <td style="padding:8px">
                                        <select :name="'entries['+index+'][category]'" x-model="entry.category" style="width:100%; padding:6px 10px; border:1px solid #cbd5e1; border-radius:4px; font-size:13px; background:#fff">
                                            <option value="exam">Exam</option>
                                            <option value="holiday">Holiday/Vacation</option>
                                            <option value="important_date">Important Date</option>
                                            <option value="working_day_note">Working Day Note</option>
                                        </select>
                                    </td>
                                    <td style="padding:8px">
                                        <input type="text" :name="'entries['+index+'][sub_type]'" x-model="entry.sub_type" style="width:100%; padding:6px 10px; border:1px solid #cbd5e1; border-radius:4px; font-size:13px">
                                    </td>
                                    <td style="padding:8px">
                                        <input type="date" :name="'entries['+index+'][start_date]'" x-model="entry.start_date" required style="width:100%; padding:6px 8px; border:1px solid #cbd5e1; border-radius:4px; font-size:13px">
                                    </td>
                                    <td style="padding:8px">
                                        <input type="date" :name="'entries['+index+'][end_date]'" x-model="entry.end_date" :min="entry.start_date" style="width:100%; padding:6px 8px; border:1px solid #cbd5e1; border-radius:4px; font-size:13px">
                                    </td>
                                    <td style="padding:12px 16px; text-align:center">
                                        <input type="hidden" :name="'entries['+index+'][is_working_day]'" value="0">
                                        <input type="checkbox" :name="'entries['+index+'][is_working_day]'" value="1" x-model="entry.is_working_day">
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div style="text-align:right">
                    <button type="submit" class="btn primary" style="padding:12px 28px; font-size:14px; font-weight:700">
                        💾 Confirm & Import reviewed entries
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Excel/CSV Import Tab -->
    <div x-show="activeTab === 'csv'" class="card" style="padding:28px; margin-bottom:24px" x-cloak>
        <div style="border-bottom:1px solid #e2e8f0; padding-bottom:14px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px">
            <div>
                <h3 style="font-size:18px; font-weight:750; color:#0e2f5e; margin:0">Excel & CSV Spreadsheet Import</h3>
                <p style="font-size:13px; color:#64748b; margin:6px 0 0 0">Bulk import schedule dates using a structured Excel-compatible CSV template spreadsheet.</p>
            </div>
            <a href="{{ route('admin.academic-calendar-entries.import.sample') }}" class="btn" style="font-size:13px">
                📥 Download Excel/CSV Sample
            </a>
        </div>

        <form method="POST" action="{{ route('admin.academic-calendar-entries.import.csv') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group" style="margin-bottom:20px">
                <label style="display:block; font-weight:600; margin-bottom:8px">Target Academic Session *</label>
                <select name="session_id" style="width:100%; padding:10px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px; background:#fff">
                    @foreach($sessions as $s)
                        <option value="{{ $s->id }}" @selected($s->is_current)>{{ $s->session_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom:24px">
                <label style="display:block; font-weight:600; margin-bottom:8px">Select Spreadsheet CSV File *</label>
                <input type="file" name="csv_file" accept=".csv" required style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px; background:#fff">
                <span style="font-size:12px; color:#64748b; margin-top:6px; display:block">Make sure dates are formatted as <strong>YYYY-MM-DD</strong>. Save your Excel spreadsheet file as CSV format before uploading.</span>
            </div>

            <button type="submit" class="btn primary" style="padding:12px 24px; font-size:14px">
                🚀 Upload and Import Schedule
            </button>
        </form>

        <div class="card" style="margin-top:35px; background:#fafafa; border-color:#e2e8f0">
            <h4 style="font-size:14px; font-weight:700; color:#0e2f5e; margin:0 0 12px 0">Spreadsheet Headers Specification Map:</h4>
            <div style="font-size:13px; line-height:1.6; color:#475569">
                <table style="width:100%; text-align:left; border-collapse:collapse">
                    <thead>
                        <tr style="border-bottom:1px solid #cbd5e1">
                            <th style="padding:6px 0; width:150px">Column Name</th>
                            <th style="padding:6px 0; width:100px">Required</th>
                            <th style="padding:6px 0">Allowed Values / Format</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding:6px 0"><strong>title</strong></td>
                            <td style="padding:6px 0; color:red">Yes</td>
                            <td style="padding:6px 0">Name of the entry (e.g. <code>Half Yearly Exams</code>)</td>
                        </tr>
                        <tr>
                            <td style="padding:6px 0"><strong>category</strong></td>
                            <td style="padding:6px 0; color:red">Yes</td>
                            <td style="padding:6px 0">Must be exactly: <code>exam</code>, <code>holiday</code>, <code>important_date</code>, or <code>working_day_note</code></td>
                        </tr>
                        <tr>
                            <td style="padding:6px 0"><strong>start_date</strong></td>
                            <td style="padding:6px 0; color:red">Yes</td>
                            <td style="padding:6px 0">Start date formatted as <code>YYYY-MM-DD</code> (e.g., <code>2026-10-15</code>)</td>
                        </tr>
                        <tr>
                            <td style="padding:6px 0"><strong>end_date</strong></td>
                            <td style="padding:6px 0; color:green">No</td>
                            <td style="padding:6px 0">End date formatted as <code>YYYY-MM-DD</code> (leave blank for single-day)</td>
                        </tr>
                        <tr>
                            <td style="padding:6px 0"><strong>sub_type</strong></td>
                            <td style="padding:6px 0; color:green">No</td>
                            <td style="padding:6px 0">Sub-category name (e.g., <code>Unit Test</code>, <code>Summer Break</code>)</td>
                        </tr>
                        <tr>
                            <td style="padding:6px 0"><strong>description</strong></td>
                            <td style="padding:6px 0; color:green">No</td>
                            <td style="padding:6px 0">Notice memo details and descriptions</td>
                        </tr>
                        <tr>
                            <td style="padding:6px 0"><strong>is_working_day</strong></td>
                            <td style="padding:6px 0; color:green">No</td>
                            <td style="padding:6px 0">Set to <code>1</code> for working day, <code>0</code> or blank for holidays/breaks.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

@endsection
