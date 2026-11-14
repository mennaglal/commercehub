<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Http\Responses\ApiResponse;
use App\Models\Page;

// About us, Privacy policy, Return policy, Terms, Contact us ... (managed from the admin panel)
class PageController extends Controller
{
    // GET /pages : slug + title of the active pages (to build a menu)
    public function index()
    {
        $pages = Page::where('is_active', true)->orderBy('id')->get(['id', 'slug', 'title_ar', 'title_en', 'updated_at']);

        return ApiResponse::success(PageResource::collection($pages), 'Pages fetched successfully');
    }

    // GET /pages/{slug}
    public function show(string $slug)
    {
        $page = Page::where('is_active', true)->where('slug', $slug)->firstOrFail();

        return ApiResponse::success(new PageResource($page), 'Page fetched successfully');
    }
}
