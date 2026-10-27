<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsHelper
{
    public static function get(string $key, $default = null): ?string
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();

            return $setting ? $setting->value : $default;
        });
    }

    public static function flush(string $key): void
    {
        Cache::forget("setting.{$key}");
    }

    public static function flushAll(): void
    {
        $keys = Setting::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("setting.{$key}");
        }
    }
}
