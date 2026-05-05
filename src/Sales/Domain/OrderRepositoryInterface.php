<?php

declare(strict_types=1);

namespace App\Sales\Domain;

interface OrderRepositoryInterface
{
    public function getById(OrderId $id): ?Order;

    public function save(Order $order): void;
}
