<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    public const CACHE_KEY = 'settings.all';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    public static function allCached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return static::query()->pluck('value', 'key')->all();
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::allCached()[$key] ?? $default;
    }

    public static function int(string $key, int $default = 0): int
    {
        $value = self::get($key);

        return $value === null || $value === '' ? $default : (int) $value;
    }

    public static function float(string $key, float $default = 0.0): float
    {
        $value = self::get($key);

        return $value === null || $value === '' ? $default : (float) $value;
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = self::get($key);

        return $value === null ? $default : filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public static function put(string $key, mixed $value, string $type = 'string', string $group = 'general'): self
    {
        if (is_bool($value)) {
            $value = $value ? '1' : '0';
            $type = 'bool';
        }

        return static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value === null ? null : (string) $value, 'type' => $type, 'group' => $group]
        );
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public static function putMany(array $values, string $group = 'general'): void
    {
        foreach ($values as $key => $value) {
            self::put($key, $value, is_bool($value) ? 'bool' : 'string', $group);
        }

        Cache::forget(self::CACHE_KEY);
    }

    public function displayValue(): ?string
    {
        return $this->value;
    }
}
