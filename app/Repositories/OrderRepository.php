<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository extends BaseRepository
{
    public function __construct(Order $order)
    {
        parent::__construct($order);
    }

    public function getUserOrders(int $userId): Collection
    {
        return Order::with('orderItems.product')->where('user_id', $userId)->latest()->get();
    }

    public function getAllOrdersWithItems(): Collection
    {
        return Order::with('orderItems.product', 'user')->latest()->get();
    }

    public function findWithItems(int $id): ?Order
    {
        return Order::with('orderItems.product', 'user')->find($id);
    }

    public function createOrderItem(int $orderId, int $productId, int $quantity, float $price): OrderItem
    {
        return OrderItem::create([
            'order_id' => $orderId,
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $price
        ]);
    }
}
