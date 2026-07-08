<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\InstagramAccount;
use Illuminate\Console\Command;

class SyncAllInstagramMedia extends Command
{
    protected $signature = 'instagram:sync-all';
    protected $description = 'Sync media for all connected Instagram accounts';

    public function handle(): int
    {
        $accounts = InstagramAccount::where('status', 'connected')->get();

        if ($accounts->isEmpty()) {
            $this->warn('No connected Instagram accounts found.');
            return self::SUCCESS;
        }

        foreach ($accounts as $account) {
            \App\Jobs\SyncInstagramMedia::dispatch($account->id);
            $this->info("Queued sync for @{$account->username}");
        }

        return self::SUCCESS;
    }
}
