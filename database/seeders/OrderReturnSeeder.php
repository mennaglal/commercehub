<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderReturn;
use App\Services\ReturnService;
use Illuminate\Database\Seeder;

class OrderReturnSeeder extends Seeder
{
    public function run(): void
    {
        $returnService = app(ReturnService::class);

        // Get delivered orders that do not already have return requests.
        $orders = Order::query()
            ->where('delivery_status', Order::DELIVERY_DELIVERED)
            ->whereDoesntHave('returns')
            ->with('items')
            ->get()
            ->filter(fn (Order $order) => $order->canBeReturned())
            ->take(5)
            ->values();

        if ($orders->isEmpty()) {
            $this->command->info(
                'No eligible orders found for return requests. Skipping.'
            );

            return;
        }

        $created = 0;

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

            // Build the quantities using the order item IDs.
            $returnItems = [];

            foreach ($selectedItems as $item) {
                $returnItems[$item->id] = rand(1, $item->quantity);
            }

            // Create the return through the actual business service.
            $return = $returnService->create($order, [
                'reason' => fake()->randomElement(
                    OrderReturn::REASONS
                ),
                'customer_note' => fake()->sentence(),
                'items' => $returnItems,
            ]);

            // Distribute different return statuses.
            switch ($index % 5) {

                case 0:
                    // Requested: waiting for admin approval.
                    break;

                case 1:
                    // Approved: waiting for returned products.
                    $returnService->changeStatus(
                        $return,
                        OrderReturn::STATUS_APPROVED,
                        ['admin_note' => 'Return request approved.']
                    );
                    break;

                case 2:
                    // Received: products arrived and were inspected.
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

                case 3:
                    // Refunded: complete return and refund process.
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
                        ['restock' => $restock]
                    );

                    $returnService->changeStatus(
                        $return,
                        OrderReturn::STATUS_REFUNDED,
                        [
                            'refund_reference' => 'REF-' . fake()->unique()->numerify('##########'),
                        ]
                    );
                    break;

                case 4:
                    // Rejected: request was not approved.
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
    }
}
