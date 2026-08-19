<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — Price Menu / Cafeteria Menu.
 * Dotted connector menu list for canteen, cafeteria or fee schedules.
 */
class PriceMenuWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-price-menu';
    }

    public function label(): string
    {
        return 'Price Menu / Cafeteria List';
    }

    public function category(): string
    {
        return 'pro-features';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Nutrition & Mess Menu',
            'heading' => 'Healthy School Cafeteria Meal Plans',
            'sub'     => 'Lorem ipsum dolor sit amet, offering organic, chef-prepared hygienic meals.',
            'items'   => [
                ['name' => 'Nutritious Breakfast Meal', 'price' => '₹80', 'desc' => 'Fresh fruits, multigrain toast, milk/juice & sprouts', 'badge' => 'Healthy'],
                ['name' => 'Complete Balanced Lunch Thali', 'price' => '₹140', 'desc' => 'Paneer/Dal, Roti, Rice, Seasonal Veg, Curd & Salad', 'badge' => 'Popular'],
                ['name' => 'Evening High-Protein Snack', 'price' => '₹60', 'desc' => 'Vegetable sandwich, baked snacks & fresh lemonade', 'badge' => 'Snacks'],
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

        $items     = (array) $this->setting($settings, 'items', []);
        $itemsHtml = '';

        foreach ($items as $it) {
            $name  = $this->e($it['name'] ?? '');
            $price = $this->e($it['price'] ?? '');
            $desc  = $this->e($it['desc'] ?? '');
            $badge = $this->e($it['badge'] ?? '');

            $badgeHtml = $badge ? '<span class="ek-pm-badge">' . $badge . '</span>' : '';

            $itemsHtml .= <<<HTML
            <div class="ek-pm-item">
                <div class="ek-pm-head">
                    <span class="ek-pm-name">{$name} {$badgeHtml}</span>
                    <span class="ek-pm-dots"></span>
                    <span class="ek-pm-price">{$price}</span>
                </div>
                <div class="ek-pm-desc">{$desc}</div>
            </div>
            HTML;
        }

        return <<<HTML
        <style>
        .ek-pm-container { max-width: 800px; margin: 30px auto 0; padding: 0 16px; }
        .ek-pm-item { margin-bottom: 20px; }
        .ek-pm-head { display: flex; align-items: baseline; gap: 12px; }
        .ek-pm-name { font-size: 16px; font-weight: 700; color: #0b2545; white-space: nowrap; }
        .ek-pm-dots { flex: 1; border-bottom: 2px dotted #cbd5e1; height: 1px; }
        .ek-pm-price { font-size: 18px; font-weight: 800; color: #c79a3b; font-family: ui-monospace, monospace; white-space: nowrap; }
        .ek-pm-desc { font-size: 13.5px; color: #64748b; margin-top: 4px; }
        .ek-pm-badge { background: #fdf6e2; color: #c79a3b; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 4px; text-transform: uppercase; margin-left: 6px; vertical-align: middle; }
        </style>

        <section class="ek-pm-sec">
            {$head}
            <div class="ek-pm-container">
                {$itemsHtml}
            </div>
        </section>
        HTML;
    }
}
