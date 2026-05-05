<?php
declare(strict_types=1);
namespace App\Reservations\Infrastructure\Persistence\Doctrine\Type;

use App\Reservations\Domain\ReservationStatus;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class ReservationStatusType extends Type
{
    public const string NAME = 'reservation_status';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?ReservationStatus
    {
        return $value ? ReservationStatus::from($value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value ? $value->value : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
