<?php

declare(strict_types=1);

namespace App\Tests\Sales\Domain;

use App\Reservations\Domain\ScreeningId;
use App\Reservations\Domain\ScreeningRoom\SeatId;
use App\Sales\Domain\Exception\OrderCannotBeEmpty;
use App\Sales\Domain\Order;
use App\Sales\Domain\OrderId;
use App\Sales\Domain\Ticket;
use App\Sales\Domain\TicketId;
use App\Shared\Domain\Email;
use App\Shared\Domain\Money;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testItCalculatesTotalAmountCorrectlyFromTickets(): void
    {
        $ticket1 = new Ticket(
            TicketId::generate(),
            ScreeningId::generate(),
            SeatId::generate(),
            new Money(1550) // 15.50 PLN
        );

        $ticket2 = new Ticket(
            TicketId::generate(),
            ScreeningId::generate(),
            SeatId::generate(),
            new Money(2000) // 20.00 PLN
        );

        $order = new Order(
            OrderId::generate(),
            [$ticket1, $ticket2],
            new Email('test@cinema.com')
        );

        // 1550 + 2000 = 3550
        $this->assertEquals(3550, $order->getTotal()->getAmountInMinorUnit());
        $this->assertEquals('test@cinema.com', $order->getEmail()->toString());
    }

    public function testItFailsWithNoItems(): void
    {
        $this->expectException(OrderCannotBeEmpty::class);

        new Order(
            OrderId::generate(),
            [],
            new Email('empty@cinema.com')
        );

    }
}
