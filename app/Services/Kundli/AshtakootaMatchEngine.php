<?php

namespace App\Services\Kundli;

use App\Models\Profile;

class AshtakootaMatchEngine
{
    private $kootaMax = [
        'varna' => 1,
        'vashya' => 2,
        'tara' => 3,
        'yoni' => 4,
        'graha_maitri' => 5,
        'gana' => 6,
        'bhakoot' => 7,
        'nadi' => 8,
    ];

    private $nakshatraOrder = [
        'ashwini',
        'bharani',
        'krittika',
        'rohini',
        'mrigashira',
        'ardra',
        'punarvasu',
        'pushya',
        'ashlesha',
        'magha',
        'purva phalguni',
        'uttara phalguni',
        'hasta',
        'chitra',
        'swati',
        'vishakha',
        'anuradha',
        'jyeshtha',
        'moola',
        'purva ashadha',
        'uttara ashadha',
        'shravana',
        'dhanishta',
        'shatabhisha',
        'purva bhadrapada',
        'uttara bhadrapada',
        'revati',
    ];

    public function calculate(Profile $maleProfile, Profile $femaleProfile): ?array
    {
        $male = $maleProfile->horoscopeDetail;
        $female = $femaleProfile->horoscopeDetail;

        $maleRashi = $this->rashiIndex(optional($male)->rashi);
        $femaleRashi = $this->rashiIndex(optional($female)->rashi);
        $maleNakshatra = $this->nakshatraIndex(optional($male)->nakshatra);
        $femaleNakshatra = $this->nakshatraIndex(optional($female)->nakshatra);

        if ($maleRashi === null || $femaleRashi === null || $maleNakshatra === null || $femaleNakshatra === null) {
            return null;
        }

        $breakdown = [];

        $breakdown[] = $this->buildItem('varna', 'Varna', $this->varnaScore($maleRashi, $femaleRashi));
        $breakdown[] = $this->buildItem('vashya', 'Vashya', $this->vashyaScore($maleRashi, $femaleRashi));
        $breakdown[] = $this->buildItem('tara', 'Tara', $this->taraScore($maleNakshatra, $femaleNakshatra));
        $breakdown[] = $this->buildItem('yoni', 'Yoni', $this->yoniScore($maleNakshatra, $femaleNakshatra));
        $breakdown[] = $this->buildItem('graha_maitri', 'Graha Maitri', $this->grahaMaitriScore($maleRashi, $femaleRashi));
        $breakdown[] = $this->buildItem('gana', 'Gana', $this->ganaScore($maleNakshatra, $femaleNakshatra));
        $breakdown[] = $this->buildItem('bhakoot', 'Bhakoot', $this->bhakootScore($maleRashi, $femaleRashi));
        $breakdown[] = $this->buildItem('nadi', 'Nadi', $this->nadiScore($maleNakshatra, $femaleNakshatra));

        $total = 0.0;
        foreach ($breakdown as $item) {
            $total += (float) $item['score'];
        }

        $percentage = round(($total / 36) * 100, 2);

        return [
            'percentage' => $percentage,
            'guna_score' => round($total, 2),
            'guna_total' => 36,
            'breakdown' => $breakdown,
            'source' => 'custom-engine',
            'lagna' => optional($male)->lagna,
            'partner_lagna' => optional($female)->lagna,
        ];
    }

    private function buildItem(string $key, string $label, float $score): array
    {
        $max = (float) $this->kootaMax[$key];

        return [
            'key' => $key,
            'label' => $label,
            'score' => round(max(0, min($score, $max)), 2),
            'max' => $max,
        ];
    }

    private function varnaScore(int $maleRashi, int $femaleRashi): float
    {
        $rank = [
            1 => 3, 2 => 2, 3 => 1, 4 => 4,
            5 => 3, 6 => 2, 7 => 1, 8 => 4,
            9 => 3, 10 => 2, 11 => 1, 12 => 4,
        ];

        return ($rank[$maleRashi] >= $rank[$femaleRashi]) ? 1.0 : 0.0;
    }

    private function vashyaScore(int $maleRashi, int $femaleRashi): float
    {
        $category = [
            1 => 'chatushpada',
            2 => 'chatushpada',
            3 => 'manav',
            4 => 'jalchar',
            5 => 'vanchar',
            6 => 'manav',
            7 => 'manav',
            8 => 'keeta',
            9 => 'chatushpada',
            10 => 'chatushpada',
            11 => 'manav',
            12 => 'jalchar',
        ];

        $male = $category[$maleRashi];
        $female = $category[$femaleRashi];

        if ($male === $female) {
            return 2.0;
        }

        $partialPairs = [
            'manav:chatushpada',
            'chatushpada:manav',
            'jalchar:chatushpada',
            'chatushpada:jalchar',
            'vanchar:chatushpada',
            'chatushpada:vanchar',
            'keeta:vanchar',
            'vanchar:keeta',
            'jalchar:keeta',
            'keeta:jalchar',
        ];

        return in_array($male . ':' . $female, $partialPairs, true) ? 1.0 : 0.0;
    }

