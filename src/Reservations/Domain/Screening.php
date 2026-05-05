<?php

declare(strict_types=1);

namespace App\Reservations\Domain;

use App\Reservations\Domain\Exception\SeatAlreadyReservedException;
use App\Reservations\Domain\ScreeningRoom\ScreeningRoomId;
use App\Reservations\Domain\ScreeningRoom\SeatId;
use App\Shared\Domain\Email;

class Screening
{
    /** @var Reservation[]|iterable */
    private iterable $reservations = [];

    public function __construct(
        private readonly ScreeningId $id,
        private readonly \DateTimeImmutable $startDate,
        private readonly ScreeningRoomId $screeningRoomId,
        private readonly MovieId $movieId
    ) {
    }

    public function getScreeningId(): ScreeningId
    {
        return $this->id;
    }

    public function getScreeningRoomId(): ScreeningRoomId
    {
        return $this->screeningRoomId;
    }

    public function getMovieId(): MovieId
    {
        return $this->movieId;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    /**
     * @return Reservation[]
     */
    public function getReservations(): array
    {
        return is_array($this->reservations) ? $this->reservations : $this->reservations->toArray();
    }

    /**
     * @param SeatId[] $seatIds
     */
    public function reserve(
        array $seatIds,
        Email $email,
        \DateTimeImmutable $date,
        \DateTimeImmutable $expirationDate
    ): Reservation {
        // Enforce business rule: Prevent double booking
        foreach ($this->reservations as $reservation) {
            $status = $reservation->getReservationStatus();
            if ($status === ReservationStatus::PENDING || $status === ReservationStatus::REDEEMED) {
                foreach ($reservation->getSeatIds() as $reservedSeatId) {
                    foreach ($seatIds as $requestedSeatId) {
                        if ($reservedSeatId->toString() === $requestedSeatId->toString()) {
                            throw new SeatAlreadyReservedException($requestedSeatId->toString());
                        }
                    }
                }
            }
        }

        $reservationId = ReservationId::generate();
        $reservation = new Reservation(
            $reservationId,
            $this->id,
            $seatIds,
            $email,
            $date,
            $expirationDate
        );
        $this->reservations[] = $reservation;

        return $reservation;
    }

    public function cancelReservation(ReservationId $reservationId): void
    {
        foreach ($this->reservations as $reservation) {
            if ($reservation->getId()->toString() === $reservationId->toString()) {
                $reservation->cancel();
                return;
            }
        }
        
        throw new \InvalidArgumentException('Reservation not found in this screening.');
    }

    public function redeemReservation(ReservationId $reservationId): void
    {
        foreach ($this->reservations as $reservation) {
            if ($reservation->getId()->toString() === $reservationId->toString()) {
                $reservation->redeem();
                return;
            }
        }

        throw new \InvalidArgumentException('Reservation not found in this screening.');
    }
}
