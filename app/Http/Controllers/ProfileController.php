<?php

namespace App\Http\Controllers;

use App\Models\CareerDetail;
use App\Models\City;
use App\Models\Country;
use App\Models\EducationDetail;
use App\Models\FamilyDetail;
use App\Models\HoroscopeDetail;
use App\Models\LifestyleDetail;
use App\Models\MediaGallery;
use App\Models\PartnerPreference;
use App\Models\Profile;
use App\Models\State;
use App\Models\Verification;
use App\Services\Astrology\AstrologyBirthDetailsService;
use App\Support\SystemSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();

        $profile = Profile::with([
            'familyDetail',
            'educationDetail',
            'careerDetail',
            'lifestyleDetail',
            'horoscopeDetail',
            'partnerPreference',
            'mediaGallery',
            'verification',
        ])->firstOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => $this->extractFirstName((string) $user->name),
                'last_name' => $this->extractLastName((string) $user->name),
                'gender' => $user->gender,
                'contact_email' => $user->email,
                'contact_mobile' => $user->phone,
                'email_verified' => $user->hasVerifiedEmail(),
            ]
        );

        $astrology = SystemSettings::astrology();
        $initialLocationOptions = $this->resolveInitialLocationOptions(
            (string) ($profile->country ?? ''),
            (string) ($profile->state ?? '')
        );

        return Inertia::render('Profiles', [
            'profileData' => $this->serializeProfile($profile),
            'updateStepUrl' => route('profiles.step.update', [], false),
            'autoFetchHoroscopeUrl' => route('profiles.horoscope.auto-fetch', [], false),
            'locationOptionsUrls' => [
                'states' => route('locations.states', [], false),
                'cities' => route('locations.cities', [], false),
            ],
            'masterData' => [
                'rashis' => DB::table('rashi_master')
                    ->where('is_active', true)
                    ->orderBy('id')
                    ->pluck('name')
                    ->values(),
                'nakshatras' => DB::table('nakshatra_master')
                    ->where('is_active', true)
                    ->orderBy('id')
                    ->pluck('name')
                    ->values(),
                'locations' => $initialLocationOptions,
            ],
            'astrologyConfig' => [
                'enabled' => data_get($astrology, 'enabled', false),
                'autoFetchEnabled' => data_get($astrology, 'active_for_birth_details', false),
                'matchmakingEnabled' => data_get($astrology, 'active_for_matchmaking', false),
                'normalMode' => data_get($astrology, 'normal_mode', true),
            ],
        ]);
    }

    public function autoFetchHoroscope(Request $request, AstrologyBirthDetailsService $birthDetailsService)
    {
        $validated = $request->validate([
            'horoscope_date_of_birth' => ['required', 'date', 'before:today'],
            'time_of_birth' => ['required', 'string', 'max:20'],
            'place_of_birth' => ['required', 'string', 'max:150'],
            'birth_state' => ['nullable', 'string', 'max:120'],
        ]);

        $details = $birthDetailsService->fetch([
            'date_of_birth' => $validated['horoscope_date_of_birth'],
            'time_of_birth' => $validated['time_of_birth'],
            'place_of_birth' => $validated['place_of_birth'],
            'state_of_birth' => $validated['birth_state'] ?? null,
        ]);

        if (!$details) {
            return response()->json([
                'message' => 'Birth details auto fetch unavailable right now. Please fill manually.',
            ], 422);
        }

        return response()->json([
            'message' => 'Horoscope details fetched successfully.',
            'data' => [
                'rashi' => $this->normalizeMasterName('rashi_master', data_get($details, 'rashi')),
                'nakshatra' => $this->normalizeMasterName('nakshatra_master', data_get($details, 'nakshatra')),
                'lagna' => $this->normalizeMasterName('rashi_master', data_get($details, 'lagna')),
            ],
        ]);
    }

    public function locationStates(Request $request)
    {
        $validated = $request->validate([
            'country' => ['required', 'string', 'max:120'],
        ]);

        $country = Country::query()
            ->where('is_active', true)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower((string) $validated['country'])])
            ->first();

        if (!$country) {
            return response()->json(['states' => []]);
        }

        $states = State::query()
            ->where('country_id', $country->id)
            ->where('is_active', true)
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name')
            ->values();

        return response()->json([
            'states' => $states,
        ]);
    }

    public function locationCities(Request $request)
    {
        $validated = $request->validate([
            'country' => ['required', 'string', 'max:120'],
            'state' => ['required', 'string', 'max:120'],
        ]);

        $country = Country::query()
            ->where('is_active', true)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower((string) $validated['country'])])
            ->first();

        if (!$country) {
            return response()->json(['cities' => []]);
        }

        $stateIds = State::query()
            ->where('country_id', $country->id)
            ->where('is_active', true)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower((string) $validated['state'])])
            ->pluck('id');

        if ($stateIds->isEmpty()) {
            return response()->json(['cities' => []]);
        }

        $cities = City::query()
            ->whereIn('state_id', $stateIds)
            ->where('is_active', true)
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name')
            ->values();

        return response()->json([
            'cities' => $cities,
        ]);
    }

    public function updateStep(Request $request)
    {
        $step = (int) $request->input('step', 0);
        if ($step < 1 || $step > 10) {
            return back()->withErrors(['step' => 'Invalid step selected.']);
        }

        $profile = Profile::firstOrCreate(['user_id' => $request->user()->id]);
        $validator = Validator::make($request->all(), $this->rulesForStep($step, $profile));
        $this->attachCustomValidation($validator, $step);
        $validated = $validator->validate();

        DB::transaction(function () use ($step, $validated, $request, $profile) {
            if ($step === 1) {
                $this->saveBasicInformation($request, $profile, $validated);
            } elseif ($step === 2) {
                $this->saveLocationDetails($profile, $validated);
            } elseif ($step === 3) {
                $this->saveEducationAndCareer($profile, $validated);
            } elseif ($step === 4) {
                $this->saveFamilyDetails($profile, $validated);
            } elseif ($step === 5) {
                $this->saveLifestyle($profile, $validated);
            } elseif ($step === 6) {
                $this->saveHoroscope($request, $profile, $validated);
            } elseif ($step === 7) {
                $this->saveMedia($request, $profile, $validated);
            } elseif ($step === 8) {
                $this->saveContactDetails($request, $profile, $validated);
            } elseif ($step === 9) {
                $this->savePartnerPreferences($profile, $validated);
            } else {
                $this->saveVerification($request, $profile, $validated);
            }

            $profile->last_completed_step = max((int) $profile->last_completed_step, $step);
            $profile->save();

            $legacyStep = (int) ceil($step / 2);
            $request->user()->update([
                'profile_completion_step' => max((int) $request->user()->profile_completion_step, $legacyStep),
            ]);
        });

        return back()->with('status', 'Step ' . $step . ' saved successfully.');
    }

    private function rulesForStep(int $step, Profile $profile): array
    {
        if ($step === 1) {
            return [
                'step' => ['required', 'integer'],
                'first_name' => ['required', 'string', 'max:80', 'regex:/^[A-Za-z][A-Za-z .\'-]*$/'],
                'last_name' => ['required', 'string', 'max:80', 'regex:/^[A-Za-z][A-Za-z .\'-]*$/'],
                'gender' => ['required', Rule::in(['Male', 'Female', 'Other'])],
                'date_of_birth' => ['required', 'date', 'before:today'],
                'height_cm' => ['nullable', 'integer', 'between:120,230'],
                'weight_kg' => ['nullable', 'numeric', 'between:30,200'],
                'marital_status' => ['required', Rule::in(['Never Married', 'Divorced', 'Widowed'])],
                'mother_tongue' => ['required', 'string', 'max:80', 'regex:/^[A-Za-z][A-Za-z ,.&\'-]*$/'],
                'religion' => ['required', 'string', 'max:80', 'regex:/^[A-Za-z][A-Za-z .\'-]*$/'],
                'caste' => ['nullable', 'string', 'max:120', 'regex:/^[A-Za-z0-9][A-Za-z0-9 .\'-]*$/'],
                'sub_caste' => ['nullable', 'string', 'max:120', 'regex:/^[A-Za-z0-9][A-Za-z0-9 .\'-]*$/'],
                'gotra' => ['nullable', 'string', 'max:120', 'regex:/^[A-Za-z][A-Za-z .\'-]*$/'],
                'profile_created_by' => ['required', Rule::in(['Self', 'Parent', 'Sibling', 'Relative'])],
            ];
        }

        if ($step === 2) {
            return [
                'step' => ['required', 'integer'],
                'country' => ['required', 'string', 'max:100', Rule::exists('countries', 'name')->where('is_active', true)],
                'state' => ['required', 'string', 'max:100'],
                'city' => ['required', 'string', 'max:100'],
                'area_locality' => ['nullable', 'string', 'max:120'],
                'pincode' => ['nullable', 'regex:/^[0-9]{6}$/'],
                'current_address' => ['nullable', 'string', 'max:500'],
                'permanent_address' => ['nullable', 'string', 'max:500'],
                'willing_to_relocate' => ['nullable', 'boolean'],
            ];
        }

        if ($step === 3) {
            return [
                'step' => ['required', 'integer'],
                'highest_qualification' => ['required', 'string', 'max:120'],
                'degree' => ['nullable', 'string', 'max:120'],
                'college_university' => ['nullable', 'string', 'max:150'],
                'field_of_study' => ['nullable', 'string', 'max:120'],
                'occupation_type' => ['required', Rule::in(['Job', 'Business', 'Profession', 'Student', 'Other'])],
                'company_name' => ['nullable', 'string', 'max:150'],
                'job_title' => ['nullable', 'string', 'max:120'],
                'annual_income_range' => ['nullable', 'string', 'max:60'],
                'work_location' => ['nullable', 'string', 'max:120'],
                'work_type' => ['nullable', Rule::in(['Remote', 'Office', 'Hybrid'])],
            ];
        }

        if ($step === 4) {
            return [
                'step' => ['required', 'integer'],
                'father_name' => ['nullable', 'string', 'max:120', 'regex:/^[A-Za-z][A-Za-z .\'-]*$/'],
                'father_occupation' => ['nullable', 'string', 'max:120'],
                'mother_name' => ['nullable', 'string', 'max:120', 'regex:/^[A-Za-z][A-Za-z .\'-]*$/'],
                'mother_occupation' => ['nullable', 'string', 'max:120'],
                'brothers_count' => ['nullable', 'integer', 'between:0,10'],
                'sisters_count' => ['nullable', 'integer', 'between:0,10'],
                'family_type' => ['nullable', Rule::in(['Joint', 'Nuclear'])],
                'family_status' => ['nullable', Rule::in(['Middle Class', 'Upper Middle Class', 'Affluent'])],
                'family_values' => ['nullable', Rule::in(['Traditional', 'Moderate', 'Modern'])],
            ];
        }

        if ($step === 5) {
            return [
                'step' => ['required', 'integer'],
                'diet' => ['nullable', Rule::in(['Veg', 'Non-Veg', 'Jain'])],
                'smoking' => ['nullable', Rule::in(['No', 'Occasionally', 'Yes'])],
                'drinking' => ['nullable', Rule::in(['No', 'Occasionally', 'Yes'])],
                'hobbies' => ['nullable', 'array', 'max:15'],
                'hobbies.*' => ['string', 'max:40', 'regex:/^[A-Za-z0-9][A-Za-z0-9 .,&\'-]*$/'],
                'interests' => ['nullable', 'array', 'max:15'],
                'interests.*' => ['string', 'max:40', 'regex:/^[A-Za-z0-9][A-Za-z0-9 .,&\'-]*$/'],
                'about_me' => ['nullable', 'string', 'max:3000'],
            ];
        }

        if ($step === 6) {
            return [
                'step' => ['required', 'integer'],
                'horoscope_date_of_birth' => ['nullable', 'date', 'before:today'],
                'time_of_birth' => ['nullable', 'string', 'max:20'],
                'place_of_birth' => ['nullable', 'string', 'max:150'],
                'birth_state' => ['nullable', 'string', 'max:120'],
                'rashi' => ['nullable', Rule::exists('rashi_master', 'name')->where('is_active', true)],
                'nakshatra' => ['nullable', Rule::exists('nakshatra_master', 'name')->where('is_active', true)],
                'lagna' => ['nullable', 'string', 'max:80'],
                'manglik' => ['nullable', Rule::in(['Yes', 'No', 'Partial'])],
                'horoscope_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            ];
        }

        if ($step === 7) {
            $rules = [
                'step' => ['required', 'integer'],
                'gallery_images' => ['nullable', 'array', 'max:10'],
                'gallery_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'video_intro' => ['nullable', 'file', 'mimes:mp4,mov,webm', 'max:15360'],
                'media_privacy' => ['required', Rule::in(['Public', 'Protected'])],
            ];

            if (empty($profile->profile_picture_path)) {
                $rules['profile_picture'] = ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
            } else {
                $rules['profile_picture'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
            }

            return $rules;
        }

        if ($step === 8) {
            return [
                'step' => ['required', 'integer'],
                'contact_mobile' => ['nullable', 'regex:/^[6-9][0-9]{9}$/'],
                'contact_email' => ['nullable', 'email:rfc,dns', 'max:120'],
                'whatsapp_number' => ['nullable', 'regex:/^[6-9][0-9]{9}$/'],
                'contact_visibility' => ['required', Rule::in(['Public', 'Premium Only'])],
            ];
        }

        if ($step === 9) {
            return [
                'step' => ['required', 'integer'],
                'age_min' => ['nullable', 'integer', 'between:18,80'],
                'age_max' => ['nullable', 'integer', 'between:18,80'],
                'height_min_cm' => ['nullable', 'integer', 'between:120,230'],
                'height_max_cm' => ['nullable', 'integer', 'between:120,230'],
                'religion_preference' => ['nullable', 'string', 'max:120'],
                'caste_preference' => ['nullable', 'string', 'max:120'],
                'location_preference' => ['nullable', 'string', 'max:180'],
                'preferred_cities' => ['nullable', 'array', 'max:20'],
                'preferred_cities.*' => ['string', 'max:80', 'regex:/^[A-Za-z0-9][A-Za-z0-9 .,&\'-]*$/'],
                'minimum_qualification' => ['nullable', 'string', 'max:120'],
                'preferred_qualifications' => ['nullable', 'array', 'max:15'],
                'preferred_qualifications.*' => ['string', 'max:120', 'regex:/^[A-Za-z0-9][A-Za-z0-9 .,&\'-]*$/'],
                'preferred_profession' => ['nullable', 'string', 'max:120'],
                'preferred_professions' => ['nullable', 'array', 'max:15'],
                'preferred_professions.*' => ['string', 'max:120', 'regex:/^[A-Za-z0-9][A-Za-z0-9 .,&\'-]*$/'],
                'income_expectation' => ['nullable', 'string', 'max:80'],
                'diet_preference' => ['nullable', Rule::in(['Veg', 'Non-Veg', 'Jain', 'Any'])],
                'smoking_preference' => ['nullable', Rule::in(['No', 'Occasionally', 'Yes', 'Any'])],
                'drinking_preference' => ['nullable', Rule::in(['No', 'Occasionally', 'Yes', 'Any'])],
                'manglik_preference' => ['nullable', Rule::in(['Yes', 'No', 'Partial', 'Any'])],
                'relocate_preference' => ['nullable', 'boolean'],
                'expectations' => ['nullable', 'string', 'max:2000'],
            ];
        }

        return [
            'step' => ['required', 'integer'],
            'profile_verified_badge' => ['nullable', 'boolean'],
            'id_proof_type' => ['nullable', Rule::in(['Aadhar', 'PAN', 'Passport', 'Driving License'])],
            'id_proof_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'photo_verified' => ['nullable', 'boolean'],
            'mobile_verified' => ['nullable', 'boolean'],
            'email_verified' => ['nullable', 'boolean'],
        ];
    }

    private function attachCustomValidation($validator, int $step): void
    {
        $validator->after(function ($innerValidator) use ($step) {
            $data = $innerValidator->getData();

            if ($step === 5 && !empty($data['about_me'])) {
                $wordCount = str_word_count((string) $data['about_me']);
                if ($wordCount > 500) {
                    $innerValidator->errors()->add('about_me', 'About Me must be within 500 words.');
                }
            }

            if ($step === 9) {
                if (!empty($data['age_min']) && !empty($data['age_max']) && (int) $data['age_max'] < (int) $data['age_min']) {
                    $innerValidator->errors()->add('age_max', 'Maximum age must be greater than or equal to minimum age.');
                }

                if (!empty($data['height_min_cm']) && !empty($data['height_max_cm']) && (int) $data['height_max_cm'] < (int) $data['height_min_cm']) {
                    $innerValidator->errors()->add('height_max_cm', 'Maximum height must be greater than or equal to minimum height.');
                }
            }

            if ($step === 2) {
                $country = Country::query()
                    ->where('is_active', true)
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower((string) ($data['country'] ?? ''))])
                    ->first();

                if (!$country) {
                    $innerValidator->errors()->add('country', 'Please select a valid country.');
                    return;
                }

                $stateIds = State::query()
                    ->where('country_id', $country->id)
                    ->where('is_active', true)
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower((string) ($data['state'] ?? ''))])
                    ->pluck('id');

                if ($stateIds->isEmpty()) {
                    $innerValidator->errors()->add('state', 'Selected state is not valid for the chosen country.');
                    return;
                }

                $cityExists = City::query()
                    ->where('country_id', $country->id)
                    ->whereIn('state_id', $stateIds)
                    ->where('is_active', true)
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower((string) ($data['city'] ?? ''))])
                    ->exists();

                if (!$cityExists) {
                    $innerValidator->errors()->add('city', 'Selected city is not valid for the chosen state.');
                }
            }
        });
    }

    private function resolveInitialLocationOptions(string $countryName, string $stateName): array
    {
        $countries = Country::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name')
            ->values();

        $states = collect();
        $cities = collect();

        if ($countryName !== '') {
            $country = Country::query()
                ->where('is_active', true)
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($countryName)])
                ->first();

            if ($country) {
                $states = State::query()
                    ->where('country_id', $country->id)
                    ->where('is_active', true)
                    ->select('name')
                    ->distinct()
                    ->orderBy('name')
                    ->pluck('name')
                    ->values();

                if ($stateName !== '') {
                    $stateIds = State::query()
                        ->where('country_id', $country->id)
                        ->where('is_active', true)
                        ->whereRaw('LOWER(name) = ?', [mb_strtolower($stateName)])
                        ->pluck('id');

                    if ($stateIds->isNotEmpty()) {
                        $cities = City::query()
                            ->where('country_id', $country->id)
                            ->whereIn('state_id', $stateIds)
                            ->where('is_active', true)
                            ->select('name')
                            ->distinct()
                            ->orderBy('name')
                            ->pluck('name')
                            ->values();
                    }
                }
            }
        }

        return [
            'countries' => $countries,
            'states' => $states,
            'cities' => $cities,
        ];
    }

    private function saveBasicInformation(Request $request, Profile $profile, array $validated): void
    {
        $profile->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'gender' => $validated['gender'],
            'date_of_birth' => $validated['date_of_birth'],
            'height_cm' => $validated['height_cm'] ?? null,
            'weight_kg' => $validated['weight_kg'] ?? null,
            'marital_status' => $validated['marital_status'],
            'mother_tongue' => $validated['mother_tongue'],
            'religion' => $validated['religion'],
            'caste' => $validated['caste'] ?? null,
            'sub_caste' => $validated['sub_caste'] ?? null,
            'gotra' => $validated['gotra'] ?? null,
            'profile_created_by' => $validated['profile_created_by'],
        ]);

        $request->user()->update([
            'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
            'gender' => $validated['gender'],
        ]);
    }

    private function saveLocationDetails(Profile $profile, array $validated): void
    {
        $profile->update([
            'country' => $validated['country'],
            'state' => $validated['state'],
            'city' => $validated['city'],
            'area_locality' => $validated['area_locality'] ?? null,
            'pincode' => $validated['pincode'] ?? null,
            'current_address' => $validated['current_address'] ?? null,
            'permanent_address' => $validated['permanent_address'] ?? null,
            'willing_to_relocate' => $validated['willing_to_relocate'] ?? null,
        ]);
    }

    private function saveEducationAndCareer(Profile $profile, array $validated): void
    {
        EducationDetail::updateOrCreate(
            ['profile_id' => $profile->id],
            [
                'highest_qualification' => $validated['highest_qualification'],
                'degree' => $validated['degree'] ?? null,
                'college_university' => $validated['college_university'] ?? null,
                'field_of_study' => $validated['field_of_study'] ?? null,
            ]
        );

        CareerDetail::updateOrCreate(
            ['profile_id' => $profile->id],
            [
                'occupation_type' => $validated['occupation_type'],
                'company_name' => $validated['company_name'] ?? null,
                'job_title' => $validated['job_title'] ?? null,
                'annual_income_range' => $validated['annual_income_range'] ?? null,
                'work_location' => $validated['work_location'] ?? null,
                'work_type' => $validated['work_type'] ?? null,
            ]
        );
    }

    private function saveFamilyDetails(Profile $profile, array $validated): void
    {
        FamilyDetail::updateOrCreate(
            ['profile_id' => $profile->id],
            [
                'father_name' => $validated['father_name'] ?? null,
                'father_occupation' => $validated['father_occupation'] ?? null,
                'mother_name' => $validated['mother_name'] ?? null,
                'mother_occupation' => $validated['mother_occupation'] ?? null,
                'brothers_count' => $validated['brothers_count'] ?? null,
                'sisters_count' => $validated['sisters_count'] ?? null,
                'family_type' => $validated['family_type'] ?? null,
                'family_status' => $validated['family_status'] ?? null,
                'family_values' => $validated['family_values'] ?? null,
            ]
        );
    }

    private function saveLifestyle(Profile $profile, array $validated): void
    {
        LifestyleDetail::updateOrCreate(
            ['profile_id' => $profile->id],
            [
                'diet' => $validated['diet'] ?? null,
                'smoking' => $validated['smoking'] ?? null,
                'drinking' => $validated['drinking'] ?? null,
                'hobbies' => $validated['hobbies'] ?? [],
                'interests' => $validated['interests'] ?? [],
                'about_me' => $validated['about_me'] ?? null,
            ]
        );
    }

    private function saveHoroscope(Request $request, Profile $profile, array $validated): void
    {
        $payload = [
            'date_of_birth' => $validated['horoscope_date_of_birth'] ?? null,
            'time_of_birth' => $validated['time_of_birth'] ?? null,
            'place_of_birth' => $validated['place_of_birth'] ?? null,
            'birth_state' => $validated['birth_state'] ?? null,
            'rashi' => $validated['rashi'] ?? null,
            'nakshatra' => $validated['nakshatra'] ?? null,
            'lagna' => $validated['lagna'] ?? null,
            'manglik' => $validated['manglik'] ?? null,
        ];

        $needsAutoFetch =
            empty($payload['rashi']) ||
            empty($payload['nakshatra']) ||
            empty($payload['lagna']);

        if ($needsAutoFetch && !empty($payload['date_of_birth']) && !empty($payload['time_of_birth']) && !empty($payload['place_of_birth'])) {
            $details = app(AstrologyBirthDetailsService::class)->fetch([
                'date_of_birth' => $payload['date_of_birth'],
                'time_of_birth' => $payload['time_of_birth'],
                'place_of_birth' => $payload['place_of_birth'],
                'state_of_birth' => $payload['birth_state'],
            ]);

            if ($details) {
                if (empty($payload['rashi'])) {
                    $payload['rashi'] = $this->normalizeMasterName('rashi_master', data_get($details, 'rashi'));
                }

                if (empty($payload['nakshatra'])) {
                    $payload['nakshatra'] = $this->normalizeMasterName('nakshatra_master', data_get($details, 'nakshatra'));
                }

                if (empty($payload['lagna'])) {
                    $payload['lagna'] = $this->normalizeMasterName('rashi_master', data_get($details, 'lagna'));
                }
            }
        }

        if ($request->hasFile('horoscope_file')) {
            $payload['horoscope_path'] = $request->file('horoscope_file')->store('profiles/horoscope', 'public');
        }

        HoroscopeDetail::updateOrCreate(['profile_id' => $profile->id], $payload);
    }

    private function saveMedia(Request $request, Profile $profile, array $validated): void
    {
        if ($request->hasFile('profile_picture')) {
            $profile->profile_picture_path = $request->file('profile_picture')->store('profiles/pictures', 'public');
        }

        if ($request->hasFile('video_intro')) {
            $profile->video_intro_path = $request->file('video_intro')->store('profiles/videos', 'public');
        }

        $uploadedImages = $request->file('gallery_images', []);
        if (!empty($uploadedImages)) {
            $existingCount = $profile->mediaGallery()->where('media_type', 'image')->count();
            if (($existingCount + count($uploadedImages)) > 10) {
                throw ValidationException::withMessages([
                    'gallery_images' => 'Maximum 10 gallery images are allowed.',
                ]);
            }

            $nextSort = (int) ($profile->mediaGallery()->max('sort_order') ?? 0);
            foreach ($uploadedImages as $image) {
                $nextSort++;
                MediaGallery::create([
                    'profile_id' => $profile->id,
                    'file_path' => $image->store('profiles/gallery', 'public'),
                    'media_type' => 'image',
                    'sort_order' => $nextSort,
                ]);
            }
        }

        $profile->media_privacy = $validated['media_privacy'];
        $profile->save();
    }

    private function saveContactDetails(Request $request, Profile $profile, array $validated): void
    {
        $profile->update([
            'contact_mobile' => $validated['contact_mobile'] ?? null,
            'contact_email' => $validated['contact_email'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
            'contact_visibility' => $validated['contact_visibility'],
        ]);

        if (!empty($validated['contact_mobile'])) {
            $request->user()->update(['phone' => $validated['contact_mobile']]);
        }
    }

    private function savePartnerPreferences(Profile $profile, array $validated): void
    {
        $preferredCities = $this->normalizePreferenceList($validated['preferred_cities'] ?? []);
        $preferredQualifications = $this->normalizePreferenceList($validated['preferred_qualifications'] ?? []);
        $preferredProfessions = $this->normalizePreferenceList($validated['preferred_professions'] ?? []);

        $legacyLocationPreference = $validated['location_preference'] ?? null;
        if (!$legacyLocationPreference && !empty($preferredCities)) {
            $legacyLocationPreference = $preferredCities[0];
        }

        $legacyMinimumQualification = $validated['minimum_qualification'] ?? null;
        if (!$legacyMinimumQualification && !empty($preferredQualifications)) {
            $legacyMinimumQualification = $preferredQualifications[0];
        }

        $legacyPreferredProfession = $validated['preferred_profession'] ?? null;
        if (!$legacyPreferredProfession && !empty($preferredProfessions)) {
            $legacyPreferredProfession = $preferredProfessions[0];
        }

        PartnerPreference::updateOrCreate(
            ['profile_id' => $profile->id],
            [
                'age_min' => $validated['age_min'] ?? null,
                'age_max' => $validated['age_max'] ?? null,
                'height_min_cm' => $validated['height_min_cm'] ?? null,
                'height_max_cm' => $validated['height_max_cm'] ?? null,
                'religion_preference' => $validated['religion_preference'] ?? null,
                'caste_preference' => $validated['caste_preference'] ?? null,
                'location_preference' => $legacyLocationPreference,
                'preferred_cities' => $preferredCities,
                'minimum_qualification' => $legacyMinimumQualification,
                'preferred_qualifications' => $preferredQualifications,
                'preferred_profession' => $legacyPreferredProfession,
                'preferred_professions' => $preferredProfessions,
                'income_expectation' => $validated['income_expectation'] ?? null,
                'diet_preference' => $validated['diet_preference'] ?? null,
                'smoking_preference' => $validated['smoking_preference'] ?? null,
                'drinking_preference' => $validated['drinking_preference'] ?? null,
                'manglik_preference' => $validated['manglik_preference'] ?? null,
                'relocate_preference' => $validated['relocate_preference'] ?? null,
                'expectations' => $validated['expectations'] ?? null,
            ]
        );
    }

    private function saveVerification(Request $request, Profile $profile, array $validated): void
    {
        $payload = [
            'profile_verified_badge' => (bool) ($validated['profile_verified_badge'] ?? false),
            'id_proof_type' => $validated['id_proof_type'] ?? null,
            'photo_verified' => (bool) ($validated['photo_verified'] ?? false),
            'mobile_verified' => (bool) ($validated['mobile_verified'] ?? false),
            'email_verified' => (bool) ($validated['email_verified'] ?? false),
        ];

        if ($request->hasFile('id_proof_file')) {
            $payload['id_proof_path'] = $request->file('id_proof_file')->store('profiles/id-proof', 'public');
        }

        Verification::updateOrCreate(['profile_id' => $profile->id], $payload);

        $profile->update([
            'mobile_verified' => $payload['mobile_verified'],
            'email_verified' => $payload['email_verified'],
        ]);
    }

    private function serializeProfile(Profile $profile): array
    {
        $age = $profile->date_of_birth ? now()->diffInYears($profile->date_of_birth) : null;
        $heightFeet = $profile->height_cm ? round(((float) $profile->height_cm) / 30.48, 2) : null;

        return [
            'meta' => [
                'profile_id' => $profile->profile_id,
                'last_completed_step' => (int) $profile->last_completed_step,
                'age' => $age,
                'height_feet' => $heightFeet,
            ],
            'basic' => [
                'first_name' => $profile->first_name,
                'last_name' => $profile->last_name,
                'gender' => $profile->gender,
                'date_of_birth' => optional($profile->date_of_birth)->format('Y-m-d'),
                'height_cm' => $profile->height_cm,
                'weight_kg' => $profile->weight_kg,
                'marital_status' => $profile->marital_status,
                'mother_tongue' => $profile->mother_tongue,
                'religion' => $profile->religion,
                'caste' => $profile->caste,
                'sub_caste' => $profile->sub_caste,
                'gotra' => $profile->gotra,
                'profile_created_by' => $profile->profile_created_by,
            ],
            'location' => [
                'country' => $profile->country,
                'state' => $profile->state,
                'city' => $profile->city,
                'area_locality' => $profile->area_locality,
                'pincode' => $profile->pincode,
                'current_address' => $profile->current_address,
                'permanent_address' => $profile->permanent_address,
                'willing_to_relocate' => $profile->willing_to_relocate,
            ],
            'education' => [
                'highest_qualification' => $profile->educationDetail->highest_qualification ?? null,
                'degree' => $profile->educationDetail->degree ?? null,
                'college_university' => $profile->educationDetail->college_university ?? null,
                'field_of_study' => $profile->educationDetail->field_of_study ?? null,
            ],
            'career' => [
                'occupation_type' => $profile->careerDetail->occupation_type ?? null,
                'company_name' => $profile->careerDetail->company_name ?? null,
                'job_title' => $profile->careerDetail->job_title ?? null,
                'annual_income_range' => $profile->careerDetail->annual_income_range ?? null,
                'work_location' => $profile->careerDetail->work_location ?? null,
                'work_type' => $profile->careerDetail->work_type ?? null,
            ],
            'family' => [
                'father_name' => $profile->familyDetail->father_name ?? null,
                'father_occupation' => $profile->familyDetail->father_occupation ?? null,
                'mother_name' => $profile->familyDetail->mother_name ?? null,
                'mother_occupation' => $profile->familyDetail->mother_occupation ?? null,
                'brothers_count' => $profile->familyDetail->brothers_count ?? null,
                'sisters_count' => $profile->familyDetail->sisters_count ?? null,
                'family_type' => $profile->familyDetail->family_type ?? null,
                'family_status' => $profile->familyDetail->family_status ?? null,
                'family_values' => $profile->familyDetail->family_values ?? null,
            ],
            'lifestyle' => [
                'diet' => $profile->lifestyleDetail->diet ?? null,
                'smoking' => $profile->lifestyleDetail->smoking ?? null,
                'drinking' => $profile->lifestyleDetail->drinking ?? null,
                'hobbies' => $profile->lifestyleDetail->hobbies ?? [],
                'interests' => $profile->lifestyleDetail->interests ?? [],
                'about_me' => $profile->lifestyleDetail->about_me ?? null,
            ],
            'horoscope' => [
                'horoscope_date_of_birth' => optional($profile->horoscopeDetail->date_of_birth ?? null)->format('Y-m-d'),
                'time_of_birth' => $profile->horoscopeDetail->time_of_birth ?? null,
                'place_of_birth' => $profile->horoscopeDetail->place_of_birth ?? null,
                'birth_state' => $profile->horoscopeDetail->birth_state ?? null,
                'rashi' => $profile->horoscopeDetail->rashi ?? null,
                'nakshatra' => $profile->horoscopeDetail->nakshatra ?? null,
                'lagna' => $profile->horoscopeDetail->lagna ?? null,
                'manglik' => $profile->horoscopeDetail->manglik ?? null,
                'horoscope_path' => $profile->horoscopeDetail && $profile->horoscopeDetail->horoscope_path
                    ? Storage::url($profile->horoscopeDetail->horoscope_path)
                    : null,
            ],
            'media' => [
                'profile_picture_path' => $profile->profile_picture_path ? Storage::url($profile->profile_picture_path) : null,
                'video_intro_path' => $profile->video_intro_path ? Storage::url($profile->video_intro_path) : null,
                'media_privacy' => $profile->media_privacy,
                'gallery' => $profile->mediaGallery
                    ->where('media_type', 'image')
                    ->map(fn ($item) => [
                        'id' => $item->id,
                        'url' => Storage::url($item->file_path),
                    ])
                    ->values(),
            ],
            'contact' => [
                'contact_mobile' => $profile->contact_mobile,
                'contact_email' => $profile->contact_email,
                'whatsapp_number' => $profile->whatsapp_number,
                'contact_visibility' => $profile->contact_visibility,
                'mobile_verified' => (bool) $profile->mobile_verified,
                'email_verified' => (bool) $profile->email_verified,
            ],
            'preferences' => [
                'age_min' => $profile->partnerPreference->age_min ?? null,
                'age_max' => $profile->partnerPreference->age_max ?? null,
                'height_min_cm' => $profile->partnerPreference->height_min_cm ?? null,
                'height_max_cm' => $profile->partnerPreference->height_max_cm ?? null,
                'religion_preference' => $profile->partnerPreference->religion_preference ?? null,
                'caste_preference' => $profile->partnerPreference->caste_preference ?? null,
                'location_preference' => $profile->partnerPreference->location_preference ?? null,
                'preferred_cities' => $profile->partnerPreference->preferred_cities
                    ?? $this->normalizePreferenceList([$profile->partnerPreference->location_preference ?? null]),
                'minimum_qualification' => $profile->partnerPreference->minimum_qualification ?? null,
                'preferred_qualifications' => $profile->partnerPreference->preferred_qualifications
                    ?? $this->normalizePreferenceList([$profile->partnerPreference->minimum_qualification ?? null]),
                'preferred_profession' => $profile->partnerPreference->preferred_profession ?? null,
                'preferred_professions' => $profile->partnerPreference->preferred_professions
                    ?? $this->normalizePreferenceList([$profile->partnerPreference->preferred_profession ?? null]),
                'income_expectation' => $profile->partnerPreference->income_expectation ?? null,
                'diet_preference' => $profile->partnerPreference->diet_preference ?? null,
                'smoking_preference' => $profile->partnerPreference->smoking_preference ?? null,
                'drinking_preference' => $profile->partnerPreference->drinking_preference ?? null,
                'manglik_preference' => $profile->partnerPreference->manglik_preference ?? null,
                'relocate_preference' => $profile->partnerPreference->relocate_preference ?? null,
                'expectations' => $profile->partnerPreference->expectations ?? null,
            ],
            'verification' => [
                'profile_verified_badge' => (bool) ($profile->verification->profile_verified_badge ?? false),
                'id_proof_type' => $profile->verification->id_proof_type ?? null,
                'id_proof_path' => $profile->verification && $profile->verification->id_proof_path
                    ? Storage::url($profile->verification->id_proof_path)
                    : null,
                'photo_verified' => (bool) ($profile->verification->photo_verified ?? false),
                'mobile_verified' => (bool) ($profile->verification->mobile_verified ?? false),
                'email_verified' => (bool) ($profile->verification->email_verified ?? false),
            ],
        ];
    }

    private function normalizePreferenceList(array $values): array
    {
        $cleaned = collect($values)
            ->map(function ($value) {
                return trim((string) $value);
            })
            ->filter(function ($value) {
                return $value !== '';
            })
            ->unique(function ($value) {
                return mb_strtolower($value);
            })
            ->values()
            ->all();

        return $cleaned;
    }

    private function extractFirstName(string $name): ?string
    {
        $parts = preg_split('/\s+/', trim($name));
        return $parts[0] ?? null;
    }

    private function extractLastName(string $name): ?string
    {
        $parts = preg_split('/\s+/', trim($name));
        if (count($parts) < 2) {
            return null;
        }

        array_shift($parts);
        return (string) Str::of(implode(' ', $parts))->trim();
    }

    private function normalizeMasterName(string $table, ?string $raw): ?string
    {
        $value = trim((string) $raw);
        if ($value === '') {
            return null;
        }

        $value = preg_replace('/\s+/', ' ', $value);

        if ($table === 'rashi_master') {
            $value = $this->normalizeRashiAlias($value);
        }

        if ($table === 'nakshatra_master') {
            $value = $this->normalizeNakshatraAlias($value);
        }

        $canonical = $this->canonicalAstro($value);

        $records = DB::table($table)
            ->where('is_active', true)
            ->pluck('name');

        foreach ($records as $name) {
            if ($this->canonicalAstro((string) $name) === $canonical) {
                return (string) $name;
            }
        }

        $exact = DB::table($table)
            ->where('is_active', true)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($value)])
            ->value('name');

        if ($exact) {
            return (string) $exact;
        }

        $like = DB::table($table)
            ->where('is_active', true)
            ->where('name', 'like', '%' . $value . '%')
            ->value('name');

        return $like ? (string) $like : null;
    }

    private function normalizeRashiAlias(string $value): string
    {
        $aliases = [
            'aries' => 'Mesh (Aries)',
            'mesh' => 'Mesh (Aries)',
            'mesha' => 'Mesh (Aries)',
            'taurus' => 'Vrishabh (Taurus)',
            'vrishabh' => 'Vrishabh (Taurus)',
            'vrishabha' => 'Vrishabh (Taurus)',
            'gemini' => 'Mithun (Gemini)',
            'mithun' => 'Mithun (Gemini)',
            'mithuna' => 'Mithun (Gemini)',
            'cancer' => 'Kark (Cancer)',
            'kark' => 'Kark (Cancer)',
            'karka' => 'Kark (Cancer)',
            'leo' => 'Singh (Leo)',
            'singh' => 'Singh (Leo)',
            'simha' => 'Singh (Leo)',
            'virgo' => 'Kanya (Virgo)',
            'kanya' => 'Kanya (Virgo)',
            'libra' => 'Tula (Libra)',
            'tula' => 'Tula (Libra)',
            'scorpio' => 'Vrishchik (Scorpio)',
            'vrishchik' => 'Vrishchik (Scorpio)',
            'vrischika' => 'Vrishchik (Scorpio)',
            'sagittarius' => 'Dhanu (Sagittarius)',
            'dhanu' => 'Dhanu (Sagittarius)',
            'capricorn' => 'Makar (Capricorn)',
            'makar' => 'Makar (Capricorn)',
            'aquarius' => 'Kumbh (Aquarius)',
            'kumbh' => 'Kumbh (Aquarius)',
            'pisces' => 'Meen (Pisces)',
            'meen' => 'Meen (Pisces)',
            'mina' => 'Meen (Pisces)',
        ];

        $key = $this->canonicalAstro($value);
        return $aliases[$key] ?? $value;
    }

    private function normalizeNakshatraAlias(string $value): string
    {
        $aliases = [
            'aardra' => 'Ardra',
            'ardra' => 'Ardra',
            'ashlesha' => 'Ashlesha',
            'ashlesa' => 'Ashlesha',
            'chitta' => 'Chitra',
            'chitra' => 'Chitra',
            'jyeshta' => 'Jyeshtha',
            'jyestha' => 'Jyeshtha',
            'jyeshtha' => 'Jyeshtha',
            'moola' => 'Moola',
            'mula' => 'Moola',
            'poorvaashaada' => 'Purva Ashadha',
            'purvashada' => 'Purva Ashadha',
            'purvaashadha' => 'Purva Ashadha',
            'purva ashadha' => 'Purva Ashadha',
            'uttaraashaada' => 'Uttara Ashadha',
            'uttarashada' => 'Uttara Ashadha',
            'uttara ashadha' => 'Uttara Ashadha',
            'uttara phalguniuttara' => 'Uttara Phalguni',
            'uttara phalguni' => 'Uttara Phalguni',
            'purva phalguni' => 'Purva Phalguni',
            'vishakha' => 'Vishakha',
            'visakha' => 'Vishakha',
            'shatabhisha' => 'Shatabhisha',
            'shatabhishak' => 'Shatabhisha',
            'dhanishta' => 'Dhanishta',
            'dhanishtha' => 'Dhanishta',
        ];

        $clean = preg_replace('/\(.*?\)/', '', $value);
        $clean = preg_replace('/\s+/', ' ', (string) $clean);
        $key = $this->canonicalAstro((string) $clean);
        return $aliases[$key] ?? trim((string) $clean);
    }

    private function canonicalAstro(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/u', ' ', $value);
        $value = preg_replace('/\s+/', ' ', (string) $value);
        return trim((string) $value);
    }
}
