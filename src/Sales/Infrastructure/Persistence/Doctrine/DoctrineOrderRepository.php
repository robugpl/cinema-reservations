<?php

declare(strict_types=1);

namespace App\Sales\Infrastructure\Persistence\Doctrine;

use App\Sales\Domain\Order;
use App\Sales\Domain\OrderId;
use App\Sales\Domain\OrderRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
    }

    public function getById(OrderId $id): ?Order
    {
        return $this->em->find(Order::class, $id->toString());
    }

    public function save(Order $order): void
    {
        $this->em->persist($order);
    }
}
