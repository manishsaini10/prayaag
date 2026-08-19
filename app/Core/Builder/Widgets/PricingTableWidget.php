<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Interactive Pricing / Fee Structure Table.
 * Features customizable plan cards, featured highlighting, feature checklist,
 * and responsive grid.
 */
class PricingTableWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-pricing';
    }

    public function label(): string
    {
        return 'Pricing Table';
    }

    public function category(): string
    {
        return 'pro-features';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Investment in Future',
            'heading' => 'Transparent Fee & Membership Plans',
            'sub'     => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Choose the best plan tailored for your needs.',
            'plans'   => [
                [
                    'name'        => 'Primary Wing (KG - V)',
                    'badge'       => 'Popular',
                    'price'       => '₹4,500',
                    'period'      => '/ month',
                    'featured'    => '0',
                    'button_text' => 'Apply for Primary',
                    'button_url'  => '/admissions',
                    'features'    => [
                        'Interactive Smart Classrooms',
                        'Activity-based Experiential Learning',
                        'Sports & Music Training Included',
                        'Regular Parent-Teacher Interactions',
                        'GPS Tracked Bus Transport',
                    ],
                ],
                [
                    'name'        => 'Middle Wing (VI - VIII)',
                    'badge'       => 'Most Popular',
                    'price'       => '₹5,800',
                    'period'      => '/ month',
                    'featured'    => '1',
                    'button_text' => 'Enroll in Middle Wing',
                    'button_url'  => '/admissions',
                    'features'    => [
                        'Advanced STEM & Robotics Labs',
                        'Comprehensive Sports Coaching',
                        'Olympiad & Quiz Preparation',
                        'Foreign Language Classes (French/German)',
                        'Personalized Student Mentorship',
                    ],
                ],
                [
                    'name'        => 'Senior Wing (IX - XII)',
                    'badge'       => 'Academic Excellence',
                    'price'       => '₹7,200',
                    'period'      => '/ month',
                    'featured'    => '0',
                    'button_text' => 'Apply for Senior Wing',
                    'button_url'  => '/admissions',
                    'features'    => [
                        'Integrated JEE / NEET Coaching',
                        'State-of-the-Art Science & Tech Labs',
                        'Career Counselling & Psychometric Tests',
                        'Leadership & MUN Conferences',
                        '100% Digital Resource Access',
                    ],
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

        $cardsHtml = '';
        $plans = (array) $this->setting($settings, 'plans', []);

        foreach ($plans as $p) {
            $name       = $this->e($p['name'] ?? 'Plan Name');
            $badge      = $this->e($p['badge'] ?? '');
            $price      = $this->e($p['price'] ?? '₹0');
            $period     = $this->e($p['period'] ?? '');
            $btnText    = $this->e($p['button_text'] ?? 'Get Started');
            $btnUrl     = $this->e($p['button_url'] ?? '#');
            $isFeatured = ! empty($p['featured']) && $p['featured'] !== '0';

            $badgeHtml = $badge ? '<span class="ek-price-badge">' . $badge . '</span>' : '';
            $cardClass = $isFeatured ? 'ek-price-card featured' : 'ek-price-card';

            $featuresList = '';
            foreach ((array) ($p['features'] ?? []) as $feat) {
                $featuresList .= '<li><svg class="ek-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg> ' . $this->e($feat) . '</li>';
            }

            $cardsHtml .= <<<HTML
            <div class="{$cardClass}">
                {$badgeHtml}
                <div class="ek-price-header">
                    <h3 class="ek-plan-title">{$name}</h3>
                    <div class="ek-price-val">
                        <span class="ek-amount">{$price}</span>
                        <span class="ek-period">{$period}</span>
                    </div>
                </div>
                <ul class="ek-features">
                    {$featuresList}
                </ul>
                <div class="ek-price-footer">
                    <a href="{$btnUrl}" class="ek-btn">{$btnText} →</a>
                </div>
            </div>
            HTML;
        }

        return <<<HTML
        <style>
        .ek-price-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; max-width: 1140px; margin: 30px auto 0; padding: 0 16px; }
        .ek-price-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 16px; padding: 32px 24px; position: relative; transition: all .3s cubic-bezier(.2,.7,.2,1); display: flex; flex-direction: column; }
        .ek-price-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(11,37,69,.12); border-color: #c79a3b; }
        .ek-price-card.featured { border-color: #0b2545; background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%); box-shadow: 0 12px 36px rgba(11,37,69,.15); transform: scale(1.03); }
        .ek-price-badge { position: absolute; top: -14px; right: 24px; background: linear-gradient(135deg, #c79a3b, #e0b94e); color: #0b2545; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; padding: 4px 14px; border-radius: 999px; box-shadow: 0 4px 12px rgba(199,154,59,.3); }
        .ek-plan-title { font-size: 20px; font-weight: 700; color: #0b2545; margin: 0 0 12px; }
        .ek-price-val { display: flex; align-items: baseline; gap: 4px; margin-bottom: 24px; }
        .ek-amount { font-size: 38px; font-weight: 800; color: #0b2545; line-height: 1; }
        .ek-period { font-size: 14px; color: #64748b; font-weight: 500; }
        .ek-features { list-style: none; padding: 0; margin: 0 0 28px; flex: 1; border-t: 1px solid #e2e8f0; padding-top: 20px; }
        .ek-features li { display: flex; align-items: center; gap: 10px; font-size: 14px; color: #334155; margin-bottom: 12px; line-height: 1.4; }
        .ek-check { width: 18px; height: 18px; color: #16a34a; flex-shrink: 0; }
        .ek-btn { display: inline-block; width: 100%; text-align: center; background: #0b2545; color: #ffffff; padding: 12px 20px; border-radius: 10px; font-weight: 600; font-size: 14px; text-decoration: none; transition: all .2s; }
        .ek-btn:hover { background: #c79a3b; color: #0b2545; box-shadow: 0 6px 18px rgba(199,154,59,.35); }
        .featured .ek-btn { background: linear-gradient(135deg, #0b2545, #1c3a6e); }
        @media(max-width: 768px) { .ek-price-card.featured { transform: none; } }
        </style>

        <section class="ek-pricing-sec">
            {$head}
            <div class="ek-price-grid">
                {$cardsHtml}
            </div>
        </section>
        HTML;
    }
}
