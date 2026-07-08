<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * RETIRED (multi-tenancy removed — single-site CMS).
 *
 * This suite proved the global TenantScope isolated per-tenant rows and
 * auto-stamped tenant_id. Neither behaviour exists anymore: there is no tenant
 * scope, no tenant_id column, and the on-the-fly `notes` fixture table is gone.
 *
 * Kept as a documented no-op only because the tooling cannot delete the file;
 * it (and tests/Fixtures/Note.php) are safe to remove from the repo. The single
 * trivial assertion keeps the suite green without re-introducing tenancy refs.
 */
class TenantScopeTest extends TestCase
{
    public function test_tenancy_suite_retired(): void
    {
        $this->assertTrue(true);
    }
}
