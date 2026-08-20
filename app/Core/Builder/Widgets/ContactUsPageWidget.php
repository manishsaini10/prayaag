<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Full-page premium Contact Us & Campus Location Widget.
 * Renders the complete contact hub — hero, contact cards,
 * interactive message form, verified campus address, Google Maps embed,
 * social media video showcases, and visitor FAQs.
 */
class ContactUsPageWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'contact-us-page';
    }

    public function label(): string
    {
        return 'Contact Us Page (Full)';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'phone'     => '+91 93507 48851',
            'email'     => 'mailus@pisp.in',
            'whatsapp'  => '919350748851',
            'address'   => 'Opp. New Police Lines, Near Indraprastha Institute of Medical Sciences, NH-44, Panipat-132103, Haryana',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $phone    = (string) $this->setting($settings, 'phone', '+91 93507 48851');
        $email    = (string) $this->setting($settings, 'email', 'mailus@pisp.in');
        $whatsapp = (string) $this->setting($settings, 'whatsapp', '919350748851');
        $address  = (string) $this->setting($settings, 'address', 'Opp. New Police Lines, Near Indraprastha Institute of Medical Sciences, NH-44, Panipat-132103, Haryana');

        return view('widgets.contact-us', compact('phone', 'email', 'whatsapp', 'address'))->render();
    }
}
