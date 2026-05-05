<?php

declare(strict_types=1);

namespace App\Reservations\Application\Command\RedeemReservation;

class RedeemReservationCommand
{
    public function __construct(
        public readonly string $reservationId
    ) {
    }
}
