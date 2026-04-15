<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\CmsPageSection;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CmsPublicPageController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $page = CmsPage::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $rows = CmsPageSection::query()
            ->where('is_active', true)
            ->where(function ($query) use ($page, $slug) {
                $query->where('cms_page_id', $page->id)
                    ->orWhereJsonContains('target_pages', $slug);
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $sections = $rows->map(function (CmsPageSection $row) {
            return [
                'id' => $row->id,
                'sectionName' => (string) $row->section_name,
                'sectionType' => (string) $row->section_type,
                'title' => (string) ($row->title ?? ''),
                'body' => (string) ($row->body ?? ''),
                'sortOrder' => (int) $row->sort_order,
            ];
        })->values();

        return Inertia::render('CmsPageView', [
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
            ],
            'sections' => $sections,
        ]);
    }
}
