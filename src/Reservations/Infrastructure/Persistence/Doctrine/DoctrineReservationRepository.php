<?php

declare(strict_types=1);

namespace App\Reservations\Infrastructure\Persistence\Doctrine;

use App\Reservations\Domain\Reservation;
use App\Reservations\Domain\ReservationId;
use App\Reservations\Domain\ReservationRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineReservationRepository implements ReservationRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
    }

    public function getById(ReservationId $id): ?Reservation
    {
        return $this->em->find(Reservation::class, $id->toString());
    }
}
