<?php

namespace App\Console\Commands;

use App\Models\Enquiry;
use App\Models\JobApplication;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Anonymise (and soft-delete) personal data in job_applications and enquiries
 * that are older than the configured retention period.
 *
 * Schedule: daily via app/Console/Kernel.php or routes/console.php.
 *
 * Usage:
 *   php artisan app:prune-personal-data            # uses PERSONAL_DATA_RETENTION_MONTHS from .env
 *   php artisan app:prune-personal-data --months=6 # override retention period
 *   php artisan app:prune-personal-data --dry-run  # preview without making changes
 *
 * What it does:
 *  - Finds job_applications older than N months (updated_at).
 *  - Replaces name, email, phone, cover_letter with "[anonymised]" / null.
 *  - Soft-deletes the record (preserves the row for audit, hides PII from app).
 *  - Same logic for enquiries (name, email, phone, message).
 *
 * GDPR / FERPA notes:
 *  - The anonymised stub rows are kept for statistics (count of applications/enquiries).
 *  - Hard-delete can be done manually if needed for a data-erasure request.
 *  - Résumé media files are deleted from storage when the record is anonymised.
 */
class PrunePersonalData extends Command
{
    protected $signature = 'app:prune-personal-data
                            {--months= : Override the retention period in months (default: from config)}
                            {--dry-run : Preview what would be anonymised without making changes}';

    protected $description = 'Anonymise and soft-delete personal data older than the configured retention period (GDPR/FERPA).';

    public function handle(): int
    {
        $months  = (int) ($this->option('months') ?: config('privacy.retention_months', 24));
        $dryRun  = $this->option('dry-run');
        $cutoff  = now()->subMonths($months);

        if ($months === 0) {
            $this->warn('Data retention is disabled (PERSONAL_DATA_RETENTION_MONTHS=0). Nothing to prune.');
            return self::SUCCESS;
        }

        $this->info("Pruning personal data older than {$months} months (cutoff: {$cutoff->toDateString()}).");
        if ($dryRun) {
            $this->warn('DRY-RUN mode — no changes will be made.');
        }

        $totalAnonymised = 0;

        // ── Job Applications ─────────────────────────────────────────────────
        $jobApps = JobApplication::withTrashed()
            ->where('updated_at', '<', $cutoff)
            ->whereNull('deleted_at') // not yet soft-deleted
            ->whereNotNull('email')   // not already anonymised
            ->get();

        $this->info("Found {$jobApps->count()} job application(s) to anonymise.");

        foreach ($jobApps as $application) {
            if ($dryRun) {
                $this->line("  [DRY-RUN] Would anonymise JobApplication #{$application->id} (email: {$application->email})");
                continue;
            }

            DB::transaction(function () use ($application) {
                // Remove résumé file from storage
                if ($application->resume) {
                    try {
                        \Illuminate\Support\Facades\Storage::disk($application->resume->disk)
                            ->delete($application->resume->path);
                    } catch (\Throwable $e) {
                        Log::warning("Could not delete résumé file for application {$application->id}", ['error' => $e->getMessage()]);
                    }
                }

                // Anonymise PII fields
                $application->update([
                    'name'         => '[anonymised]',
                    'email'        => 'anon_' . $application->id . '@deleted.invalid',
                    'phone'        => null,
                    'cover_letter' => null,
                ]);

                $application->delete(); // soft-delete
            });

            $this->line("  Anonymised JobApplication #{$application->id}");
        }

        $totalAnonymised += $jobApps->count();

        // ── Enquiries ────────────────────────────────────────────────────────
        $enquiries = Enquiry::withTrashed()
            ->where('updated_at', '<', $cutoff)
            ->whereNull('deleted_at')
            ->whereNotNull('email')
            ->get();

        $this->info("Found {$enquiries->count()} enquiry(s) to anonymise.");

        foreach ($enquiries as $enquiry) {
            if ($dryRun) {
                $this->line("  [DRY-RUN] Would anonymise Enquiry #{$enquiry->id} (email: {$enquiry->email})");
                continue;
            }

            $enquiry->update([
                'name'    => '[anonymised]',
                'email'   => 'anon_' . $enquiry->id . '@deleted.invalid',
                'phone'   => null,
                'message' => null,
                'meta'    => null,
            ]);

            $enquiry->delete();
            $this->line("  Anonymised Enquiry #{$enquiry->id}");
        }

        $totalAnonymised += $enquiries->count();

        $label = $dryRun ? 'would be anonymised' : 'anonymised';
        $this->info("Done. {$totalAnonymised} record(s) {$label}.");

        Log::info('PrunePersonalData ran', [
            'months'       => $months,
            'cutoff'       => $cutoff->toDateString(),
            'dry_run'      => $dryRun,
            'anonymised'   => $totalAnonymised,
        ]);

        return self::SUCCESS;
    }
}
