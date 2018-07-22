<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Calendar\Entity;

use PersonalGalaxy\X\Component\Calendar\Entity\Event;
use PersonalGalaxy\Calendar\Entity\Event\Identity;
use Innmind\Neo4j\ONM\Identity\Uuid;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function testInterface()
    {
        $identity = new Event('6399dcfa-bb50-4322-892b-eda3add5abe8');

        $this->assertInstanceOf(Uuid::class, $identity);
        $this->assertInstanceOf(Identity::class, $identity);
    }
}
