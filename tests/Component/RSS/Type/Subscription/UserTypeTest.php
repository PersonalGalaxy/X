<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\RSS\Type\Subscription;

use PersonalGalaxy\X\Component\{
    RSS\Type\Subscription\UserType,
    Identity\Entity\Identity,
};
use Innmind\Neo4j\ONM\{
    Type,
    Types,
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class UserTypeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Type::class,
            UserType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )
        );
    }

    public function testIsNotNullable()
    {
        $this->assertFalse(
            UserType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )->isNullable()
        );
    }

    public function testIdentifiers()
    {
        $this->assertSame(
            ['rss_subscription_user'],
            UserType::identifiers()->toPrimitive()
        );
    }

    public function testForDatabase()
    {
        $type = UserType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertSame(
            '5a6b9979-2905-4ccb-8fe5-96b62c1b0876',
            $type->forDatabase(new Identity('5a6b9979-2905-4ccb-8fe5-96b62c1b0876'))
        );
    }

    public function testFromDatabase()
    {
        $type = UserType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertInstanceOf(
            Identity::class,
            $type->fromDatabase('5a6b9979-2905-4ccb-8fe5-96b62c1b0876')
        );
        $this->assertSame(
            '5a6b9979-2905-4ccb-8fe5-96b62c1b0876',
            (string) $type->fromDatabase('5a6b9979-2905-4ccb-8fe5-96b62c1b0876')
        );
    }
}
