<?php

declare(strict_types=1);

namespace App\Tests\Reservations\Domain;

use App\Reservations\Domain\Reservation;
use App\Reservations\Domain\ReservationId;
use App\Reservations\Domain\ReservationStatus;
use App\Reservations\Domain\ScreeningId;
use App\Reservations\Domain\ScreeningRoom\SeatId;
use App\Shared\Domain\Email;
use PHPUnit\Framework\TestCase;

class ReservationTest extends TestCase
{
    public function testItChangesStatusToCanceled(): void
    {
        $reservation = new Reservation(
            ReservationId::generate(),
            ScreeningId::generate(),
            [SeatId::generate()],
            new Email('test@cinema.com'),
            new \DateTimeImmutable(),
            (new \DateTimeImmutable())->modify('+15 minutes')
        );

        $this->assertEquals(ReservationStatus::PENDING, $reservation->getReservationStatus());

        $reservation->cancel();

        $this->assertEquals(ReservationStatus::CANCELED, $reservation->getReservationStatus());
    }

    public function testItChangesStatusToExpired(): void
    {
        $reservation = new Reservation(
            ReservationId::generate(),
            ScreeningId::generate(),
            [SeatId::generate()],
            new Email('test@cinema.com'),
            new \DateTimeImmutable(),
            (new \DateTimeImmutable())->modify('+15 minutes')
        );

        $reservation->expire();

        $this->assertEquals(ReservationStatus::EXPIRED, $reservation->getReservationStatus());
    }
}
