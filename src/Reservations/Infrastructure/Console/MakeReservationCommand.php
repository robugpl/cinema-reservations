<?php

declare(strict_types=1);

namespace App\Reservations\Infrastructure\Console;

use App\Reservations\Application\Command\MakeReservation\MakeReservationCommand as MakeReservationAppCommand;
use App\Reservations\Application\Command\MakeReservation\MakeReservationCommandHandler;
use App\Reservations\Domain\Movie;
use App\Reservations\Domain\ReservationStatus;
use App\Reservations\Domain\Screening;
use App\Reservations\Domain\ScreeningRoom\ScreeningRoom;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:reservations:make',
    description: 'Make a new reservation'
)]
class MakeReservationCommand extends Command
{
    public function __construct(
        private readonly MakeReservationCommandHandler $handler,
        private readonly EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // 1. Wybór filmu
        $movies = $this->em->getRepository(Movie::class)->findAll();
        if (empty($movies)) {
            $io->error('Brak filmów w bazie.');
            return Command::FAILURE;
        }

        $movieChoices = [];
        $movieMap = [];
        $i = 1;
        foreach ($movies as $m) {
            $movieChoices[$i] = $m->getTitle();
            $movieMap[$m->getTitle()] = $m->getId()->toString();
            $i++;
        }

        $chosenMovieTitle = $io->choice('Wybierz film', $movieChoices);
        $movieId = $movieMap[$chosenMovieTitle];

        // 2. Wybór terminu
        $screenings = $this->em->getRepository(Screening::class)->findBy(['movieId' => $movieId]);
        if (empty($screenings)) {
            $io->error('Brak seansów dla wybranego filmu.');
            return Command::FAILURE;
        }

        $screeningChoices = [];
        $screeningMap = [];
        $i = 1;
        /** @var Screening $s */
        foreach ($screenings as $s) {
            $formattedDate = $s->getStartDate()->format('Y-m-d H:i');
            $screeningChoices[$i] = $formattedDate;
            $screeningMap[$formattedDate] = $s->getScreeningId()->toString();
            $i++;
        }

        $chosenScreeningDate = $io->choice('Wybierz termin', $screeningChoices);
        $screeningId = $screeningMap[$chosenScreeningDate];

        // 3. Wybór miejsc
        /** @var Screening $screening */
        $screening = $this->em->getRepository(Screening::class)->find($screeningId);
        /** @var ScreeningRoom $room */
        $room = $this->em->getRepository(ScreeningRoom::class)->find($screening->getScreeningRoomId()->toString());

        $reservedSeatIds = [];
        foreach ($screening->getReservations() as $res) {
            if ($res->getReservationStatus() === ReservationStatus::PENDING || $res->getReservationStatus() === ReservationStatus::REDEEMED) {
                foreach ($res->getSeatIds() as $resSeatId) {
                    $reservedSeatIds[] = $resSeatId->toString();
                }
            }
        }

        $availableSeats = [];
        foreach ($room->getRows() as $row) {
            foreach ($row->getSeats() as $seat) {
                if (!in_array($seat->getId()->toString(), $reservedSeatIds, true)) {
                    $availableSeats[$seat->getId()->toString()] = sprintf('[%d,%s]', $row->getRowNumber(), $seat->getLabel());
                }
            }
        }

        if (empty($availableSeats)) {
            $io->error('Brak wolnych miejsc na ten seans.');
            return Command::FAILURE;
        }

        $question = new ChoiceQuestion('Wybierz miejsca (oddziel przecinkami, np. [1,A1], [1,A2])', array_values($availableSeats));
        $question->setMultiselect(true);
        $selectedSeatLabels = $io->askQuestion($question);

        $selectedSeatIds = [];
        foreach ($selectedSeatLabels as $label) {
            $id = array_search($label, $availableSeats, true);
            if ($id !== false) {
                $selectedSeatIds[] = $id;
            }
        }

        // 4. E-mail
        $emailQuestion = new Question('Podaj adres e-mail');
        $email = $io->askQuestion($emailQuestion);

        if (!$email) {
            $io->error('Adres e-mail jest wymagany.');
            return Command::FAILURE;
        }

        // 5. Potwierdzenie i realizacja
        $command = new MakeReservationAppCommand($screeningId, $selectedSeatIds, $email);
        try {
            $reservationId = $this->handler->handle($command);
            $io->success('Zarezerwowano pomyślnie. Twój identyfikator rezerwacji to: ' . $reservationId);
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Błąd podczas rezerwacji: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
