<?php

declare(strict_types=1);

namespace App\Reservations\Domain;

use App\Reservations\Domain\ScreeningRoom\SeatId;
use App\Shared\Domain\Email;

class Reservation
{
    private ReservationStatus $status;

    public function __construct(
        private readonly ReservationId $id,
        private readonly ScreeningId $screeningId,
        private readonly SeatId $seatId,
        private readonly Email $email,
        private readonly \DateTimeImmutable $date,
        private readonly \DateTimeImmutable $expirationDate
    ) {
        $this->status = ReservationStatus::PENDING;
    }

    public function getId(): ReservationId
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

    public function getReservationStatus(): ReservationStatus
    {
        return $this->status;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getExpirationDate(): \DateTimeImmutable
    {
        return $this->expirationDate;
    }

    public function cancel(): void
    {
        $this->status = ReservationStatus::CANCELED;
    }

    public function expire(): void
    {
        $this->status = ReservationStatus::EXPIRED;
    }
}
