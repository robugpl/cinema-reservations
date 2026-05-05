<?php

$types = [
    'App\Reservations\Domain\ScreeningId' => 'screening_id',
    'App\Reservations\Domain\MovieId' => 'movie_id',
    'App\Reservations\Domain\ReservationId' => 'reservation_id',
    'App\Reservations\Domain\ScreeningRoom\ScreeningRoomId' => 'screening_room_id',
    'App\Reservations\Domain\ScreeningRoom\SeatId' => 'seat_id',
    'App\Sales\Domain\OrderId' => 'order_id',
    'App\Sales\Domain\TicketId' => 'ticket_id',
];

$baseContent = <<<PHP
<?php
declare(strict_types=1);
namespace App\Shared\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

abstract class AbstractIdType extends Type
{
    abstract protected function getIdClass(): string;

    public function getSQLDeclaration(array \$column, AbstractPlatform \$platform): string
    {
        return \$platform->getStringTypeDeclarationSQL(\$column);
    }

    public function convertToPHPValue(\$value, AbstractPlatform \$platform): mixed
    {
        if (empty(\$value)) {
            return null;
        }
        \$class = \$this->getIdClass();
        return new \$class(\$value);
    }

    public function convertToDatabaseValue(\$value, AbstractPlatform \$platform): mixed
    {
        if (empty(\$value)) {
            return null;
        }
        return (string) \$value;
    }
}
PHP;
file_put_contents('src/Shared/Infrastructure/Persistence/Doctrine/Type/AbstractIdType.php', $baseContent);

foreach ($types as $class => $name) {
    $parts = explode('\\', $class);
    $className = end($parts);
    $module = $parts[1]; // Reservations or Sales
    $typeClassName = $className . 'Type';
    
    $content = <<<PHP
<?php
declare(strict_types=1);
namespace App\\$module\Infrastructure\Persistence\Doctrine\Type;

use App\Shared\Infrastructure\Persistence\Doctrine\Type\AbstractIdType;
use $class;

class $typeClassName extends AbstractIdType
{
    public const NAME = '$name';

    protected function getIdClass(): string
    {
        return $className::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
PHP;

    file_put_contents("src/$module/Infrastructure/Persistence/Doctrine/Type/$typeClassName.php", $content);
}

// EmailType
$emailContent = <<<PHP
<?php
declare(strict_types=1);
namespace App\Shared\Infrastructure\Persistence\Doctrine\Type;

use App\Shared\Domain\Email;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class EmailType extends Type
{
    public const NAME = 'email';

    public function getSQLDeclaration(array \$column, AbstractPlatform \$platform): string
    {
        return \$platform->getStringTypeDeclarationSQL(\$column);
    }

    public function convertToPHPValue(\$value, AbstractPlatform \$platform): mixed
    {
        return \$value ? new Email(\$value) : null;
    }

    public function convertToDatabaseValue(\$value, AbstractPlatform \$platform): mixed
    {
        return \$value ? \$value->toString() : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
PHP;
file_put_contents('src/Shared/Infrastructure/Persistence/Doctrine/Type/EmailType.php', $emailContent);

// MoneyType
$moneyContent = <<<PHP
<?php
declare(strict_types=1);
namespace App\Shared\Infrastructure\Persistence\Doctrine\Type;

use App\Shared\Domain\Money;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class MoneyType extends Type
{
    public const NAME = 'money';

    public function getSQLDeclaration(array \$column, AbstractPlatform \$platform): string
    {
        return \$platform->getIntegerTypeDeclarationSQL(\$column);
    }

    public function convertToPHPValue(\$value, AbstractPlatform \$platform): mixed
    {
        return \$value !== null ? new Money((int) \$value) : null;
    }

    public function convertToDatabaseValue(\$value, AbstractPlatform \$platform): mixed
    {
        return \$value ? \$value->getAmountInMinorUnit() : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
PHP;
file_put_contents('src/Shared/Infrastructure/Persistence/Doctrine/Type/MoneyType.php', $moneyContent);

// ReservationStatusType
$statusContent = <<<PHP
<?php
declare(strict_types=1);
namespace App\Reservations\Infrastructure\Persistence\Doctrine\Type;

use App\Reservations\Domain\ReservationStatus;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class ReservationStatusType extends Type
{
    public const NAME = 'reservation_status';

    public function getSQLDeclaration(array \$column, AbstractPlatform \$platform): string
    {
        return \$platform->getStringTypeDeclarationSQL(\$column);
    }

    public function convertToPHPValue(\$value, AbstractPlatform \$platform): mixed
    {
        return \$value ? ReservationStatus::from(\$value) : null;
    }

    public function convertToDatabaseValue(\$value, AbstractPlatform \$platform): mixed
    {
        return \$value ? \$value->value : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
PHP;
file_put_contents('src/Reservations/Infrastructure/Persistence/Doctrine/Type/ReservationStatusType.php', $statusContent);

echo "Done\n";
