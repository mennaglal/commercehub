<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class PageController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:pages.index', only: ['index']),
            new Middleware('permission:pages.update', only: ['edit', 'update']),
        ];
    }


    public function index()
    {
        $pages = Page::orderBy('id')->paginate(15);

        return view(
            'admin.pages.pages.index',
            compact('pages')
        );
    }

    public function edit($id)
    {
        $page = Page::findOrFail($id);

        return view(
            'admin.pages.pages.edit',
            compact('page')
        );
    }

    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        $validated = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],

            'title_en' => ['required', 'string', 'max:255'],

            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('pages', 'slug')->ignore($page->id),
            ],

            'content_ar' => ['nullable', 'string'],

            'content_en' => ['nullable', 'string'],

            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $page->update($validated);

        return redirect()
            ->route('admin.pages.index')
            ->with('success', __('pages.updated_successfully'));
    }
}
