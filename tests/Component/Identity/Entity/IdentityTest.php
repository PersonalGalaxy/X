<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Identity\Entity;

use PersonalGalaxy\X\Component\Identity\Entity\Identity;
use PersonalGalaxy\Identity\Entity\Identity\Identity as IdentityInterface;
use Innmind\Neo4j\ONM\Identity\Uuid;
use PHPUnit\Framework\TestCase;

class IdentityTest extends TestCase
{
    public function testInterface()
    {
        $identity = new Identity('6399dcfa-bb50-4322-892b-eda3add5abe8');

        $this->assertInstanceOf(Uuid::class, $identity);
        $this->assertInstanceOf(IdentityInterface::class, $identity);
    }
}