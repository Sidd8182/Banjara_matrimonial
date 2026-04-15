<?php

namespace App\Services\Kundli;

use App\Models\KundliMatchResult;
use App\Models\Profile;

class KundliMatchService
{
    private $engine;

    public function __construct(AshtakootaMatchEngine $engine)
    {
        $this->engine = $engine;
    }

    public function compatibilityForProfiles(Profile $seeker, Profile $candidate): ?array
    {
        if ((int) $seeker->id === (int) $candidate->id) {
            return null;
        }

        list($maleProfile, $femaleProfile) = $this->resolvePair($seeker, $candidate);

        $existing = KundliMatchResult::query()
            ->where('male_profile_id', $maleProfile->id)
            ->where('female_profile_id', $femaleProfile->id)
            ->first();

        if ($existing) {
            return [
                'percentage' => $existing->percentage !== null ? (float) $existing->percentage : null,
                'guna_score' => $existing->guna_score !== null ? (float) $existing->guna_score : null,
                'guna_total' => (int) $existing->guna_total,
                'breakdown' => $existing->koota_breakdown ?: [],
                'source' => 'custom-engine-db',
                'lagna' => optional($maleProfile->horoscopeDetail)->lagna,
                'partner_lagna' => optional($femaleProfile->horoscopeDetail)->lagna,
            ];
        }

        $result = $this->engine->calculate($maleProfile, $femaleProfile);
        if (!$result) {
            return null;
        }

        KundliMatchResult::query()->updateOrCreate(
            [
                'male_profile_id' => $maleProfile->id,
                'female_profile_id' => $femaleProfile->id,
            ],
            [
                'guna_score' => $result['guna_score'],
                'guna_total' => $result['guna_total'],
                'percentage' => $result['percentage'],
                'koota_breakdown' => $result['breakdown'],
                'source' => 'custom-engine',
                'computed_at' => now(),
            ]
        );

        return $result;
    }

    private function resolvePair(Profile $left, Profile $right): array
    {
        $leftGender = strtolower((string) ($left->gender ?? ''));
        $rightGender = strtolower((string) ($right->gender ?? ''));

        if ($leftGender === 'male' && $rightGender === 'female') {
            return [$left, $right];
        }

        if ($leftGender === 'female' && $rightGender === 'male') {
            return [$right, $left];
        }

        return [$left, $right];
    }
}
