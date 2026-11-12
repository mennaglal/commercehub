<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderReturn;
use App\Services\ReturnService;
use Illuminate\Database\Seeder;

class OrderReturnSeeder extends Seeder
{
    private const RETURNS_COUNT = 25;

    public function run(): void
    {
        $returnService = app(ReturnService::class);

        // Get eligible delivered orders without existing return requests.
        $orders = Order::query()
            ->where('delivery_status', Order::DELIVERY_DELIVERED)
            ->whereDoesntHave('returns')
            ->with('items')
            ->get()
            ->filter(fn (Order $order) => $order->canBeReturned())
            ->shuffle()
            ->take(self::RETURNS_COUNT)
            ->values();

        if ($orders->isEmpty()) {
            $this->command->warn(
                'No eligible orders found for return requests. Skipping.'
            );

            return;
        }

        $created = 0;

        $statuses = [
            OrderReturn::STATUS_REQUESTED,
            OrderReturn::STATUS_APPROVED,
            OrderReturn::STATUS_RECEIVED,
            OrderReturn::STATUS_REFUNDED,
            OrderReturn::STATUS_REJECTED,
        ];

        foreach ($orders as $index => $order) {

            // Select one or two products from the order.
            $availableItems = $order->items
                ->filter(fn ($item) => $item->quantity > 0)
                ->shuffle();

            if ($availableItems->isEmpty()) {
                continue;
            }

            $selectedItems = $availableItems->take(
                rand(1, min(2, $availableItems->count()))
            );

            // Build return quantities using order item IDs.
            $returnItems = [];

            foreach ($selectedItems as $item) {
                $returnItems[$item->id] = rand(1, $item->quantity);
            }

            // Create return request through the business service.
            $return = $returnService->create($order, [
                'reason' => fake()->randomElement(OrderReturn::REASONS),
                'customer_note' => fake()->sentence(),
                'items' => $returnItems,
            ]);

            // Distribute return statuses evenly.
            $status = $statuses[$index % count($statuses)];

            switch ($status) {

                case OrderReturn::STATUS_REQUESTED:
                    // Waiting for admin approval.
                    break;

                case OrderReturn::STATUS_APPROVED:
                    // Approved, waiting for returned products.
                    $returnService->changeStatus(
                        $return,
                        OrderReturn::STATUS_APPROVED,
                        [
                            'admin_note' => 'Return request approved.',
                        ]
                    );
                    break;

                case OrderReturn::STATUS_RECEIVED:
                    // Products received and inspected.
                    $returnService->changeStatus(
                        $return,
                        OrderReturn::STATUS_APPROVED
                    );

                    $restock = array_fill_keys(
                        $return->items()->pluck('id')->all(),
                        true
                    );

                    $returnService->changeStatus(
                        $return,
                        OrderReturn::STATUS_RECEIVED,
                        [
                            'admin_note' => 'Returned products received and inspected.',
                            'restock' => $restock,
                        ]
                    );
                    break;

                case OrderReturn::STATUS_REFUNDED:
                    // Complete return and refund process.
                    $returnService->changeStatus(
                        $return,
                        OrderReturn::STATUS_APPROVED
                    );

                    $restock = array_fill_keys(
                        $return->items()->pluck('id')->all(),
                        true
                    );

                    $returnService->changeStatus(
                        $return,
                        OrderReturn::STATUS_RECEIVED,
                        [
                            'restock' => $restock,
                        ]
                    );

                    $returnService->changeStatus(
                        $return,
                        OrderReturn::STATUS_REFUNDED,
                        [
                            'refund_reference' => 'REF-' .
                                fake()->unique()->numerify('##########'),
                        ]
                    );
                    break;

                case OrderReturn::STATUS_REJECTED:
                    // Request rejected by admin.
                    $returnService->changeStatus(
                        $return,
                        OrderReturn::STATUS_REJECTED,
                        [
                            'admin_note' => 'Return request rejected for testing.',
                        ]
                    );
                    break;
            }

            $created++;
        }

        $this->command->info(
            "{$created} sample return requests created successfully."
        );

        // Display actual status distribution.
        $this->command->newLine();
        $this->command->info('Return Status Distribution:');

        foreach (OrderReturn::STATUSES as $status) {
            $count = OrderReturn::query()
                ->whereIn('order_id', $orders->pluck('id'))
                ->where('status', $status)
                ->count();

            $this->command->line(
                ucfirst($status) . ': ' . $count
            );
        }
    }
}
