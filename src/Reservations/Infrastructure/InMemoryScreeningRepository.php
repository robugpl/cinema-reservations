<?php

declare(strict_types=1);

namespace App\Reservations\Infrastructure;

use App\Reservations\Domain\MovieId;
use App\Reservations\Domain\ReservationId;
use App\Reservations\Domain\Screening;
use App\Reservations\Domain\ScreeningId;
use App\Reservations\Domain\ScreeningRepositoryInterface;
use App\Reservations\Domain\ScreeningRoom\ScreeningRoomId;

class InMemoryScreeningRepository implements ScreeningRepositoryInterface
{
    /** @var array<string, Screening> */
    private array $screenings = [];

    public function __construct()
    {
        // Seed dummy data for demo
        $screeningId = new ScreeningId('scr-123');
        $this->screenings[$screeningId->toString()] = new Screening(
            $screeningId,
            new \DateTimeImmutable('2026-05-10 20:00:00'),
            new ScreeningRoomId('room-1'),
            new MovieId('mov-1')
        );
    }

    public function getById(ScreeningId $id): ?Screening
    {
        return $this->screenings[$id->toString()] ?? null;
    }

    public function getByReservationId(ReservationId $id): ?Screening
    {
        foreach ($this->screenings as $screening) {
            foreach ($screening->getReservations() as $reservation) {
                if ($reservation->getId()->toString() === $id->toString()) {
                    return $screening;
                }
            }
        }

        return null;
    }

    public function save(Screening $screening): void
    {
        $this->screenings[$screening->getScreeningId()->toString()] = $screening;
    }
}
