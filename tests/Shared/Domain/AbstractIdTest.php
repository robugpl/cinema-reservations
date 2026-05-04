<?php

declare(strict_types=1);

namespace App\Tests\Shared\Domain;

use App\Shared\Domain\AbstractId;
use PHPUnit\Framework\TestCase;

class DummyId extends AbstractId
{
    protected static function getPrefix(): string
    {
        return 'test_';
    }
}

class AbstractIdTest extends TestCase
{
    public function testItGeneratesIdWithCorrectPrefix(): void
    {
        $id = DummyId::generate();
        
        $this->assertStringStartsWith('test_', $id->toString());
        $this->assertGreaterThan(5, strlen($id->toString()));
    }
}
