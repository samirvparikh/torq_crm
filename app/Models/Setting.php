<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'is_encrypted',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'is_encrypted' => 'boolean',
        ];
    }

    public static function getValue(string $group, string $key, mixed $default = null): mixed
    {
        $setting = static::query()
            ->where('group', $group)
            ->where('key', $key)
            ->first();

        if (! $setting) {
            return $default;
        }

        return static::castValue($setting->value, $setting->type);
    }

    public static function setValue(string $group, string $key, mixed $value, string $type = 'string'): void
    {
        static::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value, 'type' => $type]
        );

        Cache::forget(static::cacheKey($group, $key));
    }

    public static function getGroup(string $group): array
    {
        return static::query()
            ->where('group', $group)
            ->pluck('value', 'key')
            ->map(fn ($value, $key) => static::getValue($group, $key))
            ->all();
    }

    protected static function castValue(?string $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float' => (float) $value,
            'json' => json_decode($value ?? '[]', true),
            default => $value,
        };
    }

    protected static function cacheKey(string $group, string $key): string
    {
        return "settings.{$group}.{$key}";
    }
}
