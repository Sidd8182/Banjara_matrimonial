<?php

namespace App\Services\Astrology;

use App\Support\SystemSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AstrologyBirthDetailsService
{
    public function fetch(array $birthPayload): ?array
    {
        $config = SystemSettings::astrology();
        if (!data_get($config, 'active_for_birth_details', false)) {
            return null;
        }

        try {
            $baseUrl = rtrim((string) data_get($config, 'api_base_url', ''), '/');
            $configuredPath = (string) data_get($config, 'birth_details_path', '/planets/extended');
            $apiKey = (string) data_get($config, 'api_key', '');
            $timeout = (int) data_get($config, 'timeout_seconds', 8);

            $birthPayload = $this->normalizeBirthInput($birthPayload);
            $geo = $this->resolveGeoAndTimezone($birthPayload, $timeout);
            if (!$geo) {
                throw new \RuntimeException('Could not resolve latitude/longitude/timezone from place of birth.');
            }

            $providerPayload = $this->buildFreeAstrologyPayload($birthPayload, $geo);
            $candidatePaths = $this->candidateBirthDetailsPaths($configuredPath);

            foreach ($candidatePaths as $path) {
                $endpoint = $baseUrl . $path;
                $response = Http::acceptJson()
                    ->timeout($timeout)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'x-api-key' => $apiKey,
                        'Authorization' => 'Bearer ' . $apiKey,
                    ])
                    ->post($endpoint, $providerPayload);

                if (!$response->successful()) {
                    continue;
                }

                $normalized = $this->normalize((array) $response->json());
                if ($normalized) {
                    return $normalized;
                }
            }

            throw new \RuntimeException('Birth details API did not return usable data from configured endpoints.');
        } catch (\Throwable $exception) {
            Log::warning('Astrology birth details fetch failed', [
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function normalize(array $payload): ?array
    {
        $rashi = $this->asString(
            data_get($payload, 'rashi')
            ?? data_get($payload, 'moon_sign')
            ?? data_get($payload, 'data.rashi')
            ?? data_get($payload, 'data.moon_sign')
            ?? data_get($payload, 'output.moon.sign')
            ?? data_get($payload, 'output.moon.rasi')
            ?? data_get($payload, 'moon.sign')
            ?? data_get($payload, 'output.Moon.zodiac_sign_name')
            ?? data_get($payload, 'output.Moon.sign')
        );

        $nakshatra = $this->asString(
            data_get($payload, 'nakshatra')
            ?? data_get($payload, 'birth_nakshatra')
            ?? data_get($payload, 'data.nakshatra')
            ?? data_get($payload, 'data.birth_nakshatra')
            ?? data_get($payload, 'output.moon.nakshatra')
            ?? data_get($payload, 'moon.nakshatra')
            ?? data_get($payload, 'output.Moon.nakshatra_name')
            ?? data_get($payload, 'output.Moon.nakshatra')
        );

        $lagna = $this->asString(
            data_get($payload, 'lagna')
            ?? data_get($payload, 'ascendant')
            ?? data_get($payload, 'data.lagna')
            ?? data_get($payload, 'data.ascendant')
            ?? data_get($payload, 'output.ascendant.sign')
            ?? data_get($payload, 'ascendant.sign')
            ?? data_get($payload, 'output.lagna')
            ?? data_get($payload, 'output.Ascendant.zodiac_sign_name')
            ?? data_get($payload, 'output.Ascendant.sign')
        );

        if (!$rashi) {
            $rashi = $this->signNumberToName(
                data_get($payload, 'output.Moon.current_sign')
                ?? data_get($payload, 'Moon.current_sign')
            );
        }

        if (!$lagna) {
            $lagna = $this->signNumberToName(
                data_get($payload, 'output.Ascendant.current_sign')
                ?? data_get($payload, 'Ascendant.current_sign')
            );
        }

        if (!$rashi || !$nakshatra) {
            $moon = $this->findPlanetNode($payload, 'Moon');
            if ($moon) {
                if (!$rashi) {
                    $rashi = $this->asString(data_get($moon, 'sign') ?? data_get($moon, 'rasi'));
                }
                if (!$nakshatra) {
                    $nakshatra = $this->asString(data_get($moon, 'nakshatra') ?? data_get($moon, 'nakshatra_name'));
                }
            }
        }

        if (!$rashi && !$nakshatra && !$lagna) {
            return null;
        }

        return [
            'rashi' => $rashi,
            'nakshatra' => $nakshatra,
            'lagna' => $lagna,
        ];
    }

    private function asString($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            $candidate =
                data_get($value, 'name')
                ?? data_get($value, 'value')
                ?? data_get($value, 'text')
                ?? data_get($value, 'sign')
                ?? data_get($value, 'rasi')
                ?? data_get($value, 'nakshatra');

            if ($candidate === null) {
                return null;
            }

            return $this->asString($candidate);
        }

        $string = trim((string) $value);
        return $string !== '' ? $string : null;
    }

    private function signNumberToName($value): ?string
    {
        if (!is_numeric($value)) {
            return null;
        }

        $map = [
            1 => 'Aries',
            2 => 'Taurus',
            3 => 'Gemini',
            4 => 'Cancer',
            5 => 'Leo',
            6 => 'Virgo',
            7 => 'Libra',
            8 => 'Scorpio',
            9 => 'Sagittarius',
            10 => 'Capricorn',
            11 => 'Aquarius',
            12 => 'Pisces',
        ];

        return $map[(int) $value] ?? null;
    }

    private function normalizeBirthInput(array $payload): array
    {
        return [
            'date_of_birth' => trim((string) data_get($payload, 'date_of_birth', '')),
            'time_of_birth' => trim((string) data_get($payload, 'time_of_birth', '')),
            'place_of_birth' => trim((string) data_get($payload, 'place_of_birth', '')),
            'state_of_birth' => trim((string) data_get($payload, 'state_of_birth', '')),
        ];
    }

    private function resolveGeoAndTimezone(array $birthPayload, int $timeout): ?array
    {
        $query = trim($birthPayload['place_of_birth'] . ' ' . $birthPayload['state_of_birth']);
        if ($query === '') {
            return null;
        }

        $geo = Http::timeout($timeout)
            ->withHeaders(['User-Agent' => 'banjara-matrimonial/1.0'])
            ->get('https://nominatim.openstreetmap.org/search', [
                'q' => $query,
                'format' => 'json',
                'limit' => 1,
            ]);

        if (!$geo->successful()) {
            return null;
        }

        $first = data_get($geo->json(), '0');
        $latitude = $first ? (float) data_get($first, 'lat', 0) : 0;
        $longitude = $first ? (float) data_get($first, 'lon', 0) : 0;
        if (!$latitude || !$longitude) {
            return null;
        }

        $offsetHours = 5.5;
        try {
            $timezoneResponse = Http::timeout(min($timeout, 5))
                ->connectTimeout(3)
                ->get('https://timeapi.io/api/TimeZone/coordinate', [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ]);

            if ($timezoneResponse->successful()) {
                $currentUtcOffset = (string) data_get($timezoneResponse->json(), 'currentUtcOffset', '+05:30');
                $offsetHours = $this->offsetToHours($currentUtcOffset);
            }
        } catch (\Throwable $exception) {
            // Keep default IST fallback when timezone lookup is temporarily unavailable.
            $offsetHours = 5.5;
        }

        return [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'timezone' => $offsetHours,
        ];
    }

    private function offsetToHours(string $offset): float
    {
        if (!preg_match('/^([+-])(\d{2}):(\d{2})$/', $offset, $matches)) {
            return 5.5;
        }

        $sign = $matches[1] === '-' ? -1 : 1;
        $hours = (int) $matches[2];
        $minutes = (int) $matches[3];

        return $sign * ($hours + ($minutes / 60));
    }

    private function buildFreeAstrologyPayload(array $birthPayload, array $geo): array
    {
        [$year, $month, $date] = $this->splitDate($birthPayload['date_of_birth']);
        [$hours, $minutes, $seconds] = $this->splitTime($birthPayload['time_of_birth']);

        return [
            'year' => $year,
            'month' => $month,
            'date' => $date,
            'hours' => $hours,
            'minutes' => $minutes,
            'seconds' => $seconds,
            'latitude' => $geo['latitude'],
            'longitude' => $geo['longitude'],
            'timezone' => $geo['timezone'],
            'config' => [
                'observation_point' => 'topocentric',
                'ayanamsha' => 'lahiri',
                'language' => 'en',
            ],
        ];
    }

    private function splitDate(string $date): array
    {
        $parts = explode('-', $date);
        if (count($parts) !== 3) {
            return [1970, 1, 1];
        }

        return [(int) $parts[0], (int) $parts[1], (int) $parts[2]];
    }

    private function splitTime(string $time): array
    {
        $normalized = trim($time);

        if (preg_match('/^\d{1,2}:\d{2}\s?(AM|PM)$/i', $normalized)) {
            $timestamp = strtotime($normalized);
            if ($timestamp !== false) {
                return [(int) date('H', $timestamp), (int) date('i', $timestamp), 0];
            }
        }

        if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $normalized, $matches)) {
            return [
                (int) $matches[1],
                (int) $matches[2],
                isset($matches[3]) ? (int) $matches[3] : 0,
            ];
        }

        return [0, 0, 0];
    }

    private function candidateBirthDetailsPaths(string $configuredPath): array
    {
        $paths = [
            '/' . ltrim($configuredPath, '/'),
            '/planets/extended',
            '/planets',
        ];

        return array_values(array_unique($paths));
    }

    private function findPlanetNode(array $payload, string $planetName): ?array
    {
        $candidates = [
            data_get($payload, 'output.planets'),
            data_get($payload, 'data.planets'),
            data_get($payload, 'planets'),
            data_get($payload, 'output'),
            data_get($payload, 'data'),
        ];

        foreach ($candidates as $candidate) {
            if (!is_array($candidate)) {
                continue;
            }

            foreach ($candidate as $key => $row) {
                if (is_string($key) && strcasecmp($key, $planetName) === 0 && is_array($row)) {
                    return $row;
                }

                if (is_array($row) && strcasecmp((string) data_get($row, 'name', ''), $planetName) === 0) {
                    return $row;
                }
            }
        }

        return null;
    }
}
