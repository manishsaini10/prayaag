<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Mess\MessMenu;
use App\Models\Mess\MessMenuItem;
use App\Models\Mess\MessMenuSpecialDay;
use App\Core\Mess\Services\MessMenuService;
use Illuminate\Support\Carbon;

echo "Starting import for Mess Menu (17th Aug to 31st Aug, 2026)...\n";

// 1. Deactivate old menus
MessMenu::query()->update(['is_active' => false]);

// 2. Create the active Menu
$menu = MessMenu::create([
    'title' => 'Menu 17th Aug to 31st Aug, 2026',
    'effective_from' => '2026-08-17',
    'effective_to' => '2026-08-31',
    'is_active' => true,
]);

echo "Created Mess Menu ID: {$menu->id}\n";

// 3. Base weekly items (Week 1 / Standard template)
$baseWeeklyLunch = [
    'monday' => ['Kadhai Paneer', 'Dal Fry', 'Steamed Rice', 'Butter Chapati', 'Garden Salad', 'Custard'],
    'tuesday' => ['Mix Veg.', 'Punjabi Rajma', 'Steamed Rice', 'Butter Chapati', 'Raita/Curd', 'Garden Salad'],
    'wednesday' => ['Lazeez Loki', 'Chana Dal Fry', 'Peas Pulao', 'Butter Chapati', 'Raita', 'Garden Salad'],
    'thursday' => ['Jeera Aloo', 'Kadhi Pakora', 'Steamed Rice', 'Butter Chapati', 'Garden Salad', 'Halva'],
    'friday' => ['Arbi Masala', 'White Cholle', 'Steamed Rice', 'Butter Chapati', 'Raita', 'Garden Salad'],
    'saturday' => [],
    'sunday' => [],
];

$days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
$meals = ['breakfast', 'lunch', 'snacks', 'dinner'];

foreach ($days as $day) {
    foreach ($meals as $meal) {
        $items = ($meal === 'lunch') ? ($baseWeeklyLunch[$day] ?? []) : [];
        MessMenuItem::create([
            'mess_menu_id' => $menu->id,
            'day_of_week' => $day,
            'meal_type' => $meal,
            'items' => $items,
            'notes' => '',
            'sort_order' => 0,
        ]);
    }
}

// 4. Exact Date Overrides for each of the 11 scheduled days in 17th Aug to 31st Aug 2026
$scheduledDays = [
    [
        'date' => '2026-08-17',
        'label' => 'Monday Lunch',
        'meal_type' => 'lunch',
        'items' => ['Kadhai Paneer', 'Dal Fry', 'Steamed Rice', 'Butter Chapati', 'Garden Salad', 'Custard'],
    ],
    [
        'date' => '2026-08-18',
        'label' => 'Tuesday Lunch',
        'meal_type' => 'lunch',
        'items' => ['Mix Veg.', 'Punjabi Rajma', 'Steamed Rice', 'Butter Chapati', 'Raita/Curd', 'Garden Salad'],
    ],
    [
        'date' => '2026-08-19',
        'label' => 'Wednesday Lunch',
        'meal_type' => 'lunch',
        'items' => ['Lazeez Loki', 'Chana Dal Fry', 'Peas Pulao', 'Butter Chapati', 'Raita', 'Garden Salad'],
    ],
    [
        'date' => '2026-08-20',
        'label' => 'Thursday Lunch',
        'meal_type' => 'lunch',
        'items' => ['Jeera Aloo', 'Kadhi Pakora', 'Steamed Rice', 'Butter Chapati', 'Garden Salad', 'Halva'],
    ],
    [
        'date' => '2026-08-21',
        'label' => 'Friday Lunch',
        'meal_type' => 'lunch',
        'items' => ['Arbi Masala', 'White Cholle', 'Steamed Rice', 'Butter Chapati', 'Raita', 'Garden Salad'],
    ],
    [
        'date' => '2026-08-24',
        'label' => 'Monday Lunch',
        'meal_type' => 'lunch',
        'items' => ['Paneer Pasanda', 'Mix Dal', 'Peas Pulao', 'Butter Chapati', 'Garden Salad', 'Kheer'],
    ],
    [
        'date' => '2026-08-25',
        'label' => 'Tuesday Lunch',
        'meal_type' => 'lunch',
        'items' => ['Seasonal Veg', 'Kesari Chana', 'Steamed Rice', 'Butter Chapati', 'Raita', 'Garden Salad'],
    ],
    [
        'date' => '2026-08-26',
        'label' => 'Wednesday Lunch',
        'meal_type' => 'lunch',
        'items' => ['Chana Aloo Masala', 'Ajwain Poori', 'Steamed Rice', 'Mix Pickle', 'Suji Halva'],
    ],
    [
        'date' => '2026-08-27',
        'label' => 'Thursday Lunch',
        'meal_type' => 'lunch',
        'items' => ['Bhindi Fry', 'Pindi Cholle', 'Jeera Rice', 'Butter Chapati', 'Raita', 'Garden Salad'],
    ],
    [
        'date' => '2026-08-29',
        'label' => 'Saturday Lunch',
        'meal_type' => 'lunch',
        'items' => ['Nutrila Aloo', 'Pachrangi Dal', 'Steamed Rice', 'Butter Chapati', 'Raita', 'Garden Salad'],
    ],
    [
        'date' => '2026-08-31',
        'label' => 'Monday Lunch',
        'meal_type' => 'lunch',
        'items' => ['Matar Paneer', 'Dal Tadka', 'Steamed Rice', 'Butter Chapati', 'Garden Salad', 'Kheer'],
    ],
];

foreach ($scheduledDays as $sd) {
    MessMenuSpecialDay::create([
        'mess_menu_id' => $menu->id,
        'date' => $sd['date'],
        'label' => $sd['label'],
        'meal_type' => $sd['meal_type'],
        'items' => $sd['items'],
    ]);
    echo "Added date entry: {$sd['date']} ({$sd['label']})\n";
}

// 5. Flush cache
MessMenuService::flush();
echo "Flushed mess menu cache successfully.\n";
echo "Import completed successfully!\n";
