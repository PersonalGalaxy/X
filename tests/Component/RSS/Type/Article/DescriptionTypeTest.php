<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\RSS\Type\Article;

use PersonalGalaxy\X\Component\RSS\Type\Article\DescriptionType;
use PersonalGalaxy\RSS\Entity\Article\Description;
use Innmind\Neo4j\ONM\{
    Type,
    Types,
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class DescriptionTypeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Type::class,
            DescriptionType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )
        );
    }

    public function testIsNotNullable()
    {
        $this->assertFalse(
            DescriptionType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )->isNullable()
        );
    }

    public function testIdentifiers()
    {
        $this->assertSame(
            ['rss_article_description'],
            DescriptionType::identifiers()->toPrimitive()
        );
    }

    public function testForDatabase()
    {
        $type = DescriptionType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertSame('foo', $type->forDatabase(new Description('foo')));
    }

    public function testFromDatabase()
    {
        $type = DescriptionType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertInstanceOf(Description::class, $type->fromDatabase('foo'));
        $this->assertSame('foo', (string) $type->fromDatabase('foo'));
    }
}
