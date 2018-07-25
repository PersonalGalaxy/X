<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Files\Entity;

use PersonalGalaxy\X\Component\Files\Entity\Folder;
use PersonalGalaxy\Files\Entity\Folder\Identity;
use Innmind\Neo4j\ONM\Identity\Uuid;
use PHPUnit\Framework\TestCase;

class FolderTest extends TestCase
{
    public function testInterface()
    {
        $identity = new Folder('6399dcfa-bb50-4322-892b-eda3add5abe8');

        $this->assertInstanceOf(Uuid::class, $identity);
        $this->assertInstanceOf(Identity::class, $identity);
        $this->assertTrue($identity->equals($identity));
        $this->assertTrue($identity->equals(new Folder('6399dcfa-bb50-4322-892b-eda3add5abe8')));
        $this->assertFalse($identity->equals(new Folder('6399dcfa-bb50-4322-892b-eda3add5abe9')));
    }
}
