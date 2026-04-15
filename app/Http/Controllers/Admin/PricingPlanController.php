<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanFeature;
use App\Support\AdminNavigation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PricingPlanController extends Controller
{
    public function index()
    {
        $basePath = '/' . trim(config('app.super_admin_path'), '/');

        return Inertia::render('Admin/PricingPlans', [
            'plans' => SubscriptionPlan::with('features')->orderBy('sort_order')->orderBy('id')->get(),
            'createAction' => route('admin.pricing-plans.store', [], false),
            'baseUrl' => $basePath,
            'dashboardUrl' => route('admin.dashboard', [], false),
            'integrationSettingsUrl' => route('admin.integration-settings.index', [], false),
            'subscriptionsUrl' => route('admin.subscriptions.index', [], false),
            'logoutAction' => route('admin.logout', [], false),
            'sidebarMenus' => AdminNavigation::build([
                'dashboardUrl' => route('admin.dashboard', [], false),
                'pricingManagementUrl' => route('admin.pricing-plans.index', [], false),
                'integrationSettingsUrl' => route('admin.integration-settings.index', [], false),
                'subscriptionsUrl' => route('admin.subscriptions.index', [], false),
                'cmsPagesUrl' => route('admin.cms-pages.index', [], false),
                'cmsSectionsUrl' => route('admin.cms-sections.index', [], false),
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'price' => ['required', 'numeric', 'min:0'],
            'billing_cycle' => ['required', Rule::in(['monthly', 'quarterly', 'yearly', 'lifetime'])],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'is_visible' => ['nullable', 'boolean'],
            'is_recommended' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'features' => ['nullable', 'array'],
            'features.*' => ['nullable', 'string', 'max:255'],
        ]);

        $slug = $this->makeUniqueSlug($data['name']);

        $plan = SubscriptionPlan::create([
            'name' => $data['name'],
            'slug' => $slug,
            'price' => $data['price'],
            'billing_cycle' => $data['billing_cycle'],
            'description' => $data['description'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'is_visible' => (bool) ($data['is_visible'] ?? true),
            'is_recommended' => (bool) ($data['is_recommended'] ?? false),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);

        foreach (($data['features'] ?? []) as $index => $featureText) {
            $feature = trim((string) $featureText);
            if ($feature === '') {
                continue;
            }

            $plan->features()->create([
                'feature_text' => $feature,
                'sort_order' => $index,
            ]);
        }

        return back()->with('status', 'Pricing plan created successfully.');
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'price' => ['required', 'numeric', 'min:0'],
            'billing_cycle' => ['required', Rule::in(['monthly', 'quarterly', 'yearly', 'lifetime'])],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'is_visible' => ['required', 'boolean'],
            'is_recommended' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
        ]);

        $slug = $this->makeUniqueSlug($data['name'], $plan->id);

        $plan->update([
            'name' => $data['name'],
            'slug' => $slug,
            'price' => $data['price'],
            'billing_cycle' => $data['billing_cycle'],
            'description' => $data['description'] ?? null,
            'is_active' => (bool) $data['is_active'],
            'is_visible' => (bool) $data['is_visible'],
            'is_recommended' => (bool) $data['is_recommended'],
            'sort_order' => (int) $data['sort_order'],
        ]);

        return back()->with('status', 'Pricing plan updated.');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        $plan->delete();

        return back()->with('status', 'Pricing plan deleted.');
    }

    public function addFeature(Request $request, SubscriptionPlan $plan)
    {
        $data = $request->validate([
            'feature_text' => ['required', 'string', 'max:255'],
        ]);

        $sortOrder = (int) $plan->features()->max('sort_order') + 1;

        $plan->features()->create([
            'feature_text' => $data['feature_text'],
            'sort_order' => $sortOrder,
        ]);

        return back()->with('status', 'Feature added to plan.');
    }

    public function deleteFeature(SubscriptionPlanFeature $feature)
    {
        $feature->delete();

        return back()->with('status', 'Feature removed.');
    }

    private function makeUniqueSlug(string $name, ?int $ignorePlanId = null): string
    {
        $base = Str::slug($name);
        $slug = $base !== '' ? $base : 'plan';

        $suffix = 1;
        while (
            SubscriptionPlan::where('slug', $slug)
                ->when($ignorePlanId, function ($query) use ($ignorePlanId) {
                    $query->where('id', '!=', $ignorePlanId);
                })
                ->exists()
        ) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }
}
