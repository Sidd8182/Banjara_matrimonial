<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorldLocationSeeder extends Seeder
{
    public function run()
    {
        $baseDir = storage_path('app/location-data');
        $countriesPath = $baseDir . DIRECTORY_SEPARATOR . 'countries.json';
        $statesPath = $baseDir . DIRECTORY_SEPARATOR . 'states.json';
        $citiesPath = $baseDir . DIRECTORY_SEPARATOR . 'cities.json';

        if (!file_exists($countriesPath) || !file_exists($statesPath) || !file_exists($citiesPath)) {
            $this->command?->error('Location dataset missing. Please place countries.json, states.json and cities.json in storage/app/location-data.');
            return;
        }

        $countries = json_decode(file_get_contents($countriesPath), true);
        $states = json_decode(file_get_contents($statesPath), true);
        $cities = json_decode(file_get_contents($citiesPath), true);

        if (!is_array($countries) || !is_array($states) || !is_array($cities)) {
            $this->command?->error('Invalid JSON content in world location dataset files.');
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        City::query()->truncate();
        State::query()->truncate();
        Country::query()->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command?->info('Importing countries...');

        $countryRows = [];
        foreach ($countries as $country) {
            $name = trim((string) ($country['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $countryRows[] = [
                'source_id' => isset($country['id']) ? (int) $country['id'] : null,
                'name' => $name,
                'iso2' => isset($country['iso2']) ? strtoupper((string) $country['iso2']) : null,
                'phone_code' => isset($country['phonecode'])
                    ? substr('+' . ltrim((string) $country['phonecode'], '+'), 0, 10)
                    : null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($countryRows, 500) as $chunk) {
            Country::query()->insert($chunk);
        }

        $countryMap = Country::query()->pluck('id', 'source_id')->toArray();

        $this->command?->info('Importing states...');

        $stateRows = [];
        $stateKeyToSource = [];
        $stateSourceAlias = [];
        foreach ($states as $state) {
            $sourceCountryId = isset($state['country_id']) ? (int) $state['country_id'] : null;
            $countryId = $countryMap[$sourceCountryId] ?? null;
            $name = trim((string) ($state['name'] ?? ''));
            $sourceStateId = isset($state['id']) ? (int) $state['id'] : null;

            if (!$countryId || $name === '') {
                continue;
            }

            $stateUniqueKey = $countryId . '|' . mb_strtolower($name);
            if (isset($stateKeyToSource[$stateUniqueKey])) {
                if ($sourceStateId) {
                    $stateSourceAlias[$sourceStateId] = $stateKeyToSource[$stateUniqueKey];
                }
                continue;
            }

            if ($sourceStateId) {
                $stateKeyToSource[$stateUniqueKey] = $sourceStateId;
            }

            $stateRows[] = [
                'source_id' => $sourceStateId,
                'country_id' => $countryId,
                'name' => $name,
                'code' => isset($state['state_code']) ? strtoupper((string) $state['state_code']) : null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($stateRows, 1000) as $chunk) {
            State::query()->insert($chunk);
        }

        $stateMap = State::query()->pluck('id', 'source_id')->toArray();

        $this->command?->info('Importing cities (this may take a few minutes)...');

        $cityRows = [];
        $cityUniqueSet = [];
        $counter = 0;
        foreach ($cities as $city) {
            $sourceStateId = isset($city['state_id']) ? (int) $city['state_id'] : null;
            $canonicalSourceStateId = $stateSourceAlias[$sourceStateId] ?? $sourceStateId;
            $stateId = $stateMap[$canonicalSourceStateId] ?? null;
            $name = trim((string) ($city['name'] ?? ''));

            if (!$stateId || $name === '') {
                continue;
            }

            $sourceCountryId = isset($city['country_id']) ? (int) $city['country_id'] : null;
            $countryId = $countryMap[$sourceCountryId] ?? null;

            if (!$countryId) {
                continue;
            }

            $cityUniqueKey = $stateId . '|' . mb_strtolower($name);
            if (isset($cityUniqueSet[$cityUniqueKey])) {
                continue;
            }
            $cityUniqueSet[$cityUniqueKey] = true;

            $cityRows[] = [
                'source_id' => isset($city['id']) ? (int) $city['id'] : null,
                'country_id' => $countryId,
                'state_id' => $stateId,
                'name' => $name,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($cityRows) >= 2000) {
                City::query()->insert($cityRows);
                $counter += count($cityRows);
                $cityRows = [];
            }
        }

        if (!empty($cityRows)) {
            City::query()->insert($cityRows);
            $counter += count($cityRows);
        }

        $this->command?->info('World location import completed. Inserted cities: ' . $counter);
    }
}
