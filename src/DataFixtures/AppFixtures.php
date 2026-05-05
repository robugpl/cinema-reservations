<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Reservations\Domain\Movie;
use App\Reservations\Domain\MovieDuration;
use App\Reservations\Domain\MovieId;
use App\Reservations\Domain\Screening;
use App\Reservations\Domain\ScreeningId;
use App\Reservations\Domain\ScreeningRoom\Row;
use App\Reservations\Domain\ScreeningRoom\ScreeningRoom;
use App\Reservations\Domain\ScreeningRoom\ScreeningRoomId;
use App\Reservations\Domain\ScreeningRoom\Seat;
use App\Reservations\Domain\ScreeningRoom\SeatId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 1. Create a Movie
        $movieId = MovieId::generate();
        $movie = new Movie(
            $movieId,
            new MovieDuration(120 * 60), // 120 minutes
            'The Matrix',
            'Lana Wachowski, Lilly Wachowski'
        );
        $manager->persist($movie);

        // 2. Create a Screening Room
        $roomId = ScreeningRoomId::generate();
        
        // Generate 10 rows, 10 seats each
        $rows = [];
        for ($r = 1; $r <= 10; $r++) {
            $seats = [];
            for ($s = 1; $s <= 10; $s++) {
                $seatLabel = chr(64 + $r) . $s; // A1, A2, B1, etc.
                $seats[] = new Seat(SeatId::generate(), $seatLabel);
            }
            $rows[] = new Row($r, $seats);
        }

        $room = new ScreeningRoom(
            $roomId,
            'Room 1 - IMAX',
            $rows
        );
        $manager->persist($room);

        // 3. Create a Schedule (Screenings)
        // Let's create a screening for tomorrow
        $startDate = (new \DateTimeImmutable())->modify('+1 day')->setTime(20, 0, 0);
        $screening = new Screening(
            ScreeningId::generate(),
            $startDate,
            $roomId,
            $movieId
        );
        $manager->persist($screening);
        
        // Let's create another screening for the day after tomorrow
        $startDate2 = (new \DateTimeImmutable())->modify('+2 days')->setTime(18, 30, 0);
        $screening2 = new Screening(
            ScreeningId::generate(),
            $startDate2,
            $roomId,
            $movieId
        );
        $manager->persist($screening2);

        $manager->flush();
    }
}
