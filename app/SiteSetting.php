<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    private const CACHE_KEY = 'site_settings:all';

    /** Read a setting. Falls back to the supplied default. */
    public static function get(string $key, mixed $default = null): mixed
    {
        return self::all_cached()[$key] ?? $default;
    }

    /** Upsert a setting and bust the cache. */
    public static function put(string $key, ?string $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(self::CACHE_KEY);
    }

    /** All settings as a [key => value] array, cached for the duration of the request. */
    private static function all_cached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return self::query()->pluck('value', 'key')->all();
        });
    }
}
