<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\RSS\Type\Article;

use PersonalGalaxy\X\Component\RSS\Type\Article\TitleType;
use PersonalGalaxy\RSS\Entity\Article\Title;
use Innmind\Neo4j\ONM\{
    Type,
    Types,
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class TitleTypeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Type::class,
            TitleType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )
        );
    }

    public function testIsNotNullable()
    {
        $this->assertFalse(
            TitleType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )->isNullable()
        );
    }

    public function testIdentifiers()
    {
        $this->assertSame(
            ['rss_article_title'],
            TitleType::identifiers()->toPrimitive()
        );
    }

    public function testForDatabase()
    {
        $type = TitleType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertSame('foo', $type->forDatabase(new Title('foo')));
    }

    public function testFromDatabase()
    {
        $type = TitleType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertInstanceOf(Title::class, $type->fromDatabase('foo'));
        $this->assertSame('foo', (string) $type->fromDatabase('foo'));
    }
}
