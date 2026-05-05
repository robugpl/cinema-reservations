<?php

declare(strict_types=1);

namespace App\Sales\Infrastructure\Console;

use App\Sales\Application\Command\PickUpReservation\PickUpReservationCommand as PickUpReservationAppCommand;
use App\Sales\Application\Command\PickUpReservation\PickUpReservationCommandHandler;
use App\Reservations\Domain\Reservation;
use App\Reservations\Domain\ReservationStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:sales:pickup',
    description: 'Pick up reservations and create an order'
)]
class PickUpReservationCommand extends Command
{
    public function __construct(
        private readonly PickUpReservationCommandHandler $handler,
        private readonly EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $question = new Question('Podaj identyfikator rezerwacji');
        $id = $io->askQuestion($question);

        if (!$id) {
            $io->error('Musisz podać ID rezerwacji.');
            return Command::FAILURE;
        }

        $id = trim($id);

        $ticketsData = [];

        /** @var Reservation $reservation */
        $reservation = $this->em->getRepository(Reservation::class)->find($id);

        if (!$reservation) {
            $io->error(sprintf('Rezerwacja %s nie istnieje.', $id));
            return Command::FAILURE;
        }

        if ($reservation->getReservationStatus() !== ReservationStatus::PENDING) {
            $io->error(sprintf('Rezerwacja %s ma status %s i nie może zostać odebrana.', $id, $reservation->getReservationStatus()->value));
            return Command::FAILURE;
        }

        $email = $reservation->getEmail()->toString();

        foreach ($reservation->getSeatIds() as $seatId) {
            $ticketsData[] = [
                'screeningId' => $reservation->getScreeningId()->toString(),
                'seatId' => $seatId->toString(),
                'priceInMinorUnits' => 2000 // 20 PLN
            ];
        }

        // Zmiana statusu rezerwacji na zrealizowane
        $reservation->redeem();

        $command = new PickUpReservationAppCommand($email, $ticketsData);

        try {
            $ticketIds = $this->handler->handle($command);
            $this->em->flush(); // flush both Reservation updates and Order persist
            
            $io->success('Rezerwacja odebrana pomyślnie! Utworzono zamówienie i bilety na kwotę ' . (count($ticketsData) * 20) . ' PLN.');
            $io->section('Twoje wygenerowane bilety:');
            $io->listing($ticketIds);
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Błąd podczas odbioru rezerwacji: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
