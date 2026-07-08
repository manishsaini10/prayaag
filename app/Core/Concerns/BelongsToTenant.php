<?php

namespace App\Core\Concerns;

/**
 * NO-OP (multi-tenancy removed — single-site CMS).
 *
 * Previously applied the global TenantScope and auto-stamped tenant_id. The
 * platform is now single-site, so this trait intentionally does nothing. It is
 * retained only so models still declaring `use BelongsToTenant;` keep compiling
 * during the conversion; it is removed from models in a later wave and this
 * file is then safe to delete.
 */
trait BelongsToTenant
{
}
