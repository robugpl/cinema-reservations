<?php

declare(strict_types=1);

namespace App\Sales\Application\Command\PickUpReservation;

use App\Sales\Domain\Order;
use App\Sales\Domain\OrderId;
use App\Sales\Domain\Ticket;
use App\Sales\Domain\TicketId;
use App\Shared\Domain\Email;
use App\Shared\Domain\Money;
use App\Reservations\Domain\ScreeningId;
use App\Reservations\Domain\ScreeningRoom\SeatId;
use App\Sales\Domain\OrderRepositoryInterface;

class PickUpReservationCommandHandler
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository
    ) {
    }

    /**
     * @return string[]
     */
    public function handle(PickUpReservationCommand $command): array
    {
        $tickets = [];
        $ticketIds = [];
        foreach ($command->tickets as $ticketData) {
            $ticketId = TicketId::generate();
            $tickets[] = new Ticket(
                $ticketId,
                new ScreeningId($ticketData['screeningId']),
                new SeatId($ticketData['seatId']),
                new Money($ticketData['priceInMinorUnits'])
            );
            $ticketIds[] = $ticketId->toString();
        }

        $order = new Order(
            OrderId::generate(),
            $tickets,
            new Email($command->email)
        );

        $this->orderRepository->save($order);

        return $ticketIds;
    }
}
