<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\UserSubscription;
use App\Services\Kundli\KundliMatchService;
use App\Support\SystemSettings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BrowseController extends Controller
{
    public function index(Request $request, KundliMatchService $matchService)
    {
        $viewer = $request->user();
        $viewerProfile = null;

        if ($viewer) {
            $viewerProfile = Profile::with('horoscopeDetail')->where('user_id', $viewer->id)->first();
        }

        $profiles = Profile::query()
            ->with(['careerDetail', 'horoscopeDetail'])
            ->when($viewer, function ($query) use ($viewer) {
                $query->where('user_id', '!=', $viewer->id);
            })
            ->whereNotNull('first_name')
            ->orderByDesc('id')
            ->limit(40)
            ->get();

        $astrologyConfig = SystemSettings::astrology();

        $resultProfiles = $profiles->map(function (Profile $profile) use ($viewerProfile, $matchService) {
            $compatibility = null;
            if ($viewerProfile) {
                $compatibility = $matchService->compatibilityForProfiles($viewerProfile, $profile);
            }

            $age = null;
            if ($profile->date_of_birth) {
                $age = Carbon::parse($profile->date_of_birth)->age;
            }

            return [
                'id' => $profile->id,
                'name' => trim((string) ($profile->first_name . ' ' . $profile->last_name)),
                'age' => $age,
                'location' => trim((string) ($profile->city ?: $profile->state ?: $profile->country ?: '')),
                'religion' => $profile->religion,
                'height' => $profile->height_cm ? ((string) $profile->height_cm . ' cm') : 'N/A',
                'occupation' => (string) optional($profile->careerDetail)->job_title ?: (string) optional($profile->careerDetail)->occupation_type ?: 'N/A',
                'profilePictureUrl' => $profile->profile_picture_path ? Storage::url($profile->profile_picture_path) : null,
                'kundli' => $compatibility,
            ];
        });

        if ($viewerProfile) {
            $resultProfiles = $resultProfiles->sortByDesc(function (array $profile) {
                $score = data_get($profile, 'kundli.percentage');
                return $score === null ? -1 : (float) $score;
            })->values();
        }

        return Inertia::render('Browse', [
            'profiles' => $resultProfiles,
            'astrologyConfig' => [
                'enabled' => data_get($astrologyConfig, 'enabled', false),
                'matchmakingEnabled' => $viewerProfile ? true : data_get($astrologyConfig, 'active_for_matchmaking', false),
                'normalMode' => data_get($astrologyConfig, 'normal_mode', true),
            ],
        ]);
    }

    public function show(Request $request, Profile $profile, KundliMatchService $matchService)
    {
        UserSubscription::expireEnded();

        $profile->load([
            'careerDetail',
            'educationDetail',
            'familyDetail',
            'lifestyleDetail',
            'horoscopeDetail',
            'mediaGallery' => function ($query) {
                $query->where('media_type', 'image')->orderBy('sort_order')->orderBy('id');
            },
        ]);

        $viewer = $request->user();
        $viewerGender = strtolower((string) ($viewer?->gender ?? ''));
        $targetGender = strtolower((string) ($profile->gender ?? ''));

        $viewerActiveSubscription = null;
        if ($viewer) {
            $viewerActiveSubscription = UserSubscription::with('plan')
                ->where('user_id', $viewer->id)
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('ends_at')->orWhere('ends_at', '>', now());
                })
                ->latest('id')
                ->first();
        }
        $isPaidViewer = (bool) $viewerActiveSubscription;

        $viewMode = 'neutral';
        if ($viewerGender === 'male' && $targetGender === 'female') {
            $viewMode = 'male_viewing_female';
        } elseif ($viewerGender === 'female' && $targetGender === 'male') {
            $viewMode = 'female_viewing_male';
        }

        $age = null;
        if ($profile->date_of_birth) {
            $age = Carbon::parse($profile->date_of_birth)->age;
        }

        $viewerProfile = null;
        if ($viewer) {
            $viewerProfile = Profile::query()
                ->with('horoscopeDetail')
                ->where('user_id', $viewer->id)
                ->first();
        }

        $kundliMatch = null;
        if ($viewerProfile && (int) $viewerProfile->id !== (int) $profile->id) {
            $kundliMatch = $matchService->compatibilityForProfiles($viewerProfile, $profile);
        }

        return Inertia::render('ProfileView', [
            'viewer' => [
                'name' => $viewer?->name,
                'gender' => $viewerGender ?: null,
            ],
            'viewerAccess' => [
                'isPaid' => $isPaidViewer,
                'planName' => $viewerActiveSubscription?->plan?->name,
            ],
            'viewMode' => $viewMode,
            'kundliMatch' => $kundliMatch,
            'profile' => [
                'id' => $profile->id,
                'profileId' => $profile->profile_id,
                'name' => trim((string) ($profile->first_name . ' ' . $profile->last_name)),
                'gender' => $targetGender ?: null,
                'age' => $age,
                'location' => trim((string) ($profile->city ?: $profile->state ?: $profile->country ?: 'N/A')),
                'religion' => $profile->religion,
                'caste' => $profile->caste,
                'heightCm' => $profile->height_cm,
                'maritalStatus' => $profile->marital_status,
                'about' => optional($profile->lifestyleDetail)->about_me,
                'profession' => optional($profile->careerDetail)->job_title ?: optional($profile->careerDetail)->occupation_type,
                'company' => optional($profile->careerDetail)->company_name,
                'annualIncome' => optional($profile->careerDetail)->annual_income_range,
                'education' => optional($profile->educationDetail)->highest_qualification,
                'fieldOfStudy' => optional($profile->educationDetail)->field_of_study,
                'motherTongue' => $profile->mother_tongue,
                'diet' => optional($profile->lifestyleDetail)->diet,
                'smoking' => optional($profile->lifestyleDetail)->smoking,
                'drinking' => optional($profile->lifestyleDetail)->drinking,
                'rashi' => optional($profile->horoscopeDetail)->rashi,
                'nakshatra' => optional($profile->horoscopeDetail)->nakshatra,
                'lagna' => optional($profile->horoscopeDetail)->lagna,
                'profilePictureUrl' => $profile->profile_picture_path ? Storage::url($profile->profile_picture_path) : null,
                'gallery' => $profile->mediaGallery->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'url' => Storage::url($item->file_path),
                    ];
                })->values(),
                'family' => $isPaidViewer ? [
                    'fatherName' => optional($profile->familyDetail)->father_name,
                    'fatherOccupation' => optional($profile->familyDetail)->father_occupation,
                    'motherName' => optional($profile->familyDetail)->mother_name,
                    'motherOccupation' => optional($profile->familyDetail)->mother_occupation,
                    'brothersCount' => optional($profile->familyDetail)->brothers_count,
                    'sistersCount' => optional($profile->familyDetail)->sisters_count,
                    'familyType' => optional($profile->familyDetail)->family_type,
                    'familyStatus' => optional($profile->familyDetail)->family_status,
                    'familyValues' => optional($profile->familyDetail)->family_values,
                ] : null,
                'contact' => $isPaidViewer ? [
                    'mobile' => $profile->contact_mobile,
                    'email' => $profile->contact_email,
                    'whatsapp' => $profile->whatsapp_number,
                    'areaLocality' => $profile->area_locality,
                    'currentAddress' => $profile->current_address,
                    'permanentAddress' => $profile->permanent_address,
                ] : null,
            ],
        ]);
    }

    public function match(Request $request, Profile $profile, KundliMatchService $matchService)
    {
        $viewer = $request->user();

        $viewerProfile = Profile::query()
            ->with(['careerDetail', 'educationDetail', 'lifestyleDetail', 'horoscopeDetail'])
            ->where('user_id', $viewer->id)
            ->first();

        if (!$viewerProfile) {
            return redirect()->route('profiles.index');
        }

        $targetProfile = Profile::query()
            ->with(['careerDetail', 'educationDetail', 'lifestyleDetail', 'horoscopeDetail'])
            ->findOrFail($profile->id);

        if ((int) $viewerProfile->id === (int) $targetProfile->id) {
            return redirect()->route('profiles.view', ['profile' => $targetProfile->id]);
        }

        $matchItems = [
            $this->buildMatchItem('Gender Preference', $viewerProfile->gender, $targetProfile->gender, function ($left, $right) {
                $leftNormalized = strtolower((string) $left);
                $rightNormalized = strtolower((string) $right);

                return ($leftNormalized === 'male' && $rightNormalized === 'female')
                    || ($leftNormalized === 'female' && $rightNormalized === 'male');
            }),
            $this->buildMatchItem('Religion', $viewerProfile->religion, $targetProfile->religion),
            $this->buildMatchItem('Caste', $viewerProfile->caste, $targetProfile->caste),
            $this->buildMatchItem('Mother Tongue', $viewerProfile->mother_tongue, $targetProfile->mother_tongue),
            $this->buildMatchItem('City', $viewerProfile->city, $targetProfile->city),
            $this->buildMatchItem('Marital Status', $viewerProfile->marital_status, $targetProfile->marital_status),
            $this->buildMatchItem('Diet', optional($viewerProfile->lifestyleDetail)->diet, optional($targetProfile->lifestyleDetail)->diet),
            $this->buildMatchItem('Smoking', optional($viewerProfile->lifestyleDetail)->smoking, optional($targetProfile->lifestyleDetail)->smoking),
            $this->buildMatchItem('Drinking', optional($viewerProfile->lifestyleDetail)->drinking, optional($targetProfile->lifestyleDetail)->drinking),
            $this->buildMatchItem('Rashi', optional($viewerProfile->horoscopeDetail)->rashi, optional($targetProfile->horoscopeDetail)->rashi),
            $this->buildMatchItem('Nakshatra', optional($viewerProfile->horoscopeDetail)->nakshatra, optional($targetProfile->horoscopeDetail)->nakshatra),
            $this->buildRangeMatchItem('Age Difference', $viewerProfile->date_of_birth, $targetProfile->date_of_birth, 6, 'years'),
            $this->buildRangeMatchItem('Height Difference', $viewerProfile->height_cm, $targetProfile->height_cm, 12, 'cm'),
        ];

        $eligibleCount = collect($matchItems)->where('hasData', true)->count();
        $matchedCount = collect($matchItems)->where('isMatch', true)->count();
        $overallPercentage = $eligibleCount > 0 ? round(($matchedCount / $eligibleCount) * 100, 2) : 0;

        $viewerGender = strtolower((string) ($viewerProfile->gender ?? ''));
        $targetGender = strtolower((string) ($targetProfile->gender ?? ''));

        $kundli = null;
        if ($viewerGender === 'female' && $targetGender === 'male') {
            $kundli = $matchService->compatibilityForProfiles($targetProfile, $viewerProfile);
        } else {
            $kundli = $matchService->compatibilityForProfiles($viewerProfile, $targetProfile);
        }

        return Inertia::render('MatchProfile', [
            'viewerProfile' => $this->matchProfilePayload($viewerProfile),
            'targetProfile' => $this->matchProfilePayload($targetProfile),
            'matchSummary' => [
                'matchedItems' => $matchedCount,
                'totalItems' => $eligibleCount,
                'percentage' => $overallPercentage,
            ],
            'matchItems' => $matchItems,
            'kundli' => $kundli,
        ]);
    }

    private function matchProfilePayload(Profile $profile): array
    {
        $age = null;
        if ($profile->date_of_birth) {
            $age = Carbon::parse($profile->date_of_birth)->age;
        }

        return [
            'id' => $profile->id,
            'name' => trim((string) ($profile->first_name . ' ' . $profile->last_name)),
            'age' => $age,
            'gender' => $profile->gender,
            'city' => $profile->city ?: $profile->state ?: $profile->country ?: 'N/A',
            'occupation' => (string) (optional($profile->careerDetail)->job_title ?: optional($profile->careerDetail)->occupation_type ?: 'Not specified'),
            'religion' => $profile->religion,
            'caste' => $profile->caste,
            'motherTongue' => $profile->mother_tongue,
            'rashi' => optional($profile->horoscopeDetail)->rashi,
            'nakshatra' => optional($profile->horoscopeDetail)->nakshatra,
            'profilePictureUrl' => $profile->profile_picture_path ? Storage::url($profile->profile_picture_path) : null,
        ];
    }

    private function buildMatchItem(string $label, $leftValue, $rightValue, ?callable $resolver = null): array
    {
        $left = trim((string) ($leftValue ?? ''));
        $right = trim((string) ($rightValue ?? ''));
        $hasData = $left !== '' && $right !== '';

        $isMatch = false;
        if ($hasData) {
            if ($resolver) {
                $isMatch = (bool) $resolver($left, $right);
            } else {
                $isMatch = strcasecmp($left, $right) === 0;
            }
        }

        return [
            'label' => $label,
            'leftValue' => $left !== '' ? $left : 'N/A',
            'rightValue' => $right !== '' ? $right : 'N/A',
            'hasData' => $hasData,
            'isMatch' => $isMatch,
        ];
    }

    private function buildRangeMatchItem(string $label, $leftRaw, $rightRaw, float $threshold, string $unit): array
    {
        if ($label === 'Age Difference') {
            $left = $leftRaw ? Carbon::parse($leftRaw)->age : null;
            $right = $rightRaw ? Carbon::parse($rightRaw)->age : null;
        } else {
            $left = is_numeric($leftRaw) ? (float) $leftRaw : null;
            $right = is_numeric($rightRaw) ? (float) $rightRaw : null;
        }

        $hasData = $left !== null && $right !== null;
        $difference = $hasData ? abs($left - $right) : null;

        return [
            'label' => $label,
            'leftValue' => $left !== null ? ((string) $left . ' ' . $unit) : 'N/A',
            'rightValue' => $right !== null ? ((string) $right . ' ' . $unit) : 'N/A',
            'hasData' => $hasData,
            'isMatch' => $hasData ? $difference <= $threshold : false,
            'meta' => [
                'difference' => $difference,
                'threshold' => $threshold,
                'unit' => $unit,
            ],
        ];
    }
}
