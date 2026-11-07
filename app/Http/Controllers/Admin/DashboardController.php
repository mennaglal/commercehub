<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Blogger;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index()
    {
        $customersCount = User::role('customer')->count();


        $vendorsCount = Store::whereHas('user', function ($query) {
            $query->role('vendor');
        })->count();

        $pendingVendorsCount = Store::where('approval_status', 'pending')->count();

        $productsCount = Product::count();
        $activeProductsCount = Product::where('status', true)->count();

        // A variant product is out of stock only when it has active variants and none has stock.
        $simpleOutOfStockProductsCount = Product::where('status', true)
            ->where('has_variants', false)
            ->where('stock', '<=', 0)
            ->count();

        $variantOutOfStockProductsCount = Product::where('status', true)
            ->where('has_variants', true)
            ->whereHas('variants', function ($query) {
                $query->where('status', true);
            })
            ->whereDoesntHave('variants', function ($query) {
                $query->where('status', true)->where('quantity', '>', 0);
            })
            ->count();

        $outOfStockProductsCount = $simpleOutOfStockProductsCount + $variantOutOfStockProductsCount;
        $outOfStockVariantsCount = ProductVariant::where('status', true)
            ->where('quantity', '<=', 0)
            ->count();

        // The threshold is configurable in config/inventory.php (defaults to 5).
        $lowStockThreshold = (int) config('inventory.low_stock_threshold', 5);

        $simpleLowStockProductsCount = Product::where('status', true)
            ->where('has_variants', false)
            ->whereBetween('stock', [1, $lowStockThreshold])
            ->count();

        $variantLowStockProductsCount = Product::where('status', true)
            ->where('has_variants', true)
            ->whereHas('variants', function ($query) use ($lowStockThreshold) {
                $query->where('status', true)
                    ->whereBetween('quantity', [1, $lowStockThreshold]);
            })
            ->count();

        $lowStockProductsCount = $simpleLowStockProductsCount + $variantLowStockProductsCount;
        $lowStockVariantsCount = ProductVariant::where('status', true)
            ->whereBetween('quantity', [1, $lowStockThreshold])
            ->count();

        $ordersCount = Order::count();
        $todayOrdersCount = Order::whereDate('created_at', today())->count();

        $orderStatusStats = $this->statusStats(
            Order::STATUSES,
            'status',
            $ordersCount,
            [
                Order::STATUS_PENDING => 'warning',
                Order::STATUS_CONFIRMED => 'success',
                Order::STATUS_CANCELLED => 'danger',
            ]
        );

        $deliveryStatusStats = $this->statusStats(
            Order::DELIVERY_STATUSES,
            'delivery_status',
            $ordersCount,
            [
                Order::DELIVERY_PENDING => 'secondary',
                Order::DELIVERY_PREPARING => 'warning',
                Order::DELIVERY_SHIPPED => 'primary',
                Order::DELIVERY_DELIVERED => 'success',
                Order::DELIVERY_RETURNED => 'danger',
            ]
        );

        $paymentStatusStats = $this->statusStats(
            Order::PAYMENT_STATUSES,
            'payment_status',
            $ordersCount,
            [
                Order::PAYMENT_PENDING => 'warning',
                Order::PAYMENT_PAID => 'success',
                Order::PAYMENT_FAILED => 'danger',
                Order::PAYMENT_REFUNDED => 'secondary',
            ]
        );

        // Payment method x payment status matrix. Counts are safe to compare across currencies.
        $paymentRows = DB::table('orders')
            ->select('payment_method', 'payment_status')
            ->selectRaw('COUNT(*) as orders_count')
            ->groupBy('payment_method', 'payment_status')
            ->get()
            ->groupBy('payment_method');

        $paymentMethodStats = collect(Order::PAYMENT_METHODS)->map(function ($method) use ($paymentRows) {
            $rows = $paymentRows->get($method, collect())->keyBy('payment_status');

            return [
                'key' => $method,
                'total' => (int) $rows->sum('orders_count'),
                'paid' => (int) ($rows->get(Order::PAYMENT_PAID)->orders_count ?? 0),
                'pending' => (int) ($rows->get(Order::PAYMENT_PENDING)->orders_count ?? 0),
                'failed' => (int) ($rows->get(Order::PAYMENT_FAILED)->orders_count ?? 0),
                'refunded' => (int) ($rows->get(Order::PAYMENT_REFUNDED)->orders_count ?? 0),
            ];
        });

        $gatewayRows = DB::table('orders')
            ->where('payment_method', Order::PAYMENT_ONLINE)
            ->whereNotNull('payment_gateway')
            ->select('payment_gateway', 'payment_status')
            ->selectRaw('COUNT(*) as orders_count')
            ->groupBy('payment_gateway', 'payment_status')
            ->get()
            ->groupBy('payment_gateway');

        $paymentGatewayStats = collect(Order::PAYMENT_GATEWAYS)->map(function ($gateway) use ($gatewayRows) {
            $rows = $gatewayRows->get($gateway, collect())->keyBy('payment_status');

            return [
                'key' => $gateway,
                'total' => (int) $rows->sum('orders_count'),
                'paid' => (int) ($rows->get(Order::PAYMENT_PAID)->orders_count ?? 0),
                'pending' => (int) ($rows->get(Order::PAYMENT_PENDING)->orders_count ?? 0),
                'failed' => (int) ($rows->get(Order::PAYMENT_FAILED)->orders_count ?? 0),
                'refunded' => (int) ($rows->get(Order::PAYMENT_REFUNDED)->orders_count ?? 0),
            ];
        });

        // Financial totals are grouped by the order's country currency. Never add different currencies.
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $previousMonthStart = now()->subMonthNoOverflow()->startOfMonth();
        $previousMonthEnd = now()->subMonthNoOverflow()->endOfMonth();

        $allTimeRevenue = $this->paidRevenueByCurrency();
        $monthRevenue = $this->paidRevenueByCurrency($monthStart, $monthEnd);
        $previousMonthRevenue = $this->paidRevenueByCurrency($previousMonthStart, $previousMonthEnd);
        $refundsByCurrency = $this->refundedReturnsByCurrency();
        $pendingRefundsByCurrency = $this->pendingReturnsByCurrency();
        $bloggerCommissionsByCurrency = $this->bloggerCommissionsByCurrency();

        $currencyCodes = $allTimeRevenue->keys()
            ->merge($monthRevenue->keys())
            ->merge($previousMonthRevenue->keys())
            ->merge($refundsByCurrency->keys())
            ->merge($pendingRefundsByCurrency->keys())
            ->merge($bloggerCommissionsByCurrency->keys())
            ->unique()
            ->values();

        $currencyFinancialStats = $currencyCodes->map(function ($code) use (
            $allTimeRevenue,
            $monthRevenue,
            $previousMonthRevenue,
            $refundsByCurrency,
            $pendingRefundsByCurrency,
            $bloggerCommissionsByCurrency
        ) {
            $allTime = $allTimeRevenue->get($code);
            $current = $monthRevenue->get($code);
            $previous = $previousMonthRevenue->get($code);
            $refunds = (float) ($refundsByCurrency->get($code)->refunded_amount ?? 0);
            $pendingRefunds = (float) ($pendingRefundsByCurrency->get($code)->pending_refund_amount ?? 0);
            $bloggerCommission = (float) ($bloggerCommissionsByCurrency->get($code)->blogger_commission ?? 0);
            $gross = (float) ($allTime->gross_revenue ?? 0);
            $paidCount = (int) ($allTime->paid_orders ?? 0);
            $previousAmount = (float) ($previous->gross_revenue ?? 0);
            $currentAmount = (float) ($current->gross_revenue ?? 0);

            return [
                'currency_code' => $code,
                'currency_symbol' => $allTime->currency_symbol
                    ?? $current->currency_symbol
                    ?? $previous->currency_symbol
                    ?? $refundsByCurrency->get($code)->currency_symbol
                    ?? $pendingRefundsByCurrency->get($code)->currency_symbol
                    ?? $bloggerCommissionsByCurrency->get($code)->currency_symbol
                    ?? $code,
                'paid_orders' => $paidCount,
                'gross_revenue' => $gross,
                'refunded_amount' => $refunds,
                'pending_refund_amount' => $pendingRefunds,
                'blogger_commission' => $bloggerCommission,
                'net_revenue' => $gross - $refunds,
                'average_order_value' => $paidCount > 0 ? $gross / $paidCount : 0,
                'month_revenue' => $currentAmount,
                'previous_month_revenue' => $previousAmount,
                'revenue_change' => $previousAmount > 0
                    ? round((($currentAmount - $previousAmount) / $previousAmount) * 100, 1)
                    : null,
            ];
        })->sortBy('currency_code')->values();

        $returnStatusStats = $this->statusStats(
            OrderReturn::STATUSES,
            'status',
            OrderReturn::count(),
            [
                OrderReturn::STATUS_REQUESTED => 'warning',
                OrderReturn::STATUS_APPROVED => 'info',
                OrderReturn::STATUS_REJECTED => 'danger',
                OrderReturn::STATUS_RECEIVED => 'primary',
                OrderReturn::STATUS_REFUNDED => 'success',
            ],
            OrderReturn::class
        );

        $openReturnsCount = OrderReturn::whereIn('status', [
            OrderReturn::STATUS_REQUESTED,
            OrderReturn::STATUS_APPROVED,
            OrderReturn::STATUS_RECEIVED,
        ])->count();

        $refundedReturnsCount = OrderReturn::where('status', OrderReturn::STATUS_REFUNDED)->count();

        // Monthly chart uses order counts, not mixed-currency money values.
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonthsNoOverflow($i)->startOfMonth();
            $monthEnd = (clone $month)->endOfMonth();

            $monthlyStats[] = [
                'label' => $month->translatedFormat('M'),
                'orders' => Order::whereBetween('created_at', [$month, $monthEnd])->count(),
                'paid_orders' => Order::whereNotNull('paid_at')
                    ->whereIn('payment_status', [Order::PAYMENT_PAID, Order::PAYMENT_REFUNDED])
                    ->whereBetween('paid_at', [$month, $monthEnd])
                    ->count(),
            ];
        }

        $maxMonthlyOrders = max(1, max(array_column($monthlyStats, 'orders')));

        $dailyStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->startOfDay();
            $dayEnd = (clone $day)->endOfDay();

            $dailyStats[] = [
                'label' => $day->format('d/m'),
                'orders' => Order::whereBetween('created_at', [$day, $dayEnd])->count(),
            ];
        }

        $maxDailyOrders = max(1, max(array_column($dailyStats, 'orders')));

        $latestOrders = Order::latest()->take(8)->get();
        $latestCustomers = User::role('customer')->latest()->take(5)->get();

        return view('admin.pages.dashboard.index', compact(
            'customersCount',
            'vendorsCount',
            'pendingVendorsCount',
            'productsCount',
            'activeProductsCount',
            'simpleOutOfStockProductsCount',
            'variantOutOfStockProductsCount',
            'outOfStockProductsCount',
            'outOfStockVariantsCount',
            'lowStockThreshold',
            'lowStockProductsCount',
            'lowStockVariantsCount',
            'ordersCount',
            'todayOrdersCount',
            'orderStatusStats',
            'deliveryStatusStats',
            'paymentStatusStats',
            'paymentMethodStats',
            'paymentGatewayStats',
            'currencyFinancialStats',
            'returnStatusStats',
            'openReturnsCount',
            'refundedReturnsCount',
            'monthlyStats',
            'maxMonthlyOrders',
            'dailyStats',
            'maxDailyOrders',
            'latestOrders',
            'latestCustomers',
        ));
    }

    private function statusStats(array $statuses, string $column, int $total, array $colors, string $model = Order::class)
    {
        $counts = $model::query()
            ->select($column)
            ->selectRaw('COUNT(*) as aggregate')
            ->groupBy($column)
            ->pluck('aggregate', $column);

        return collect($statuses)->map(function ($status) use ($counts, $total, $colors) {
            $count = (int) $counts->get($status, 0);

            return [
                'key' => $status,
                'count' => $count,
                'percentage' => $total > 0 ? ($count / $total) * 100 : 0,
                'color' => $colors[$status] ?? 'secondary',
            ];
        });
    }

    private function paidRevenueByCurrency($start = null, $end = null)
    {
        $query = DB::table('orders')
            ->leftJoin('countries', 'countries.id', '=', 'orders.country_id')
            ->leftJoin('currencies', 'currencies.id', '=', 'countries.currency_id')
            ->whereNotNull('orders.paid_at')
            ->whereIn('orders.payment_status', [Order::PAYMENT_PAID, Order::PAYMENT_REFUNDED]);

        if ($start && $end) {
            $query->whereBetween('orders.paid_at', [$start, $end]);
        }

        return $query
            ->selectRaw("COALESCE(currencies.code, 'UNKNOWN') as currency_code")
            ->selectRaw("COALESCE(currencies.symbol, currencies.code, 'UNKNOWN') as currency_symbol")
            ->selectRaw('COUNT(orders.id) as paid_orders, SUM(orders.total) as gross_revenue')
            ->groupBy('currencies.id', 'currencies.code', 'currencies.symbol')
            ->get()
            ->keyBy('currency_code');
    }

    private function refundedReturnsByCurrency()
    {
        return DB::table('order_returns')
            ->join('orders', 'orders.id', '=', 'order_returns.order_id')
            ->leftJoin('countries', 'countries.id', '=', 'orders.country_id')
            ->leftJoin('currencies', 'currencies.id', '=', 'countries.currency_id')
            ->where('order_returns.status', OrderReturn::STATUS_REFUNDED)
            ->selectRaw("COALESCE(currencies.code, 'UNKNOWN') as currency_code")
            ->selectRaw("COALESCE(currencies.symbol, currencies.code, 'UNKNOWN') as currency_symbol")
            ->selectRaw('SUM(order_returns.refund_amount) as refunded_amount')
            ->groupBy('currencies.id', 'currencies.code', 'currencies.symbol')
            ->get()
            ->keyBy('currency_code');
    }



    private function bloggerCommissionsByCurrency()
    {
        return DB::table('orders')
            ->leftJoin('countries', 'countries.id', '=', 'orders.country_id')
            ->leftJoin('currencies', 'currencies.id', '=', 'countries.currency_id')
            ->where('orders.commission_status', Order::COMMISSION_EARNED)
            ->selectRaw("COALESCE(currencies.code, 'UNKNOWN') as currency_code")
            ->selectRaw("COALESCE(currencies.symbol, currencies.code, 'UNKNOWN') as currency_symbol")
            ->selectRaw('SUM(orders.commission_amount) as blogger_commission')
            ->groupBy('currencies.id', 'currencies.code', 'currencies.symbol')
            ->get()
            ->keyBy('currency_code');
    }

    private function pendingReturnsByCurrency()
    {
        return DB::table('order_returns')
            ->join('orders', 'orders.id', '=', 'order_returns.order_id')
            ->leftJoin('countries', 'countries.id', '=', 'orders.country_id')
            ->leftJoin('currencies', 'currencies.id', '=', 'countries.currency_id')
            ->whereIn('order_returns.status', [
                OrderReturn::STATUS_REQUESTED,
                OrderReturn::STATUS_APPROVED,
                OrderReturn::STATUS_RECEIVED,
            ])
            ->selectRaw("COALESCE(currencies.code, 'UNKNOWN') as currency_code")
            ->selectRaw("COALESCE(currencies.symbol, currencies.code, 'UNKNOWN') as currency_symbol")
            ->selectRaw('SUM(order_returns.refund_amount) as pending_refund_amount')
            ->groupBy('currencies.id', 'currencies.code', 'currencies.symbol')
            ->get()
            ->keyBy('currency_code');
    }

    private const RESOURCES = [
        'customers'      => [User::class,     'users.delete',      ['customer']],
        'vendors'        => [User::class,     'vendors.delete',    ['vendor']],
        'admins'         => [User::class,     'admins.delete',     ['admin', 'moderator', 'support']],
        'categories'     => [Category::class, 'categories.delete'],
        'sub-categories' => [Category::class, 'categories.delete'],
        'countries'      => [Country::class,  'countries.delete'],
        'cities'         => [City::class,     'cities.delete'],
        'areas'          => [Area::class,     'areas.delete'],
        'currencies'     => [Currency::class, 'currencies.delete'],
        'products'       => [Product::class,  'products.delete'],
        'orders'         => [Order::class,    'orders.delete'],
        'bloggers'       => [Blogger::class,  'bloggers.delete'],
        'coupons'        => [Coupon::class,   'coupons.delete'],
    ];

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'model' => ['required', Rule::in(array_keys(self::RESOURCES))],
            'ids'   => ['required', 'string'],
        ]);

        [$class, $permission, $roles] = self::RESOURCES[$request->model] + [2 => null];

        abort_unless($request->user()->can($permission), 403);

        $ids = array_filter(array_map('trim', explode(',', $request->ids)), 'ctype_digit');
        $query = $class::whereIn('id', $ids);

        if ($roles) {
            $query->role($roles)->where('id', '!=', $request->user()->id);
        }

        $query->get()->each(function ($model) {
            try {
                $model->delete();
            } catch (QueryException $exception) {
                // Skip records that are still referenced by related records.
            }
        });

        return back()->with('success', __('messages.deleted'));
    }
}
