<?php

declare(strict_types=1);

namespace App\Reservations\Domain\ScreeningRoom;

class ScreeningRoom
{
    /** @var array<string, Seat> */
    private array $seatsIndex = [];

    /**
     * @param Row[] $rows
     */
    public function __construct(
        private readonly ScreeningRoomId $id,
        private readonly string $name,
        private readonly array $rows
    ) {
        foreach ($this->rows as $row) {
            foreach ($row->getSeats() as $seat) {
                $this->seatsIndex[$seat->getId()->toString()] = $seat;
            }
        }
    }

    public function getId(): ScreeningRoomId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Row[]
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    public function getSeat(SeatId $seatId): ?Seat
    {
        return $this->seatsIndex[$seatId->toString()] ?? null;
    }
}
