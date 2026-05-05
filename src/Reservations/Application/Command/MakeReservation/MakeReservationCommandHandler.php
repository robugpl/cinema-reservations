<?php

declare(strict_types=1);

namespace App\Reservations\Application\Command\MakeReservation;

use App\Reservations\Domain\ScreeningId;
use App\Reservations\Domain\ScreeningRoom\SeatId;
use App\Reservations\Domain\ScreeningRepositoryInterface;
use App\Shared\Domain\Email;

readonly class MakeReservationCommandHandler
{
    public function __construct(
        private ScreeningRepositoryInterface $screeningRepository
    ) {
    }

    public function handle(MakeReservationCommand $command): string
    {
        $screeningId = new ScreeningId($command->screeningId);
        $screening = $this->screeningRepository->getById($screeningId);

        if ($screening === null) {
            throw new \InvalidArgumentException('Screening not found.');
        }

        $seatIds = [];
        foreach ($command->seatIds as $idString) {
            $seatIds[] = new SeatId($idString);
        }
        $email = new Email($command->email);
        $date = new \DateTimeImmutable();

        $reservation = $screening->reserve($seatIds, $email, $date);

        $this->screeningRepository->save($screening);

        return $reservation->getId()->toString();
    }
}
