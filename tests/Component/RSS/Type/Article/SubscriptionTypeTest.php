<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\RSS\Type\Article;

use PersonalGalaxy\X\Component\RSS\{
    Type\Article\SubscriptionType,
    Entity\Subscription,
};
use Innmind\Neo4j\ONM\{
    Type,
    Types,
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class SubscriptionTypeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Type::class,
            SubscriptionType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )
        );
    }

    public function testIsNotNullable()
    {
        $this->assertFalse(
            SubscriptionType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )->isNullable()
        );
    }

    public function testIdentifiers()
    {
        $this->assertSame(
            ['rss_article_subscription'],
            SubscriptionType::identifiers()->toPrimitive()
        );
    }

    public function testForDatabase()
    {
        $type = SubscriptionType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertSame(
            '198c3332-a262-475b-9d93-8e90e5a6613b',
            $type->forDatabase(new Subscription('198c3332-a262-475b-9d93-8e90e5a6613b'))
        );
    }

    public function testFromDatabase()
    {
        $type = SubscriptionType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertInstanceOf(
            Subscription::class,
            $type->fromDatabase('198c3332-a262-475b-9d93-8e90e5a6613b')
        );
        $this->assertSame(
            '198c3332-a262-475b-9d93-8e90e5a6613b',
            (string) $type->fromDatabase('198c3332-a262-475b-9d93-8e90e5a6613b')
        );
    }
}
