<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CmsPageController;
use App\Http\Controllers\Admin\CmsPageSectionController;
use App\Http\Controllers\Admin\IntegrationSettingsController;
use App\Http\Controllers\Admin\PricingPlanController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\BrowseController;
use App\Http\Controllers\CmsPublicPageController;
use App\Http\Controllers\DashboardMatchActionController;
use App\Http\Controllers\KundliHistoryController;
use App\Http\Controllers\KundliMatchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PricingController;
use App\Models\CmsPageSection;
use App\Models\Profile;
use App\Models\ProfileMatchAction;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Home page
Route::get('/', function () {
    $featuredProfileRows = Profile::query()
        ->with(['careerDetail', 'lifestyleDetail'])
        ->whereNotNull('first_name')
        ->orderByDesc('id')
        ->limit(8)
        ->get();

    $cardThemes = [
        [
            'bgClass' => 'bg-gradient-to-br from-rose-100 to-amber-100',
            'buttonClass' => 'border border-rose-300 text-rose-700 bg-rose-50 hover:bg-rose-100',
        ],
        [
            'bgClass' => 'bg-gradient-to-br from-sky-100 to-indigo-100',
            'buttonClass' => 'border border-sky-300 text-sky-700 bg-sky-50 hover:bg-sky-100',
        ],
        [
            'bgClass' => 'bg-gradient-to-br from-emerald-100 to-lime-100',
            'buttonClass' => 'border border-emerald-300 text-emerald-700 bg-emerald-50 hover:bg-emerald-100',
        ],
        [
            'bgClass' => 'bg-gradient-to-br from-orange-100 to-rose-100',
            'buttonClass' => 'border border-orange-300 text-orange-700 bg-orange-50 hover:bg-orange-100',
        ],
        [
            'bgClass' => 'bg-gradient-to-br from-purple-100 to-fuchsia-100',
            'buttonClass' => 'border border-purple-300 text-purple-700 bg-purple-50 hover:bg-purple-100',
        ],
    ];

    $featuredProfiles = $featuredProfileRows->values()->map(function (Profile $profile, int $index) use ($cardThemes) {
        $age = null;
        if ($profile->date_of_birth) {
            try {
                $age = now()->diffInYears($profile->date_of_birth);
            } catch (\Throwable $e) {
                $age = null;
            }
        }

        $theme = $cardThemes[$index % count($cardThemes)];

        return [
            'id' => $profile->id,
            'name' => trim((string) ($profile->first_name . ' ' . $profile->last_name)),
            'age' => $age,
            'profession' => (string) (optional($profile->careerDetail)->job_title ?: optional($profile->careerDetail)->occupation_type ?: 'Not specified'),
            'city' => (string) ($profile->city ?: $profile->state ?: $profile->country ?: 'Location not set'),
            'about' => (string) (optional($profile->lifestyleDetail)->about_me ?: 'Looking for a meaningful relationship based on shared values.'),
            'bgClass' => $theme['bgClass'],
            'buttonClass' => $theme['buttonClass'],
            'profilePictureUrl' => $profile->profile_picture_path ? Storage::url($profile->profile_picture_path) : null,
            'profileUrl' => route('profiles.view', ['profile' => $profile->id]),
        ];
    });

    $faqRows = CmsPageSection::query()
        ->where('is_active', true)
        ->where('section_type', 'faq')
        ->where(function ($query) {
            $query->whereJsonContains('target_pages', 'home')
                ->orWhereJsonContains('target_pages', 'index');
        })
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get(['title', 'body']);

    $faqs = $faqRows->map(function ($row) {
        return [
            'question' => (string) ($row->title ?? ''),
            'answer' => (string) ($row->body ?? ''),
        ];
    })->filter(function (array $item) {
        return $item['question'] !== '' && $item['answer'] !== '';
    })->values();

    return Inertia::render('Welcome', [
        'featuredProfiles' => $featuredProfiles,
        'faqs' => $faqs,
    ]);
});

