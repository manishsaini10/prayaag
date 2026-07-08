<?php

namespace App\Core\Settings;

use App\Models\Setting;
use App\Models\SettingGroup;
use Illuminate\Support\Facades\Cache;

/**
 * The Settings Engine. Database-driven key/value store with typed values and
 * caching. Resolve via app(SettingsManager::class) or the Settings facade.
 *
 * Reads from a single cached map; writes invalidate that map.
 */
class SettingsManager
{
    protected function cacheKey(): string
    {
        return 'settings';
    }

    /** @return array<string, mixed> */
    public function all(): array
    {
        return Cache::rememberForever($this->cacheKey(), function () {
            return Setting::all()
                ->mapWithKeys(fn (Setting $setting) => [$setting->key => $setting->castedValue()])
                ->all();
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->all());
    }

    public function set(string $key, mixed $value, ?string $type = null, ?string $groupSlug = null): Setting
    {
        $type ??= $this->inferType($value);
        $groupId = $groupSlug
            ? SettingGroup::where('slug', $groupSlug)->value('id')
            : null;

        $setting = Setting::updateOrCreate(
            ['key' => $key],
            [
                'value'    => $this->serialize($value, $type),
                'type'     => $type,
                'group_id' => $groupId,
            ]
        );

        $this->flush();

        return $setting;
    }

    public function forget(string $key): void
    {
        Setting::where('key', $key)->delete();
        $this->flush();
    }

    public function flush(): void
    {
        Cache::forget($this->cacheKey());
        Cache::forget('theme.header'); // chrome depends on settings (logo, CTA, contact, social)
    }

    protected function inferType(mixed $value): string
    {
        return match (true) {
            is_bool($value)  => 'boolean',
            is_int($value)   => 'integer',
            is_float($value) => 'float',
            is_array($value) => 'json',
            default          => 'string',
        };
    }

    protected function serialize(mixed $value, string $type): ?string
    {
        return match ($type) {
            'json', 'array' => json_encode($value),
            'boolean'       => $value ? '1' : '0',
            default         => $value === null ? null : (string) $value,
        };
    }
}
