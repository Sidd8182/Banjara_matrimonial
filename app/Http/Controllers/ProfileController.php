<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return Inertia::render('Profiles', [
            'profile' => $request->user(),
        ]);
    }

    public function updateStep(Request $request)
    {
        $step = (int) $request->input('step', 1);

        if ($step < 1 || $step > 5) {
            return back()->withErrors(['step' => 'Invalid step selected.']);
        }

        $validated = $request->validate($this->rulesForStep($step));

        $user = $request->user();
        $data = $validated;
        unset($data['step']);

        $data['profile_completion_step'] = max((int) $user->profile_completion_step, $step + 1);

        if ($step >= 5) {
            $data['profile_completion_step'] = 5;
        }

        $user->update($data);

        return back()->with('status', 'Step ' . $step . ' saved successfully.');
    }

    private function rulesForStep(int $step): array
    {
        $common = [
            'step' => ['required', 'integer'],
        ];

        if ($step === 1) {
            return array_merge($common, [
                'profile_for' => ['required', Rule::in(['Self', 'Son', 'Daughter', 'Brother', 'Sister', 'Friend', 'Relative'])],
                'marital_status' => ['required', Rule::in(['Never Married', 'Divorced', 'Widowed', 'Awaiting Divorce'])],
                'date_of_birth' => ['required', 'date', 'before:today'],
                'height_cm' => ['required', 'integer', 'between:120,230'],
                'religion' => ['required', 'string', 'max:50'],
                'mother_tongue' => ['required', 'string', 'max:50'],
            ]);
        }

        if ($step === 2) {
            return array_merge($common, [
                'current_city' => ['required', 'string', 'max:100'],
                'current_state' => ['required', 'string', 'max:100'],
                'current_country' => ['required', 'string', 'max:100'],
                'diet' => ['required', Rule::in(['Vegetarian', 'Eggetarian', 'Non-Vegetarian', 'Jain', 'Vegan'])],
                'smoke' => ['required', Rule::in(['No', 'Occasionally', 'Yes'])],
                'drink' => ['required', Rule::in(['No', 'Occasionally', 'Yes'])],
                'about_me' => ['required', 'string', 'min:40', 'max:1200'],
            ]);
        }

        if ($step === 3) {
            return array_merge($common, [
                'education' => ['required', 'string', 'max:100'],
                'education_detail' => ['nullable', 'string', 'max:150'],
                'occupation' => ['required', 'string', 'max:100'],
                'income' => ['required', 'string', 'max:50'],
                'company_name' => ['nullable', 'string', 'max:120'],
            ]);
        }

        if ($step === 4) {
            return array_merge($common, [
                'family_type' => ['required', Rule::in(['Joint Family', 'Nuclear Family'])],
                'father_occupation' => ['nullable', 'string', 'max:120'],
                'mother_occupation' => ['nullable', 'string', 'max:120'],
                'brothers' => ['nullable', 'integer', 'between:0,10'],
                'sisters' => ['nullable', 'integer', 'between:0,10'],
                'family_values' => ['required', Rule::in(['Traditional', 'Moderate', 'Liberal'])],
            ]);
        }

        return array_merge($common, [
            'manglik' => ['required', Rule::in(['Yes', 'No', 'Anshik', 'Dont Know'])],
            'rashi' => ['nullable', 'string', 'max:50'],
            'nakshatra' => ['nullable', 'string', 'max:50'],
            'time_of_birth' => ['nullable', 'string', 'max:20'],
            'place_of_birth' => ['nullable', 'string', 'max:120'],
            'gotra' => ['nullable', 'string', 'max:80'],
        ]);
    }
}
