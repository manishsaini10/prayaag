<?php

namespace App\Livewire;

use App\Models\AcademicCalendarEntry;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use Livewire\Component;
use Illuminate\Support\Carbon;

class AcademicCalendarView extends Component
{
    public $sessionId;
    public $termId;
    public $viewMode = 'month'; // 'month', 'term', 'year'
    public $currentYearMonth; // format: 'YYYY-MM'

    // Modal state
    public $isModalOpen = false;
    public $selectedDate;
    public $dateEntries = [];

    protected $listeners = [
        'changeMonth' => 'setYearMonth',
    ];

    public function mount()
    {
        $currentSession = AcademicSession::where('is_current', true)->first()
            ?? AcademicSession::orderBy('start_date', 'desc')->first();

        $this->sessionId = $currentSession ? $currentSession->id : null;
        $this->currentYearMonth = now()->format('Y-m');
        $this->dateEntries = collect();
    }

    public function updatedSessionId($value)
    {
        $this->termId = null;
        $this->dispatch('sessionChanged', sessionId: $value);
    }

    public function updatedTermId($value)
    {
        $this->dispatch('termChanged', termId: $value);
    }

    public function setYearMonth($yearMonth)
    {
        $this->currentYearMonth = $yearMonth;
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    /**
     * Fetch all entries overlapping with a specific date to display in the modal.
     */
    public function showDateEntries($date)
    {
        $this->selectedDate = $date;
        $this->dateEntries = AcademicCalendarEntry::with(['session', 'term', 'class'])
            ->where('session_id', $this->sessionId)
            ->where('status', 'published')
            ->where(function ($q) use ($date) {
                $q->where(fn($sub) => $sub->where('start_date', '<=', $date)->where('end_date', '>=', $date))
                  ->orWhere(fn($sub) => $sub->where('start_date', $date)->whereNull('end_date'));
            })
            ->orderBy('category', 'asc')
            ->get()
            ->toArray(); // convert to array for clean Livewire serialization

        if (count($this->dateEntries) > 0) {
            $this->isModalOpen = true;
            $this->dispatch('open-details-modal');
        }
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->dateEntries = [];
    }

    public function render()
    {
        $sessions = AcademicSession::orderBy('start_date', 'desc')->get();
        $activeSession = $this->sessionId ? AcademicSession::find($this->sessionId) : null;

        // 1. Calculate working days
        $workingDays = $this->calculateWorkingDays($activeSession);

        // 2. Today's Events
        $todayString = now()->toDateString();
        $todayEntries = $this->sessionId
            ? AcademicCalendarEntry::with(['class', 'session'])
                ->where('session_id', $this->sessionId)
                ->where('status', 'published')
                ->where(function ($q) use ($todayString) {
                    $q->where(fn($sub) => $sub->where('start_date', '<=', $todayString)->where('end_date', '>=', $todayString))
                      ->orWhere(fn($sub) => $sub->where('start_date', $todayString)->whereNull('end_date'));
                })
                ->get()
            : collect();

        // 3. Next 3 Upcoming Events starting after today
        $upcomingEntries = $this->sessionId
            ? AcademicCalendarEntry::with(['class', 'session'])
                ->where('session_id', $this->sessionId)
                ->where('status', 'published')
                ->where('start_date', '>', $todayString)
                ->orderBy('start_date', 'asc')
                ->take(3)
                ->get()
            : collect();

        return view('livewire.academic-calendar-view', compact(
            'sessions',
            'activeSession',
            'workingDays',
            'todayEntries',
            'upcomingEntries'
        ));
    }

    protected function calculateWorkingDays($session)
    {
        if (!$session) {
            return ['total' => 0, 'working' => 0, 'non_working' => 0];
        }

        $query = AcademicCalendarEntry::where('session_id', $session->id)
            ->where('status', 'published');

        // Default to current year month or session limits
        $start = Carbon::parse($this->currentYearMonth . '-01')->startOfDay();
        $startDate = $start->copy()->startOfMonth()->startOfDay();
        $endDate = $start->copy()->endOfMonth()->startOfDay();

        // Make sure we clamp it to session dates
        if ($startDate->lt($session->start_date)) {
            $startDate = Carbon::parse($session->start_date)->startOfDay();
        }
        if ($endDate->gt($session->end_date)) {
            $endDate = Carbon::parse($session->end_date)->startOfDay();
        }

        if ($startDate->gt($endDate)) {
            return ['total' => 0, 'working' => 0, 'non_working' => 0];
        }

        // Count calendar days in this range using clean integers
        $totalDays = (int) $startDate->diffInDays($endDate) + 1;

        // Get holiday or vacation entry days that are non-working
        $nonWorkingEntries = AcademicCalendarEntry::where('session_id', $session->id)
            ->where('status', 'published')
            ->where('is_working_day', false)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate->toDateString(), $endDate->toDateString()])
                  ->orWhereBetween('end_date', [$startDate->toDateString(), $endDate->toDateString()])
                  ->orWhere(function ($sub) use ($startDate, $endDate) {
                      $sub->where('start_date', '<=', $startDate->toDateString())
                          ->where('end_date', '>=', $endDate->toDateString());
                  });
            })
            ->get();

        // Calculate specific dates that are marked as non-working
        $nonWorkingDates = [];
        foreach ($nonWorkingEntries as $entry) {
            $start = Carbon::parse($entry->start_date)->startOfDay();
            $end = Carbon::parse($entry->end_date ?? $entry->start_date)->startOfDay();
            
            // Clamp start and end to calculation boundaries
            $start = $start->max($startDate);
            $end = $end->min($endDate);
            
            while ($start->lte($end)) {
                $nonWorkingDates[$start->toDateString()] = true;
                $start->addDay();
            }
        }

        $nonWorkingDaysCount = count($nonWorkingDates);
        $workingDaysCount = $totalDays - $nonWorkingDaysCount;

        // Count recurring holidays: all Sundays and 2nd Saturdays of the month
        $recurringHolidays = 0;
        $tempDate = $startDate->copy()->startOfDay();
        $endLimit = $endDate->copy()->startOfDay();
        while ($tempDate->lte($endLimit)) {
            $isSunday = $tempDate->isSunday();
            $isSecondSaturday = $tempDate->isSaturday() && ($tempDate->day >= 8 && $tempDate->day <= 14);

            if (($isSunday || $isSecondSaturday) && !isset($nonWorkingDates[$tempDate->toDateString()])) {
                $recurringHolidays++;
            }
            $tempDate->addDay();
        }

        $workingDaysCount = (int) max(0, $workingDaysCount - $recurringHolidays);
        $nonWorkingDaysCount += $recurringHolidays;

        return [
            'total' => (int) $totalDays,
            'working' => (int) $workingDaysCount,
            'non_working' => (int) $nonWorkingDaysCount
        ];
    }
}
