<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicCalendarEntry;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AcademicCalendarEntryController extends Controller
{
    public function index(Request $request): View
    {
        $sessions = AcademicSession::orderBy('start_date', 'desc')->get();

        $currentSession = AcademicSession::where('is_current', true)->first()
            ?? AcademicSession::orderBy('start_date', 'desc')->first();

        $selectedSessionId = $request->input('session_id', $currentSession ? $currentSession->id : null);

        $query = AcademicCalendarEntry::with(['session', 'term', 'class']);

        if ($selectedSessionId) {
            $query->where('session_id', $selectedSessionId);
        }

        $entries = $query->orderBy('start_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.academic-calendar.index', compact('entries', 'sessions', 'selectedSessionId'));
    }

    public function create(): View
    {
        $sessions = AcademicSession::orderBy('start_date', 'desc')->get();
        $classes = SchoolClass::orderBy('class_name', 'asc')->get();

        return view('admin.academic-calendar.create', compact('sessions', 'classes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'session_id'     => 'required|exists:academic_sessions,id',
            'title'          => 'required|string|max:255',
            'category'       => 'required|in:exam,holiday,important_date,working_day_note',
            'sub_type'       => 'nullable|string|max:100',
            'description'    => 'nullable|string',
            'start_date'     => 'required|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'class_id'       => 'nullable|exists:classes,id',
            'is_working_day' => 'nullable|boolean',
            'color_tag'      => 'required|string|max:20',
            'attachment'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            'status'         => 'required|in:published,draft',
        ]);

        $session = AcademicSession::findOrFail($validated['session_id']);

        // 1. Session bounds validation
        if ($validated['start_date'] < $session->start_date->toDateString() || ($validated['end_date'] && $validated['end_date'] > $session->end_date->toDateString())) {
            return back()->withErrors([
                'start_date' => "Entry dates must fall within the selected session's range ({$session->start_date->format('Y-m-d')} to {$session->end_date->format('Y-m-d')})."
            ])->withInput();
        }

        // Handle attachment file upload
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('academic-calendar', 'public');
            $validated['attachment'] = $path;
        }

        // Set default is_working_day if holiday/vacation
        $validated['is_working_day'] = $request->has('is_working_day') ? (bool) $request->input('is_working_day') : true;
        if (in_array($validated['category'], ['holiday'])) {
            $validated['is_working_day'] = false;
        }

        $validated['created_by'] = auth()->id();

        // 3. Overlap check for exams
        $warning = null;
        if ($validated['category'] === 'exam' && !empty($validated['class_id'])) {
            $endDate = $validated['end_date'] ?? $validated['start_date'];
            $overlapCount = AcademicCalendarEntry::query()
                ->where('category', 'exam')
                ->where('class_id', $validated['class_id'])
                ->where(function ($q) use ($validated, $endDate) {
                    $q->whereBetween('start_date', [$validated['start_date'], $endDate])
                      ->orWhereBetween('end_date', [$validated['start_date'], $endDate])
                      ->orWhere(function ($sub) use ($validated, $endDate) {
                          $sub->where('start_date', '<=', $validated['start_date'])
                              ->where('end_date', '>=', $endDate);
                      });
                })->count();

            if ($overlapCount > 0) {
                $warning = "Warning: Another exam is already scheduled for this class during the selected dates.";
            }
        }

        $entry = AcademicCalendarEntry::create($validated);

        if ($warning) {
            return redirect()->route('admin.academic-calendar-entries.index')
                ->with('status', 'Calendar entry created successfully.')
                ->with('warning', $warning);
        }

        return redirect()->route('admin.academic-calendar-entries.index')
            ->with('status', 'Calendar entry created successfully.');
    }

    public function edit(AcademicCalendarEntry $academicCalendarEntry): View
    {
        $sessions = AcademicSession::orderBy('start_date', 'desc')->get();
        $classes = SchoolClass::orderBy('class_name', 'asc')->get();

        return view('admin.academic-calendar.edit', [
            'entry'    => $academicCalendarEntry,
            'sessions' => $sessions,
            'classes'  => $classes
        ]);
    }

    public function update(Request $request, AcademicCalendarEntry $academicCalendarEntry): RedirectResponse
    {
        $validated = $request->validate([
            'session_id'     => 'required|exists:academic_sessions,id',
            'title'          => 'required|string|max:255',
            'category'       => 'required|in:exam,holiday,important_date,working_day_note',
            'sub_type'       => 'nullable|string|max:100',
            'description'    => 'nullable|string',
            'start_date'     => 'required|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'class_id'       => 'nullable|exists:classes,id',
            'is_working_day' => 'nullable|boolean',
            'color_tag'      => 'required|string|max:20',
            'attachment'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'status'         => 'required|in:published,draft',
        ]);

        $session = AcademicSession::findOrFail($validated['session_id']);

        // 1. Session bounds validation
        if ($validated['start_date'] < $session->start_date->toDateString() || ($validated['end_date'] && $validated['end_date'] > $session->end_date->toDateString())) {
            return back()->withErrors([
                'start_date' => "Entry dates must fall within the selected session's range ({$session->start_date->format('Y-m-d')} to {$session->end_date->format('Y-m-d')})."
            ])->withInput();
        }

        // Handle attachment file upload
        if ($request->hasFile('attachment')) {
            if ($academicCalendarEntry->attachment) {
                Storage::disk('public')->delete($academicCalendarEntry->attachment);
            }
            $path = $request->file('attachment')->store('academic-calendar', 'public');
            $validated['attachment'] = $path;
        }

        $validated['is_working_day'] = $request->has('is_working_day') ? (bool) $request->input('is_working_day') : true;
        if (in_array($validated['category'], ['holiday'])) {
            $validated['is_working_day'] = false;
        }

        // 3. Overlap check for exams
        $warning = null;
        if ($validated['category'] === 'exam' && !empty($validated['class_id'])) {
            $endDate = $validated['end_date'] ?? $validated['start_date'];
            $overlapCount = AcademicCalendarEntry::query()
                ->where('id', '!=', $academicCalendarEntry->id)
                ->where('category', 'exam')
                ->where('class_id', $validated['class_id'])
                ->where(function ($q) use ($validated, $endDate) {
                    $q->whereBetween('start_date', [$validated['start_date'], $endDate])
                      ->orWhereBetween('end_date', [$validated['start_date'], $endDate])
                      ->orWhere(function ($sub) use ($validated, $endDate) {
                          $sub->where('start_date', '<=', $validated['start_date'])
                              ->where('end_date', '>=', $endDate);
                      });
                })->count();

            if ($overlapCount > 0) {
                $warning = "Warning: Another exam is already scheduled for this class during the selected dates.";
            }
        }

        $academicCalendarEntry->update($validated);

        if ($warning) {
            return redirect()->route('admin.academic-calendar-entries.index')
                ->with('status', 'Calendar entry updated successfully.')
                ->with('warning', $warning);
        }

        return redirect()->route('admin.academic-calendar-entries.index')
            ->with('status', 'Calendar entry updated successfully.');
    }

    public function destroy(AcademicCalendarEntry $academicCalendarEntry): RedirectResponse
    {
        if ($academicCalendarEntry->attachment) {
            Storage::disk('public')->delete($academicCalendarEntry->attachment);
        }
        $academicCalendarEntry->delete();
        return redirect()->route('admin.academic-calendar-entries.index')->with('status', 'Calendar entry deleted successfully.');
    }
}
