<?php

declare(strict_types=1);

namespace App\Reservations\Infrastructure\Persistence\Doctrine;

use App\Reservations\Domain\ReservationId;
use App\Reservations\Domain\Screening;
use App\Reservations\Domain\ScreeningId;
use App\Reservations\Domain\ScreeningRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineScreeningRepository implements ScreeningRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
    }

    public function getById(ScreeningId $id): ?Screening
    {
        return $this->em->find(Screening::class, $id->toString());
    }

    public function getByReservationId(ReservationId $id): ?Screening
    {
        return $this->em->createQueryBuilder()
            ->select('s')
            ->from(Screening::class, 's')
            ->join('s.reservations', 'r')
            ->where('r.id = :reservationId')
            ->setParameter('reservationId', $id->toString())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save(Screening $screening): void
    {
        $this->em->persist($screening);
        $this->em->flush();
    }
}
