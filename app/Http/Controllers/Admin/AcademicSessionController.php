<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicSessionController extends Controller
{
    public function index(): View
    {
        $sessions = AcademicSession::withCount(['terms', 'entries'])->orderBy('start_date', 'desc')->get();
        return view('admin.academic-sessions.index', compact('sessions'));
    }

    public function create(): View
    {
        return view('admin.academic-sessions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'session_name' => 'required|string|max:255|unique:academic_sessions,session_name',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after:start_date',
            'is_current'   => 'nullable|boolean',
        ]);

        $isCurrent = $request->boolean('is_current');

        if ($isCurrent) {
            AcademicSession::query()->update(['is_current' => false]);
        }

        AcademicSession::create([
            'session_name' => $validated['session_name'],
            'start_date'   => $validated['start_date'],
            'end_date'     => $validated['end_date'],
            'is_current'   => $isCurrent,
        ]);

        return redirect()->route('admin.academic-sessions.index')->with('status', 'Academic session created successfully.');
    }

    public function edit(AcademicSession $academicSession): View
    {
        return view('admin.academic-sessions.edit', ['session' => $academicSession]);
    }

    public function update(Request $request, AcademicSession $academicSession): RedirectResponse
    {
        $validated = $request->validate([
            'session_name' => 'required|string|max:255|unique:academic_sessions,session_name,' . $academicSession->id,
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after:start_date',
            'is_current'   => 'nullable|boolean',
        ]);

        $isCurrent = $request->boolean('is_current');

        if ($isCurrent) {
            AcademicSession::where('id', '!=', $academicSession->id)->update(['is_current' => false]);
        }

        $academicSession->update([
            'session_name' => $validated['session_name'],
            'start_date'   => $validated['start_date'],
            'end_date'     => $validated['end_date'],
            'is_current'   => $isCurrent,
        ]);

        return redirect()->route('admin.academic-sessions.index')->with('status', 'Academic session updated successfully.');
    }

    public function destroy(AcademicSession $academicSession): RedirectResponse
    {
        $academicSession->delete();
        return redirect()->route('admin.academic-sessions.index')->with('status', 'Academic session deleted successfully.');
    }

    public function toggle(AcademicSession $academicSession): RedirectResponse
    {
        $newStatus = !$academicSession->is_current;
        if ($newStatus) {
            // Disable all other sessions
            AcademicSession::query()->update(['is_current' => false]);
        }
        $academicSession->update(['is_current' => $newStatus]);

        return back()->with('status', 'Session activation status updated successfully.');
    }
}
