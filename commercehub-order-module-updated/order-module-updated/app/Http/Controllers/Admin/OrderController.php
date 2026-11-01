<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function __construct(private OrderService $orders)
    {
    }

    public function index(Request $request)
    {
        // A filter value that is not in the list is ignored.
        $pick = fn (string $name, array $allowed) => in_array($request->query($name), $allowed, true) ? $request->query($name) : null;

        $status = $pick('status', Order::STATUSES);
        $delivery = $pick('delivery_status', Order::DELIVERY_STATUSES);
        $payment = $pick('payment_status', Order::PAYMENT_STATUSES);
        $search = trim((string) $request->query('search'));

        $orders = Order::with('items:id,order_id,store_name')
            ->withCount('items')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($delivery, fn ($query) => $query->where('delivery_status', $delivery))
            ->when($payment, fn ($query) => $query->where('payment_status', $payment))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.orders.index', ['orders' => $orders]);
    }

    public function create()
    {
        $isArabic = app()->getLocale() === 'ar';

        // Data used by the JS on the page. The price here is for DISPLAY only:
        // the server calculates it again when the order is saved.
        $products = Product::where('status', 1)
            ->with(['variants.values', 'store:id,name'])
            ->orderBy('name_en')
            ->get()
            ->map(fn ($product) => [
                'id' => $product->id,
                'name' => ($isArabic ? $product->name_ar : $product->name_en) . ' (' . $product->sku . ')',
                'has_variants' => (bool) $product->has_variants,
                'price' => $this->orders->unitPrice($product),
                'variants' => $product->has_variants
                    ? $product->variants->map(fn ($variant) => [
                        'id' => $variant->id,
                        'label' => $this->orders->variantLabel($variant),
                        'price' => $this->orders->unitPrice($product, $variant),
                    ])->values()
                    : [],
            ])
            ->values();

        return view('admin.pages.orders.create', [
            'products' => $products,
            'gateways' => config('orders.gateways'),
        ]);
    }

    public function store(OrderRequest $request)
    {
        $order = $this->orders->create($request->validated());

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', __('orders.messages.created'));
    }

    public function show(Order $order)
    {
        $order->load(['items.store:id,name', 'blogger:id,name', 'statusHistories.user:id,name']);

        return view('admin.pages.orders.show', ['order' => $order]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(Order::STATUSES)],
        ]);

        $this->orders->changeStatus($order, $data['status']);

        return back()->with('success', __('orders.messages.status_updated'));
    }

    public function updateDelivery(Request $request, Order $order)
    {
        $data = $request->validate([
            'delivery_status' => ['required', Rule::in(Order::DELIVERY_STATUSES)],
            'tracking_number' => ['nullable', 'string', 'max:100'],
        ]);

        $this->orders->changeDelivery($order, $data['delivery_status'], $data['tracking_number'] ?? null);

        return back()->with('success', __('orders.messages.delivery_updated'));
    }

    public function updatePayment(Request $request, Order $order)
    {
        $data = $request->validate([
            'payment_status' => ['required', Rule::in(Order::PAYMENT_STATUSES)],
            'payment_id' => ['nullable', 'string', 'max:255'],
            'invoice_id' => ['nullable', 'string', 'max:255'],
            // Only http(s) links, so nobody can save a "javascript:" link.
            'invoice_url' => ['nullable', 'url', 'starts_with:https://,http://', 'max:2000'],
        ]);

        $this->orders->updatePayment($order, $data);

        return back()->with('success', __('orders.messages.payment_updated'));
    }
}
