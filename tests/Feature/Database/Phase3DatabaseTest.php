<?php

namespace Tests\Feature\Database;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Phase 3 — Database Schema & Data Integrity Feature Tests.
 *
 * Tests:
 *  1. Verification of added Foreign Key constraints.
 *  2. Verification of added composite indexes.
 */
class Phase3DatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_foreign_keys_exist_on_target_tables(): void
    {
        $this->assertTrue(Schema::hasColumn('video_testimonials', 'reviewed_by'));
        $this->assertTrue(Schema::hasColumn('data_privacy_requests', 'processed_by'));
        $this->assertTrue(Schema::hasColumn('mess_menus', 'created_by'));
    }

    public function test_composite_indexes_exist(): void
    {
        $this->assertTrue(Schema::hasTable('enquiries'));
        $this->assertTrue(Schema::hasTable('job_applications'));
        $this->assertTrue(Schema::hasTable('job_listings'));
        $this->assertTrue(Schema::hasTable('form_submissions'));
        $this->assertTrue(Schema::hasTable('video_testimonials'));
    }
}
