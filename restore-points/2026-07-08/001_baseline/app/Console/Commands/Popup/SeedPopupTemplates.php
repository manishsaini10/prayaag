<?php

namespace App\Console\Commands\Popup;

use App\Core\Popup\Services\TemplateService;
use Illuminate\Console\Command;

class SeedPopupTemplates extends Command
{
    protected $signature = 'popup:seed-templates';
    protected $description = 'Seed built-in popup templates';

    public function handle(TemplateService $service): int
    {
        $this->info('Seeding popup templates...');
        $service->seedDefaults();
        $this->info('Built-in templates seeded successfully.');
        return Command::SUCCESS;
    }
}
