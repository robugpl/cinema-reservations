<?php

declare(strict_types=1);

namespace App\Tests\Reservations\Domain;

use App\Reservations\Domain\Exception\InvalidMovieDurationException;
use App\Reservations\Domain\MovieDuration;
use PHPUnit\Framework\TestCase;

class MovieDurationTest extends TestCase
{
    public function testItConvertsSecondsToMinutesCorrectly(): void
    {
        $duration = new MovieDuration(7200); // 120 minutes

        $this->assertEquals(120, $duration->toMinutes());
        $this->assertEquals(7200, $duration->toSeconds());
    }

    public function testItHandlesRoundingDownMinutes(): void
    {
        $duration = new MovieDuration(7259); // 120 minutes and 59 seconds

        $this->assertEquals(120, $duration->toMinutes());
    }

    public function testItValidatesPositiveDuration(): void
    {
        $this->expectException(InvalidMovieDurationException::class);
        new MovieDuration(-1);
    }
}
