<?php

declare(strict_types=1);

namespace App\Tests\Sales\Application;

use App\Reservations\Application\Command\MakeReservation\MakeReservationCommand;
use App\Reservations\Application\Command\MakeReservation\MakeReservationCommandHandler;
use App\Reservations\Domain\Reservation;
use App\Reservations\Domain\ReservationStatus;
use App\Reservations\Domain\Screening;
use App\Reservations\Domain\ScreeningRoom\SeatId;
use App\Sales\Application\Command\PickUpReservation\PickUpReservationCommand;
use App\Sales\Application\Command\PickUpReservation\PickUpReservationCommandHandler;
use App\Sales\Domain\Order;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\DataFixtures\AppFixtures;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;

class PickUpReservationIntegrationTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private MakeReservationCommandHandler $makeHandler;
    private PickUpReservationCommandHandler $pickUpHandler;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
        $this->makeHandler = static::getContainer()->get(MakeReservationCommandHandler::class);
        $this->pickUpHandler = static::getContainer()->get(PickUpReservationCommandHandler::class);

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

    public function testItSuccessfullyPicksUpAReservationAndCreatesOrder(): void
    {
        // 1. Arrange: Make a reservation
        /** @var Screening[] $screenings */
        $screenings = $this->em->getRepository(Screening::class)->findAll();
        $screening = $screenings[0];
        $screeningId = $screening->getScreeningId()->toString();

        $seatIds = [SeatId::generate()->toString(), SeatId::generate()->toString()];
        $email = 'buy@cinema.com';

        $reservationIdString = $this->makeHandler->handle(
            new MakeReservationCommand($screeningId, $seatIds, $email)
        );

        $this->em->clear();

        // 2. Act: Pick it up via CommandHandler (orchestrates both BCs)
        $ticketIds = $this->pickUpHandler->handle(new PickUpReservationCommand($reservationIdString));

        $this->em->clear();

        // 3. Assert
        $this->assertCount(2, $ticketIds);

        /** @var Reservation $updatedReservation */
        $updatedReservation = $this->em->getRepository(Reservation::class)->find($reservationIdString);
        $this->assertEquals(ReservationStatus::REDEEMED, $updatedReservation->getReservationStatus());

        $orders = $this->em->getRepository(Order::class)->findAll();
        $this->assertCount(1, $orders);

        /** @var Order $order */
        $order = $orders[0];
        $this->assertEquals($email, $order->getEmail()->toString());
        $this->assertEquals(4000, $order->getTotal()->getAmountInMinorUnit()); // 2 tickets * 2000
        $this->assertCount(2, $order->getTickets());
    }
}
