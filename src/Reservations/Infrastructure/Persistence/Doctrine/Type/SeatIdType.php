<?php
declare(strict_types=1);
namespace App\Reservations\Infrastructure\Persistence\Doctrine\Type;

use App\Shared\Infrastructure\Persistence\Doctrine\Type\AbstractIdType;
use App\Reservations\Domain\ScreeningRoom\SeatId;

class SeatIdType extends AbstractIdType
{
    public const string NAME = 'seat_id';

    protected function getIdClass(): string
    {
        return SeatId::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
