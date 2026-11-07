@extends('admin.layouts.app')

@section('style')
<style>
    .dashboard-heading { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin:22px 0 20px; }
    .dashboard-heading h3 { margin:0; font-weight:700; color:#263238; }
    .dashboard-subtitle { color:#89939d; font-size:13px; margin-top:5px; }
    .dashboard-stat { border:0; border-radius:14px; min-height:125px; overflow:hidden; transition:transform .2s,box-shadow .2s; }
    .dashboard-stat:hover { transform:translateY(-3px); box-shadow:0 12px 28px rgba(30,45,62,.10)!important; }
    .dashboard-stat .card-body { padding:18px; }
    .stat-label { color:#7b8794; font-size:13px; margin-bottom:9px; }
    .stat-value { color:#263238; font-size:25px; line-height:1.25; font-weight:700; overflow-wrap:anywhere; }
    .stat-note { color:#89939d; font-size:11px; margin-top:8px; }
    .stat-icon { width:42px; height:42px; display:flex; align-items:center; justify-content:center; border-radius:12px; font-size:19px; }
    .stat-blue { background:#edf3ff; color:#4778d8; }
    .stat-green { background:#e8f8f1; color:#20a477; }
    .stat-gold { background:#fff5df; color:#c28a20; }
    .stat-purple { background:#f2edff; color:#8061c9; }
    .stat-red { background:#fff0ef; color:#d45b55; }
    .stat-cyan { background:#e9f8fb; color:#2796aa; }
    .dashboard-panel { border:0; border-radius:14px; box-shadow:0 4px 18px rgba(30,45,62,.055); height:100%; }
    .dashboard-panel .card-header { background:#fff; border-bottom:1px solid #f0f2f4; padding:16px 19px; border-radius:14px 14px 0 0; }
    .dashboard-panel .card-header h5 { font-size:15px; font-weight:700; margin:0; color:#303b46; }
    .dashboard-panel .card-body { padding:19px; }
    .dashboard-table { margin-bottom:0; }
    .dashboard-table thead th { border-top:0; color:#89939d; font-size:11px; font-weight:600; white-space:nowrap; }
    .dashboard-table td { vertical-align:middle; font-size:12px; }
    .dashboard-badge { display:inline-block; padding:5px 9px; border-radius:20px; font-size:11px; font-weight:600; white-space:nowrap; }
    .dashboard-badge.warning { color:#9a6b12; background:#fff4d9; }
    .dashboard-badge.success { color:#16835f; background:#e6f7ef; }
    .dashboard-badge.danger { color:#b94a45; background:#ffeded; }
    .dashboard-badge.info,.dashboard-badge.primary { color:#3266a8; background:#eaf2ff; }
    .dashboard-badge.secondary { color:#68737d; background:#eef0f2; }
    .status-row { margin-bottom:15px; }
    .status-row:last-child { margin-bottom:0; }
    .status-meta { display:flex; justify-content:space-between; gap:10px; font-size:12px; margin-bottom:6px; }
    .status-track { height:8px; background:#f0f2f4; border-radius:20px; overflow:hidden; }
    .status-fill { height:100%; border-radius:20px; }
    .status-fill.warning { background:#e8b34d; }
    .status-fill.success { background:#37b98b; }
    .status-fill.danger { background:#df7771; }
    .status-fill.info,.status-fill.primary { background:#6e91d6; }
    .status-fill.secondary { background:#aab2ba; }
    .chart-bars { display:flex; align-items:flex-end; justify-content:space-between; gap:10px; height:175px; padding:10px 2px 0; border-bottom:1px solid #edf0f2; }
    .chart-bar-item { flex:1; min-width:0; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:flex-end; gap:7px; }
    .chart-bar-value { font-size:11px; color:#87919b; white-space:nowrap; }
    .chart-bar { width:min(34px,75%); min-height:4px; border-radius:7px 7px 2px 2px; background:linear-gradient(180deg,#e4bd6a,#c99a3c); }
    .chart-bar.blue { background:linear-gradient(180deg,#82a8f4,#527bd4); }
    .chart-bar-label { font-size:11px; color:#89939d; white-space:nowrap; }
    .dashboard-link { color:#b18431; font-size:12px; font-weight:600; }
    .dashboard-empty { text-align:center; color:#9aa3ab; padding:22px 10px; font-size:13px; }
    .finance-note { font-size:11px; color:#8b949d; margin-top:10px; }
    .finance-number { font-weight:700; white-space:nowrap; }
    @media(max-width:767px) { .stat-value {font-size:21px;} .dashboard-panel .card-body {padding:14px;} .chart-bars {gap:5px;} }
</style>
@endsection

@section('content')
<div class="dashboard-heading">
    <div>
        <h3>{{ __('dashboard.stats.page_title') }}</h3>
        <div class="dashboard-subtitle">{{ __('dashboard.stats.welcome_subtitle') }}</div>
    </div>
    <div class="text-muted small"><i class="fe fe-calendar mr-1"></i>{{ now()->translatedFormat('l, d F Y') }}</div>
</div>

{{-- General KPIs --}}
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-stat shadow-sm"><div class="card-body d-flex justify-content-between">
            <div><div class="stat-label">{{ __('dashboard.stats.total_orders') }}</div><div class="stat-value">{{ number_format($ordersCount) }}</div><div class="stat-note">{{ __('dashboard.stats.today_orders') }}: {{ number_format($todayOrdersCount) }}</div></div>
            <div class="stat-icon stat-blue"><i class="fe fe-shopping-cart"></i></div>
        </div></div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-stat shadow-sm"><div class="card-body d-flex justify-content-between">
            <div><div class="stat-label">{{ __('dashboard.stats.customers') }}</div><div class="stat-value">{{ number_format($customersCount) }}</div><div class="stat-note">{{ __('dashboard.stats.registered_customers') }}</div></div>
            <div class="stat-icon stat-blue"><i class="fe fe-users"></i></div>
        </div></div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-stat shadow-sm"><div class="card-body d-flex justify-content-between">
            <div><div class="stat-label">{{ __('dashboard.stats.vendors') }}</div><div class="stat-value">{{ number_format($vendorsCount) }}</div><div class="stat-note">{{ __('dashboard.stats.pending_vendors') }}: {{ number_format($pendingVendorsCount) }}</div></div>
            <div class="stat-icon stat-purple"><i class="fe fe-shopping-bag"></i></div>
        </div></div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-stat shadow-sm"><div class="card-body d-flex justify-content-between">
            <div><div class="stat-label">{{ __('dashboard.stats.products') }}</div><div class="stat-value">{{ number_format($productsCount) }}</div><div class="stat-note">{{ __('dashboard.stats.active_products') }}: {{ number_format($activeProductsCount) }}</div></div>
            <div class="stat-icon stat-cyan"><i class="fe fe-box"></i></div>
        </div></div>
    </div>
</div>

{{-- Financial overview, separated by currency --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card dashboard-panel">
            <div class="card-header d-flex justify-content-between align-items-center"><h5>{{ __('dashboard.stats.financial_overview') }}</h5><span class="text-muted small">{{ __('dashboard.stats.grouped_by_currency') }}</span></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table dashboard-table">
                        <thead><tr>
                            <th class="pl-3">{{ __('dashboard.stats.currency') }}</th>
                            <th>{{ __('dashboard.stats.gross_paid_sales') }}</th>
                            <th>{{ __('dashboard.stats.refunded_amount') }}</th>
                            <th>{{ __('dashboard.stats.pending_refund_amount') }}</th>
                            <th>{{ __('dashboard.stats.blogger_commission') }}</th>
                            <th>{{ __('dashboard.stats.net_sales') }}</th>
                            <th>{{ __('dashboard.stats.paid_orders') }}</th>
                            <th>{{ __('dashboard.stats.average_order_value') }}</th>
                            <th>{{ __('dashboard.stats.month_revenue') }}</th>
                            <th>{{ __('dashboard.stats.vs_previous_month') }}</th>
                        </tr></thead>
                        <tbody>
                        @forelse($currencyFinancialStats as $finance)
                            <tr>
                                <td class="pl-3 font-weight-bold">{{ $finance['currency_code'] }} <span class="text-muted">({{ $finance['currency_symbol'] }})</span></td>
                                <td class="finance-number">{{ number_format($finance['gross_revenue'], 2) }}</td>
                                <td class="finance-number text-danger">{{ number_format($finance['refunded_amount'], 2) }}</td>
                                <td class="finance-number">{{ number_format($finance['pending_refund_amount'], 2) }}</td>
                                <td class="finance-number">{{ number_format($finance['blogger_commission'], 2) }}</td>
                                <td class="finance-number text-success">{{ number_format($finance['net_revenue'], 2) }}</td>
                                <td>{{ number_format($finance['paid_orders']) }}</td>
                                <td>{{ number_format($finance['average_order_value'], 2) }}</td>
                                <td>{{ number_format($finance['month_revenue'], 2) }}</td>
                                <td>
                                    @if($finance['revenue_change'] !== null)
                                        <span class="{{ $finance['revenue_change'] >= 0 ? 'text-success' : 'text-danger' }}">{{ $finance['revenue_change'] }}%</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="dashboard-empty">{{ __('dashboard.stats.no_financial_data') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-3 pb-3 finance-note">{{ __('dashboard.stats.finance_definition_note') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Inventory overview --}}
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4"><div class="card dashboard-stat shadow-sm"><div class="card-body d-flex justify-content-between">
        <div><div class="stat-label">{{ __('dashboard.stats.out_of_stock_products') }}</div><div class="stat-value">{{ number_format($outOfStockProductsCount) }}</div><div class="stat-note">{{ __('dashboard.stats.simple_out_of_stock') }}: {{ number_format($simpleOutOfStockProductsCount) }} | {{ __('dashboard.stats.variant_out_of_stock') }}: {{ number_format($variantOutOfStockProductsCount) }}</div></div>
        <div class="stat-icon stat-red"><i class="fe fe-alert-triangle"></i></div>
    </div></div></div>
    <div class="col-xl-3 col-md-6 mb-4"><div class="card dashboard-stat shadow-sm"><div class="card-body d-flex justify-content-between">
        <div><div class="stat-label">{{ __('dashboard.stats.out_of_stock_variants') }}</div><div class="stat-value">{{ number_format($outOfStockVariantsCount) }}</div><div class="stat-note">{{ __('dashboard.stats.active_variants_only') }}</div></div>
        <div class="stat-icon stat-red"><i class="fe fe-layers"></i></div>
    </div></div></div>
    <div class="col-xl-3 col-md-6 mb-4"><div class="card dashboard-stat shadow-sm"><div class="card-body d-flex justify-content-between">
        <div><div class="stat-label">{{ __('dashboard.stats.low_stock_products') }}</div><div class="stat-value">{{ number_format($lowStockProductsCount) }}</div><div class="stat-note">{{ __('dashboard.stats.low_stock_threshold') }}: {{ $lowStockThreshold }}</div></div>
        <div class="stat-icon stat-gold"><i class="fe fe-trending-down"></i></div>
    </div></div></div>
    <div class="col-xl-3 col-md-6 mb-4"><div class="card dashboard-stat shadow-sm"><div class="card-body d-flex justify-content-between">
        <div><div class="stat-label">{{ __('dashboard.stats.low_stock_variants') }}</div><div class="stat-value">{{ number_format($lowStockVariantsCount) }}</div><div class="stat-note">{{ __('dashboard.stats.variant_low_stock_note') }}</div></div>
        <div class="stat-icon stat-gold"><i class="fe fe-alert-circle"></i></div>
    </div></div></div>
</div>

{{-- Order, delivery and payment statuses --}}
<div class="row">
    @foreach([
        ['title' => 'order_status_distribution', 'stats' => $orderStatusStats, 'translation' => 'order_status'],
        ['title' => 'delivery_status_distribution', 'stats' => $deliveryStatusStats, 'translation' => 'delivery_status'],
        ['title' => 'payment_status_distribution', 'stats' => $paymentStatusStats, 'translation' => 'payment_status'],
    ] as $group)
        <div class="col-xl-4 mb-4">
            <div class="card dashboard-panel">
                <div class="card-header"><h5>{{ __('dashboard.stats.' . $group['title']) }}</h5></div>
                <div class="card-body">
                    @foreach($group['stats'] as $stat)
                        <div class="status-row">
                            <div class="status-meta"><span>{{ __('dashboard.stats.' . $group['translation'] . '.' . $stat['key']) }}</span><strong>{{ number_format($stat['count']) }} ({{ number_format($stat['percentage'], 1) }}%)</strong></div>
                            <div class="status-track"><div class="status-fill {{ $stat['color'] }}" style="width:{{ $stat['percentage'] }}%"></div></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Payment method breakdown --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card dashboard-panel">
            <div class="card-header"><h5>{{ __('dashboard.stats.payment_methods_breakdown') }}</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table dashboard-table">
                        <thead><tr>
                            <th class="pl-3">{{ __('dashboard.stats.payment_method') }}</th>
                            <th>{{ __('dashboard.stats.payment_total') }}</th>
                            <th>{{ __('dashboard.stats.paid') }}</th>
                            <th>{{ __('dashboard.stats.unpaid_pending') }}</th>
                            <th>{{ __('dashboard.stats.failed') }}</th>
                            <th>{{ __('dashboard.stats.refunded') }}</th>
                        </tr></thead>
                        <tbody>
                        @foreach($paymentMethodStats as $method)
                            <tr>
                                <td class="pl-3 font-weight-bold">{{ __('dashboard.stats.payment_method_names.' . $method['key']) }}</td>
                                <td>{{ number_format($method['total']) }}</td>
                                <td><span class="dashboard-badge success">{{ number_format($method['paid']) }}</span></td>
                                <td><span class="dashboard-badge warning">{{ number_format($method['pending']) }}</span></td>
                                <td><span class="dashboard-badge danger">{{ number_format($method['failed']) }}</span></td>
                                <td><span class="dashboard-badge secondary">{{ number_format($method['refunded']) }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-3 pb-3 finance-note">{{ __('dashboard.stats.payment_methods_note') }}</div>
            </div>
        </div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-12">
        <div class="card dashboard-panel">
            <div class="card-header"><h5>{{ __('dashboard.stats.online_gateways_breakdown') }}</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table dashboard-table">
                        <thead><tr>
                            <th class="pl-3">{{ __('dashboard.stats.payment_gateway') }}</th>
                            <th>{{ __('dashboard.stats.payment_total') }}</th>
                            <th>{{ __('dashboard.stats.paid') }}</th>
                            <th>{{ __('dashboard.stats.unpaid_pending') }}</th>
                            <th>{{ __('dashboard.stats.failed') }}</th>
                            <th>{{ __('dashboard.stats.refunded') }}</th>
                        </tr></thead>
                        <tbody>
                        @foreach($paymentGatewayStats as $gateway)
                            <tr>
                                <td class="pl-3 font-weight-bold">{{ __('dashboard.stats.payment_gateway_names.' . $gateway['key']) }}</td>
                                <td>{{ number_format($gateway['total']) }}</td>
                                <td><span class="dashboard-badge success">{{ number_format($gateway['paid']) }}</span></td>
                                <td><span class="dashboard-badge warning">{{ number_format($gateway['pending']) }}</span></td>
                                <td><span class="dashboard-badge danger">{{ number_format($gateway['failed']) }}</span></td>
                                <td><span class="dashboard-badge secondary">{{ number_format($gateway['refunded']) }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Returns and refunds --}}
<div class="row">
    <div class="col-xl-4 col-md-6 mb-4"><div class="card dashboard-stat shadow-sm"><div class="card-body d-flex justify-content-between">
        <div><div class="stat-label">{{ __('dashboard.stats.open_returns') }}</div><div class="stat-value">{{ number_format($openReturnsCount) }}</div><div class="stat-note">{{ __('dashboard.stats.open_returns_note') }}</div></div>
        <div class="stat-icon stat-gold"><i class="fe fe-rotate-ccw"></i></div>
    </div></div></div>
    <div class="col-xl-4 col-md-6 mb-4"><div class="card dashboard-stat shadow-sm"><div class="card-body d-flex justify-content-between">
        <div><div class="stat-label">{{ __('dashboard.stats.refunded_returns') }}</div><div class="stat-value">{{ number_format($refundedReturnsCount) }}</div><div class="stat-note">{{ __('dashboard.stats.refunded_returns_note') }}</div></div>
        <div class="stat-icon stat-green"><i class="fe fe-check-circle"></i></div>
    </div></div></div>
</div>
<div class="row mb-4">
    <div class="col-12 mb-4">
        <div class="card dashboard-panel">
            <div class="card-header"><h5>{{ __('dashboard.stats.return_status_distribution') }}</h5></div>
            <div class="card-body">
                @foreach($returnStatusStats as $stat)
                    <div class="status-row">
                        <div class="status-meta"><span>{{ __('dashboard.stats.return_status.' . $stat['key']) }}</span><strong>{{ number_format($stat['count']) }} ({{ number_format($stat['percentage'], 1) }}%)</strong></div>
                        <div class="status-track"><div class="status-fill {{ $stat['color'] }}" style="width:{{ $stat['percentage'] }}%"></div></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Charts --}}
<div class="row">
    <div class="col-xl-8 mb-4">
        <div class="card dashboard-panel">
            <div class="card-header d-flex justify-content-between align-items-center"><h5>{{ __('dashboard.stats.monthly_orders_chart') }}</h5><span class="text-muted small">{{ __('dashboard.stats.last_six_months') }}</span></div>
            <div class="card-body">
                <div class="chart-bars">
                    @foreach($monthlyStats as $stat)
                        <div class="chart-bar-item"><span class="chart-bar-value">{{ number_format($stat['orders']) }}</span><div class="chart-bar" style="height:{{ max(5, ($stat['orders'] / $maxMonthlyOrders) * 100) }}%"></div><span class="chart-bar-label">{{ $stat['label'] }}</span></div>
                    @endforeach
                </div>
                <div class="d-flex flex-wrap mt-3">
                    @foreach($monthlyStats as $stat)
                        <div class="mr-3 mb-1 small text-muted">{{ $stat['label'] }}: <strong class="text-dark">{{ number_format($stat['paid_orders']) }}</strong> {{ __('dashboard.stats.paid_orders') }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 mb-4">
        <div class="card dashboard-panel">
            <div class="card-header"><h5>{{ __('dashboard.stats.last_seven_days') }}</h5></div>
            <div class="card-body">
                <div class="chart-bars">
                    @foreach($dailyStats as $stat)
                        <div class="chart-bar-item"><span class="chart-bar-value">{{ number_format($stat['orders']) }}</span><div class="chart-bar blue" style="height:{{ max(5, ($stat['orders'] / $maxDailyOrders) * 100) }}%"></div><span class="chart-bar-label">{{ $stat['label'] }}</span></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recent activity --}}
<div class="row">
    <div class="col-xl-8 mb-4">
        <div class="card dashboard-panel">
            <div class="card-header d-flex justify-content-between align-items-center"><h5>{{ __('dashboard.stats.latest_orders') }}</h5><a href="{{ route('admin.orders.index') }}" class="dashboard-link">{{ __('dashboard.stats.view_all') }}</a></div>
            <div class="card-body p-0">
                <div class="table-responsive"><table class="table dashboard-table">
                    <thead><tr><th class="pl-3">{{ __('dashboard.stats.order_number') }}</th><th>{{ __('dashboard.stats.customer') }}</th><th>{{ __('dashboard.stats.total') }}</th><th>{{ __('dashboard.stats.order_status_label') }}</th><th>{{ __('dashboard.stats.delivery_status_label') }}</th><th>{{ __('dashboard.stats.payment_status_label') }}</th><th>{{ __('dashboard.stats.date') }}</th></tr></thead>
                    <tbody>
                    @forelse($latestOrders as $order)
                        <tr>
                            <td class="pl-3"><a class="dashboard-link" href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                            <td>{{ $order->customer_name }}</td>
                            <td class="font-weight-bold">{{ number_format((float) $order->total, 2) }}</td>
                            <td><span class="dashboard-badge {{ $order->badge('status') }}">{{ __('dashboard.stats.order_status.' . $order->status) }}</span></td>
                            <td><span class="dashboard-badge {{ $order->badge('delivery_status') }}">{{ __('dashboard.stats.delivery_status.' . $order->delivery_status) }}</span></td>
                            <td><span class="dashboard-badge {{ $order->badge('payment_status') }}">{{ __('dashboard.stats.payment_status.' . $order->payment_status) }}</span></td>
                            <td class="text-muted">{{ optional($order->created_at)->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="dashboard-empty">{{ __('dashboard.stats.no_orders_yet') }}</td></tr>
                    @endforelse
                    </tbody>
                </table></div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 mb-4">
        <div class="card dashboard-panel">
            <div class="card-header d-flex justify-content-between align-items-center"><h5>{{ __('dashboard.stats.latest_customers') }}</h5><a href="{{ route('admin.users.index') }}" class="dashboard-link">{{ __('dashboard.stats.view_all') }}</a></div>
            <div class="card-body">
                @forelse($latestCustomers as $customer)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom"><div class="mr-2"><div class="font-weight-bold small">{{ $customer->name }}</div><div class="text-muted" style="font-size:11px;overflow-wrap:anywhere">{{ $customer->email }}</div></div><span class="text-muted" style="font-size:10px">{{ optional($customer->created_at)->format('d/m/Y') }}</span></div>
                @empty
                    <div class="dashboard-empty">{{ __('dashboard.stats.no_customers_yet') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
