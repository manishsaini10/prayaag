<?php

namespace App\Core\Tenancy;

/**
 * NO-OP STUB (multi-tenancy removed — single-site CMS).
 *
 * Retained only so existing callers (controllers, services, seeders) keep
 * compiling during the conversion. Performs no tenant logic:
 *  - check() returns true  (content always resolves on a single site)
 *  - id() returns null     (no tenant scoping)
 * All callers are de-referenced in later waves, after which this file is safe
 * to delete.
 */
class TenantManager
{
    public function set(mixed $tenant = null): void
    {
    }

    public function current(): mixed
    {
        return null;
    }

    public function id(): ?string
    {
        return null;
    }

    public function check(): bool
    {
        return true;
    }

    public function forget(): void
    {
    }
}
