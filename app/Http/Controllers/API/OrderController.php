<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderRequest;
use App\Http\Requests\Order\UpdateStatusRequest;
use App\Services\OrderService;
use App\Http\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    use JsonResponseTrait;

    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(): JsonResponse
    {
        $user = Auth::user();
        if ($user->role->name === 'admin' || $user->role->name === 'employe') {
            $orders = $this->orderService->getAllOrders();
        } else {
            $orders = $this->orderService->getUserOrders($user->id);
        }

        return $this->successResponse($orders);
    }

    public function store(OrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder(Auth::id(), $request->items);
        return $this->successResponse($order, 'Commande créée avec succès', 201);
    }

    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->getOrderById($id);
        $user = Auth::user();

        if ($user->role->name !== 'admin' && $user->role->name !== 'employe' && $order->user_id !== $user->id) {
            return $this->errorResponse("Vous n'êtes pas autorisé à voir cette commande.", 403);
        }

        return $this->successResponse($order);
    }

    public function updateStatus(UpdateStatusRequest $request, int $id): JsonResponse
    {
        try {
            $order = $this->orderService->updateOrderStatus($id, $request->status);
            return $this->successResponse($order, "Statut de la commande mis à jour : {$request->status}");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    public function cancel(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->cancelOrder($id, Auth::id());
            return $this->successResponse($order, 'Commande annulée avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}