    private function taraScore(int $maleNakshatra, int $femaleNakshatra): float
    {
        $maleToFemale = $this->taraDirectionScore($maleNakshatra, $femaleNakshatra);
        $femaleToMale = $this->taraDirectionScore($femaleNakshatra, $maleNakshatra);

        return $maleToFemale + $femaleToMale;
    }

    private function taraDirectionScore(int $from, int $to): float
    {
        $distance = (($to - $from + 27) % 27) + 1;
        $tara = $distance % 9;
        if ($tara === 0) {
            $tara = 9;
        }

        $favourable = [2, 4, 6, 8, 9];

        return in_array($tara, $favourable, true) ? 1.5 : 0.0;
    }

    private function yoniScore(int $maleNakshatra, int $femaleNakshatra): float
    {
        $maleYoni = $this->yoniByNakshatra($maleNakshatra);
        $femaleYoni = $this->yoniByNakshatra($femaleNakshatra);

        if ($maleYoni === $femaleYoni) {
            return 4.0;
        }

        $friendPairs = [
            'horse:elephant',
            'elephant:horse',
            'cow:buffalo',
            'buffalo:cow',
            'goat:sheep',
            'sheep:goat',
            'dog:deer',
            'deer:dog',
            'cat:rat',
            'rat:cat',
            'lion:tiger',
            'tiger:lion',
        ];

        $enemyPairs = [
            'snake:mongoose',
            'mongoose:snake',
            'cat:dog',
            'dog:cat',
            'lion:elephant',
            'elephant:lion',
        ];

        $pair = $maleYoni . ':' . $femaleYoni;

        if (in_array($pair, $friendPairs, true)) {
            return 3.0;
        }

        if (in_array($pair, $enemyPairs, true)) {
            return 0.0;
        }

        return 2.0;
    }

    private function yoniByNakshatra(int $nakshatra): string
    {
        $map = [
            1 => 'horse',
            2 => 'elephant',
            3 => 'sheep',
            4 => 'snake',
            5 => 'snake',
            6 => 'dog',
            7 => 'cat',
            8 => 'sheep',
            9 => 'cat',
            10 => 'rat',
            11 => 'rat',
            12 => 'cow',
            13 => 'buffalo',
            14 => 'tiger',
            15 => 'buffalo',
            16 => 'tiger',
            17 => 'deer',
            18 => 'deer',
            19 => 'dog',
            20 => 'monkey',
            21 => 'mongoose',
            22 => 'monkey',
            23 => 'lion',
            24 => 'horse',
            25 => 'lion',
            26 => 'cow',
            27 => 'elephant',
        ];

        return $map[$nakshatra] ?? 'unknown';
    }

    private function grahaMaitriScore(int $maleRashi, int $femaleRashi): float
    {
        $lords = [
            1 => 'mars', 2 => 'venus', 3 => 'mercury', 4 => 'moon',
            5 => 'sun', 6 => 'mercury', 7 => 'venus', 8 => 'mars',
            9 => 'jupiter', 10 => 'saturn', 11 => 'saturn', 12 => 'jupiter',
        ];

        $friendships = [
            'sun' => ['friend' => ['moon', 'mars', 'jupiter'], 'neutral' => ['mercury'], 'enemy' => ['venus', 'saturn']],
            'moon' => ['friend' => ['sun', 'mercury'], 'neutral' => ['mars', 'jupiter', 'venus', 'saturn'], 'enemy' => []],
            'mars' => ['friend' => ['sun', 'moon', 'jupiter'], 'neutral' => ['venus', 'saturn'], 'enemy' => ['mercury']],
            'mercury' => ['friend' => ['sun', 'venus'], 'neutral' => ['mars', 'jupiter', 'saturn'], 'enemy' => ['moon']],
            'jupiter' => ['friend' => ['sun', 'moon', 'mars'], 'neutral' => ['saturn'], 'enemy' => ['mercury', 'venus']],
            'venus' => ['friend' => ['mercury', 'saturn'], 'neutral' => ['mars', 'jupiter'], 'enemy' => ['sun', 'moon']],
            'saturn' => ['friend' => ['mercury', 'venus'], 'neutral' => ['jupiter'], 'enemy' => ['sun', 'moon', 'mars']],
        ];

        $maleLord = $lords[$maleRashi];
        $femaleLord = $lords[$femaleRashi];

        $maleRelation = $this->planetRelation($maleLord, $femaleLord, $friendships);
        $femaleRelation = $this->planetRelation($femaleLord, $maleLord, $friendships);

        $pair = $maleRelation . ':' . $femaleRelation;

        $scoreMap = [
            'friend:friend' => 5.0,
            'friend:neutral' => 4.0,
            'neutral:friend' => 4.0,
            'neutral:neutral' => 3.0,
            'friend:enemy' => 1.0,
            'enemy:friend' => 1.0,
            'neutral:enemy' => 0.5,
            'enemy:neutral' => 0.5,
            'enemy:enemy' => 0.0,
        ];

        return $scoreMap[$pair] ?? 2.0;
    }

