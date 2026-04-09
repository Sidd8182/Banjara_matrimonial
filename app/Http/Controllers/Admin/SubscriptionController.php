<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use App\Support\AdminNavigation;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index()
    {
        UserSubscription::expireEnded();

        $subscriptions = UserSubscription::with([
            'user:id,name,email',
            'plan:id,name,billing_cycle',
        ])
            ->latest('id')
            ->limit(200)
            ->get();

        return Inertia::render('Admin/Subscriptions', [
            'subscriptions' => $subscriptions,
            'stats' => [
                'active' => UserSubscription::where('status', 'active')->count(),
                'expired' => UserSubscription::where('status', 'expired')->count(),
                'pending' => UserSubscription::where('status', 'pending')->count(),
                'failed' => UserSubscription::where('status', 'failed')->count(),
                'revenue' => (float) UserSubscription::whereIn('status', ['active', 'expired'])->sum('amount'),
            ],
            'dashboardUrl' => route('admin.dashboard', [], false),
            'pricingManagementUrl' => route('admin.pricing-plans.index', [], false),
            'integrationSettingsUrl' => route('admin.integration-settings.index', [], false),
            'subscriptionsUrl' => route('admin.subscriptions.index', [], false),
            'logoutAction' => route('admin.logout', [], false),
            'sidebarMenus' => AdminNavigation::build([
                'dashboardUrl' => route('admin.dashboard', [], false),
                'pricingManagementUrl' => route('admin.pricing-plans.index', [], false),
                'integrationSettingsUrl' => route('admin.integration-settings.index', [], false),
                'subscriptionsUrl' => route('admin.subscriptions.index', [], false),
            ]),
        ]);
    }
}
