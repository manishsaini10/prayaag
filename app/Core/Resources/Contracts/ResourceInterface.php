<?php

namespace App\Core\Resources\Contracts;

/**
 * Contract for an admin resource. The full Resource Engine is a later
 * phase; Phase 1 only defines the shape so plugins and the engine have
 * something to register against.
 */
interface ResourceInterface
{
    public static function key(): string;

    public static function label(): string;

    /** @return class-string */
    public static function model(): string;

    public function fields(): array;
}
