<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — FAQ / Accordion.
 * Features animated collapsible accordions with smooth toggle & icons.
 */
class FaqAccordionWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-accordion';
    }

    public function label(): string
    {
        return 'Accordion / FAQ';
    }

    public function category(): string
    {
        return 'pro-advanced';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Frequently Asked Questions',
            'heading' => 'Everything You Need to Know',
            'sub'     => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Find quick answers to common queries.',
            'items'   => [
                [
                    'question' => 'What is the admission procedure for Academic Session 2026-27?',
                    'answer'   => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Admissions can be completed online via our Online Registration Portal or by visiting the school campus during working hours (8:00 AM – 3:00 PM). Parents are required to submit the birth certificate, previous school report card, and residence proof.',
                ],
                [
                    'question' => 'What safety and security measures are available on campus?',
                    'answer'   => 'Our campus is monitored 24/7 with over 150 HD CCTV cameras, trained security personnel at all entry gates, GPS-enabled buses with real-time tracking, and a dedicated medical infirmary with a certified full-time nurse.',
                ],
                [
                    'question' => 'What sports and extra-curricular activities are offered?',
                    'answer'   => 'We offer a wide range of activities including Karate, Swimming, Basketball, Lawn Tennis, Cricket Net Practice, Music (Vocal & Instrumental), Classical Dance, Robotics Club, and MUN debating society.',
                ],
                [
                    'question' => 'Is transport facility available for all major sectors and nearby areas?',
                    'answer'   => 'Yes, the school operates a comprehensive fleet of air-conditioned buses covering all major sectors in Panipat and surrounding areas with verified drivers, female bus attendants, and live GPS location sharing for parents.',
                ],
                [
                    'question' => 'What are the school timings for Primary and Senior wings?',
                    'answer'   => 'Pre-Primary & Primary Wing (Nursery to Class V): 8:00 AM to 1:30 PM. Middle & Senior Wing (Class VI to XII): 8:00 AM to 2:30 PM. Administrative office remains open till 4:00 PM on all working days.',
                ],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading'),
            $this->setting($settings, 'sub')
        );

        $accordionItems = '';
        $items = (array) $this->setting($settings, 'items', []);

        foreach ($items as $idx => $item) {
            $q = $this->e($item['question'] ?? 'Question');
            $a = $this->e($item['answer'] ?? 'Answer content');
            $activeClass = $idx === 0 ? 'ek-acc-item active' : 'ek-acc-item';

            $accordionItems .= <<<HTML
            <div class="{$activeClass}">
                <button type="button" class="ek-acc-header" onclick="this.parentElement.classList.toggle('active')">
                    <span class="ek-acc-title">{$q}</span>
                    <span class="ek-acc-icon"></span>
                </button>
                <div class="ek-acc-body">
                    <div class="ek-acc-content">{$a}</div>
                </div>
            </div>
            HTML;
        }

        return <<<HTML
        <style>
        .ek-acc-wrapper { max-width: 900px; margin: 30px auto 0; padding: 0 16px; }
        .ek-acc-item { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 12px; margin-bottom: 12px; overflow: hidden; transition: all .25s ease; }
        .ek-acc-item:hover { border-color: #c79a3b; }
        .ek-acc-item.active { border-color: #0b2545; box-shadow: 0 8px 24px rgba(11,37,69,.08); }
        .ek-acc-header { width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 18px 22px; background: none; border: none; text-align: left; cursor: pointer; color: #0b2545; font-family: inherit; }
        .ek-acc-title { font-size: 16px; font-weight: 600; line-height: 1.4; }
        .ek-acc-icon { width: 24px; height: 24px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; position: relative; flex-shrink: 0; transition: transform .3s; }
        .ek-acc-icon::before, .ek-acc-icon::after { content: ''; position: absolute; background: #0b2545; transition: transform .25s ease; }
        .ek-acc-icon::before { width: 10px; height: 2px; }
        .ek-acc-icon::after { width: 2px; height: 10px; }
        .ek-acc-item.active .ek-acc-icon { background: #0b2545; transform: rotate(180deg); }
        .ek-acc-item.active .ek-acc-icon::before { background: #ffffff; }
        .ek-acc-item.active .ek-acc-icon::after { transform: rotate(90deg); opacity: 0; }
        .ek-acc-body { max-height: 0; overflow: hidden; transition: max-height .35s ease, padding .35s ease; }
        .ek-acc-item.active .ek-acc-body { max-height: 400px; }
        .ek-acc-content { padding: 0 22px 20px; font-size: 14.5px; color: #475569; line-height: 1.65; border-top: 1px solid #f1f5f9; margin-top: 4px; padding-top: 14px; }
        </style>

        <section class="ek-acc-sec">
            {$head}
            <div class="ek-acc-wrapper">
                {$accordionItems}
            </div>
        </section>
        HTML;
    }
}
