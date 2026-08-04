<?php

namespace App\Repositories\Contracts;

interface OrderRepositoryInterface
{
    public function create(array $data);
    
    public function findByUuid(string $uuid);
    
    public function findByOrderNumber(string $orderNumber);
    
    public function getUserOrders(int $userId, int $perPage = 10);
    
    public function getLatest(int $limit = 10);
    
    public function updateStatus(int $orderId, string $status);
}
