<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Calendar\Entity;

use PersonalGalaxy\X\Component\Calendar\Entity\Agenda;
use PersonalGalaxy\Calendar\Entity\Agenda\Identity;
use Innmind\Neo4j\ONM\Identity\Uuid;
use PHPUnit\Framework\TestCase;

class AgendaTest extends TestCase
{
    public function testInterface()
    {
        $identity = new Agenda('6399dcfa-bb50-4322-892b-eda3add5abe8');

        $this->assertInstanceOf(Uuid::class, $identity);
        $this->assertInstanceOf(Identity::class, $identity);
    }
}
