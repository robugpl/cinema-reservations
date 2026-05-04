<?php

declare(strict_types=1);

namespace App\Reservations\Domain\Exception;

class InvalidMovieDurationException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Movie duration must be greater than 0 seconds');
    }
}
