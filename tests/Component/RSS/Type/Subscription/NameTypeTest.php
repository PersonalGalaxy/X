<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\RSS\Type\Subscription;

use PersonalGalaxy\X\Component\RSS\Type\Subscription\NameType;
use PersonalGalaxy\RSS\Entity\Subscription\Name;
use Innmind\Neo4j\ONM\{
    Type,
    Types,
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class NameTypeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Type::class,
            NameType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )
        );
    }

    public function testIsNotNullable()
    {
        $this->assertFalse(
            NameType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )->isNullable()
        );
    }

    public function testIdentifiers()
    {
        $this->assertSame(
            ['rss_subscription_name'],
            NameType::identifiers()->toPrimitive()
        );
    }

    public function testForDatabase()
    {
        $type = NameType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertSame('foo', $type->forDatabase(new Name('foo')));
    }

    public function testFromDatabase()
    {
        $type = NameType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertInstanceOf(Name::class, $type->fromDatabase('foo'));
        $this->assertSame('foo', (string) $type->fromDatabase('foo'));
    }
}
