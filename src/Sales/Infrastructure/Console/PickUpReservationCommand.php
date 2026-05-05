<?php

declare(strict_types=1);

namespace App\Sales\Infrastructure\Console;

use App\Sales\Application\Command\PickUpReservation\PickUpReservationCommand as AppPickUpReservationCommand;
use App\Sales\Application\Command\PickUpReservation\PickUpReservationCommandHandler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:sales:pickup',
    description: 'Pick up a reservation and create an order'
)]
class PickUpReservationCommand extends Command
{
    public function __construct(
        private readonly PickUpReservationCommandHandler $handler
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

        try {
            $ticketIds = $this->handler->handle(new AppPickUpReservationCommand($id));

            $count = count($ticketIds);
            $totalPln = $count * 20;

            $io->success(sprintf(
                'Rezerwacja odebrana pomyślnie! Utworzono zamówienie i %d bilet(ów) na kwotę %d PLN.',
                $count,
                $totalPln
            ));
            $io->section('Twoje wygenerowane bilety:');
            $io->listing($ticketIds);

            return Command::SUCCESS;
        } catch (\InvalidArgumentException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        } catch (\Exception $e) {
            $io->error('Błąd podczas odbioru rezerwacji: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
