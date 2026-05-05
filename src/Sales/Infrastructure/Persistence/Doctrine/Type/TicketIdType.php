<?php
declare(strict_types=1);
namespace App\Sales\Infrastructure\Persistence\Doctrine\Type;

use App\Shared\Infrastructure\Persistence\Doctrine\Type\AbstractIdType;
use App\Sales\Domain\TicketId;

class TicketIdType extends AbstractIdType
{
    public const string NAME = 'ticket_id';

    protected function getIdClass(): string
    {
        return TicketId::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
