<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\Product;
use App\Models\Store;
use App\Services\ReturnService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DemoVendorDashboardSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'tech-zone')->firstOrFail();

        $vendor = $store->user;

        if (! $vendor) {
            throw new RuntimeException('The tech-zone store does not have an owner.');
        }

        if (! $vendor->hasRole('vendor')) {
            throw new RuntimeException('The tech-zone store owner does not have the vendor role.');
        }

        $store->update(['status' => true]);

        $products = Product::query()
            ->where('store_id', $store->id)
            ->where('status', true)
            ->where('has_variants', false)
            ->whereIn('sku', [
                'DEMO-002',
                'DEMO-004',
                'DEMO-009',
                'DEMO-010',
            ])
            ->orderBy('id')
            ->get();

        if ($products->count() < 4) {
            throw new RuntimeException(
                'Required demo products are missing. Run ProductSeeder first.'
            );
        }

        DB::transaction(function () use ($store, $products) {
            $orders = [
                [
                    'number' => 'DEMO-THZ-0001',
                    'product' => 0,
                    'quantity' => 1,
                    'status' => Order::STATUS_PENDING,
                    'payment_status' => Order::PAYMENT_PENDING,
                    'delivery_status' => Order::DELIVERY_PENDING,
                    'payment_method' => Order::PAYMENT_COD,
                    'days_ago' => 1,
                ],
                [
                    'number' => 'DEMO-THZ-0002',
                    'product' => 1,
                    'quantity' => 1,
                    'status' => Order::STATUS_CONFIRMED,
                    'payment_status' => Order::PAYMENT_PAID,
                    'delivery_status' => Order::DELIVERY_PREPARING,
                    'payment_method' => Order::PAYMENT_ONLINE,
                    'days_ago' => 2,
                ],
                [
                    'number' => 'DEMO-THZ-0003',
                    'product' => 2,
                    'quantity' => 1,
                    'status' => Order::STATUS_CONFIRMED,
                    'payment_status' => Order::PAYMENT_PAID,
                    'delivery_status' => Order::DELIVERY_SHIPPED,
                    'payment_method' => Order::PAYMENT_ONLINE,
                    'days_ago' => 3,
                ],
                [
                    'number' => 'DEMO-THZ-0004',
                    'product' => 3,
                    'quantity' => 2,
                    'status' => Order::STATUS_CONFIRMED,
                    'payment_status' => Order::PAYMENT_PAID,
                    'delivery_status' => Order::DELIVERY_DELIVERED,
                    'payment_method' => Order::PAYMENT_COD,
                    'days_ago' => 4,
                ],
                [
                    'number' => 'DEMO-THZ-0005',
                    'product' => 0,
                    'quantity' => 2,
                    'status' => Order::STATUS_CONFIRMED,
                    'payment_status' => Order::PAYMENT_PAID,
                    'delivery_status' => Order::DELIVERY_DELIVERED,
                    'payment_method' => Order::PAYMENT_ONLINE,
                    'days_ago' => 5,
                ],
                [
                    'number' => 'DEMO-THZ-0006',
                    'product' => 1,
                    'quantity' => 2,
                    'status' => Order::STATUS_CONFIRMED,
                    'payment_status' => Order::PAYMENT_PAID,
                    'delivery_status' => Order::DELIVERY_DELIVERED,
                    'payment_method' => Order::PAYMENT_COD,
                    'days_ago' => 6,
                ],
                [
                    'number' => 'DEMO-THZ-0007',
                    'product' => 2,
                    'quantity' => 2,
                    'status' => Order::STATUS_CONFIRMED,
                    'payment_status' => Order::PAYMENT_PAID,
                    'delivery_status' => Order::DELIVERY_DELIVERED,
                    'payment_method' => Order::PAYMENT_ONLINE,
                    'days_ago' => 7,
                ],
                [
                    'number' => 'DEMO-THZ-0008',
                    'product' => 3,
                    'quantity' => 1,
                    'status' => Order::STATUS_CANCELLED,
                    'payment_status' => Order::PAYMENT_FAILED,
                    'delivery_status' => Order::DELIVERY_PENDING,
                    'payment_method' => Order::PAYMENT_ONLINE,
                    'days_ago' => 8,
                ],
                [
                    'number' => 'DEMO-THZ-0009',
                    'product' => 0,
                    'quantity' => 2,
                    'status' => Order::STATUS_CONFIRMED,
                    'payment_status' => Order::PAYMENT_PAID,
                    'delivery_status' => Order::DELIVERY_DELIVERED,
                    'payment_method' => Order::PAYMENT_COD,
                    'days_ago' => 9,
                ],
                [
                    'number' => 'DEMO-THZ-0010',
                    'product' => 1,
                    'quantity' => 1,
                    'status' => Order::STATUS_CONFIRMED,
                    'payment_status' => Order::PAYMENT_PAID,
                    'delivery_status' => Order::DELIVERY_DELIVERED,
                    'payment_method' => Order::PAYMENT_ONLINE,
                    'days_ago' => 10,
                ],
            ];

            foreach ($orders as $data) {
                $product = $products[$data['product']];

                $this->createOrder($store, $product, $data);
            }
        });

        $this->seedReturns();
    }

    private function createOrder(Store $store, Product $product, array $data): Order
    {
        $existingOrder = Order::where('order_number', $data['number'])->first();

        if ($existingOrder) {
            return $existingOrder;
        }

        $quantity = $data['quantity'];

        $unitPrice = (float) (
        $product->sale_price > 0
            ? $product->sale_price
            : $product->price
        );

        $subtotal = round($unitPrice * $quantity, 2);
        $shipping = 25;
        $createdAt = now()->subDays($data['days_ago']);

        $deliveredAt = $data['delivery_status'] === Order::DELIVERY_DELIVERED
            ? $createdAt->copy()->addDays(1)
            : null;

        $order = Order::create([
            'user_id' => null,
            'order_number' => $data['number'],
            'status' => $data['status'],

            'customer_name' => 'Demo Customer ' . substr($data['number'], -4),
            'customer_phone' => '01000000000',
            'shipping_address' => 'Demo Address, Cairo, Egypt',

            'subtotal' => $subtotal,
            'discount_total' => 0,
            'shipping_total' => $shipping,
            'total' => $subtotal + $shipping,

            'payment_method' => $data['payment_method'],
            'payment_gateway' => $data['payment_method'] === Order::PAYMENT_ONLINE
                ? Order::GATEWAY_MYFATOORAH
                : null,

            'payment_status' => $data['payment_status'],
            'paid_at' => $data['payment_status'] === Order::PAYMENT_PAID
                ? $createdAt->copy()
                : null,

            'delivery_status' => $data['delivery_status'],
            'tracking_number' => $data['delivery_status'] === Order::DELIVERY_SHIPPED
                ? 'TRK-' . substr($data['number'], -4)
                : null,

            'shipped_at' => in_array($data['delivery_status'], [
                Order::DELIVERY_SHIPPED,
                Order::DELIVERY_DELIVERED,
            ], true) ? $createdAt->copy()->addHours(12) : null,

            'delivered_at' => $deliveredAt,

            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        $order->items()->create([
            'store_id' => $store->id,
            'store_name' => $store->name,
            'product_id' => $product->id,
            'variant_id' => null,
            'product_name' => $product->name,
            'variant_label' => null,
            'sku' => $product->sku,
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'line_total' => $subtotal,
            'discount_amount' => 0,
            'commission_amount' => 0,
        ]);

        return $order;
    }

    private function seedReturns(): void
    {
        $returnService = app(ReturnService::class);

        $scenarios = [
            [
                'order' => 'DEMO-THZ-0004',
                'quantity' => 1,
                'reason' => 'defective',
                'status' => OrderReturn::STATUS_REQUESTED,
            ],
            [
                'order' => 'DEMO-THZ-0005',
                'quantity' => 1,
                'reason' => 'wrong_item',
                'status' => OrderReturn::STATUS_APPROVED,
            ],
            [
                'order' => 'DEMO-THZ-0006',
                'quantity' => 1,
                'reason' => 'not_as_described',
                'status' => OrderReturn::STATUS_RECEIVED,
            ],
            [
                'order' => 'DEMO-THZ-0007',
                'quantity' => 2,
                'reason' => 'defective',
                'status' => OrderReturn::STATUS_REFUNDED,
            ],
            [
                'order' => 'DEMO-THZ-0009',
                'quantity' => 1,
                'reason' => 'changed_mind',
                'status' => OrderReturn::STATUS_REJECTED,
            ],
        ];

        foreach ($scenarios as $scenario) {
            $order = Order::with('items')
                ->where('order_number', $scenario['order'])
                ->firstOrFail();

            if ($order->returns()->exists()) {
                continue;
            }

            $item = $order->items->first();

            if (! $item) {
                throw new RuntimeException(
                    "Order {$scenario['order']} has no order items."
                );
            }

            $return = $returnService->create($order, [
                'reason' => $scenario['reason'],
                'customer_note' => 'Demo return request for dashboard testing.',
                'items' => [
                    $item->id => $scenario['quantity'],
                ],
            ]);

            if (in_array($scenario['status'], [
                OrderReturn::STATUS_APPROVED,
                OrderReturn::STATUS_RECEIVED,
                OrderReturn::STATUS_REFUNDED,
            ], true)) {
                $returnService->changeStatus(
                    $return,
                    OrderReturn::STATUS_APPROVED,
                    ['admin_note' => 'Demo return approved.']
                );
            }

            if (in_array($scenario['status'], [
                OrderReturn::STATUS_RECEIVED,
                OrderReturn::STATUS_REFUNDED,
            ], true)) {
                $return->load('items');

                $returnService->changeStatus(
                    $return,
                    OrderReturn::STATUS_RECEIVED,
                    [
                        'admin_note' => 'Demo returned items received.',
                        'restock' => [],
                    ]
                );
            }

            if ($scenario['status'] === OrderReturn::STATUS_REFUNDED) {
                $returnService->changeStatus(
                    $return,
                    OrderReturn::STATUS_REFUNDED,
                    [
                        'admin_note' => 'Demo refund completed.',
                        'refund_reference' => 'DEMO-REF-' . $return->id,
                    ]
                );
            }

            if ($scenario['status'] === OrderReturn::STATUS_REJECTED) {
                $returnService->changeStatus(
                    $return,
                    OrderReturn::STATUS_REJECTED,
                    ['admin_note' => 'Demo return rejected.']
                );
            }
        }
    }
}
