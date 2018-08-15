<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\Identity\Entity;

use PersonalGalaxy\X\Component\Identity\Entity\Identity;
use PersonalGalaxy\Identity\Entity\Identity\Identity as IdentityInterface;
use PersonalGalaxy\Calendar\Entity\Agenda\User;
use PersonalGalaxy\RSS\Entity\Subscription\User as Subscription;
use Innmind\Neo4j\ONM\Identity\Uuid;
use Innmind\HttpAuthentication\Identity as Auth;
use PHPUnit\Framework\TestCase;

class IdentityTest extends TestCase
{
    public function testInterface()
    {
        $identity = new Identity('6399dcfa-bb50-4322-892b-eda3add5abe8');

        $this->assertInstanceOf(Uuid::class, $identity);
        $this->assertInstanceOf(IdentityInterface::class, $identity);
        $this->assertInstanceOf(User::class, $identity);
        $this->assertInstanceOf(Subscription::class, $identity);
        $this->assertInstanceOf(Auth::class, $identity);
    }
}
