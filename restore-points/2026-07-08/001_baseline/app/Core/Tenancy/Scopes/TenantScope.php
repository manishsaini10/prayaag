<?php

namespace App\Core\Tenancy\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * NO-OP (multi-tenancy removed — single-site CMS).
 *
 * Retained only so any lingering reference resolves during the conversion.
 * Applies no constraint. Safe to delete once nothing references it.
 */
class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Single-site: no tenant constraint.
    }
}
