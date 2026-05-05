<?php

declare(strict_types=1);

namespace App\Reservations\Application\Command\RedeemReservation;

use App\Reservations\Domain\ReservationId;
use App\Reservations\Domain\ReservationRepositoryInterface;
use App\Reservations\Domain\ScreeningRepositoryInterface;

class RedeemReservationCommandHandler
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservationRepository,
        private readonly ScreeningRepositoryInterface $screeningRepository
    ) {
    }

    public function handle(RedeemReservationCommand $command): void
    {
        $reservationId = new ReservationId($command->reservationId);
        
        $reservation = $this->reservationRepository->getById($reservationId);
        if ($reservation === null) {
            throw new \InvalidArgumentException('Reservation not found.');
        }

        $screening = $this->screeningRepository->getById($reservation->getScreeningId());
        if ($screening === null) {
            throw new \InvalidArgumentException('Screening not found.');
        }

        $screening->redeemReservation($reservationId);

        $this->screeningRepository->save($screening);
    }
}
