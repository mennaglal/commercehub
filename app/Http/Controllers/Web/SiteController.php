<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\CatalogService;

/**
 * The public website. Almost every page is only a shell: the data comes from the API (public/assets/web/js/pages/*.js).
 * The pages that are about ONE record (a product, a store, a page) check it here first, so:
 *  - an unknown address is a real 404 (good for the search engines),
 *  - the title, the description and the picture for Google / Facebook / WhatsApp previews are in the HTML.
 */
class SiteController extends Controller
{
    public function __construct(private CatalogService $catalog)
    {
    }

    public function product(string $slug)
    {
        $product = $this->catalog->visibleProducts()->where('products.slug', $slug)->firstOrFail();

        return view('web.product', ['product' => $product, 'slug' => $slug]);
    }

    public function store(string $slug)
    {
        $store = \App\Models\Store::where('status', true)->where('approval_status', 'approved')->where('slug', $slug)->firstOrFail();

        return view('web.store', ['store' => $store, 'slug' => $slug]);
    }

    public function page(string $slug)
    {
        $page = Page::where('is_active', true)->where('slug', $slug)->firstOrFail();

        return view('web.page', ['page' => $page, 'slug' => $slug]);
    }

    public function orderSuccess(string $orderNumber)
    {
        return view('web.order-success', ['orderNumber' => $orderNumber]);
    }

    public function order(string $orderNumber)
    {
        return view('web.account.order', ['orderNumber' => $orderNumber]);
    }

    public function returnForm(string $orderNumber)
    {
        return view('web.account.return-new', ['orderNumber' => $orderNumber]);
    }

    public function returnShow(string $returnNumber)
    {
        return view('web.account.return', ['returnNumber' => $returnNumber]);
    }

    public function vendorProductForm(?int $id = null)
    {
        return view('web.vendor.product-form', ['productId' => $id]);
    }
}
