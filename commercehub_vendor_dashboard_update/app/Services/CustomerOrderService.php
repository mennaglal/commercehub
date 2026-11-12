<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * The orders and returns of the logged-in customer.
 *
 * SECURITY: every query starts from the user (where user_id = ...).
 * An order number of another customer simply does not exist for him (404), so nobody can read or
 * change the order of somebody else.
 * The real work (prices, stock, coupon, refund) stays in OrderService and ReturnService.
 */
class CustomerOrderService
{
    public function __construct(private OrderService $orders, private ReturnService $returns)
    {
    }

    /* ---------------- Cart summary and checkout ---------------- */

    public function preview(array $data): array
    {
        return $this->orders->preview($data);
    }

    public function place(User $user, array $data): Order
    {
        $order = $this->orders->create($data + ['user_id' => $user->id]);

        return $this->loadDetails($order);
    }

    /* ---------------- Orders ---------------- */

    public function orders(User $user, int $perPage): LengthAwarePaginator
    {
        return Order::where('user_id', $user->id)->withCount('items')->latest()->paginate($perPage);
    }

    public function order(User $user, string $number): Order
    {
        return $this->loadDetails($this->findOrder($user, $number));
    }

    // OrderService decides if the order can still be cancelled (before shipping) and gives everything back.
    public function cancel(User $user, string $number): Order
    {
        $order = $this->findOrder($user, $number);

        $this->orders->changeStatus($order, Order::STATUS_CANCELLED);

        return $this->loadDetails($order->fresh());
    }

    /* ---------------- Returns ---------------- */

    // The items of the order that can still be returned (and how much money each piece gives back).
    public function returnableItems(User $user, string $number): Collection
    {
        return $this->returns->returnableItems($this->findOrder($user, $number));
    }

    public function requestReturn(User $user, string $number, array $data): OrderReturn
    {
        $order = $this->findOrder($user, $number);

        // [ ['order_item_id' => 5, 'quantity' => 1] ]  =>  [ 5 => 1 ]  (the shape ReturnService expects)
        $items = collect($data['items'])->mapWithKeys(fn ($row) => [$row['order_item_id'] => $row['quantity']])->all();

        $return = $this->returns->create($order, [
            'reason' => $data['reason'],
            'customer_note' => $data['customer_note'] ?? null,
            'items' => $items,
        ]);

        return $return->load(['order:id,order_number', 'items.orderItem']);
    }

    public function returns(User $user, int $perPage): LengthAwarePaginator
    {
        return OrderReturn::whereHas('order', fn ($order) => $order->where('user_id', $user->id))
            ->with('order:id,order_number')
            ->withCount('items')
            ->latest()
            ->paginate($perPage);
    }

    public function orderReturn(User $user, string $number): OrderReturn
    {
        return OrderReturn::where('return_number', $number)
            ->whereHas('order', fn ($order) => $order->where('user_id', $user->id))
            ->with(['order:id,order_number', 'items.orderItem'])
            ->firstOrFail();
    }

    /* ---------------- Internals ---------------- */

    private function findOrder(User $user, string $number): Order
    {
        return Order::where('user_id', $user->id)->where('order_number', $number)->firstOrFail();
    }

    private function loadDetails(Order $order): Order
    {
        return $order->load(['items.product:id,slug,image', 'country', 'city', 'area']);
    }
}
