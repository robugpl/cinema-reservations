<?php
declare(strict_types=1);
namespace App\Reservations\Infrastructure\Persistence\Doctrine\Type;

use App\Shared\Infrastructure\Persistence\Doctrine\Type\AbstractIdType;
use App\Reservations\Domain\ReservationId;

class ReservationIdType extends AbstractIdType
{
    public const string NAME = 'reservation_id';

    protected function getIdClass(): string
    {
        return ReservationId::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
