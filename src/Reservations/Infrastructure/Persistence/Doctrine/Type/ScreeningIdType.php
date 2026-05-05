<?php
declare(strict_types=1);
namespace App\Reservations\Infrastructure\Persistence\Doctrine\Type;

use App\Shared\Infrastructure\Persistence\Doctrine\Type\AbstractIdType;
use App\Reservations\Domain\ScreeningId;

class ScreeningIdType extends AbstractIdType
{
    public const string NAME = 'screening_id';

    protected function getIdClass(): string
    {
        return ScreeningId::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
