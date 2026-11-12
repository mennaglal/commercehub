<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OrdersExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    private bool $isArabic;

    public function __construct(private array $filters)
    {
        $this->isArabic = app()->getLocale() === 'ar';
    }

    public function query(): Builder
    {
        $filters = $this->filters;

        return Order::query()
            ->with([
                'items',
                'country',
                'city',
                'area',
            ])
            ->when($filters['status'] ?? null, fn ($query, $value) =>
            $query->where('status', $value)
            )
            ->when($filters['delivery_status'] ?? null, fn ($query, $value) =>
            $query->where('delivery_status', $value)
            )
            ->when($filters['payment_status'] ?? null, fn ($query, $value) =>
            $query->where('payment_status', $value)
            )
            ->when($filters['country_id'] ?? null, fn ($query, $value) =>
            $query->where('country_id', $value)
            )
            ->when($filters['region_id'] ?? null, fn ($query, $value) =>
            $query->where('city_id', $value)
            )
            ->when($filters['area_id'] ?? null, fn ($query, $value) =>
            $query->where('area_id', $value)
            )
            ->when(($filters['search'] ?? '') !== '', function ($query) use ($filters) {
                $search = $filters['search'];

                $query->where(function ($query) use ($search) {
                    $query->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%")
                        ->orWhere('coupon_code', 'like', "%{$search}%");
                });
            })
            ->latest('id');
    }

    public function headings(): array
    {
        return $this->isArabic
            ? [
                'رقم الطلب',
                'اسم العميل',
                'هاتف العميل',
                'المتاجر',
                'المنتجات',
                'الدولة',
                'المدينة',
                'المنطقة',
                'العنوان',
                'كود الخصم',
                'المجموع الفرعي',
                'الخصم',
                'الشحن',
                'الإجمالي',
                'طريقة الدفع',
                'حالة الدفع',
                'حالة الطلب',
                'حالة التوصيل',
                'رقم التتبع',
                'تاريخ الطلب',
            ]
            : [
                'Order Number',
                'Customer Name',
                'Customer Phone',
                'Stores',
                'Products',
                'Country',
                'City',
                'Area',
                'Shipping Address',
                'Coupon Code',
                'Subtotal',
                'Discount',
                'Shipping',
                'Total',
                'Payment Method',
                'Payment Status',
                'Order Status',
                'Delivery Status',
                'Tracking Number',
                'Order Date',
            ];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->customer_name,
            $order->customer_phone,

            $order->items->pluck('store_name')->filter()->unique()->implode(', '),

            $order->items->map(function ($item) {
                return $item->product_name . ' x ' . $item->quantity;
            })->implode(' | '),

            $order->country?->name,
            $order->city?->name,
            $order->area?->name,
            $order->shipping_address,

            $order->coupon_code,

            (float) $order->subtotal,
            (float) $order->discount_total,
            (float) $order->shipping_total,
            (float) $order->total,

            $order->payment_method,
            $order->payment_status,
            $order->status,
            $order->delivery_status,

            $order->tracking_number,

            $order->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
