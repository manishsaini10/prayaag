<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicCalendarEntry;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AcademicCalendarImportController extends Controller
{
    public function show(): View
    {
        $sessions = AcademicSession::orderBy('start_date', 'desc')->get();
        return view('admin.academic-calendar.import', compact('sessions'));
    }

    /**
     * Download a sample CSV template for calendar imports.
     */
    public function downloadSample()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="academic_calendar_sample.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Microsoft Excel compatibility
            fwrite($file, "\xEF\xBB\xBF");
            
            // CSV Header
            fputcsv($file, [
                'title',
                'category',
                'start_date',
                'end_date',
                'sub_type',
                'description',
                'is_working_day'
            ]);

            // Sample Rows
            fputcsv($file, ['Nursery Orientation Day', 'important_date', '2026-07-15', '', 'Orientation', 'Welcome day for new students', '1']);
            fputcsv($file, ['Independence Day Vacation', 'holiday', '2026-08-15', '2026-08-16', 'National Holiday', 'Independence Day school holidays', '0']);
            fputcsv($file, ['Unit Test 1', 'exam', '2026-09-01', '2026-09-07', 'Unit Test', 'First term examinations for Grade 10', '1']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import entries from a uploaded CSV file.
     */
    public function importCsv(Request $request): RedirectResponse
    {
        $request->validate([
            'session_id' => 'required|exists:academic_sessions,id',
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $sessionId = $request->input('session_id');
        $session = AcademicSession::findOrFail($sessionId);
        $file = $request->file('csv_file');
        
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');
        
        // Read header
        $header = fgetcsv($handle);
        if (!$header || count($header) < 3) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'Invalid CSV format. Please make sure the headers match the template.']);
        }

        // Map column headers
        $headerMap = array_flip(array_map('trim', $header));
        
        $requiredKeys = ['title', 'category', 'start_date'];
        foreach ($requiredKeys as $key) {
            if (!isset($headerMap[$key])) {
                fclose($handle);
                return back()->withErrors(['csv_file' => "Missing required CSV column: '{$key}'."]);
            }
        }

        $importedCount = 0;
        $errorsList = [];
        $rowNum = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Extract row values
            $title = $row[$headerMap['title']] ?? null;
            $category = $row[$headerMap['category']] ?? null;
            $startDateStr = $row[$headerMap['start_date']] ?? null;
            $endDateStr = isset($headerMap['end_date']) ? ($row[$headerMap['end_date']] ?? null) : null;
            $subType = isset($headerMap['sub_type']) ? ($row[$headerMap['sub_type']] ?? null) : null;
            $description = isset($headerMap['description']) ? ($row[$headerMap['description']] ?? null) : null;
            $isWorkingDayVal = isset($headerMap['is_working_day']) ? ($row[$headerMap['is_working_day']] ?? null) : null;

            if (empty($title) || empty($category) || empty($startDateStr)) {
                $errorsList[] = "Row {$rowNum}: Missing Title, Category, or Start Date.";
                continue;
            }

            // Normalize category
            $category = strtolower(trim($category));
            if (!in_array($category, ['exam', 'holiday', 'important_date', 'working_day_note'])) {
                $errorsList[] = "Row {$rowNum}: Invalid category '{$category}'. Must be one of: exam, holiday, important_date, working_day_note.";
                continue;
            }

            // Parse dates
            try {
                $startDate = Carbon::parse(trim($startDateStr));
                $endDate = !empty($endDateStr) ? Carbon::parse(trim($endDateStr)) : null;
            } catch (\Exception $e) {
                $errorsList[] = "Row {$rowNum}: Invalid date format.";
                continue;
            }

            // Session boundaries check
            if ($startDate->lt($session->start_date) || ($endDate && $endDate->gt($session->end_date))) {
                $errorsList[] = "Row {$rowNum}: Date range falls outside the selected Session dates.";
                continue;
            }

            // Find class relevance
            $classId = null;

            // Set color tag
            $colorMap = [
                'exam' => '#ef4444',
                'holiday' => '#f59e0b',
                'important_date' => '#3b82f6',
                'working_day_note' => '#6b7280',
            ];
            $colorTag = $colorMap[$category] ?? '#6b7280';

            // Normalize is_working_day
            $isWorkingDay = true;
            if ($category === 'holiday') {
                $isWorkingDay = false;
            } elseif ($isWorkingDayVal !== null && $isWorkingDayVal !== '') {
                $isWorkingDay = filter_var($isWorkingDayVal, FILTER_VALIDATE_BOOLEAN);
            }

            AcademicCalendarEntry::create([
                'session_id' => $sessionId,
                'title' => trim($title),
                'category' => $category,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate ? $endDate->toDateString() : null,
                'sub_type' => $subType ? trim($subType) : null,
                'description' => $description ? trim($description) : null,
                'class_id' => $classId,
                'is_working_day' => $isWorkingDay,
                'color_tag' => $colorTag,
                'status' => 'published',
                'created_by' => auth()->id(),
            ]);

            $importedCount++;
        }

        fclose($handle);

        $statusMsg = "Successfully imported {$importedCount} entries.";
        if (count($errorsList) > 0) {
            $statusMsg .= " However, " . count($errorsList) . " rows failed.";
            return redirect()->route('admin.academic-calendar-entries.index')
                ->with('status', $statusMsg)
                ->withErrors($errorsList);
        }

        return redirect()->route('admin.academic-calendar-entries.index')->with('status', $statusMsg);
    }

    /**
     * Extract entries from uploaded Image using Gemini API.
     */
    public function importAi(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:academic_sessions,id',
            'image_file' => 'required|image|max:10240', // 10MB max
            'api_key' => 'nullable|string',
        ]);

        $image = $request->file('image_file');
        $apiKey = $request->input('api_key') ?? env('GEMINI_API_KEY');

        if (!$apiKey) {
            // No API Key provided: run a gorgeous simulation mode
            sleep(2); // Simulate network latency

            $sampleYear = date('Y');
            $extracted = [
                [
                    'title' => 'School Reopening & Orientation',
                    'category' => 'important_date',
                    'sub_type' => 'Orientation',
                    'description' => 'Welcome assembly and curriculum overview sessions for parents and students.',
                    'start_date' => "{$sampleYear}-07-15",
                    'end_date' => null,
                    'is_working_day' => true
                ],
                [
                    'title' => 'Independence Day Celebration',
                    'category' => 'holiday',
                    'sub_type' => 'National Holiday',
                    'description' => 'Flag hoisting ceremony at 8:00 AM. Student attendance is mandatory. Non-working day for classes.',
                    'start_date' => "{$sampleYear}-08-15",
                    'end_date' => null,
                    'is_working_day' => false
                ],
                [
                    'title' => 'Unit Test 1',
                    'category' => 'exam',
                    'sub_type' => 'Unit Test',
                    'description' => 'First terminal exams covering syllabus from July and August months.',
                    'start_date' => "{$sampleYear}-09-07",
                    'end_date' => "{$sampleYear}-09-14",
                    'is_working_day' => true
                ],
                [
                    'title' => 'Gandhi Jayanti Holiday',
                    'category' => 'holiday',
                    'sub_type' => 'Gazetted Holiday',
                    'description' => 'School closed in honor of Mahatma Gandhi Jayanti.',
                    'start_date' => "{$sampleYear}-10-02",
                    'end_date' => null,
                    'is_working_day' => false
                ]
            ];

            return response()->json([
                'success' => true,
                'simulated' => true,
                'message' => 'AI simulation completed successfully. You can review the extracted events below.',
                'data' => $extracted
            ]);
        }

        // Call Gemini API
        try {
            $base64Image = base64_encode(file_get_contents($image->getRealPath()));
            $mimeType = $image->getMimeType();

            // Prompt instructing structured JSON output
            $prompt = "You are an expert school coordinator. Extract all academic schedule entries (exams, holidays, school events, PTMs, workshops etc.) from this image.
            Return a JSON array of objects. Each object must have these exact keys:
            - 'title' (string, name of the event)
            - 'category' (string, MUST be exactly one of: 'exam', 'holiday', 'important_date', 'working_day_note')
            - 'sub_type' (string, optional, e.g. Unit Test, PTM, Vacation, Gazetted)
            - 'description' (string, optional details)
            - 'start_date' (string, format YYYY-MM-DD, try to extrapolate or infer the year. If year is not mentioned, use the current year)
            - 'end_date' (string, format YYYY-MM-DD or null if single day event)
            - 'is_working_day' (boolean, default true, but false for holidays/vacations)
            
            Respond ONLY with the raw JSON array. Do NOT wrap it in markdown or backticks (no ```json).";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inlineData' => [
                                    'mimeType' => $mimeType,
                                    'data' => $base64Image
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json'
                ]
            ]);

            if ($response->failed()) {
                throw new \Exception('Gemini API request failed: ' . $response->body());
            }

            $result = $response->json();
            $textResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            // Sanitize response text
            $textResponse = trim($textResponse);
            if (str_starts_with($textResponse, '```')) {
                $textResponse = preg_replace('/^```(?:json)?|```$/m', '', $textResponse);
                $textResponse = trim($textResponse);
            }

            $data = json_decode($textResponse, true);
            if (!is_array($data)) {
                throw new \Exception('Failed to parse JSON response from Gemini API: ' . $textResponse);
            }

            return response()->json([
                'success' => true,
                'simulated' => false,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('AI calendar import error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'AI Extraction failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save the reviewed entries confirmed by the admin.
     */
    public function saveReviewed(Request $request): RedirectResponse
    {
        $request->validate([
            'session_id' => 'required|exists:academic_sessions,id',
            'entries' => 'required|array',
            'entries.*.title' => 'required|string|max:255',
            'entries.*.category' => 'required|in:exam,holiday,important_date,working_day_note',
            'entries.*.start_date' => 'required|date',
            'entries.*.end_date' => 'nullable|date|after_or_equal:entries.*.start_date',
            'entries.*.sub_type' => 'nullable|string|max:100',
            'entries.*.description' => 'nullable|string',
            'entries.*.is_working_day' => 'nullable|boolean',
        ]);

        $sessionId = $request->input('session_id');
        $session = AcademicSession::findOrFail($sessionId);
        $entries = $request->input('entries');

        $savedCount = 0;
        $colorMap = [
            'exam' => '#ef4444',
            'holiday' => '#f59e0b',
            'important_date' => '#3b82f6',
            'working_day_note' => '#6b7280',
        ];

        foreach ($entries as $item) {
            // Check checkbox status
            if (!isset($item['selected']) || !$item['selected']) {
                continue;
            }

            $startDate = Carbon::parse($item['start_date']);
            $endDate = !empty($item['end_date']) ? Carbon::parse($item['end_date']) : null;

            // Session bounds verification
            if ($startDate->lt($session->start_date) || ($endDate && $endDate->gt($session->end_date))) {
                continue; // skip entries out of session range
            }

            // Find class relevance
            $classId = null;

            $category = $item['category'];
            $isWorkingDay = isset($item['is_working_day']) ? filter_var($item['is_working_day'], FILTER_VALIDATE_BOOLEAN) : true;
            if ($category === 'holiday') {
                $isWorkingDay = false;
            }

            AcademicCalendarEntry::create([
                'session_id' => $sessionId,
                'title' => trim($item['title']),
                'category' => $category,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate ? $endDate->toDateString() : null,
                'sub_type' => !empty($item['sub_type']) ? trim($item['sub_type']) : null,
                'description' => !empty($item['description']) ? trim($item['description']) : null,
                'class_id' => $classId,
                'is_working_day' => $isWorkingDay,
                'color_tag' => $colorMap[$category] ?? '#6b7280',
                'status' => 'published',
                'created_by' => auth()->id(),
            ]);

            $savedCount++;
        }

        return redirect()->route('admin.academic-calendar-entries.index')
            ->with('status', "Successfully imported {$savedCount} reviewed entries.");
    }
}
