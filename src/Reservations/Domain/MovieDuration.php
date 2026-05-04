<?php

declare(strict_types=1);

namespace App\Reservations\Domain;

use App\Reservations\Domain\Exception\InvalidMovieDurationException;

readonly class MovieDuration
{
    public function __construct(private int $seconds)
    {
        if ($seconds <= 0) {
            throw new InvalidMovieDurationException();
        }
    }

    public function toMinutes(): int
    {
        return (int) floor($this->seconds / 60);
    }

    public function toSeconds(): int
    {
        return $this->seconds;
    }
}
