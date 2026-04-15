<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $locationTree = [
            [
                'country' => ['name' => 'India', 'iso2' => 'IN', 'phone_code' => '+91'],
                'states' => [
                    ['name' => 'Maharashtra', 'code' => 'MH', 'cities' => ['Mumbai', 'Pune', 'Nagpur', 'Nashik']],
                    ['name' => 'Uttar Pradesh', 'code' => 'UP', 'cities' => ['Lucknow', 'Kanpur', 'Noida', 'Varanasi']],
                    ['name' => 'Karnataka', 'code' => 'KA', 'cities' => ['Bengaluru', 'Mysuru', 'Mangaluru', 'Hubballi']],
                    ['name' => 'Gujarat', 'code' => 'GJ', 'cities' => ['Ahmedabad', 'Surat', 'Vadodara', 'Rajkot']],
                    ['name' => 'Rajasthan', 'code' => 'RJ', 'cities' => ['Jaipur', 'Jodhpur', 'Udaipur', 'Kota']],
                    ['name' => 'West Bengal', 'code' => 'WB', 'cities' => ['Kolkata', 'Howrah', 'Siliguri', 'Durgapur']],
                ],
            ],
            [
                'country' => ['name' => 'United States', 'iso2' => 'US', 'phone_code' => '+1'],
                'states' => [
                    ['name' => 'California', 'code' => 'CA', 'cities' => ['Los Angeles', 'San Francisco', 'San Diego', 'Sacramento']],
                    ['name' => 'Texas', 'code' => 'TX', 'cities' => ['Houston', 'Dallas', 'Austin', 'San Antonio']],
                    ['name' => 'New York', 'code' => 'NY', 'cities' => ['New York City', 'Buffalo', 'Rochester', 'Albany']],
                    ['name' => 'Florida', 'code' => 'FL', 'cities' => ['Miami', 'Orlando', 'Tampa', 'Jacksonville']],
                ],
            ],
            [
                'country' => ['name' => 'Canada', 'iso2' => 'CA', 'phone_code' => '+1'],
                'states' => [
                    ['name' => 'Ontario', 'code' => 'ON', 'cities' => ['Toronto', 'Ottawa', 'Mississauga', 'Hamilton']],
                    ['name' => 'British Columbia', 'code' => 'BC', 'cities' => ['Vancouver', 'Victoria', 'Surrey', 'Burnaby']],
                    ['name' => 'Alberta', 'code' => 'AB', 'cities' => ['Calgary', 'Edmonton', 'Red Deer', 'Lethbridge']],
                ],
            ],
            [
                'country' => ['name' => 'United Kingdom', 'iso2' => 'GB', 'phone_code' => '+44'],
                'states' => [
                    ['name' => 'England', 'code' => 'ENG', 'cities' => ['London', 'Manchester', 'Birmingham', 'Leeds']],
                    ['name' => 'Scotland', 'code' => 'SCT', 'cities' => ['Edinburgh', 'Glasgow', 'Aberdeen', 'Dundee']],
                    ['name' => 'Wales', 'code' => 'WLS', 'cities' => ['Cardiff', 'Swansea', 'Newport', 'Wrexham']],
                ],
            ],
            [
                'country' => ['name' => 'Australia', 'iso2' => 'AU', 'phone_code' => '+61'],
                'states' => [
                    ['name' => 'New South Wales', 'code' => 'NSW', 'cities' => ['Sydney', 'Newcastle', 'Wollongong', 'Maitland']],
                    ['name' => 'Victoria', 'code' => 'VIC', 'cities' => ['Melbourne', 'Geelong', 'Ballarat', 'Bendigo']],
                    ['name' => 'Queensland', 'code' => 'QLD', 'cities' => ['Brisbane', 'Gold Coast', 'Cairns', 'Townsville']],
                ],
            ],
            [
                'country' => ['name' => 'United Arab Emirates', 'iso2' => 'AE', 'phone_code' => '+971'],
                'states' => [
                    ['name' => 'Dubai', 'code' => 'DU', 'cities' => ['Dubai City', 'Jebel Ali', 'Al Awir']],
                    ['name' => 'Abu Dhabi', 'code' => 'AZ', 'cities' => ['Abu Dhabi City', 'Al Ain', 'Madinat Zayed']],
                    ['name' => 'Sharjah', 'code' => 'SH', 'cities' => ['Sharjah City', 'Khor Fakkan', 'Kalba']],
                ],
            ],
            [
                'country' => ['name' => 'Singapore', 'iso2' => 'SG', 'phone_code' => '+65'],
                'states' => [
                    ['name' => 'Central Region', 'code' => 'CR', 'cities' => ['Novena', 'Bukit Merah', 'Queenstown']],
                    ['name' => 'East Region', 'code' => 'ER', 'cities' => ['Tampines', 'Pasir Ris', 'Bedok']],
                    ['name' => 'West Region', 'code' => 'WR', 'cities' => ['Jurong East', 'Bukit Batok', 'Choa Chu Kang']],
                ],
            ],
        ];

        foreach ($locationTree as $countryBlock) {
            $country = Country::query()->firstOrCreate(
                ['name' => $countryBlock['country']['name']],
                [
                    'iso2' => $countryBlock['country']['iso2'],
                    'phone_code' => $countryBlock['country']['phone_code'],
                    'is_active' => true,
                ]
            );

            foreach ($countryBlock['states'] as $stateRow) {
                $state = State::query()->firstOrCreate(
                    [
                        'country_id' => $country->id,
                        'name' => $stateRow['name'],
                    ],
                    [
                        'code' => $stateRow['code'],
                        'is_active' => true,
                    ]
                );

                foreach ($stateRow['cities'] as $cityName) {
                    City::query()->firstOrCreate(
                        [
                            'country_id' => $country->id,
                            'state_id' => $state->id,
                            'name' => $cityName,
                        ],
                        [
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}
