<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicTermController extends Controller
{
    public function index(): View
    {
        $terms = AcademicTerm::with('session')->orderBy('start_date', 'desc')->get();
        return view('admin.academic-terms.index', compact('terms'));
    }

    public function create(): View
    {
        $sessions = AcademicSession::orderBy('start_date', 'desc')->get();
        return view('admin.academic-terms.create', compact('sessions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'session_id' => 'required|exists:academic_sessions,id',
            'term_name'  => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        $session = AcademicSession::findOrFail($validated['session_id']);

        // Check if term dates fall within the academic session range
        if ($validated['start_date'] < $session->start_date->toDateString() || $validated['end_date'] > $session->end_date->toDateString()) {
            return back()->withErrors([
                'start_date' => "Term dates must fall within the selected session's range ({$session->start_date->format('Y-m-d')} to {$session->end_date->format('Y-m-d')})."
            ])->withInput();
        }

        AcademicTerm::create($validated);

        return redirect()->route('admin.academic-terms.index')->with('status', 'Academic term created successfully.');
    }

    public function edit(AcademicTerm $academicTerm): View
    {
        $sessions = AcademicSession::orderBy('start_date', 'desc')->get();
        return view('admin.academic-terms.edit', ['term' => $academicTerm, 'sessions' => $sessions]);
    }

    public function update(Request $request, AcademicTerm $academicTerm): RedirectResponse
    {
        $validated = $request->validate([
            'session_id' => 'required|exists:academic_sessions,id',
            'term_name'  => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        $session = AcademicSession::findOrFail($validated['session_id']);

        // Check if term dates fall within the academic session range
        if ($validated['start_date'] < $session->start_date->toDateString() || $validated['end_date'] > $session->end_date->toDateString()) {
            return back()->withErrors([
                'start_date' => "Term dates must fall within the selected session's range ({$session->start_date->format('Y-m-d')} to {$session->end_date->format('Y-m-d')})."
            ])->withInput();
        }

        $academicTerm->update($validated);

        return redirect()->route('admin.academic-terms.index')->with('status', 'Academic term updated successfully.');
    }

    public function destroy(AcademicTerm $academicTerm): RedirectResponse
    {
        $academicTerm->delete();
        return redirect()->route('admin.academic-terms.index')->with('status', 'Academic term deleted successfully.');
    }
}
