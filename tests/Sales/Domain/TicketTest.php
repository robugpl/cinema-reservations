<?php

declare(strict_types=1);

namespace App\Tests\Sales\Domain;

use App\Reservations\Domain\ScreeningId;
use App\Reservations\Domain\ScreeningRoom\SeatId;
use App\Sales\Domain\Ticket;
use App\Sales\Domain\TicketId;
use App\Shared\Domain\Money;
use PHPUnit\Framework\TestCase;

class TicketTest extends TestCase
{
    public function testItCreatesTicketSuccessfully(): void
    {
        $ticketId = TicketId::generate();
        $screeningId = ScreeningId::generate();
        $seatId = SeatId::generate();
        $price = new Money(2500); // 25 PLN

        $ticket = new Ticket($ticketId, $screeningId, $seatId, $price);

        $this->assertEquals($ticketId, $ticket->getId());
        $this->assertEquals($screeningId, $ticket->getScreeningId());
        $this->assertEquals($seatId, $ticket->getSeatId());
        $this->assertEquals($price, $ticket->getPrice());
        $this->assertEquals(2500, $ticket->getPrice()->getAmountInMinorUnit());
    }
}
