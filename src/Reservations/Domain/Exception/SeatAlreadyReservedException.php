<?php

declare(strict_types=1);

namespace App\Reservations\Domain\Exception;

class SeatAlreadyReservedException extends \DomainException
{
    public function __construct(string $seatId)
    {
        parent::__construct(sprintf('Seat %s is already reserved for this screening.', $seatId));
    }
}
