<?php

declare(strict_types=1);

namespace App\Reservations\Infrastructure\Persistence\Doctrine\Type;

use App\Reservations\Domain\ScreeningRoom\SeatId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class ReservationSeatIdsType extends Type
{
    public const string NAME = 'reservation_seat_ids';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return [];
        }

        $data = json_decode($value, true);
        $seatIds = [];
        foreach ($data as $idString) {
            $seatIds[] = new SeatId($idString);
        }
        return $seatIds;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return null;
        }

        $data = [];
        /** @var SeatId $seatId */
        foreach ($value as $seatId) {
            $data[] = $seatId->toString();
        }

        return json_encode($data);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
