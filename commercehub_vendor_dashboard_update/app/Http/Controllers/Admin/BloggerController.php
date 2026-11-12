<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BloggerRequest;
use App\Models\Blogger;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BloggerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:bloggers.view', only: ['index', 'show']),
            new Middleware('permission:bloggers.create', only: ['create', 'store']),
            new Middleware('permission:bloggers.update', only: ['edit', 'update']),
            new Middleware('permission:bloggers.delete', only: ['destroy']),
        ];
    }
    public function index(Request $request)
    {
        $bloggers = Blogger::withCount('coupons')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.bloggers.index', compact('bloggers'));
    }
    public function create()
    {
        return view('admin.pages.bloggers.create');
    }

    public function store(BloggerRequest $request)
    {
        $blogger = Blogger::create($request->validated());

        return redirect()
            ->route('admin.bloggers.show', $blogger)
            ->with('success', __('bloggers.messages.created'));
    }

    public function show(Blogger $blogger)
    {
        // Count and sum of the commissions, grouped by status (one query).
        $stats = Order::where('blogger_id', $blogger->id)
            ->selectRaw('commission_status, COUNT(*) as orders_count, SUM(commission_amount) as amount')
            ->groupBy('commission_status')
            ->get()
            ->keyBy('commission_status');

        $orders = Order::where('blogger_id', $blogger->id)->latest()->paginate(10);

        return view('admin.pages.bloggers.show', [
            'blogger' => $blogger->load('coupons:id,blogger_id,code,status'),
            'stats' => $stats,
            'orders' => $orders,
        ]);
    }

    public function edit(Blogger $blogger)
    {
        return view('admin.pages.bloggers.edit', ['blogger' => $blogger]);
    }

    public function update(BloggerRequest $request, Blogger $blogger)
    {
        $blogger->update($request->validated());

        return redirect()
            ->route('admin.bloggers.index')
            ->with('success', __('bloggers.messages.updated'));
    }
    public function destroy(Blogger $blogger)
    {
        $blogger->delete();

        return redirect()
            ->route('admin.bloggers.index')
            ->with('success', __('bloggers.messages.deleted'));
    }
}
