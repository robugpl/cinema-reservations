<?php
declare(strict_types=1);
namespace App\Reservations\Infrastructure\Persistence\Doctrine\Type;

use App\Reservations\Domain\ScreeningRoom\Row;
use App\Reservations\Domain\ScreeningRoom\Seat;
use App\Reservations\Domain\ScreeningRoom\SeatId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class ScreeningRoomRowsType extends Type
{
    public const string NAME = 'screening_room_rows';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return [];
        }

        $data = json_decode($value, true);
        $rows = [];
        foreach ($data as $rowData) {
            $seats = [];
            foreach ($rowData['seats'] as $seatData) {
                $seats[] = new Seat(new SeatId($seatData['id']), $seatData['label']);
            }
            $rows[] = new Row($rowData['rowNumber'], $seats);
        }
        return $rows;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return null;
        }

        $data = [];
        /** @var Row $row */
        foreach ($value as $row) {
            $seats = [];
            foreach ($row->getSeats() as $seat) {
                $seats[] = [
                    'id' => $seat->getId()->toString(),
                    'label' => $seat->getLabel(),
                ];
            }
            $data[] = [
                'rowNumber' => $row->getRowNumber(),
                'seats' => $seats,
            ];
        }

        return json_encode($data);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