Route::get('/faqs', function () {
    $faqRows = CmsPageSection::query()
        ->where('is_active', true)
        ->where('section_type', 'faq')
        ->where(function ($query) {
            $query->whereJsonContains('target_pages', 'faq')
                ->orWhereJsonContains('target_pages', 'home')
                ->orWhereJsonContains('target_pages', 'index');
        })
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get(['title', 'body']);

    $faqs = $faqRows->map(function ($row) {
        return [
            'question' => (string) ($row->title ?? ''),
            'answer' => (string) ($row->body ?? ''),
        ];
    })->filter(function (array $item) {
        return $item['question'] !== '' && $item['answer'] !== '';
    })->values();

    return Inertia::render('Faqs', [
        'faqs' => $faqs,
    ]);
})->name('faqs');

Route::get('/pages/{slug}', [CmsPublicPageController::class, 'show'])->name('cms.page.view');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])->name('verification.send');
});

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [AuthController::class, 'showVerifyEmail'])->name('verification.notice');
    Route::post('/email/verification-notification-auth', [AuthController::class, 'resendVerificationAuthenticated'])
        ->middleware('throttle:6,1')
        ->name('verification.send.auth');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Public Browse (Unauthenticated can view)
Route::get('/browse', [BrowseController::class, 'index'])->name('profiles.browse');
Route::get('/profiles/{profile}/view', [BrowseController::class, 'show'])->name('profiles.view');

Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');

Route::prefix(config('app.super_admin_path'))->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminController::class, 'login'])->name('login.attempt');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

        Route::get('/pricing-plans', [PricingPlanController::class, 'index'])->name('pricing-plans.index');
        Route::post('/pricing-plans', [PricingPlanController::class, 'store'])->name('pricing-plans.store');
        Route::put('/pricing-plans/{plan}', [PricingPlanController::class, 'update'])->name('pricing-plans.update');
        Route::delete('/pricing-plans/{plan}', [PricingPlanController::class, 'destroy'])->name('pricing-plans.destroy');

        Route::post('/pricing-plans/{plan}/features', [PricingPlanController::class, 'addFeature'])->name('pricing-plans.features.store');
        Route::delete('/pricing-plan-features/{feature}', [PricingPlanController::class, 'deleteFeature'])->name('pricing-plans.features.destroy');

        Route::get('/integration-settings', [IntegrationSettingsController::class, 'index'])->name('integration-settings.index');
        Route::post('/integration-settings', [IntegrationSettingsController::class, 'update'])->name('integration-settings.update');

        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');

        Route::get('/cms-pages', [CmsPageController::class, 'index'])->name('cms-pages.index');
        Route::post('/cms-pages', [CmsPageController::class, 'store'])->name('cms-pages.store');
        Route::put('/cms-pages/{cmsPage}', [CmsPageController::class, 'update'])->name('cms-pages.update');
        Route::delete('/cms-pages/{cmsPage}', [CmsPageController::class, 'destroy'])->name('cms-pages.destroy');

        Route::get('/cms-sections', [CmsPageSectionController::class, 'index'])->name('cms-sections.index');
        Route::post('/cms-sections', [CmsPageSectionController::class, 'store'])->name('cms-sections.store');
        Route::put('/cms-sections/{cmsSection}', [CmsPageSectionController::class, 'update'])->name('cms-sections.update');
        Route::delete('/cms-sections/{cmsSection}', [CmsPageSectionController::class, 'destroy'])->name('cms-sections.destroy');
    });
});

