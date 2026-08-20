<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Full-page Classrooms ("Modern Learning Spaces & Infrastructure") Widget.
 * Features:
 *  - High-impact Hero Showcase
 *  - 4 Key Smart Infrastructure Badges (AC, Digital Boards, 1:25 Ratio, Ergonomic Seating)
 *  - Junior Wing & Senior Wing Interactive Classrooms Tour
 *  - 6 Pedagogical Excellence & Teaching Methodologies Pillars
 *  - High-Resolution Gallery with Zoom Lightbox
 *  - Safety, Hygiene & Air Quality Standards
 *  - Direct Admissions Call to Action
 */
class ClassroomsPageWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'classrooms-page';
    }

    public function label(): string
    {
        return 'Classrooms Showcase (Full Page)';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'hero_eyebrow'      => 'Modern Campus Infrastructure & Learning Spaces',
            'hero_title'        => 'Smart Classrooms & Innovative Learning Environments',
            'hero_subtitle'     => 'Spacious, centralized air-conditioned, and digitally-enabled smart classrooms at Prayaag International School, Panipat — designed with ergonomic seating, interactive digital boards, and an ideal 1:25 teacher-student ratio for focused individual mentorship.',
            'hero_bg_image'     => '/images/classrooms/junior-classroom.webp',

            // Key Infrastructure Stats / Highlights (4 items)
            'highlights'        => [
                ['icon' => '❄️', 'title' => 'Centralized Air-Conditioned', 'desc' => '100% climate-controlled classrooms maintaining optimal temperature and fresh filtered air for uninterrupted focus.'],
                ['icon' => '💻', 'title' => 'Digital Smart Classboards', 'desc' => 'Next-generation interactive touchboards with 3D learning modules, multimedia simulations, and syllabus maps.'],
                ['icon' => '👥', 'title' => '1:25 Optimal Student Ratio', 'desc' => 'Limited batch strength of 25–30 students per section ensuring personalized care and one-on-one attention.'],
                ['icon' => '🪑', 'title' => 'Ergonomic Age-Safe Seating', 'desc' => 'Posture-supportive, rounded-edge modular furniture tailored for each age group from Kindergarten to Grade XII.'],
            ],

            // Junior vs Senior Wings
            'junior_wing_title' => 'Junior Wing Classrooms (Pre-Nursery – Grade II)',
            'junior_wing_desc'  => 'Our Early Years classrooms are vibrant, joyful wonderland spaces equipped with sensory learning stations, Montessori manipulatives, non-toxic activity mats, storytelling circles, and thematic audio-visual phonics corners.',
            'junior_wing_image' => '/images/classrooms/junior-classroom.webp',
            'junior_wing_tags'  => ['Sensory Activity Corners', 'Montessori Tools', 'Play-Way Phonics Stations', 'Soft Flooring & Child Safety'],

            'senior_wing_title' => 'Senior Wing Smart Classrooms (Grade III – Grade XII)',
            'senior_wing_desc'  => 'Designed for collegiate preparation and CBSE academic rigor. Equipped with high-speed digital podiums, hybrid interactive lecture capture, STEM lab integration, and collaborative discussion pods for competitive exam readiness.',
            'senior_wing_image' => '/images/classrooms/classroom-main.jpg',
            'senior_wing_tags'  => ['Smart Digital Touchboards', 'STEM Lab Integration', 'Olympiad & NEET/JEE Prep Pods', 'Acoustic Audio Clarity'],

            // 6 Teaching Methodologies & Pedagogical Pillars
            'methodologies_title' => 'Teaching Methodologies in Classrooms',
            'methodologies_sub'   => 'Empowering every student with student-centric, application-oriented learning frameworks approved by CBSE and international best practices.',
            'methodologies'       => [
                ['icon' => '📝', 'title' => 'MCQs & Adaptive Worksheets', 'desc' => 'Daily comprehension checks and graded practice worksheets reinforcing core foundational concepts.'],
                ['icon' => '🔬', 'title' => 'Virtual Simulation Labs', 'desc' => 'Live digital scientific simulations enabling students to visualize complex physics, chemistry, and biology laws.'],
                ['icon' => '🧠', 'title' => 'Cognitive Mind Maps', 'desc' => 'Visual brainstorming and structured concept diagrams that make memorization and revision intuitive.'],
                ['icon' => '📑', 'title' => 'Topic Synopsis & Flashcards', 'desc' => 'Concise chapter summaries, formula sheets, and key learning milestones provided before each lesson.'],
                ['icon' => '🌍', 'title' => 'Real-World Problem Solving', 'desc' => 'Experiential project-based learning connecting textbook concepts to everyday practical scenarios.'],
                ['icon' => '🎯', 'title' => 'Continuous Formative Review', 'desc' => 'Gentle regular assessments with constructive feedback loops for sustained student confidence.'],
            ],

            // Photo Gallery
            'gallery_title'     => 'Classrooms & Learning Zones Gallery',
            'gallery_images'    => [
                ['image' => '/images/classrooms/junior-classroom.webp', 'caption' => 'Junior Wing Vibrant Smart Classroom'],
                ['image' => '/images/classrooms/classroom-main.jpg', 'caption' => 'Senior Wing High-Tech Digital Classroom'],
                ['image' => '/images/classrooms/senior-classroom.jpg', 'caption' => 'Ergonomic Modular Study Seating & Natural Light'],
                ['image' => '/images/classrooms/campus-overview.webp', 'caption' => 'Spacious Green Campus & Academic Building'],
            ],

            // Safety & Hygiene Standards (3 cards)
            'standards'         => [
                ['icon' => '📹', 'title' => '24x7 CCTV & Access Control', 'desc' => 'Complete high-definition surveillance coverage in all corridors and common spaces for child safety.'],
                ['icon' => '☀️', 'title' => 'Natural Sunlight & Cross Ventilation', 'desc' => 'Expansive architectural windows ensuring abundant natural daylight and high air exchange rates.'],
                ['icon' => '🧯', 'title' => 'Fire & Electrical Safety Certified', 'desc' => 'State-approved fire alarm sensors, emergency evacuation routes, and safe concealed wiring.'],
            ],

            // CTA
            'cta_title'         => 'Experience the Classrooms of Tomorrow at Prayaag',
            'cta_sub'           => 'Schedule a personalized campus tour and see how our smart infrastructure nurtures your child’s academic and creative potential. Admissions open for Academic Session 2026-27.',
            'cta_btn_primary'   => 'Apply for Admission',
            'cta_btn_link'      => '/admissions',
            'cta_btn_secondary' => 'Contact Us',
            'cta_btn_sec_link'  => '/contact-us',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $merged = array_merge($this->defaultSettings(), $settings);

        return view('widgets.classrooms-page', [
            'settings' => $merged,
        ])->render();
    }
}
