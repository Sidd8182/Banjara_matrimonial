<?php

namespace App\Services\Astrology;

use App\Models\Profile;
use App\Support\SystemSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AstrologyMatchService
{
    public function compatibilityForProfiles(Profile $seeker, Profile $candidate): ?array
    {
        $config = SystemSettings::astrology();
        if (!data_get($config, 'active_for_matchmaking', false)) {
            return null;
        }

        $seekerBirth = $this->buildBirthPayload($seeker);
        $candidateBirth = $this->buildBirthPayload($candidate);

        if (!$seekerBirth || !$candidateBirth) {
            return null;
        }

        $cacheMinutes = (int) data_get($config, 'cache_minutes', 1440);
        $cacheKey = sprintf('astrology.match.%d.%d', (int) $seeker->id, (int) $candidate->id);

        return Cache::remember($cacheKey, now()->addMinutes($cacheMinutes), function () use ($config, $seekerBirth, $candidateBirth) {
            try {
                $endpoint = rtrim((string) data_get($config, 'api_base_url', ''), '/') . (string) data_get($config, 'matchmaking_path', '/matchmaking');
                $apiKey = (string) data_get($config, 'api_key', '');

                $response = Http::acceptJson()
                    ->timeout((int) data_get($config, 'timeout_seconds', 8))
                    ->withHeaders([
                        'x-api-key' => $apiKey,
                        'Authorization' => 'Bearer ' . $apiKey,
                    ])
                    ->post($endpoint, [
                        'male' => $seekerBirth,
                        'female' => $candidateBirth,
                    ]);

                if (!$response->successful()) {
                    throw new \RuntimeException('Astrology API failed with HTTP ' . $response->status());
                }

                return $this->normalizeCompatibilityResponse((array) $response->json());
            } catch (\Throwable $exception) {
                Log::warning('Astrology compatibility fetch failed', [
                    'message' => $exception->getMessage(),
                ]);

                if (data_get($config, 'fail_open', true)) {
                    return null;
                }

                return [
                    'percentage' => null,
                    'guna_score' => null,
                    'guna_total' => 36,
                    'lagna' => null,
                    'partner_lagna' => null,
                    'source' => 'error',
                ];
            }
        });
    }

    private function buildBirthPayload(Profile $profile): ?array
    {
        $horoscope = $profile->horoscopeDetail;
        if (!$horoscope || !$horoscope->date_of_birth || !$horoscope->time_of_birth || !$horoscope->place_of_birth) {
            return null;
        }

        return [
            'date_of_birth' => $horoscope->date_of_birth->format('Y-m-d'),
            'time_of_birth' => (string) $horoscope->time_of_birth,
            'place_of_birth' => (string) $horoscope->place_of_birth,
            'state_of_birth' => (string) ($horoscope->birth_state ?? ''),
        ];
    }

    private function normalizeCompatibilityResponse(array $payload): ?array
    {
        $percentage = $this->asFloat(
            data_get($payload, 'percentage')
            ?? data_get($payload, 'match_percentage')
            ?? data_get($payload, 'compatibility_percentage')
            ?? data_get($payload, 'data.percentage')
            ?? data_get($payload, 'data.match_percentage')
            ?? data_get($payload, 'data.compatibility_percentage')
        );

        $gunaScore = $this->asFloat(
            data_get($payload, 'guna_score')
            ?? data_get($payload, 'total_guna')
            ?? data_get($payload, 'score')
            ?? data_get($payload, 'data.guna_score')
            ?? data_get($payload, 'data.total_guna')
            ?? data_get($payload, 'data.score')
        );

        $gunaTotal = $this->asFloat(
            data_get($payload, 'guna_total')
            ?? data_get($payload, 'total_guna_max')
            ?? data_get($payload, 'data.guna_total')
            ?? data_get($payload, 'data.total_guna_max')
        );

        if ($gunaTotal === null) {
            $gunaTotal = 36.0;
        }

        if ($percentage === null && $gunaScore !== null && $gunaTotal > 0) {
            $percentage = round(($gunaScore / $gunaTotal) * 100, 2);
        }

        if ($percentage === null && $gunaScore === null) {
            return null;
        }

        return [
            'percentage' => $percentage,
            'guna_score' => $gunaScore,
            'guna_total' => $gunaTotal,
            'lagna' => data_get($payload, 'lagna') ?? data_get($payload, 'data.lagna'),
            'partner_lagna' => data_get($payload, 'partner_lagna') ?? data_get($payload, 'data.partner_lagna'),
            'source' => 'api',
        ];
    }

    private function asFloat($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }
}
