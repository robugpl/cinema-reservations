<?php

declare(strict_types=1);

namespace App\Reservations\Domain;

class Movie
{
    public function __construct(
        private readonly MovieId $id,
        private readonly MovieDuration $duration,
        private readonly string $title,
        private readonly string $director
    ) {
    }

    public function getId(): MovieId
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDirector(): string
    {
        return $this->director;
    }

    public function getDuration(): MovieDuration
    {
        return $this->duration;
    }
}
