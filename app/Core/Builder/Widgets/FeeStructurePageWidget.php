<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Full-page Fee Structure ("Transparent Academic Investment 2026-27") Widget.
 * Features:
 *  - Hero Banner with direct Online Registration & Payment CTAs
 *  - Grade-Wise Tuition Fee Table (Pre-Nursery to XII)
 *  - One-Time & Refundable Security Charges Table
 *  - Interactive Fee Estimator Calculator for Parents
 *  - Verified Official Policy Notes & Quality Education Commitment
 *  - 1-Click PDF Download buttons (Fee Structure & Transport Fee)
 *  - 100% Editable in Page Builder Settings
 */
class FeeStructurePageWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'fee-structure-page';
    }

    public function label(): string
    {
        return 'Fee Structure Showcase (Full Page)';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'hero_eyebrow'           => 'Academic Session 2026-27 · Transparent Investment',
            'hero_title'             => 'Fee Structure 2026-27',
            'hero_subtitle'          => 'Explore the comprehensive fee schedule for the academic year 2026-27 at Prayaag International School, Panipat. Transparent, inclusive, and structured to support all-round academic, sports, and holistic development.',
            'hero_bg_image'          => '/images/classrooms/classroom-main.jpg',

            'registration_url'       => '/registration',
            'online_payment_url'     => 'https://pisp.accevate.com/online/main',
            'pdf_fee_url'            => '/docs/Fee_Structure_2026-27.pdf',
            'pdf_transport_url'      => '/docs/Transport_Fee_Structure-2026-27.pdf',

            // Key Transparency Highlights (4 items)
            'highlights'             => [
                ['icon' => '🎓', 'title' => 'Inclusive Academic Facilities', 'desc' => 'Tuition fee covers smart classrooms, computer labs, library, and core curriculum.'],
                ['icon' => '💳', 'title' => 'Flexible Payment Terms', 'desc' => 'Fees payable in advance on a quarterly, bi-annual, or annual cycle as per school policy.'],
                ['icon' => '🛡️', 'title' => '100% Refundable Security', 'desc' => 'Security deposit is fully refundable at the end of the student’s tenure at Prayaag.'],
                ['icon' => '📲', 'title' => 'Digital Portal & Instant Receipts', 'desc' => 'Secure online portal integration for credit card, debit card, UPI, and NetBanking.'],
            ],

            // Grade-wise Tuition Fee Table (exact values)
            'tuition_fees'           => [
                ['grade' => 'Pre Nursery – I', 'monthly' => 7250, 'annual' => 87000],
                ['grade' => 'II – V',          'monthly' => 7750, 'annual' => 93000],
                ['grade' => 'VI – VIII',       'monthly' => 8000, 'annual' => 96000],
                ['grade' => 'IX – X',          'monthly' => 8500, 'annual' => 102000],
                ['grade' => 'XI – XII',        'monthly' => 8750, 'annual' => 105000],
            ],

            // One-Time & Other Charges Table (exact values)
            'other_charges'          => [
                ['charge' => 'Registration', 'type' => 'One Time (at the time of Admission)', 'amount' => 1000],
                ['charge' => 'Security', 'type' => 'Refundable (at the end of tenure)', 'amount' => 10000],
                ['charge' => 'Admission Charges', 'type' => 'One Time', 'amount' => 20000],
            ],

            // Official Policy Notes
            'notes'                  => [
                'The above fee structure is inclusive of all academic facilities and does not cover additional activities or special programs.',
                'Transportation, books and uniform have separate charges.',
                'The Security fee is refundable at the end of the student\'s tenure at the school.',
                'All fees are payable in advance, on a quarterly, bi-annual, or annual basis, as per the school\'s policy.',
            ],

            // Closing Commitment Statement
            'closing_title'          => 'Investing in Quality Education',
            'closing_text'           => 'We believe that quality education is an investment in the future. Prayaag International School, Panipat is committed to providing an enriching learning experience for your child. For any clarification or further assistance regarding the fee structure or any other aspect of the school, please feel free to reach out to our administration. We are here to support you in every step of your child\'s educational journey.',

            // Help & Contact Info
            'admin_phone'            => '+919350748851',
            'admin_email'            => 'info@prayaagschool.com',
            'admin_hours'            => 'Mon - Sat : 08:00 AM - 03:30 PM',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $merged = array_merge($this->defaultSettings(), $settings);

        return view('widgets.fee-structure-page', [
            'settings' => $merged,
        ])->render();
    }
}
