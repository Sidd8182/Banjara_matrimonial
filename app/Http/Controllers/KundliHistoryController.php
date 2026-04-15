<?php

namespace App\Http\Controllers;

use App\Models\KundliMatchResult;
use App\Models\Profile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class KundliHistoryController extends Controller
{
    public function index(Request $request)
    {
        $viewer = $request->user();

        $viewerProfiles = Profile::query()
            ->where('user_id', $viewer->id)
            ->pluck('id')
            ->values();

        if ($viewerProfiles->isEmpty()) {
            return Inertia::render('KundliHistory', [
                'rows' => [],
            ]);
        }

        $rows = KundliMatchResult::query()
            ->with([
                'maleProfile:id,first_name,last_name,date_of_birth,city,state,country,profile_picture_path',
                'femaleProfile:id,first_name,last_name,date_of_birth,city,state,country,profile_picture_path',
            ])
            ->where(function ($query) use ($viewerProfiles) {
                $query->whereIn('male_profile_id', $viewerProfiles)
                    ->orWhereIn('female_profile_id', $viewerProfiles);
            })
            ->orderByDesc('computed_at')
            ->orderByDesc('id')
            ->limit(200)
            ->get()
            ->map(function (KundliMatchResult $result) use ($viewerProfiles) {
                $viewerIsMale = $viewerProfiles->contains((int) $result->male_profile_id);

                $selfProfile = $viewerIsMale ? $result->maleProfile : $result->femaleProfile;
                $targetProfile = $viewerIsMale ? $result->femaleProfile : $result->maleProfile;

                $selfName = trim((string) (optional($selfProfile)->first_name . ' ' . optional($selfProfile)->last_name));
                $targetName = trim((string) (optional($targetProfile)->first_name . ' ' . optional($targetProfile)->last_name));

                $targetAge = null;
                if (optional($targetProfile)->date_of_birth) {
                    $targetAge = Carbon::parse($targetProfile->date_of_birth)->age;
                }

                $targetLocation = trim((string) (
                    optional($targetProfile)->city
                    ?: optional($targetProfile)->state
                    ?: optional($targetProfile)->country
                    ?: 'N/A'
                ));

                return [
                    'id' => $result->id,
                    'selfProfileId' => optional($selfProfile)->id,
                    'selfName' => $selfName !== '' ? $selfName : 'You',
                    'targetProfileId' => optional($targetProfile)->id,
                    'targetName' => $targetName !== '' ? $targetName : 'N/A',
                    'targetAge' => $targetAge,
                    'targetLocation' => $targetLocation,
                    'targetProfilePictureUrl' => optional($targetProfile)->profile_picture_path
                        ? Storage::url($targetProfile->profile_picture_path)
                        : null,
                    'gunaScore' => $result->guna_score !== null ? (float) $result->guna_score : null,
                    'gunaTotal' => (int) ($result->guna_total ?: 36),
                    'percentage' => $result->percentage !== null ? (float) $result->percentage : null,
                    'computedAt' => optional($result->computed_at)->toDateTimeString(),
                    'targetProfileUrl' => optional($targetProfile)->id ? route('profiles.view', ['profile' => $targetProfile->id]) : null,
                    'matchUrl' => optional($targetProfile)->id ? route('profiles.match', ['profile' => $targetProfile->id]) : null,
                ];
            })
            ->values();

        return Inertia::render('KundliHistory', [
            'rows' => $rows,
        ]);
    }
}
