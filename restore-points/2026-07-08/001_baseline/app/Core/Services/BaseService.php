<?php

namespace App\Core\Services;

use App\Core\Repositories\Contracts\RepositoryInterface;

/**
 * Holds business logic and orchestration. The ONLY layer that
 * presentation code (Livewire, controllers, API) is allowed to call.
 * Keeps logic out of Blade per the platform's architecture rule.
 */
abstract class BaseService
{
    public function __construct(protected RepositoryInterface $repository)
    {
    }
}
