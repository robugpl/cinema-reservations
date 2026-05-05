<?php
declare(strict_types=1);
namespace App\Reservations\Infrastructure\Persistence\Doctrine\Type;

use App\Reservations\Domain\MovieDuration;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class MovieDurationType extends Type
{
    public const string NAME = 'movie_duration';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        return $value !== null ? new MovieDuration((int) $value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value ? $value->toSeconds() : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
