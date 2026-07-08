<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * RETIRED (multi-tenancy removed — single-site CMS).
 *
 * Phase 17 proved one engine could serve two isolated verticals (school +
 * restaurant) on different hosts. With single-site there is no host resolution
 * and no second tenant, so the premise no longer applies. Single-site content
 * rendering is covered by PageRenderingTest.
 *
 * Kept as a documented no-op only because the tooling cannot delete the file;
 * safe to remove from the repo.
 */
class ReusabilityValidationTest extends TestCase
{
    public function test_reusability_suite_retired(): void
    {
        $this->assertTrue(true);
    }
}
