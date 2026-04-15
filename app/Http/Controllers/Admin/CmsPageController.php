<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Support\AdminNavigation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CmsPageController extends Controller
{
    public function index()
    {
        $basePath = '/' . trim(config('app.super_admin_path'), '/');

        return Inertia::render('Admin/CmsPages', [
            'pages' => CmsPage::orderBy('sort_order')->orderBy('id')->get(),
            'createAction' => route('admin.cms-pages.store', [], false),
            'baseUrl' => $basePath,
            'sidebarMenus' => AdminNavigation::build([
                'dashboardUrl' => route('admin.dashboard', [], false),
                'pricingManagementUrl' => route('admin.pricing-plans.index', [], false),
                'integrationSettingsUrl' => route('admin.integration-settings.index', [], false),
                'subscriptionsUrl' => route('admin.subscriptions.index', [], false),
                'cmsPagesUrl' => route('admin.cms-pages.index', [], false),
                'cmsSectionsUrl' => route('admin.cms-sections.index', [], false),
            ]),
            'logoutAction' => route('admin.logout', [], false),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:150', 'unique:cms_pages,slug'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        CmsPage::create([
            'title' => $data['title'],
            'slug' => $this->sanitizeSlug($data['slug'] ?? $data['title']),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        return back()->with('status', 'Page created.');
    }

    public function update(Request $request, CmsPage $cmsPage)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:150', 'unique:cms_pages,slug,' . $cmsPage->id],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $cmsPage->update([
            'title' => $data['title'],
            'slug' => $this->sanitizeSlug($data['slug'] ?? $data['title']),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        return back()->with('status', 'Page updated.');
    }

    public function destroy(CmsPage $cmsPage)
    {
        $cmsPage->delete();

        return back()->with('status', 'Page deleted.');
    }

    private function sanitizeSlug(string $value): string
    {
        $slug = Str::slug($value);
        return $slug !== '' ? $slug : 'page-' . Str::lower(Str::random(8));
    }
}
