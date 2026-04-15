<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\CmsPageSection;
use App\Support\AdminNavigation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CmsPageSectionController extends Controller
{
    private const ALLOWED_TARGETS = ['home', 'index', 'about-us', 'faq', 'browse'];

    public function index()
    {
        $basePath = '/' . trim(config('app.super_admin_path'), '/');

        return Inertia::render('Admin/CmsSections', [
            'sections' => CmsPageSection::with('page:id,title,slug')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
            'pages' => CmsPage::orderBy('sort_order')->orderBy('id')->get(['id', 'title', 'slug']),
            'targetPageOptions' => self::ALLOWED_TARGETS,
            'createAction' => route('admin.cms-sections.store', [], false),
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
        $data = $this->validatePayload($request);

        CmsPageSection::create($data);

        return back()->with('status', 'Section created.');
    }

    public function update(Request $request, CmsPageSection $cmsSection)
    {
        $data = $this->validatePayload($request);

        $cmsSection->update($data);

        return back()->with('status', 'Section updated.');
    }

    public function destroy(CmsPageSection $cmsSection)
    {
        $cmsSection->delete();

        return back()->with('status', 'Section deleted.');
    }

    private function validatePayload(Request $request): array
    {
        $data = $request->validate([
            'cms_page_id' => ['nullable', 'integer', 'exists:cms_pages,id'],
            'section_name' => ['required', 'string', 'max:120'],
            'section_type' => ['required', 'string', 'max:40'],
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'target_pages' => ['nullable', 'array'],
            'target_pages.*' => ['string', 'in:' . implode(',', self::ALLOWED_TARGETS)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        return [
            'cms_page_id' => $data['cms_page_id'] ?? null,
            'section_name' => $data['section_name'],
            'section_type' => strtolower((string) $data['section_type']),
            'title' => $data['title'] ?? null,
            'body' => $data['body'] ?? null,
            'target_pages' => array_values(array_unique($data['target_pages'] ?? [])),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ];
    }
}
