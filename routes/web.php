<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\IntegrationSettingsController;
use App\Http\Controllers\Admin\PricingPlanController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\BrowseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PricingController;
use App\Models\Profile;
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
    return Inertia::render('Welcome');
});

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
        ]);
    })->name('dashboard');

    Route::get('/profiles', [ProfileController::class, 'edit'])->name('profiles.index');
    Route::post('/profiles/step', [ProfileController::class, 'updateStep'])->name('profiles.step.update');
    Route::post('/profiles/horoscope/auto-fetch', [ProfileController::class, 'autoFetchHoroscope'])->name('profiles.horoscope.auto-fetch');

    Route::post('/pricing/purchase', [PricingController::class, 'purchase'])->name('pricing.purchase');
    Route::post('/pricing/create-order', [PricingController::class, 'createOrder'])->name('pricing.create-order');
    Route::post('/pricing/verify-payment', [PricingController::class, 'verifyPayment'])->name('pricing.verify-payment');
});

