<?php

namespace App\Support;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class SystemSettings
{
    public static function get(string $key, string $default = ''): string
    {
        $record = self::all()->get($key);

        if (!$record || $record->value === null || $record->value === '') {
            return $default;
        }

        $value = (string) $record->value;
        if (!(bool) ($record->is_encrypted ?? false)) {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $exception) {
            return $default;
        }
    }

    public static function boolean(string $key, bool $default = false): bool
    {
        $raw = self::get($key, $default ? '1' : '0');

        return filter_var($raw, FILTER_VALIDATE_BOOLEAN);
    }

    public static function astrology(): array
    {
        $enabled = self::boolean('astrology_enabled', false);
        $matchmakingEnabled = self::boolean('astrology_matchmaking_enabled', false);
        $provider = strtolower(trim(self::get('astrology_provider', 'freeastrologyapi')));
        $baseUrl = trim(self::get('astrology_api_base_url', 'https://json.freeastrologyapi.com'));
        $birthDetailsPath = '/' . ltrim(trim(self::get('astrology_birth_details_path', '/planets/extended')), '/');
        $matchmakingPath = '/' . ltrim(trim(self::get('astrology_matchmaking_path', '/matchmaking')), '/');
        $apiKey = trim(self::get('astrology_api_key', ''));

        if ($provider === 'freeastrologyapi') {
            if ($baseUrl === '' || str_contains($baseUrl, 'freeastrologyapi.com/api')) {
                $baseUrl = 'https://json.freeastrologyapi.com';
            }

            if ($birthDetailsPath === '/birth-details' || $birthDetailsPath === '/') {
                $birthDetailsPath = '/planets/extended';
            }
        }

        $timeout = (int) self::get('astrology_timeout_seconds', '8');
        $cacheMinutes = (int) self::get('astrology_cache_minutes', '1440');

        $timeout = max(2, min($timeout, 30));
        $cacheMinutes = max(5, min($cacheMinutes, 10080));

        $credentialsConfigured = $provider !== '' && $baseUrl !== '' && $apiKey !== '';
        $activeForMatchmaking = $enabled && $matchmakingEnabled && $credentialsConfigured;

        return [
            'enabled' => $enabled,
            'provider' => $provider,
            'api_base_url' => $baseUrl,
            'birth_details_path' => $birthDetailsPath,
            'matchmaking_path' => $matchmakingPath,
            'api_key' => $apiKey,
            'api_key_configured' => $apiKey !== '',
            'active_for_birth_details' => $enabled && $credentialsConfigured,
            'matchmaking_enabled' => $matchmakingEnabled,
            'fail_open' => self::boolean('astrology_fail_open', true),
            'timeout_seconds' => $timeout,
            'cache_minutes' => $cacheMinutes,
            'active_for_matchmaking' => $activeForMatchmaking,
            'normal_mode' => !$activeForMatchmaking,
        ];
    }

    private static function all()
    {
        return Cache::remember('system_settings.all', now()->addMinutes(5), function () {
            try {
                return SystemSetting::query()->get(['key', 'value', 'is_encrypted'])->keyBy('key');
            } catch (\Throwable $exception) {
                return collect();
            }
        });
    }
}
