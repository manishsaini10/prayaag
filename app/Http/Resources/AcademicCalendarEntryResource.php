<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcademicCalendarEntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $colors = [
            'exam' => [
                'bg'     => '#fef3c7',
                'border' => '#fcd34d',
                'text'   => '#92400e',
            ],
            'holiday' => [
                'bg'     => '#fee2e2',
                'border' => '#fca5a5',
                'text'   => '#991b1b',
            ],
            'important_date' => [
                'bg'     => '#dbeafe',
                'border' => '#93c5fd',
                'text'   => '#1e40af',
            ],
            'working_day_note' => [
                'bg'     => '#f3f4f6',
                'border' => '#e5e7eb',
                'text'   => '#374151',
            ],
        ];

        $category = $this->category;
        $colorScheme = $colors[$category] ?? $colors['working_day_note'];

        // FullCalendar v6 dayGridMonth treats the end date of all-day events as exclusive.
        // E.g. if start_date = '2026-07-15' and end_date = '2026-07-15', to show it spanning the 15th,
        // we specify end = '2026-07-16'.
        $formattedEnd = null;
        if ($this->end_date) {
            $formattedEnd = $this->end_date->addDay()->toDateString();
        } else {
            $formattedEnd = $this->start_date->addDay()->toDateString();
        }

        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'start'           => $this->start_date->toDateString(),
            'end'             => $formattedEnd,
            'allDay'          => true,
            'backgroundColor' => $colorScheme['bg'],
            'borderColor'     => $colorScheme['border'],
            'textColor'       => $colorScheme['text'],
            'extendedProps'   => [
                'id'             => $this->id,
                'category'       => $this->category,
                'category_label' => str_replace('_', ' ', ucfirst($this->category)),
                'sub_type'       => $this->sub_type,
                'description'    => $this->description,
                'start_date_raw' => $this->start_date->toDateString(),
                'end_date_raw'   => $this->end_date ? $this->end_date->toDateString() : null,
                'class_name'     => $this->class ? $this->class->class_name : null,
                'is_working_day' => $this->is_working_day,
                'color_tag'      => $this->color_tag,
                'attachment'     => $this->attachment ? asset('storage/' . $this->attachment) : null,
                'attachment_raw' => $this->attachment,
                'session_name'   => $this->session ? $this->session->session_name : null,
                'term_name'      => $this->term ? $this->term->term_name : null,
            ],
        ];
    }
}
