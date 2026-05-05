<?php
declare(strict_types=1);
namespace App\Sales\Infrastructure\Persistence\Doctrine\Type;

use App\Shared\Infrastructure\Persistence\Doctrine\Type\AbstractIdType;
use App\Sales\Domain\OrderId;

class OrderIdType extends AbstractIdType
{
    public const string NAME = 'order_id';

    protected function getIdClass(): string
    {
        return OrderId::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
