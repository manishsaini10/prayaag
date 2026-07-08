<?php

namespace App\Core\Popup\Services;

use App\Core\Popup\DTOs\TemplateDTO;
use App\Models\Popup\PopupTemplate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class TemplateService
{
    public function __construct(
        private readonly PopupTemplate $model,
    ) {}

    public function all(): Collection
    {
        return Cache::remember('popup:templates', 86400, function () {
            return $this->model->where('is_active', true)
                ->orderBy('category')
                ->orderBy('name')
                ->get();
        });
    }

    public function findByCategory(string $category): Collection
    {
        return $this->all()->where('category', $category);
    }

    public function find(string $id): ?PopupTemplate
    {
        return $this->model->find($id);
    }

    public function findBySlug(string $slug): ?PopupTemplate
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function create(TemplateDTO $dto): PopupTemplate
    {
        $template = $this->model->create($dto->toArray());
        Cache::forget('popup:templates');
        return $template;
    }

    public function delete(PopupTemplate $template): bool
    {
        $result = $template->delete();
        Cache::forget('popup:templates');
        return $result;
    }

    public function getBuiltInTemplates(): array
    {
        return $this->model->where('is_built_in', true)->where('is_active', true)->get()->toArray();
    }

    public function seedDefaults(): void
    {
        $defaults = $this->getDefaultTemplates();
        foreach ($defaults as $template) {
            $this->model->firstOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }

    private function getDefaultTemplates(): array
    {
        return [
            [
                'name' => 'Newsletter Subscribe',
                'slug' => 'newsletter-subscribe',
                'type' => 'modal',
                'category' => 'Newsletter',
                'is_built_in' => true,
                'is_premium' => false,
                'structure' => [
                    'container' => ['type' => 'container', 'styles' => ['padding' => '40', 'background' => '#ffffff', 'borderRadius' => '12']],
                    'rows' => [
                        ['columns' => [['widgets' => [
                            ['type' => 'heading', 'content' => 'Subscribe to Our Newsletter', 'settings' => ['tag' => 'h2', 'align' => 'center', 'fontSize' => '28', 'color' => '#1e293b']],
                            ['type' => 'paragraph', 'content' => 'Get the latest updates and offers directly in your inbox.', 'settings' => ['align' => 'center', 'fontSize' => '16', 'color' => '#64748b']],
                            ['type' => 'spacer', 'settings' => ['height' => '20']],
                            ['type' => 'newsletter_form', 'settings' => ['buttonText' => 'Subscribe', 'buttonColor' => '#6366f1', 'placeholder' => 'Enter your email']],
                        ]]]]
                    ]
                ],
                'settings' => ['width' => '500', 'overlay' => true, 'close_button' => true, 'animation' => 'fade'],
            ],
            [
                'name' => 'Exit Intent Offer',
                'slug' => 'exit-intent-offer',
                'type' => 'exit_intent',
                'category' => 'Exit Popup',
                'is_built_in' => true,
                'is_premium' => false,
                'structure' => [
                    'container' => ['type' => 'container', 'styles' => ['padding' => '30', 'background' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'borderRadius' => '8']],
                    'rows' => [
                        ['columns' => [['widgets' => [
                            ['type' => 'heading', 'content' => "Wait! Don't Go!", 'settings' => ['tag' => 'h2', 'align' => 'center', 'fontSize' => '32', 'color' => '#ffffff']],
                            ['type' => 'paragraph', 'content' => 'Get 20% off your first order. This offer won\'t last long!', 'settings' => ['align' => 'center', 'fontSize' => '18', 'color' => '#e2e8f0']],
                            ['type' => 'spacer', 'settings' => ['height' => '20']],
                            ['type' => 'button', 'content' => 'Claim Offer Now', 'settings' => ['align' => 'center', 'backgroundColor' => '#f59e0b', 'textColor' => '#ffffff', 'borderRadius' => '50', 'padding' => '14 32', 'fontSize' => '18']],
                        ]]]]
                    ]
                ],
                'settings' => ['width' => '550', 'overlay' => true, 'close_button' => true, 'animation' => 'slide'],
            ],
            [
                'name' => 'Admission Open',
                'slug' => 'admission-open',
                'type' => 'admission',
                'category' => 'Admission',
                'is_built_in' => true,
                'is_premium' => false,
                'structure' => [
                    'container' => ['type' => 'container', 'styles' => ['padding' => '40', 'background' => '#ffffff', 'borderRadius' => '16', 'border' => '2 solid #6366f1']],
                    'rows' => [
                        ['columns' => [['widgets' => [
                            ['type' => 'image', 'settings' => ['src' => '/images/admission-badge.svg', 'width' => '80', 'align' => 'center']],
                            ['type' => 'heading', 'content' => 'Admissions Open 2026-27', 'settings' => ['tag' => 'h2', 'align' => 'center', 'fontSize' => '28', 'color' => '#1e293b']],
                            ['type' => 'paragraph', 'content' => 'Apply now for the upcoming academic session. Limited seats available.', 'settings' => ['align' => 'center', 'fontSize' => '16', 'color' => '#64748b']],
                            ['type' => 'countdown', 'settings' => ['targetDate' => '2026-12-31', 'align' => 'center', 'labelColor' => '#6366f1', 'valueColor' => '#1e293b']],
                            ['type' => 'spacer', 'settings' => ['height' => '20']],
                            ['type' => 'button', 'content' => 'Apply Now', 'settings' => ['align' => 'center', 'backgroundColor' => '#6366f1', 'textColor' => '#ffffff', 'borderRadius' => '8', 'padding' => '14 40', 'fontSize' => '16', 'url' => '/admissions']],
                        ]]]]
                    ]
                ],
                'settings' => ['width' => '550', 'overlay' => true, 'close_button' => true, 'animation' => 'zoom'],
            ],
            [
                'name' => 'Cookie Consent',
                'slug' => 'cookie-consent',
                'type' => 'floating_bar',
                'category' => 'Compliance',
                'is_built_in' => true,
                'is_premium' => false,
                'structure' => [
                    'container' => ['type' => 'container', 'styles' => ['padding' => '16 24', 'background' => '#1e293b', 'borderRadius' => '0']],
                    'rows' => [
                        ['columns' => [
                            ['width' => 8, 'widgets' => [
                                ['type' => 'paragraph', 'content' => '🍪 We use cookies to enhance your experience. By continuing, you agree to our use of cookies.', 'settings' => ['fontSize' => '14', 'color' => '#cbd5e1']],
                            ]],
                            ['width' => 4, 'widgets' => [
                                ['type' => 'button', 'content' => 'Accept All', 'settings' => ['align' => 'right', 'backgroundColor' => '#6366f1', 'textColor' => '#ffffff', 'borderRadius' => '6', 'padding' => '8 20', 'fontSize' => '14']],
                                ['type' => 'button', 'content' => 'Settings', 'settings' => ['align' => 'right', 'backgroundColor' => 'transparent', 'textColor' => '#94a3b8', 'borderRadius' => '6', 'padding' => '8 20', 'fontSize' => '14', 'border' => '1 solid #475569']],
                            ]],
                        ]]
                    ]
                ],
                'settings' => ['position' => 'bottom-center', 'width' => '100%', 'overlay' => false, 'close_button' => false, 'animation' => 'slide', 'frequency_type' => 'once_per_session'],
            ],
            [
                'name' => 'Fee Reminder',
                'slug' => 'fee-reminder',
                'type' => 'fee_reminder',
                'category' => 'School',
                'is_built_in' => true,
                'is_premium' => false,
                'structure' => [
                    'container' => ['type' => 'container', 'styles' => ['padding' => '30', 'background' => '#fef2f2', 'borderRadius' => '12', 'border' => '1 solid #fecaca']],
                    'rows' => [
                        ['columns' => [['widgets' => [
                            ['type' => 'icon', 'settings' => ['icon' => 'bell', 'color' => '#dc2626', 'size' => '48', 'align' => 'center']],
                            ['type' => 'heading', 'content' => 'Fee Payment Reminder', 'settings' => ['tag' => 'h3', 'align' => 'center', 'fontSize' => '24', 'color' => '#991b1b']],
                            ['type' => 'paragraph', 'content' => 'Your tuition fee for this month is due. Please pay before the due date to avoid late fees.', 'settings' => ['align' => 'center', 'fontSize' => '15', 'color' => '#7f1d1d']],
                            ['type' => 'spacer', 'settings' => ['height' => '16']],
                            ['type' => 'button', 'content' => 'Pay Online Now', 'settings' => ['align' => 'center', 'backgroundColor' => '#dc2626', 'textColor' => '#ffffff', 'borderRadius' => '8', 'padding' => '12 32', 'fontSize' => '16']],
                        ]]]]
                    ]
                ],
                'settings' => ['width' => '480', 'overlay' => true, 'close_button' => true, 'animation' => 'fade'],
            ],
            [
                'name' => 'Event Registration',
                'slug' => 'event-registration',
                'type' => 'registration',
                'category' => 'Events',
                'is_built_in' => true,
                'is_premium' => false,
                'structure' => [
                    'container' => ['type' => 'container', 'styles' => ['padding' => '35', 'background' => '#f0f9ff', 'borderRadius' => '12']],
                    'rows' => [
                        ['columns' => [['widgets' => [
                            ['type' => 'heading', 'content' => 'Annual Sports Day 2026', 'settings' => ['tag' => 'h2', 'align' => 'center', 'fontSize' => '26', 'color' => '#0369a1']],
                            ['type' => 'paragraph', 'content' => 'Register your child for the Annual Sports Day event. Last date: 15 Dec 2026', 'settings' => ['align' => 'center', 'fontSize' => '15', 'color' => '#475569']],
                            ['type' => 'spacer', 'settings' => ['height' => '16']],
                            ['type' => 'registration_form', 'settings' => ['buttonText' => 'Register Now', 'fields' => 'name,email,phone,class', 'buttonColor' => '#0284c7']],
                        ]]]]
                    ]
                ],
                'settings' => ['width' => '520', 'overlay' => true, 'close_button' => true, 'animation' => 'zoom'],
            ],
            [
                'name' => 'Holiday Notice',
                'slug' => 'holiday-notice',
                'type' => 'holiday',
                'category' => 'School',
                'is_built_in' => true,
                'is_premium' => false,
                'structure' => [
                    'container' => ['type' => 'container', 'styles' => ['padding' => '30', 'background' => '#fffbeb', 'borderRadius' => '12', 'border' => '1 solid #fde68a']],
                    'rows' => [
                        ['columns' => [['widgets' => [
                            ['type' => 'icon', 'settings' => ['icon' => 'sun', 'color' => '#f59e0b', 'size' => '56', 'align' => 'center']],
                            ['type' => 'heading', 'content' => 'Summer Vacation Notice', 'settings' => ['tag' => 'h2', 'align' => 'center', 'fontSize' => '28', 'color' => '#92400e']],
                            ['type' => 'paragraph', 'content' => 'School will remain closed for summer break from 20 May to 30 June 2026. Re-opening on 1 July 2026.', 'settings' => ['align' => 'center', 'fontSize' => '16', 'color' => '#78350f']],
                            ['type' => 'countdown', 'settings' => ['targetDate' => '2026-07-01', 'align' => 'center', 'label' => 'Re-opening in']],
                        ]]]]
                    ]
                ],
                'settings' => ['width' => '500', 'overlay' => true, 'close_button' => true, 'animation' => 'slide'],
            ],
        ];
    }
}
