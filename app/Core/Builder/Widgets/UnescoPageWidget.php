<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * UNESCO Page Widget — UNESCO ASPNet Club, Global Citizenship & International Initiatives
 */
class UnescoPageWidget extends AbstractWidget
{
    public function type(): string { return 'unesco-page'; }
    public function label(): string { return 'UNESCO & Global Citizenship (Full Page)'; }
    public function category(): string { return 'school'; }

    public function defaultSettings(): array
    {
        return [
            'hero_title'    => 'UNESCO & Global Citizenship',
            'hero_subtitle' => 'Prayaag International School is proud to host a UNESCO club that actively engages students in the principles of the United Nations Educational, Scientific and Cultural Organization — promoting peace, education, and cultural heritage.',
            'hero_bg'       => '/storage/media/imported/01KWEC2PW5J7Y50P4YSWK7Z82G.jpg',

            'intro' => 'As part of the UNESCO Associated Schools Programme Network (ASPNet), Prayaag International School commits to promoting UNESCO\'s four pillars: Education for Sustainable Development, Learning to Live Together, Cultural Heritage, and Human Rights.',

            'objectives' => [
                'Disseminate the principles set out in the UNESCO Constitution, United Nations Charter, and Universal Declaration of Human Rights.',
                'Participate in celebrations of International Days and Years proclaimed by the UN General Assembly and UNESCO General Conference.',
                'Promote literacy activities and the preservation and presentation of cultural heritage; organise study camps for international students.',
                'Educate and sensitize children for disease prevention, global health, and environmental sustainability.',
                'Foster international understanding and respect for cultural diversity, peace, and non-violence.',
                'Encourage student participation in global forums, Model United Nations (MUN), and UNESCO youth initiatives.',
            ],

            'activities' => [
                [
                    'icon'  => '🕊️',
                    'title' => 'World Peace Day Celebrations',
                    'desc'  => 'Annual peace rallies, candlelight vigils, and pledge ceremonies on September 21st observed with the entire school community.',
                ],
                [
                    'icon'  => '🌍',
                    'title' => 'Cultural Heritage Week',
                    'desc'  => 'A week-long celebration showcasing India\'s rich cultural traditions through folk art, regional cuisine, traditional dress, and live performances.',
                ],
                [
                    'icon'  => '♻️',
                    'title' => 'Environment & Sustainability Drives',
                    'desc'  => 'Tree plantation drives, plastic-free campus campaigns, water conservation workshops, and climate change awareness seminars.',
                ],
                [
                    'icon'  => '📚',
                    'title' => 'Literacy & Human Rights Programs',
                    'desc'  => 'Regular seminars, reading programs, and community outreach activities promoting universal literacy and fundamental human rights education.',
                ],
            ],

            'network_note' => 'UNESCO Associated Schools Programme Network (ASPNet) links over 11,500 schools in 182 countries, making Prayaag students part of a truly global educational community.',
        ];
    }

    public function settingsSchema(): array
    {
        return [
            ['key' => 'hero_title',    'label' => 'Hero Title',    'type' => 'text'],
            ['key' => 'hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea'],
            ['key' => 'intro',         'label' => 'Intro Text',    'type' => 'textarea'],
            ['key' => 'network_note',  'label' => 'Network Note',  'type' => 'textarea'],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        return view('widgets.unesco-page', $settings)->render();
    }
}
