<?php

declare(strict_types=1);

namespace App\Reservations\Domain;

enum ReservationStatus: string
{
    case PENDING = 'pending';
    case REDEEMED = 'redeemed';
    case EXPIRED = 'expired';
    case CANCELED = 'canceled';
}
