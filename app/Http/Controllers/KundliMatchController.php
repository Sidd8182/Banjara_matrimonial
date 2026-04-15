<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Services\Kundli\KundliMatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KundliMatchController extends Controller
{
    public function match(Request $request, KundliMatchService $service): JsonResponse
    {
        $payload = $request->validate([
            'target_profile_id' => ['required', 'integer', 'exists:profiles,id'],
            'seeker_profile_id' => ['nullable', 'integer', 'exists:profiles,id'],
        ]);

        $seekerProfile = null;
        if (!empty($payload['seeker_profile_id'])) {
            $seekerProfile = Profile::query()
                ->where('id', $payload['seeker_profile_id'])
                ->where('user_id', $request->user()->id)
                ->first();
        }

        if (!$seekerProfile) {
            $seekerProfile = Profile::query()
                ->where('user_id', $request->user()->id)
                ->latest('id')
                ->first();
        }

        if (!$seekerProfile) {
            return response()->json([
                'message' => 'Please complete your profile before kundli matching.',
            ], 422);
        }

        $targetProfile = Profile::query()->findOrFail($payload['target_profile_id']);

        $result = $service->compatibilityForProfiles(
            $seekerProfile->loadMissing('horoscopeDetail'),
            $targetProfile->loadMissing('horoscopeDetail')
        );

        if (!$result) {
            return response()->json([
                'message' => 'Kundli data incomplete. Rashi and Nakshatra required for both profiles.',
                'data' => null,
            ], 422);
        }

        return response()->json([
            'message' => 'Kundli matching calculated successfully.',
            'data' => $result,
        ]);
    }
}
