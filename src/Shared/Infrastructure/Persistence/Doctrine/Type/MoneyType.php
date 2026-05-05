<?php
declare(strict_types=1);
namespace App\Shared\Infrastructure\Persistence\Doctrine\Type;

use App\Shared\Domain\Money;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class MoneyType extends Type
{
    public const string NAME = 'money';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Money
    {
        return $value !== null ? new Money((int) $value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value ? $value->getAmountInMinorUnit() : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
