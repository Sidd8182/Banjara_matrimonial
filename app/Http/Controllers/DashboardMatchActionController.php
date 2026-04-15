<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\ProfileMatchAction;
use Illuminate\Http\Request;

class DashboardMatchActionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'target_profile_id' => ['required', 'integer', 'exists:profiles,id'],
            'action' => ['required', 'in:skip,connect'],
            'rejection_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $targetProfile = Profile::query()->select('id', 'user_id')->findOrFail((int) $validated['target_profile_id']);
        if ((int) $targetProfile->user_id === (int) $request->user()->id) {
            return response()->json([
                'message' => 'You cannot perform this action on your own profile.',
            ], 422);
        }

        ProfileMatchAction::updateOrCreate(
            [
                'user_id' => (int) $request->user()->id,
                'target_profile_id' => (int) $targetProfile->id,
            ],
            [
                'action' => (string) $validated['action'],
                'rejection_reason' => $validated['action'] === 'skip'
                    ? (string) ($validated['rejection_reason'] ?? '')
                    : null,
            ]
        );

        return response()->json([
            'message' => 'Action saved.',
            'data' => [
                'target_profile_id' => (int) $targetProfile->id,
                'action' => (string) $validated['action'],
            ],
        ]);
    }
}
