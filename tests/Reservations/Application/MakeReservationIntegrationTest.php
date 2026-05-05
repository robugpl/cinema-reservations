<?php

declare(strict_types=1);

namespace App\Tests\Reservations\Application;

use App\Reservations\Application\Command\MakeReservation\MakeReservationCommand;
use App\Reservations\Application\Command\MakeReservation\MakeReservationCommandHandler;
use App\Reservations\Domain\Reservation;
use App\Reservations\Domain\Screening;
use App\Reservations\Domain\ScreeningId;
use App\Reservations\Domain\ScreeningRoom\SeatId;
use App\Reservations\Domain\ReservationStatus;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\DataFixtures\AppFixtures;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;

class MakeReservationIntegrationTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private MakeReservationCommandHandler $handler;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
        $this->handler = static::getContainer()->get(MakeReservationCommandHandler::class);

        // Update schema
        $schemaTool = new SchemaTool($this->em);
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        // Load fixtures
        $loader = new Loader();
        $loader->addFixture(new AppFixtures());
        
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->execute($loader->getFixtures());
    }

    public function testItSuccessfullyMakesAReservation(): void
    {
        // 1. Arrange: Get a screening from fixtures
        /** @var Screening[] $screenings */
        $screenings = $this->em->getRepository(Screening::class)->findAll();
        $screening = $screenings[0];
        $screeningId = $screening->getScreeningId()->toString();

        $seatIds = [
            SeatId::generate()->toString(),
            SeatId::generate()->toString()
        ];
        $email = 'test_integration@cinema.com';

        $command = new MakeReservationCommand($screeningId, $seatIds, $email);

        // 2. Act
        $reservationIdString = $this->handler->handle($command);

        // 3. Assert
        $this->em->clear(); // Clear identity map to force fetch from DB

        /** @var Reservation $reservation */
        $reservation = $this->em->getRepository(Reservation::class)->find($reservationIdString);

        $this->assertNotNull($reservation);
        $this->assertEquals($screeningId, $reservation->getScreeningId()->toString());
        $this->assertEquals($email, $reservation->getEmail()->toString());
        $this->assertEquals(ReservationStatus::PENDING, $reservation->getReservationStatus());
        
        $savedSeatIds = array_map(fn($id) => $id->toString(), $reservation->getSeatIds());
        $this->assertEquals($seatIds, $savedSeatIds);
    }
}