// Authenticated Routes
Route::middleware(['auth', 'verified', 'member'])->group(function () {
    Route::get('/dashboard', function () {
        UserSubscription::expireEnded();

        $latestActiveSubscription = UserSubscription::with('plan')
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->latest('id')
            ->first();

        $latestSubscription = UserSubscription::with('plan')
            ->where('user_id', auth()->id())
            ->latest('id')
            ->first();

        $profile = Profile::with([
            'mediaGallery' => function ($query) {
                $query->where('media_type', 'image')->orderBy('sort_order')->orderBy('id');
            },
        ])->where('user_id', auth()->id())->first();

        $myProfileId = (int) ($profile?->id ?? 0);
        $requestOverview = [
            'stats' => [
                'received_total' => 0,
                'received_pending' => 0,
                'received_accepted' => 0,
                'received_rejected' => 0,
                'sent_total' => 0,
                'sent_pending' => 0,
                'sent_accepted' => 0,
                'sent_rejected' => 0,
            ],
            'incoming' => [],
            'outgoing' => [],
        ];

        $myResponsesByTargetProfileId = collect();
        $responsesToMeByUserId = collect();
        $outgoingConnectActions = collect();

        if ($myProfileId > 0) {
            $myResponses = ProfileMatchAction::query()
                ->where('user_id', auth()->id())
                ->get(['target_profile_id', 'action', 'rejection_reason', 'updated_at']);
            $myResponsesByTargetProfileId = $myResponses->keyBy('target_profile_id');

            $incomingConnectActions = ProfileMatchAction::query()
                ->where('target_profile_id', $myProfileId)
                ->where('action', 'connect')
                ->latest('updated_at')
                ->get(['user_id', 'updated_at']);

            $incomingUserIds = $incomingConnectActions->pluck('user_id')->unique()->values();
            $incomingProfilesByUserId = Profile::query()
                ->with('careerDetail')
                ->whereIn('user_id', $incomingUserIds)
                ->get()
                ->keyBy('user_id');

            $incomingRequests = $incomingConnectActions->map(function (ProfileMatchAction $action) use ($incomingProfilesByUserId, $myResponsesByTargetProfileId) {
                $senderProfile = $incomingProfilesByUserId->get((int) $action->user_id);
                if (!$senderProfile) {
                    return null;
                }

                $myResponse = $myResponsesByTargetProfileId->get((int) $senderProfile->id);
                $status = 'pending';
                $rejectionReason = null;

                if ($myResponse) {
                    if ($myResponse->action === 'connect') {
                        $status = 'accepted';
                    } elseif ($myResponse->action === 'skip') {
                        $status = 'rejected';
                        $rejectionReason = (string) ($myResponse->rejection_reason ?: 'No reason provided.');
                    }
                }

                return [
                    'profile_id' => (int) $senderProfile->id,
                    'name' => trim((string) ($senderProfile->first_name . ' ' . $senderProfile->last_name)) ?: 'Member',
                    'city' => (string) ($senderProfile->city ?: 'N/A'),
                    'profession' => (string) ($senderProfile->careerDetail?->job_title ?: $senderProfile->careerDetail?->occupation_type ?: 'Not specified'),
                    'profile_picture_url' => $senderProfile->profile_picture_path ? Storage::url($senderProfile->profile_picture_path) : null,
                    'requested_at' => optional($action->updated_at)->toISOString(),
                    'status' => $status,
                    'rejection_reason' => $rejectionReason,
                ];
            })->filter()->values();

            $requestOverview['incoming'] = $incomingRequests->take(5)->values();
            $requestOverview['stats']['received_total'] = (int) $incomingRequests->count();
            $requestOverview['stats']['received_pending'] = (int) $incomingRequests->where('status', 'pending')->count();
            $requestOverview['stats']['received_accepted'] = (int) $incomingRequests->where('status', 'accepted')->count();
            $requestOverview['stats']['received_rejected'] = (int) $incomingRequests->where('status', 'rejected')->count();

            $outgoingConnectActions = ProfileMatchAction::query()
                ->where('user_id', auth()->id())
                ->where('action', 'connect')
                ->latest('updated_at')
                ->get(['target_profile_id', 'updated_at']);

            $outgoingTargetIds = $outgoingConnectActions->pluck('target_profile_id')->unique()->values();
            $outgoingProfiles = Profile::query()
                ->with('careerDetail')
                ->whereIn('id', $outgoingTargetIds)
                ->get();

            $responsesToMeByUserId = ProfileMatchAction::query()
                ->where('target_profile_id', $myProfileId)
                ->whereIn('user_id', $outgoingProfiles->pluck('user_id')->unique()->values())
                ->get(['user_id', 'action', 'rejection_reason', 'updated_at'])
                ->keyBy('user_id');

            $outgoingProfilesById = $outgoingProfiles->keyBy('id');
            $outgoingRequests = $outgoingConnectActions->map(function (ProfileMatchAction $action) use ($outgoingProfilesById, $responsesToMeByUserId) {
                $targetProfile = $outgoingProfilesById->get((int) $action->target_profile_id);
                if (!$targetProfile) {
                    return null;
                }

                $targetResponse = $responsesToMeByUserId->get((int) $targetProfile->user_id);
                $status = 'pending';
                $rejectionReason = null;

                if ($targetResponse) {
                    if ($targetResponse->action === 'connect') {
                        $status = 'accepted';
                    } elseif ($targetResponse->action === 'skip') {
                        $status = 'rejected';
                        $rejectionReason = (string) ($targetResponse->rejection_reason ?: 'No reason provided.');
                    }
                }

                return [
                    'profile_id' => (int) $targetProfile->id,
                    'name' => trim((string) ($targetProfile->first_name . ' ' . $targetProfile->last_name)) ?: 'Member',
                    'city' => (string) ($targetProfile->city ?: 'N/A'),
                    'profession' => (string) ($targetProfile->careerDetail?->job_title ?: $targetProfile->careerDetail?->occupation_type ?: 'Not specified'),
                    'profile_picture_url' => $targetProfile->profile_picture_path ? Storage::url($targetProfile->profile_picture_path) : null,
                    'requested_at' => optional($action->updated_at)->toISOString(),
                    'status' => $status,
                    'rejection_reason' => $rejectionReason,
                ];
            })->filter()->values();

            $requestOverview['outgoing'] = $outgoingRequests->take(5)->values();
            $requestOverview['stats']['sent_total'] = (int) $outgoingRequests->count();
            $requestOverview['stats']['sent_pending'] = (int) $outgoingRequests->where('status', 'pending')->count();
            $requestOverview['stats']['sent_accepted'] = (int) $outgoingRequests->where('status', 'accepted')->count();
            $requestOverview['stats']['sent_rejected'] = (int) $outgoingRequests->where('status', 'rejected')->count();
        }

        $currentGender = strtolower((string) (auth()->user()->gender ?? $profile?->gender ?? ''));
        $targetGender = match ($currentGender) {
            'male' => 'female',
            'female' => 'male',
            default => null,
        };

        $recommendedProfiles = collect();
        if ($targetGender) {
            $recommendedProfiles = Profile::query()
                ->with('careerDetail')
                ->where('user_id', '!=', auth()->id())
                ->whereNotNull('first_name')
                ->whereRaw('LOWER(gender) = ?', [$targetGender])
                ->latest('id')
                ->take(12)
                ->get()
                ->map(function (Profile $item) use ($profile) {
                $age = null;
                if ($item->date_of_birth) {
                    $age = now()->diffInYears($item->date_of_birth);
                }

                // Keep score deterministic and DB-driven (no random values).
                $compatibility = 72;
                if ($profile && $profile->city && $item->city && strcasecmp($profile->city, $item->city) === 0) {
                    $compatibility += 10;
                }
                if ($profile && $profile->religion && $item->religion && strcasecmp($profile->religion, $item->religion) === 0) {
                    $compatibility += 8;
                }
                if ($profile && $profile->caste && $item->caste && strcasecmp($profile->caste, $item->caste) === 0) {
                    $compatibility += 6;
                }
                if ($profile && $profile->mother_tongue && $item->mother_tongue && strcasecmp($profile->mother_tongue, $item->mother_tongue) === 0) {
                    $compatibility += 4;
                }
                $compatibility = min($compatibility, 97);

                return [
                    'id' => $item->id,
                    'user_id' => (int) $item->user_id,
                    'name' => trim((string) ($item->first_name . ' ' . $item->last_name)),
                    'age' => $age,
                    'city' => $item->city ?: 'N/A',
                    'profession' => $item->careerDetail?->job_title
                        ?: $item->careerDetail?->occupation_type
                        ?: 'Not specified',
                    'compatibility' => $compatibility,
                    'profile_picture_url' => $item->profile_picture_path ? Storage::url($item->profile_picture_path) : null,
                    'bgClass' => 'bg-gradient-to-br from-rose-100 to-orange-100',
                    'request_status' => 'none',
                ];
            })
            ->values();

            $recommendedProfiles = $recommendedProfiles->map(function (array $item) use ($myResponsesByTargetProfileId, $responsesToMeByUserId, $myProfileId) {
                $myAction = $myResponsesByTargetProfileId->get((int) $item['id']);
                if (!$myAction) {
                    unset($item['user_id']);
                    return $item;
                }

                if ($myAction->action === 'skip') {
                    $item['request_status'] = 'skipped';
                    unset($item['user_id']);
                    return $item;
                }

                $targetResponse = $myProfileId > 0
                    ? $responsesToMeByUserId->get((int) ($item['user_id'] ?? 0))
                    : null;

                if (!$targetResponse) {
                    $item['request_status'] = 'pending';
                    unset($item['user_id']);
                    return $item;
                }

                if ($targetResponse->action === 'connect') {
                    $item['request_status'] = 'accepted';
                } elseif ($targetResponse->action === 'skip') {
                    $item['request_status'] = 'rejected';
                } else {
                    $item['request_status'] = 'pending';
                }

                unset($item['user_id']);

                return $item;
            });
        }

        $dashboardProfile = null;
        if ($profile) {
            $dashboardProfile = [
                'full_name' => trim((string) ($profile->first_name . ' ' . $profile->last_name)) ?: auth()->user()->name,
                'profile_id' => $profile->profile_id,
                'last_completed_step' => (int) $profile->last_completed_step,
                'profile_picture_url' => $profile->profile_picture_path ? Storage::url($profile->profile_picture_path) : null,
                'video_intro_url' => $profile->video_intro_path ? Storage::url($profile->video_intro_path) : null,
                'gallery' => $profile->mediaGallery
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'url' => Storage::url($item->file_path),
                        ];
                    })
                    ->values(),
            ];
        }

        return Inertia::render('Dashboard', [
            'subscription' => $latestActiveSubscription ?: $latestSubscription,
            'dashboardProfile' => $dashboardProfile,
            'recommendedProfiles' => $recommendedProfiles,
            'requestOverview' => $requestOverview,
        ]);
    })->name('dashboard');

    Route::get('/dashboard/requests', function () {
        $profile = Profile::query()->where('user_id', auth()->id())->first();
        $myProfileId = (int) ($profile?->id ?? 0);

        $requestOverview = [
            'stats' => [
                'received_total' => 0,
                'received_pending' => 0,
                'received_accepted' => 0,
                'received_rejected' => 0,
                'sent_total' => 0,
                'sent_pending' => 0,
                'sent_accepted' => 0,
                'sent_rejected' => 0,
            ],
        ];

        $incomingRequests = collect();
        $outgoingRequests = collect();

        if ($myProfileId > 0) {
            $myResponsesByTargetProfileId = ProfileMatchAction::query()
                ->where('user_id', auth()->id())
                ->get(['target_profile_id', 'action', 'rejection_reason'])
                ->keyBy('target_profile_id');

            $incomingConnectActions = ProfileMatchAction::query()
                ->where('target_profile_id', $myProfileId)
                ->where('action', 'connect')
                ->latest('updated_at')
                ->get(['user_id', 'updated_at']);

            $incomingUserIds = $incomingConnectActions->pluck('user_id')->unique()->values();
            $incomingProfilesByUserId = Profile::query()
                ->with('careerDetail')
                ->whereIn('user_id', $incomingUserIds)
                ->get()
                ->keyBy('user_id');

            $incomingRequests = $incomingConnectActions->map(function (ProfileMatchAction $action) use ($incomingProfilesByUserId, $myResponsesByTargetProfileId) {
                $senderProfile = $incomingProfilesByUserId->get((int) $action->user_id);
                if (!$senderProfile) {
                    return null;
                }

                $myResponse = $myResponsesByTargetProfileId->get((int) $senderProfile->id);
                $status = 'pending';
                $rejectionReason = null;

                if ($myResponse) {
                    if ($myResponse->action === 'connect') {
                        $status = 'accepted';
                    } elseif ($myResponse->action === 'skip') {
                        $status = 'rejected';
                        $rejectionReason = (string) ($myResponse->rejection_reason ?: 'No reason provided.');
                    }
                }

                return [
                    'profile_id' => (int) $senderProfile->id,
                    'name' => trim((string) ($senderProfile->first_name . ' ' . $senderProfile->last_name)) ?: 'Member',
                    'city' => (string) ($senderProfile->city ?: 'N/A'),
                    'profession' => (string) ($senderProfile->careerDetail?->job_title ?: $senderProfile->careerDetail?->occupation_type ?: 'Not specified'),
                    'profile_picture_url' => $senderProfile->profile_picture_path ? Storage::url($senderProfile->profile_picture_path) : null,
                    'requested_at' => optional($action->updated_at)->toISOString(),
                    'status' => $status,
                    'rejection_reason' => $rejectionReason,
                ];
            })->filter()->values();

            $outgoingConnectActions = ProfileMatchAction::query()
                ->where('user_id', auth()->id())
                ->where('action', 'connect')
                ->latest('updated_at')
                ->get(['target_profile_id', 'updated_at']);

            $outgoingTargetIds = $outgoingConnectActions->pluck('target_profile_id')->unique()->values();
            $outgoingProfiles = Profile::query()
                ->with('careerDetail')
                ->whereIn('id', $outgoingTargetIds)
                ->get();

            $responsesToMeByUserId = ProfileMatchAction::query()
                ->where('target_profile_id', $myProfileId)
                ->whereIn('user_id', $outgoingProfiles->pluck('user_id')->unique()->values())
                ->get(['user_id', 'action', 'rejection_reason'])
                ->keyBy('user_id');

            $outgoingProfilesById = $outgoingProfiles->keyBy('id');
            $outgoingRequests = $outgoingConnectActions->map(function (ProfileMatchAction $action) use ($outgoingProfilesById, $responsesToMeByUserId) {
                $targetProfile = $outgoingProfilesById->get((int) $action->target_profile_id);
                if (!$targetProfile) {
                    return null;
                }

                $targetResponse = $responsesToMeByUserId->get((int) $targetProfile->user_id);
                $status = 'pending';
                $rejectionReason = null;

                if ($targetResponse) {
                    if ($targetResponse->action === 'connect') {
                        $status = 'accepted';
                    } elseif ($targetResponse->action === 'skip') {
                        $status = 'rejected';
                        $rejectionReason = (string) ($targetResponse->rejection_reason ?: 'No reason provided.');
                    }
                }

                return [
                    'profile_id' => (int) $targetProfile->id,
                    'name' => trim((string) ($targetProfile->first_name . ' ' . $targetProfile->last_name)) ?: 'Member',
                    'city' => (string) ($targetProfile->city ?: 'N/A'),
                    'profession' => (string) ($targetProfile->careerDetail?->job_title ?: $targetProfile->careerDetail?->occupation_type ?: 'Not specified'),
                    'profile_picture_url' => $targetProfile->profile_picture_path ? Storage::url($targetProfile->profile_picture_path) : null,
                    'requested_at' => optional($action->updated_at)->toISOString(),
                    'status' => $status,
                    'rejection_reason' => $rejectionReason,
                ];
            })->filter()->values();

            $requestOverview['stats']['received_total'] = (int) $incomingRequests->count();
            $requestOverview['stats']['received_pending'] = (int) $incomingRequests->where('status', 'pending')->count();
            $requestOverview['stats']['received_accepted'] = (int) $incomingRequests->where('status', 'accepted')->count();
            $requestOverview['stats']['received_rejected'] = (int) $incomingRequests->where('status', 'rejected')->count();
            $requestOverview['stats']['sent_total'] = (int) $outgoingRequests->count();
            $requestOverview['stats']['sent_pending'] = (int) $outgoingRequests->where('status', 'pending')->count();
            $requestOverview['stats']['sent_accepted'] = (int) $outgoingRequests->where('status', 'accepted')->count();
            $requestOverview['stats']['sent_rejected'] = (int) $outgoingRequests->where('status', 'rejected')->count();
        }

        $perPage = 12;
        $incomingPage = max((int) request('incoming_page', 1), 1);
        $outgoingPage = max((int) request('outgoing_page', 1), 1);

        $incomingTotal = $incomingRequests->count();
        $outgoingTotal = $outgoingRequests->count();

        $incomingLastPage = max((int) ceil($incomingTotal / $perPage), 1);
        $outgoingLastPage = max((int) ceil($outgoingTotal / $perPage), 1);

        $incomingPage = min($incomingPage, $incomingLastPage);
        $outgoingPage = min($outgoingPage, $outgoingLastPage);

        return Inertia::render('DashboardRequests', [
            'requestOverview' => $requestOverview,
            'incoming' => [
                'data' => $incomingRequests->forPage($incomingPage, $perPage)->values(),
                'meta' => [
                    'current_page' => $incomingPage,
                    'last_page' => $incomingLastPage,
                    'per_page' => $perPage,
                    'total' => $incomingTotal,
                ],
            ],
            'outgoing' => [
                'data' => $outgoingRequests->forPage($outgoingPage, $perPage)->values(),
                'meta' => [
                    'current_page' => $outgoingPage,
                    'last_page' => $outgoingLastPage,
                    'per_page' => $perPage,
                    'total' => $outgoingTotal,
                ],
            ],
        ]);
    })->name('dashboard.requests');

    Route::post('/dashboard/match-action', [DashboardMatchActionController::class, 'store'])->name('dashboard.match-action');

    Route::get('/profiles', [ProfileController::class, 'edit'])->name('profiles.index');
    Route::post('/profiles/step', [ProfileController::class, 'updateStep'])->name('profiles.step.update');
    Route::post('/profiles/horoscope/auto-fetch', [ProfileController::class, 'autoFetchHoroscope'])->name('profiles.horoscope.auto-fetch');
    Route::post('/match-kundli', [KundliMatchController::class, 'match'])->name('kundli.match');
    Route::get('/kundli-history', [KundliHistoryController::class, 'index'])->name('kundli.history');
    Route::get('/locations/states', [ProfileController::class, 'locationStates'])->name('locations.states');
    Route::get('/locations/cities', [ProfileController::class, 'locationCities'])->name('locations.cities');

    Route::post('/pricing/purchase', [PricingController::class, 'purchase'])->name('pricing.purchase');
    Route::post('/pricing/create-order', [PricingController::class, 'createOrder'])->name('pricing.create-order');
    Route::post('/pricing/verify-payment', [PricingController::class, 'verifyPayment'])->name('pricing.verify-payment');

    Route::get('/profiles/{profile}/match', [BrowseController::class, 'match'])->name('profiles.match');
});

