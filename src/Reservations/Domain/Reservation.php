<?php

declare(strict_types=1);

namespace App\Reservations\Domain;

use App\Reservations\Domain\ScreeningRoom\SeatId;
use App\Shared\Domain\Email;

class Reservation
{
    private ReservationStatus $status;

    /**
     * @param SeatId[] $seatIds
     */
    public function __construct(
        private readonly ReservationId $id,
        private readonly ScreeningId $screeningId,
        private readonly array $seatIds,
        private readonly Email $email,
        private readonly \DateTimeImmutable $date,
        private readonly \DateTimeImmutable $expirationDate
    ) {
        if (empty($seatIds)) {
            throw new \InvalidArgumentException('Reservation must have at least one seat.');
        }
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

    /**
     * @return SeatId[]
     */
    public function getSeatIds(): array
    {
        return $this->seatIds;
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

    public function redeem(): void
    {
        $this->status = ReservationStatus::REDEEMED;
    }
}
