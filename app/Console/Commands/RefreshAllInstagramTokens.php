<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\InstagramAccount;
use Illuminate\Console\Command;

class RefreshAllInstagramTokens extends Command
{
    protected $signature = 'instagram:refresh-tokens';
    protected $description = 'Refresh tokens for all connected Instagram accounts';

    public function handle(): int
    {
        $accounts = InstagramAccount::where('status', 'connected')->get();

        if ($accounts->isEmpty()) {
            $this->warn('No connected Instagram accounts found.');
            return self::SUCCESS;
        }

        foreach ($accounts as $account) {
            \App\Jobs\RefreshInstagramToken::dispatch($account->id);
            $this->info("Queued token refresh for @{$account->username}");
        }

        return self::SUCCESS;
    }
}
