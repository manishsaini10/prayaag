<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Transportation Page Widget — GPS Bus Routes, Safety Protocols & Fleet Coverage
 */
class TransportationPageWidget extends AbstractWidget
{
    public function type(): string { return 'transportation-page'; }
    public function label(): string { return 'Transportation & Fleet Safety (Full Page)'; }
    public function category(): string { return 'school'; }

    public function defaultSettings(): array
    {
        return [
            'hero_title'    => 'Safe & Reliable School Transportation',
            'hero_subtitle' => 'The need for safe passage of each and every child to school and back home is of utmost importance to us. Our modern AC bus fleet, equipped with CCTV and trained attendants, ensures every child arrives safely.',
            'hero_bg'       => '/storage/media/imported/01KTQWWXGF62TW89BPJN4T592J.webp',

            'highlights' => [
                ['icon' => '🚌', 'stat' => '30+', 'label' => 'Air-Conditioned Buses'],
                ['icon' => '📹', 'stat' => 'CCTV', 'label' => 'On Every Bus'],
                ['icon' => '🗺️', 'stat' => 'GPS', 'label' => 'Live Tracking'],
                ['icon' => '👮', 'stat' => 'Trained', 'label' => 'Safety Attendants'],
            ],

            'features' => [
                [
                    'icon'  => '❄️',
                    'title' => 'Air-Conditioned Fleet',
                    'desc'  => 'All school buses are fully air-conditioned for comfortable travel in all weather conditions — summer, winter, and monsoon.',
                ],
                [
                    'icon'  => '📹',
                    'title' => 'CCTV Cameras on Every Bus',
                    'desc'  => 'All buses are equipped with high-resolution CCTV cameras. Live feeds are monitored by the school administration for complete accountability.',
                ],
                [
                    'icon'  => '🗺️',
                    'title' => 'Real-Time GPS Tracking',
                    'desc'  => 'GPS tracking devices on every bus allow parents to monitor their child\'s bus location in real-time via the school\'s transport management portal.',
                ],
                [
                    'icon'  => '👮',
                    'title' => 'Trained Bus Attendants',
                    'desc'  => 'Every bus has a trained transport attendant (male & female) on board throughout the journey to supervise students and ensure discipline and safety.',
                ],
                [
                    'icon'  => '🚦',
                    'title' => 'Certified, Experienced Drivers',
                    'desc'  => 'All bus drivers hold valid commercial licenses and undergo background verification, defensive driving training, and annual medical fitness checks.',
                ],
                [
                    'icon'  => '📱',
                    'title' => 'Parent SMS & App Alerts',
                    'desc'  => 'Automated SMS notifications and app alerts are sent when buses depart from school and at key route stops, keeping parents always informed.',
                ],
            ],

            'coverage_note' => 'Our bus routes cover all major sectors of Panipat city, Samalkha, and surrounding areas including NH-44 corridor. New routes are added based on demand — contact the transport office for coverage details.',

            'safety_measures' => [
                'Speed limiters installed on all buses (max 40 km/h in city)',
                'Monthly maintenance checks and fitness certificates',
                'First Aid Kit and fire extinguisher on every bus',
                'Strict no-mobile-phone policy for drivers while driving',
                'School gate entry only via verified parent/guardian ID',
            ],

            'transport_contact'  => '+91 93507 48851',
            'pdf_url'            => '/docs/Transport_Fee_Structure-2026-27.pdf',
        ];
    }

    public function settingsSchema(): array
    {
        return [
            ['key' => 'hero_title',         'label' => 'Hero Title',          'type' => 'text'],
            ['key' => 'hero_subtitle',       'label' => 'Hero Subtitle',       'type' => 'textarea'],
            ['key' => 'coverage_note',       'label' => 'Coverage Note',       'type' => 'textarea'],
            ['key' => 'transport_contact',   'label' => 'Transport Phone',     'type' => 'text'],
            ['key' => 'pdf_url',             'label' => 'Transport Fee PDF URL','type' => 'text'],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        return view('widgets.transportation-page', $settings)->render();
    }
}
