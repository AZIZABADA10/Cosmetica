<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderService extends BaseService
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function createOrder(int $userId, array $items): Order
    {
        return DB::transaction(function () use ($userId, $items) {
            $order = $this->orderRepository->create([
                'user_id' => $userId,
                'status' => 'pending',
                'total_price' => 0 // Calculated after
            ]);

            $totalPrice = 0;
            foreach ($items as $item) {
                $product = Product::where('slug', $item['slug'])->firstOrFail();
                $price = $product->price * $item['quantity'];
                $totalPrice += $price;

                $this->orderRepository->createOrderItem(
                    $order->id,
                    $product->id,
                    $item['quantity'],
                    $product->price
                );
            }

            $order->update(['total_price' => $totalPrice]);

            return $order->load('orderItems.product');
        });
    }

    public function getUserOrders(int $userId): Collection
    {
        return $this->orderRepository->getUserOrders($userId);
    }

    public function getAllOrders(): Collection
    {
        return $this->orderRepository->getAllOrdersWithItems();
    }

    public function getOrderById(int $id): Order
    {
        return $this->orderRepository->findWithItems($id) ?? throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
    }

    public function updateOrderStatus(int $id, string $status): Order
    {
        $order = $this->getOrderById($id);

        $allowedNextStatus = [
            'pending' => ['preparing', 'cancelled'],
            'preparing' => ['delivered'],
            'delivered' => [],
            'cancelled' => []
        ];

        if (!in_array($status, $allowedNextStatus[$order->status])) {
            throw new \Exception("Impossible de passer de {$order->status} à {$status}.");
        }

        $order->update(['status' => $status]);

        return $order;
    }

    public function cancelOrder(int $id, int $userId): Order
    {
        $order = $this->getOrderById($id);

        if ($order->user_id !== $userId) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException("Vous n'êtes pas autorisé à annuler cette commande.");
        }

        if ($order->status !== 'pending') {
            throw new \Exception("Une commande ne peut être annulée que si elle est en attente (pending).");
        }

        $order->update(['status' => 'cancelled']);

        return $order;
    }
}
