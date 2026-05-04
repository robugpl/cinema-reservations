<?php

declare(strict_types=1);

namespace App\Reservations\Domain;

interface ScreeningRepositoryInterface
{
    public function getById(ScreeningId $id): ?Screening;

    public function save(Screening $screening): void;
}
