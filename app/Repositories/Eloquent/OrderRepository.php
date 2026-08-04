<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    protected $model;

    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function findByUuid(string $uuid)
    {
        return $this->model->with(['items.product', 'payments', 'trackingUpdates', 'shipment'])->where('uuid', $uuid)->firstOrFail();
    }

    public function findByOrderNumber(string $orderNumber)
    {
        return $this->model->with(['items.product', 'payments', 'trackingUpdates', 'shipment'])->where('order_number', $orderNumber)->firstOrFail();
    }

    public function getUserOrders(int $userId, int $perPage = 10)
    {
        return $this->model->where('user_id', $userId)->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getLatest(int $limit = 10)
    {
        return $this->model->with('user')->orderBy('created_at', 'desc')->limit($limit)->get();
    }

    public function updateStatus(int $orderId, string $status)
    {
        $order = $this->model->findOrFail($orderId);
        $order->update(['status' => $status]);
        return $order;
    }
}
