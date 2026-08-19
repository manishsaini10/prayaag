<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Full-page premium Book List & Academic Textbook Catalog Widget.
 * Renders the complete interactive book list catalogue — hero, wing filters,
 * class-by-class syllabus cards, official PDF download center, CBSE compliance declaration,
 * parent FAQs, and academic counseling CTA.
 */
class BookListWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'book-list';
    }

    public function label(): string
    {
        return 'Book List & Syllabus Page (Full)';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'current_session' => '2025–26',
            'whatsapp_number' => '919350748851',
            'academic_phone'  => '+919350748851',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $currentSession = (string) $this->setting($settings, 'current_session', '2025–26');
        $whatsappNumber = (string) $this->setting($settings, 'whatsapp_number', '919350748851');
        $academicPhone  = (string) $this->setting($settings, 'academic_phone', '+919350748851');

        return view('widgets.book-list', compact('currentSession', 'whatsappNumber', 'academicPhone'))->render();
    }
}
