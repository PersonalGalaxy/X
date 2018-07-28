<?php
declare(strict_types = 1);

namespace Tests\PersonalGalaxy\X\Component\RSS\Type\Article;

use PersonalGalaxy\X\Component\RSS\Type\Article\AuthorType;
use PersonalGalaxy\RSS\Entity\Article\Author;
use Innmind\Neo4j\ONM\{
    Type,
    Types,
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class AuthorTypeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Type::class,
            AuthorType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )
        );
    }

    public function testIsNotNullable()
    {
        $this->assertFalse(
            AuthorType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )->isNullable()
        );
    }

    public function testIdentifiers()
    {
        $this->assertSame(
            ['rss_article_author'],
            AuthorType::identifiers()->toPrimitive()
        );
    }

    public function testForDatabase()
    {
        $type = AuthorType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertSame('foo', $type->forDatabase(new Author('foo')));
    }

    public function testFromDatabase()
    {
        $type = AuthorType::fromConfig(
            $this->createMock(MapInterface::class),
            new Types
        );

        $this->assertInstanceOf(Author::class, $type->fromDatabase('foo'));
        $this->assertSame('foo', (string) $type->fromDatabase('foo'));
    }
}
