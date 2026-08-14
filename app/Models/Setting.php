<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = Cache::rememberForever("setting.{$key}", function () use ($key) {
            return self::query()->where('key', $key)->value('value');
        });

        return $value === null ? $default : $value;
    }

    public static function set(string $key, mixed $value): void
    {
        self::query()->updateOrCreate(['key' => $key], ['value' => (string) $value]);
        Cache::forget("setting.{$key}");
    }

    public static function allSettings(): array
    {
        return self::query()->pluck('value', 'key')->toArray();
    }
}
