<?php

declare(strict_types=1);

namespace App\Tests\Reservations\Domain;

use App\Reservations\Domain\Exception\SeatAlreadyReservedException;
use App\Reservations\Domain\MovieId;
use App\Reservations\Domain\ReservationStatus;
use App\Reservations\Domain\Screening;
use App\Reservations\Domain\ScreeningId;
use App\Reservations\Domain\ScreeningRoom\ScreeningRoomId;
use App\Reservations\Domain\ScreeningRoom\SeatId;
use App\Shared\Domain\Email;
use PHPUnit\Framework\TestCase;

class ScreeningTest extends TestCase
{
    private Screening $screening;

    protected function setUp(): void
    {
        $this->screening = new Screening(
            ScreeningId::generate(),
            new \DateTimeImmutable('2026-05-10 20:00:00'),
            ScreeningRoomId::generate(),
            MovieId::generate()
        );
    }

    public function testItCreatesScreeningSuccessfully(): void
    {
        $id = ScreeningId::generate();
        $roomId = ScreeningRoomId::generate();
        $movieId = MovieId::generate();

        $screening = new Screening($id, new \DateTimeImmutable(), $roomId, $movieId);

        $this->assertEquals($id, $screening->getScreeningId());
        $this->assertEquals($roomId, $screening->getScreeningRoomId());
        $this->assertEquals($movieId, $screening->getMovieId());
        $this->assertEmpty($screening->getReservations());
    }

    public function testItSuccessfullyReservesAnAvailableSeat(): void
    {
        $seatId = SeatId::generate();
        $email = new Email('test@cinema.com');
        $date = new \DateTimeImmutable();
        $expirationDate = $date->modify('+15 minutes');

        $reservation = $this->screening->reserve($seatId, $email, $date, $expirationDate);

        $this->assertEquals(ReservationStatus::PENDING, $reservation->getReservationStatus());
        $this->assertEquals($seatId, $reservation->getSeatId());
        $this->assertCount(1, $this->screening->getReservations());
    }

    public function testItPreventsDoubleBookingOfAnActiveReservation(): void
    {
        $seatId = SeatId::generate();
        $email = new Email('test@cinema.com');
        $date = new \DateTimeImmutable();
        $expirationDate = $date->modify('+15 minutes');

        // First reservation (Happy Path)
        $this->screening->reserve($seatId, $email, $date, $expirationDate);

        // Second reservation attempts to book the same exact seat
        $this->expectException(SeatAlreadyReservedException::class);
        $this->screening->reserve($seatId, new Email('other@cinema.com'), $date, $expirationDate);
    }

    public function testItAllowsReservationAfterPreviousWasCanceled(): void
    {
        $seatId = SeatId::generate();
        $email = new Email('test@cinema.com');
        $date = new \DateTimeImmutable();
        $expirationDate = $date->modify('+15 minutes');

        $reservation = $this->screening->reserve($seatId, $email, $date, $expirationDate);
        
        // Customer cancels the reservation
        $this->screening->cancelReservation($reservation->getId());

        // A new customer tries to book the now-canceled seat
        $newReservation = $this->screening->reserve($seatId, new Email('other@cinema.com'), $date, $expirationDate);

        $this->assertEquals(ReservationStatus::PENDING, $newReservation->getReservationStatus());
        // There will be 2 reservations in the history, one CANCELED and one PENDING
        $this->assertCount(2, $this->screening->getReservations());
    }

    public function testItAllowsReservationIfPreviousIsExpired(): void
    {
        $seatId = SeatId::generate();
        $email = new Email('test@cinema.com');
        $date = new \DateTimeImmutable();
        $expirationDate = $date->modify('+15 minutes');

        $reservation = $this->screening->reserve($seatId, $email, $date, $expirationDate);
        
        // System manually expires the reservation due to lack of payment
        $reservation->expire();

        // A new customer claims the expired seat
        $newReservation = $this->screening->reserve($seatId, new Email('other@cinema.com'), $date, $expirationDate);

        $this->assertEquals(ReservationStatus::PENDING, $newReservation->getReservationStatus());
    }
}
