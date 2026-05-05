<?php

declare(strict_types=1);

namespace App\Reservations\Application\Command\RedeemReservation;

use App\Reservations\Domain\ReservationId;
use App\Reservations\Domain\ScreeningRepositoryInterface;

class RedeemReservationCommandHandler
{
    public function __construct(
        private readonly ScreeningRepositoryInterface $screeningRepository
    ) {
    }

    public function handle(RedeemReservationCommand $command): void
    {
        $reservationId = new ReservationId($command->reservationId);

        $screening = $this->screeningRepository->getByReservationId($reservationId);
        if ($screening === null) {
            throw new \InvalidArgumentException('Screening for this reservation not found.');
        }

        $screening->redeemReservation($reservationId);

        $this->screeningRepository->save($screening);
    }
}
