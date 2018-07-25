<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Files\Entity;

use PersonalGalaxy\X\Component\Files\Entity\File;
use PersonalGalaxy\Files\Entity\File\Identity;
use Innmind\Neo4j\ONM\Identity\Uuid;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testInterface()
    {
        $identity = new File('6399dcfa-bb50-4322-892b-eda3add5abe8');

        $this->assertInstanceOf(Uuid::class, $identity);
        $this->assertInstanceOf(Identity::class, $identity);
    }
}
