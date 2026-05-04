<?php

declare(strict_types=1);

namespace App\Sales\Domain;

use App\Reservations\Domain\ScreeningId;
use App\Reservations\Domain\ScreeningRoom\SeatId;
use App\Shared\Domain\Money;

class Ticket
{
    public function __construct(
        private readonly TicketId $id,
        private readonly ScreeningId $screeningId,
        private readonly SeatId $seatId,
        private readonly Money $price
    ) {
    }

    public function getId(): TicketId
    {
        return $this->id;
    }

    public function getScreeningId(): ScreeningId
    {
        return $this->screeningId;
    }

    public function getSeatId(): SeatId
    {
        return $this->seatId;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }
}
