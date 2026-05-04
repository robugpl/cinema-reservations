<?php

declare(strict_types=1);

namespace App\Tests\Reservations\Domain\ScreeningRoom;

use App\Reservations\Domain\ScreeningRoom\Row;
use App\Reservations\Domain\ScreeningRoom\ScreeningRoom;
use App\Reservations\Domain\ScreeningRoom\ScreeningRoomId;
use App\Reservations\Domain\ScreeningRoom\Seat;
use App\Reservations\Domain\ScreeningRoom\SeatId;
use PHPUnit\Framework\TestCase;

class ScreeningRoomTest extends TestCase
{
    public function testItCreatesScreeningRoomWithRowsAndBuildsSeatsIndex(): void
    {
        $seat1 = new Seat(SeatId::generate(), '01');
        $seat2 = new Seat(SeatId::generate(), '02');
        $seat3 = new Seat(SeatId::generate(), '01');

        $rows = [
            new Row(1, [$seat1, $seat2]),
            new Row(2, [$seat3])
        ];

        $roomId = ScreeningRoomId::generate();
        $room = new ScreeningRoom($roomId, 'Sala nr 1 (IMAX)', $rows);

        $this->assertEquals($roomId, $room->getId());
        $this->assertEquals('Sala nr 1 (IMAX)', $room->getName());
        $this->assertCount(2, $room->getRows());
        $this->assertEquals(2, $room->getRows()[1]->getRowNumber());

        // Assert seats index works correctly
        $this->assertSame($seat1, $room->getSeat($seat1->getId()));
        $this->assertSame($seat3, $room->getSeat($seat3->getId()));

        $nonExistentSeatId = SeatId::generate();
        $this->assertNull($room->getSeat($nonExistentSeatId));
    }
}
