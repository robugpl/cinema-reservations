<?php

declare(strict_types=1);

namespace App\Sales\Application\Command\PickUpReservation;

use App\Reservations\Application\Command\RedeemReservation\RedeemReservationCommand;
use App\Reservations\Application\Command\RedeemReservation\RedeemReservationCommandHandler;
use App\Reservations\Domain\ReservationId;
use App\Reservations\Domain\ScreeningRepositoryInterface;
use App\Sales\Application\Command\CreateOrder\CreateOrderCommand;
use App\Sales\Application\Command\CreateOrder\CreateOrderCommandHandler;
use Doctrine\ORM\EntityManagerInterface;

class PickUpReservationCommandHandler
{
    /** Default ticket price in minor units (2000 = 20.00 PLN) */
    private const DEFAULT_TICKET_PRICE_MINOR_UNITS = 2000;

    public function __construct(
        private readonly ScreeningRepositoryInterface $screeningRepository,
        private readonly RedeemReservationCommandHandler $redeemHandler,
        private readonly CreateOrderCommandHandler $createOrderHandler,
        private readonly EntityManagerInterface $em
    ) {
    }

    /**
     * Orchestrates the pick-up of a reservation across two Bounded Contexts:
     * - Reservations BC: marks the reservation as redeemed
     * - Sales BC: creates an Order with Tickets
     *
     * @return string[] List of generated ticket IDs
     */
    public function handle(PickUpReservationCommand $command): array
    {
        $domainReservationId = new ReservationId($command->reservationId);

        $screening = $this->screeningRepository->getByReservationId($domainReservationId);
        if ($screening === null) {
            throw new \InvalidArgumentException(
                sprintf('Nie znaleziono seansu dla rezerwacji o ID: %s', $command->reservationId)
            );
        }

        $reservation = null;
        foreach ($screening->getReservations() as $r) {
            if ($r->getId()->toString() === $command->reservationId) {
                $reservation = $r;
                break;
            }
        }

        if ($reservation === null) {
            throw new \InvalidArgumentException(
                sprintf('Nie znaleziono rezerwacji o ID: %s', $command->reservationId)
            );
        }

        // Step 1: Mark reservation as redeemed (Reservations BC)
        $this->redeemHandler->handle(new RedeemReservationCommand($command->reservationId));

        // Step 2: Build tickets data and create order (Sales BC)
        $ticketsData = [];
        foreach ($reservation->getSeatIds() as $seatId) {
            $ticketsData[] = [
                'screeningId' => $reservation->getScreeningId()->toString(),
                'seatId' => $seatId->toString(),
                'priceInMinorUnits' => self::DEFAULT_TICKET_PRICE_MINOR_UNITS,
            ];
        }

        $ticketIds = $this->createOrderHandler->handle(
            new CreateOrderCommand($reservation->getEmail()->toString(), $ticketsData)
        );

        // Commit Order/Tickets persist (Screening was already flushed by RedeemHandler)
        $this->em->flush();

        return $ticketIds;
    }
}
