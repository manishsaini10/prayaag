<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Multi-tenancy removed (single-site CMS). The `tenant_domains` table is no
 * longer created. This migration is intentionally a no-op; the file is retained
 * only because the tooling cannot delete it. Safe to remove from the repo.
 */
return new class extends Migration {
    public function up(): void
    {
        // no-op
    }

    public function down(): void
    {
        // no-op
    }
};
