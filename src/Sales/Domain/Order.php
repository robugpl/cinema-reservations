<?php

declare(strict_types=1);

namespace App\Sales\Domain;

use App\Sales\Domain\Exception\OrderCannotBeEmpty;
use App\Shared\Domain\Email;
use App\Shared\Domain\Money;

class Order
{
    private Money $total;

    /**
     * @param Ticket[] $tickets
     */
    public function __construct(
        private readonly OrderId  $id,
        private iterable $tickets,
        private readonly Email    $email
    ) {

        if(empty($this->tickets)) {
            throw new OrderCannotBeEmpty();
        }
        $this->countTotal();
    }

    private function countTotal(): void
    {
        $sumInMinorUnit = 0;
        foreach ($this->tickets as $ticket) {
            $sumInMinorUnit += $ticket->getPrice()->getAmountInMinorUnit();
        }
        $this->total = new Money($sumInMinorUnit);
    }

    public function getTotal(): Money
    {
        return $this->total;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @return iterable<Ticket>
     */
    public function getTickets(): iterable
    {
        return $this->tickets;
    }
}
