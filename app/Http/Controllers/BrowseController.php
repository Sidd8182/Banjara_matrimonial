<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Services\Astrology\AstrologyMatchService;
use App\Support\SystemSettings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BrowseController extends Controller
{
    public function index(Request $request, AstrologyMatchService $matchService)
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

        $resultProfiles = $profiles->map(function (Profile $profile) use ($viewerProfile, $matchService, $astrologyConfig) {
            $compatibility = null;
            if ($viewerProfile && data_get($astrologyConfig, 'active_for_matchmaking', false)) {
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

        if (data_get($astrologyConfig, 'active_for_matchmaking', false)) {
            $resultProfiles = $resultProfiles->sortByDesc(function (array $profile) {
                $score = data_get($profile, 'kundli.percentage');
                return $score === null ? -1 : (float) $score;
            })->values();
        }

        return Inertia::render('Browse', [
            'profiles' => $resultProfiles,
            'astrologyConfig' => [
                'enabled' => data_get($astrologyConfig, 'enabled', false),
                'matchmakingEnabled' => data_get($astrologyConfig, 'active_for_matchmaking', false),
                'normalMode' => data_get($astrologyConfig, 'normal_mode', true),
            ],
        ]);
    }
}
