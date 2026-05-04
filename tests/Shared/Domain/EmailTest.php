<?php

declare(strict_types=1);

namespace App\Tests\Shared\Domain;

use App\Shared\Domain\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function testItCreatesEmailWithValidAddress(): void
    {
        $email = new Email('test@example.com');
        $this->assertEquals('test@example.com', $email->toString());
        $this->assertEquals('test@example.com', (string) $email);
    }

    public function testItThrowsExceptionForInvalidEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address format.');

        new Email('invalid-email-address');
    }
}
