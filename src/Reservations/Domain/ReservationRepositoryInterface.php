<?php

declare(strict_types=1);

namespace App\Reservations\Domain;

interface ReservationRepositoryInterface
{
    public function getById(ReservationId $id): ?Reservation;
}
