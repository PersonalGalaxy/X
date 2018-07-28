<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\RSS\Entity;

use PersonalGalaxy\X\Component\RSS\Entity\Subscription;
use PersonalGalaxy\RSS\Entity\Subscription\Identity;
use Innmind\Neo4j\ONM\Identity\Uuid;
use PHPUnit\Framework\TestCase;

class SubscriptionTest extends TestCase
{
    public function testInterface()
    {
        $identity = new Subscription('6399dcfa-bb50-4322-892b-eda3add5abe8');

        $this->assertInstanceOf(Uuid::class, $identity);
        $this->assertInstanceOf(Identity::class, $identity);
    }
}
