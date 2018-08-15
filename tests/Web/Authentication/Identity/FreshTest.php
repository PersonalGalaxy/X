<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Web\Authentication\Identity;

use PersonalGalaxy\X\Web\Authentication\Identity\Fresh;
use Innmind\HttpAuthentication\Identity;
use PHPUnit\Framework\TestCase;

class FreshTest extends TestCase
{
    public function testInterface()
    {
        $fresh = new Fresh(
            $original = $this->createMock(Identity::class)
        );
        $original
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('foo');

        $this->assertInstanceOf(Identity::class, $fresh);
        $this->assertSame($original, $fresh->original());
        $this->assertSame('foo', (string) $fresh);
    }
}