    private function planetRelation(string $from, string $to, array $friendships): string
    {
        if (in_array($to, $friendships[$from]['friend'], true)) {
            return 'friend';
        }

        if (in_array($to, $friendships[$from]['enemy'], true)) {
            return 'enemy';
        }

        return 'neutral';
    }

    private function ganaScore(int $maleNakshatra, int $femaleNakshatra): float
    {
        $male = $this->ganaByNakshatra($maleNakshatra);
        $female = $this->ganaByNakshatra($femaleNakshatra);

        if ($male === $female) {
            return 6.0;
        }

        $pair = $male . ':' . $female;

        $map = [
            'deva:manushya' => 5.0,
            'manushya:deva' => 5.0,
            'manushya:rakshasa' => 1.0,
            'rakshasa:manushya' => 1.0,
            'deva:rakshasa' => 0.0,
            'rakshasa:deva' => 0.0,
        ];

        return $map[$pair] ?? 0.0;
    }

    private function ganaByNakshatra(int $nakshatra): string
    {
        $deva = [1, 5, 7, 8, 13, 15, 17, 22, 27];
        $manushya = [2, 4, 6, 11, 12, 20, 21, 25, 26];

        if (in_array($nakshatra, $deva, true)) {
            return 'deva';
        }

        if (in_array($nakshatra, $manushya, true)) {
            return 'manushya';
        }

        return 'rakshasa';
    }

    private function bhakootScore(int $maleRashi, int $femaleRashi): float
    {
        $forward = (($femaleRashi - $maleRashi + 12) % 12) + 1;
        $backward = (($maleRashi - $femaleRashi + 12) % 12) + 1;

        $doshaPairs = [
            '2:12',
            '12:2',
            '5:9',
            '9:5',
            '6:8',
            '8:6',
        ];

        return in_array($forward . ':' . $backward, $doshaPairs, true) ? 0.0 : 7.0;
    }

    private function nadiScore(int $maleNakshatra, int $femaleNakshatra): float
    {
        $male = ($maleNakshatra - 1) % 3;
        $female = ($femaleNakshatra - 1) % 3;

        return $male === $female ? 0.0 : 8.0;
    }

    private function rashiIndex(?string $value): ?int
    {
        if (!$value) {
            return null;
        }

        $normalized = $this->normalizeName($value);

        $map = [
            1 => ['mesh', 'aries'],
            2 => ['vrishabh', 'taurus'],
            3 => ['mithun', 'gemini'],
            4 => ['kark', 'cancer'],
            5 => ['singh', 'leo'],
            6 => ['kanya', 'virgo'],
            7 => ['tula', 'libra'],
            8 => ['vrishchik', 'scorpio'],
            9 => ['dhanu', 'sagittarius'],
            10 => ['makar', 'capricorn'],
            11 => ['kumbh', 'aquarius'],
            12 => ['meen', 'pisces'],
        ];

        foreach ($map as $index => $tokens) {
            foreach ($tokens as $token) {
                if (strpos($normalized, $token) !== false) {
                    return $index;
                }
            }
        }

        return null;
    }

    private function nakshatraIndex(?string $value): ?int
    {
        if (!$value) {
            return null;
        }

        $normalized = $this->normalizeName($value);
        $normalized = str_replace(['  ', '-', '_'], ' ', $normalized);

        foreach ($this->nakshatraOrder as $index => $name) {
            if ($normalized === $name || strpos($normalized, $name) !== false || strpos($name, $normalized) !== false) {
                return $index + 1;
            }
        }

        return null;
    }

    private function normalizeName(string $value): string
    {
        $plain = strtolower(trim($value));
        $plain = preg_replace('/\([^)]*\)/', ' ', $plain);
        $plain = preg_replace('/\s+/', ' ', (string) $plain);

        return trim((string) $plain);
    }
}
