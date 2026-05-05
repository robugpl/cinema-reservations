<?php

declare(strict_types=1);

namespace App\Reservations\Application\Command\MakeReservation;

class MakeReservationCommand
{
    /**
     * @param string[] $seatIds
     */
    public function __construct(
        public readonly string $screeningId,
        public readonly array $seatIds,
        public readonly string $email
    ) {
    }
}
