<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::where('status', 1)->get();

        if ($products->isEmpty()) {
            throw new RuntimeException(
                'No active products found. Please seed products first.'
            );
        }

        $scenarios = [
            [
                'status' => Order::STATUS_PENDING,
                'delivery_status' => Order::DELIVERY_PENDING,
                'payment_status' => Order::PAYMENT_PENDING,
                'payment_method' => Order::PAYMENT_COD,
                'payment_gateway' => null,
            ],
            [
                'status' => Order::STATUS_CONFIRMED,
                'delivery_status' => Order::DELIVERY_PREPARING,
                'payment_status' => Order::PAYMENT_PENDING,
                'payment_method' => Order::PAYMENT_COD,
                'payment_gateway' => null,
            ],
            [
                'status' => Order::STATUS_CONFIRMED,
                'delivery_status' => Order::DELIVERY_SHIPPED,
                'payment_status' => Order::PAYMENT_PAID,
                'payment_method' => Order::PAYMENT_ONLINE,
                'payment_gateway' => Order::GATEWAY_MYFATOORAH,
            ],
            [
                'status' => Order::STATUS_CONFIRMED,
                'delivery_status' => Order::DELIVERY_DELIVERED,
                'payment_status' => Order::PAYMENT_PAID,
                'payment_method' => Order::PAYMENT_ONLINE,
                'payment_gateway' => Order::GATEWAY_PAYMOB,
            ],
            [
                'status' => Order::STATUS_CANCELLED,
                'delivery_status' => Order::DELIVERY_PENDING,
                'payment_status' => Order::PAYMENT_FAILED,
                'payment_method' => Order::PAYMENT_ONLINE,
                'payment_gateway' => Order::GATEWAY_PAYPAL,
            ],
            [
                'status' => Order::STATUS_CANCELLED,
                'delivery_status' => Order::DELIVERY_RETURNED,
                'payment_status' => Order::PAYMENT_REFUNDED,
                'payment_method' => Order::PAYMENT_ONLINE,
                'payment_gateway' => Order::GATEWAY_MYFATOORAH,
            ],
            [
                'status' => Order::STATUS_CONFIRMED,
                'delivery_status' => Order::DELIVERY_PENDING,
                'payment_status' => Order::PAYMENT_PENDING,
                'payment_method' => Order::PAYMENT_BANK,
                'payment_gateway' => null,
            ],
        ];

        for ($i = 1; $i <= 30; $i++) {
            $scenario = fake()->randomElement($scenarios);

            // Select a valid shipping location.
            $area = Area::inRandomOrder()->first();

            $city = $area
                ? City::find($area->city_id)
                : City::inRandomOrder()->first();

            $country = $city
                ? Country::find($city->country_id)
                : Country::inRandomOrder()->first();

            // Select between 1 and 4 products.
            $selectedProducts = $products
                ->shuffle()
                ->take(rand(1, min(4, $products->count())));

            $items = [];
            $subtotal = 0;

            foreach ($selectedProducts as $product) {
                $variant = ProductVariant::where('product_id', $product->id)
                    ->where('status', 1)
                    ->inRandomOrder()
                    ->first();

                // Use variant price when a variant is selected.
                if ($variant) {
                    $unitPrice = $variant->discount_price
                        ?? $variant->price;

                    $sku = $variant->sku ?? $product->sku;

                    $variantLabel = 'Variant #' . $variant->id;
                } else {
                    $unitPrice = (
                        $product->is_sale && $product->sale_price
                    )
                        ? $product->sale_price
                        : $product->price;

                    $sku = $product->sku;
                    $variantLabel = null;
                }

                $quantity = rand(1, 3);
                $lineTotal = $unitPrice * $quantity;

                $subtotal += $lineTotal;

                $items[] = [
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'store_id' => $product->store_id,
                    'store_name' => null,
                    'product_name' => $product->name_ar
                        ?: ($product->name_en ?: $product->name),
                    'variant_label' => $variantLabel,
                    'sku' => $sku,
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'line_total' => $lineTotal,
                ];
            }

            $discountTotal = 0;
            $shippingTotal = $area
                ? $area->shipping_price
                : 0;

            $total = $subtotal - $discountTotal + $shippingTotal;

            $isPaid = in_array($scenario['payment_status'], [
                Order::PAYMENT_PAID,
                Order::PAYMENT_REFUNDED,
            ]);

            $isShipped = in_array($scenario['delivery_status'], [
                Order::DELIVERY_SHIPPED,
                Order::DELIVERY_DELIVERED,
                Order::DELIVERY_RETURNED,
            ]);

            $order = Order::create([
                'order_number' => 'ORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(8)),

                'status' => $scenario['status'],

                'customer_name' => fake()->name(),
                'customer_phone' => '01' . fake()->numerify('#########'),

                'country_id' => $country?->id,
                'city_id' => $city?->id,
                'area_id' => $area?->id,

                'shipping_address' => fake()->address(),
                'shipping_notes' => fake()->optional()->sentence(),
                'notes' => fake()->optional()->sentence(),

                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'shipping_total' => $shippingTotal,
                'total' => $total,

                'coupon_id' => null,
                'coupon_code' => null,

                'blogger_id' => null,
                'commission_amount' => 0,
                'commission_status' => null,

                'payment_method' => $scenario['payment_method'],
                'payment_gateway' => $scenario['payment_gateway'],
                'payment_status' => $scenario['payment_status'],

                'payment_id' => $scenario['payment_gateway']
                    ? 'PAY-' . Str::upper(Str::random(10))
                    : null,

                'invoice_id' => $scenario['payment_gateway']
                    ? 'INV-' . Str::upper(Str::random(8))
                    : null,

                'invoice_url' => null,

                'payment_data' => $scenario['payment_gateway']
                    ? [
                        'gateway' => $scenario['payment_gateway'],
                        'reference' => Str::upper(Str::random(12)),
                        'test' => true,
                    ]
                    : null,

                'paid_at' => $isPaid
                    ? now()->subDays(rand(1, 15))
                    : null,

                'delivery_status' => $scenario['delivery_status'],

                'tracking_number' => $isShipped
                    ? 'TRK-' . Str::upper(Str::random(10))
                    : null,

                'shipped_at' => $isShipped
                    ? now()->subDays(rand(1, 10))
                    : null,

                'delivered_at' => $scenario['delivery_status'] === Order::DELIVERY_DELIVERED
                    ? now()->subDays(rand(1, 5))
                    : null,
            ]);

            foreach ($items as $item) {
                $order->items()->create($item);
            }

            // Spread order dates across the last 30 days.
            $orderDate = now()->subDays(rand(0, 30));

            $order->forceFill([
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ])->save();
        }

        $this->command->info('30 sample orders created successfully.');
    }
}
