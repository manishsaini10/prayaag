<?php

namespace App\Core\Resources;

use App\Core\Resources\Contracts\ResourceInterface;
use InvalidArgumentException;

/**
 * Registry the future Resource Engine and plugins register resources
 * into. Bound as a singleton in CoreServiceProvider.
 */
class ResourceRegistry
{
    /** @var array<string, class-string<ResourceInterface>> */
    protected array $resources = [];

    public function register(string $resourceClass): void
    {
        if (! is_subclass_of($resourceClass, ResourceInterface::class)) {
            throw new InvalidArgumentException("{$resourceClass} must implement ResourceInterface.");
        }

        $this->resources[$resourceClass::key()] = $resourceClass;
    }

    /** @return array<string, class-string<ResourceInterface>> */
    public function all(): array
    {
        return $this->resources;
    }

    public function get(string $key): ?string
    {
        return $this->resources[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return isset($this->resources[$key]);
    }
}
